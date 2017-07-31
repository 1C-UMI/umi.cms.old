<?php
	class dispatches extends def_module implements iDispatches{
		public function __construct() {
			parent::__construct();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				cmsController::getInstance()->getModule('users');
				$this->__loadLib("__admin.php");
				$this->__implement("__dispatches");

				$this->__loadLib("__dispatches.php");
				$this->__implement("__dispatches_dispatches");

				$this->__loadLib("__dispatches_edit.php");
				$this->__implement("__dispatches_edit_dispatches");

				$this->__loadLib("__dispatches_add.php");
				$this->__implement("__dispatches_add_dispatches");

				$this->__loadLib("__messages.php");
				$this->__implement("__messages_messages");

				$this->__loadLib("__messages_add.php");
				$this->__implement("__messages_add_messages");

				$this->__loadLib("__messages_edit.php");
				$this->__implement("__messages_edit_messages");

				$this->__loadLib("__subscribers.php");
				$this->__implement("__subscribers_subscribers");

				$this->__loadLib("__subscribers_add.php");
				$this->__implement("__subscribers_add_subscribers");

				$this->__loadLib("__subscribers_edit.php");
				$this->__implement("__subscribers_edit_subscribers");

				$this->__loadLib("__releasees.php");
				$this->__implement("__releasees_releasees");

				$this->sheets_reset();
				$this->sheets_add("Рассылки", "dispatches_list");
				$this->sheets_add("Подписчики", "subscribers_list");
			} else {
				$this->__loadLib("__custom.php");
				$this->__implement("__custom_dispatches");
			}

			$regedit = regedit::getInstance();
			$this->per_page = (int) $regedit->getVal("//modules/dispatches/per_page");
			if (!$this->per_page) $this->per_page = 15;
		}

		public function unsubscribe() {
			$sResult = "%subscribe_unsubscribed_failed%";
			$iSbsId = (int) $_REQUEST['param0'];
			$oSubscriber = umiObjectsCollection::getInstance()->getObject($iSbsId);

			if ($oSubscriber instanceof umiObject) { 
				$iSubscriberType = $oSubscriber->getTypeId();
				$oSubscriberType = umiObjectTypesCollection::getInstance()->getType($iSubscriberType);
				$iSubscriberHierarchyType = $oSubscriberType->getHierarchyTypeId();
				$oSubscriberHierarchyType = umiHierarchyTypesCollection::getInstance()->getType($iSubscriberHierarchyType);

				if($oSubscriberHierarchyType->getName() != "dispatches" || $oSubscriberHierarchyType->getExt() != "subscriber") {
					return $sResult;
				}

				if($oSubscriber->getValue("uid")) {
					$oSubscriber->setValue('subscriber_dispatches', null);
					$oSubscriber->commit();
				} else {
					umiObjectsCollection::getInstance()->delObject($iSbsId);
				}

				$sResult = "%subscribe_unsubscribed_ok%";
			}
			return $sResult;
		}

		public function subscribe($sTemplate = "default") {
			$sResult = "";
			if(!$sTemplate) $sTemplate = "default";
			list($sUnregistredForm, $sRegistredForm, $sDispatchesForm, $sDispatchRowForm) = def_module::loadTemplates("tpls/dispatches/{$sTemplate}.tpl", "subscribe_unregistred_user", "subscribe_registred_user", "subscriber_dispatches", "subscriber_dispatch_row");
			// subscriber type
			$iSbsHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "subscriber")->getId();
			$iSbsTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iSbsHierarchyTypeId);
			$oSbsType = umiObjectTypesCollection::getInstance()->getType($iSbsTypeId);
			// check user registred
			$this->is_auth = false;
			if($oMdlUsers = cmsController::getInstance()->getModule("users")) {
				if($oMdlUsers->is_auth()) {
					$iUserId = (int) $oMdlUsers->user_id;
					$this->is_auth = true;
					$this->user_id = $iUserId;
				}
			}
			if ($this->is_auth) {
				$arrRegBlock = array();
				// gen subscribe_registred_user form
				// check curr user in subscribers list
				$oSbsSelection = new umiSelection;
				$oSbsSelection->setObjectTypeFilter();
				$oSbsSelection->addObjectType($iSbsTypeId);
				$oSbsSelection->setPropertyFilter();
				$oSbsSelection->addPropertyFilterEqual($oSbsType->getFieldId('uid'), $this->user_id);
				$arrSbsSelResults = umiSelectionsParser::runSelection($oSbsSelection);
				$arrSbsDispatches = array();
				if (is_array($arrSbsSelResults) && count($arrSbsSelResults)) {
					// check user dispatches
					$iSbsId = $arrSbsSelResults[0];
					$oSubscriber = umiObjectsCollection::getInstance()->getObject($iSbsId);
					$arrSbsDispatches = $oSubscriber->getValue('subscriber_dispatches');
				}
				$arrRegBlock['subscriber_dispatches'] = self::parseDispatches($sDispatchesForm, $sDispatchRowForm, $arrSbsDispatches);
				$sResult = def_module::parseTemplate($sRegistredForm, $arrRegBlock);
			} else {
				// gen subscribe_unregistred_user form
				$arrUnregBlock = array();
				$iSbsGenderFldId = $oSbsType->getFieldId('gender');
				$oSbsGenderFld = umiFieldsCollection::getInstance()->getField($iSbsGenderFldId);
				$arrGenders = umiObjectsCollection::getInstance()->getGuidedItems($oSbsGenderFld->getGuideId());
				$sGenders = "";
				foreach ($arrGenders as $iGenderId => $sGenderName) {
					$sGenders .= "<option value=\"".$iGenderId."\">".$sGenderName."</option>";
				}
				$arrUnregBlock['sbs_genders'] = $sGenders;
				$sResult = def_module::parseTemplate($sUnregistredForm, $arrUnregBlock);
			}
			//$block_arr['action'] = $this->pre_lang . "/dispatcher/subscribe_do/";

			return $sResult;
		}
		
		protected function getAllDispatches() {
			$oDispsSelection = new umiSelection;
			$oDispsSelection->setObjectTypeFilter();
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "dispatch")->getId();
			$iDispTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oDispType = umiObjectTypesCollection::getInstance()->getType($iDispTypeId);
			$oDispsSelection->addObjectType($iDispTypeId);
			return umiSelectionsParser::runSelection($oDispsSelection);
		}

		protected function parseDispatches($sDispatchesForm, $sDispatchRowForm, $arrChecked=array(), $bOnlyChecked=false) {
			$arrDispSelResults = self::getAllDispatches();
			$arrDispsBlock = array();
			$arrDispsBlock['rows'] = "";
			if (is_array($arrDispSelResults) && count($arrDispSelResults)) {
				for ($iI=0; $iI<count($arrDispSelResults); $iI++) {
					$iNextDispId = $arrDispSelResults[$iI];
					$oNextDisp = umiObjectsCollection::getInstance()->getObject($iNextDispId);
					$arrDispRowBlock = array();
					$arrDispRowBlock['disp_id'] = $oNextDisp->getId();
					$arrDispRowBlock['disp_name'] = $oNextDisp->getName();
					$arrDispRowBlock['is_checked'] = (in_array($iNextDispId, $arrChecked)? 1: 0);
					$arrDispRowBlock['checked'] = ($arrDispRowBlock['is_checked']? "checked": "");
					if ($arrDispRowBlock['is_checked'] && $bOnlyChecked || !$bOnlyChecked) {
						$arrDispsBlock['rows'] .= def_module::parseTemplate($sDispatchRowForm, $arrDispRowBlock);
					}
				}
			}
			return def_module::parseTemplate($sDispatchesForm, $arrDispsBlock);
		}
		
		public function subscribe_do() {
			$sResult = "";
			// input
			$sSbsMail = trim($_REQUEST['sbs_mail']);
			$sSbsLName = $_REQUEST['sbs_lname'];
			$sSbsFName = $_REQUEST['sbs_fname'];
			$iSbsGender = (int) $_REQUEST['sbs_gender'];
			$sSbsFatherName = $_REQUEST['sbs_father_name'];
			$arrSbsDispatches = $_REQUEST['subscriber_dispatches'];
			// check user registred
			$this->is_auth = false;
			if($oMdlUsers = cmsController::getInstance()->getModule("users")) {
				if($oMdlUsers->is_auth()) {
					$iUserId = (int) $oMdlUsers->user_id;
					$this->is_auth = true;
					$this->user_id = $iUserId;
					if($oUserObj = umiObjectsCollection::getInstance()->getObject($iUserId)) {
						$sSbsMail = $oUserObj->getValue("e-mail");
						$sSbsLName = $oUserObj->getValue("lname");
						$sSbsFName = $oUserObj->getValue("fname");
						$sSbsFatherName = $oUserObj->getValue("father_name");
						$iSbsGender = $oUserObj->getValue("gender");
					}
				}
			}
			// check valid mail
			if (!umiMail::checkEmail($sSbsMail)) {
				return "%subscribe_incorrect_email%";
			}
			// check curr user in subscribers list
			$oSbsSelection = new umiSelection;
			$oSbsSelection->setObjectTypeFilter();
			$iSbsHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "subscriber")->getId();
			$iSbsTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iSbsHierarchyTypeId);
			$oSbsType = umiObjectTypesCollection::getInstance()->getType($iSbsTypeId);
			$oSbsSelection->addObjectType($iSbsTypeId);
			$oSbsSelection->setNamesFilter();
			$oSbsSelection->addNameFilterEquals($sSbsMail);
			$arrSbsSelResults = umiSelectionsParser::runSelection($oSbsSelection);
			$iSbsObjId = null;
			if (is_array($arrSbsSelResults) && count($arrSbsSelResults)) {
					$iSbsObjId = $arrSbsSelResults[0];

					if(!$this->is_auth) {
						list($template_block) = def_module::loadTemplates("tpls/dispatches/default.tpl", "subscribe_guest_alredy_subscribed");
						$block_arr = Array();
						$block_arr['unsubscribe_link'] = $this->pre_lang . "/dispatches/unsubscribe/" . $iSbsObjId . "/";
						return def_module::parseTemplate($template_block, $block_arr);
					}
			} else {
				// create sbs
				$iSbsObjId = umiObjectsCollection::getInstance()->addObject($sSbsMail, $iSbsTypeId);

				$from = regedit::getInstance()->getVal("//settings/fio_from");
        			$from_email = regedit::getInstance()->getVal("//settings/email_from");

				list($template_mail, $template_mail_subject) = def_module::loadTemplates("tpls/dispatches/default.tpl", "subscribe_confirm", "subscribe_confirm_subject");

				$mail_arr = Array();
				$mail_arr['domain'] = $domain = $_SERVER['HTTP_HOST'];
				$mail_arr['unsubscribe_link'] = "http://" . $domain . $this->pre_lang . "/dispatches/unsubscribe/" . $iSbsObjId . "/";
				$mail_content = def_module::parseTemplate($template_mail, $mail_arr);

				$confirmMail = new umiMail();
				$confirmMail->addRecipient($sSbsMail);
				$confirmMail->setFrom($from_email, $from);
				$confirmMail->setSubject($template_mail_subject);
				$confirmMail->setContent($mail_content);
				$confirmMail->commit();
				//$confirmMail->send();
			}
			// try get object
			$oSubscriber = umiObjectsCollection::getInstance()->getObject($iSbsObjId);
			if ($oSubscriber instanceof umiObject) {
				$oSubscriber->setName($sSbsMail);
				$oSubscriber->setValue('lname', $sSbsLName);
				$oSubscriber->setValue('fname', $sSbsFName);
				$oSubscriber->setValue('father_name', $sSbsFatherName);
				$oCurrDate = new umiDate(time());
				$oSubscriber->setValue('subscribe_date', $oCurrDate);
				$oSubscriber->setValue('gender', $iSbsGender);
				if ($this->is_auth) {
					$oSubscriber->setValue('uid', $this->user_id);
					$oSubscriber->setValue('subscriber_dispatches', $arrSbsDispatches);
					$sDispForm = "%subscribe_subscribe_user%:<br /><ul>%rows%</ul>";
					$sDispFormRow = "<li>%disp_name%</li>";
					$sResult = self::parseDispatches($sDispForm, $sDispFormRow, $arrSbsDispatches, true);
					$sResult .= "";
				} else {
					// subscriber has all dispatches
					$oSubscriber->setValue('subscriber_dispatches', self::getAllDispatches());
					$sResult = "%subscribe_subscribe%";
				}
				$oSubscriber->commit();
			}
			return $sResult;
		}
	};
?>