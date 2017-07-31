<?php
	abstract class __releasees_releasees {
		//
		private $arrReleasees = array();
		public function getNewReleaseInstanceId($iDispId) {
			$iReleaseId = false;
			if (isset($arrReleasees[$iDispId])) {
				$iReleaseId = $arrReleasees[$iDispId];
			} else {
				$oReleaseesSelection = new umiSelection;
				$oReleaseesSelection->setObjectTypeFilter();
				$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "release")->getId();
				$iReleaseTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
				$oReleaseType = umiObjectTypesCollection::getInstance()->getType($iReleaseTypeId);
				$oReleaseesSelection->addObjectType($iReleaseTypeId);
				$oReleaseesSelection->setPropertyFilter();

				$oReleaseesSelection->addPropertyFilterIsNull($oReleaseType->getFieldId('status'), 0, true);
				$oReleaseesSelection->addPropertyFilterEqual($oReleaseType->getFieldId('disp_reference'), $iDispId);
				$arrSelResults = umiSelectionsParser::runSelection($oReleaseesSelection);
				$bIsNewRelease = false;
				if (is_array($arrSelResults) && isset($arrSelResults[0])) {
					$iReleaseId = $arrSelResults[0];
					$oRelease = umiObjectsCollection::getInstance()->getObject($iReleaseId);
				} else {
					$iReleaseId = umiObjectsCollection::getInstance()->addObject("", $iReleaseTypeId);
					$bIsNewRelease = true;
				}
				$oRelease = umiObjectsCollection::getInstance()->getObject($iReleaseId);
				if ($oRelease instanceof umiObject) {
					if ($bIsNewRelease) {
						$oRelease->setValue('status', false);
						$oRelease->setValue('disp_reference', $iDispId);
						$oRelease->commit();
					}
					$arrReleasees[$iDispId] = $iRealeaseId;
				}
			}
			return $iReleaseId;
		}
		public function releasees_list() {
			// input:
			$iDispId = $_REQUEST['param0'];
			$oDispatch = umiObjectsCollection::getInstance()->getObject($iDispId);
			// forms
			$this->sheets_set_active("dispatches_list");
			$this->load_forms();
			$params = array();
			$oReleaseesSelection = new umiSelection;
			$oReleaseesSelection->setObjectTypeFilter();
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "release")->getId();
			$iReleaseTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oReleaseType = umiObjectTypesCollection::getInstance()->getType($iReleaseTypeId);
			$oReleaseesSelection->addObjectType($iReleaseTypeId);
			if ($oDispatch instanceof umiObject) {
				$oReleaseesSelection->setPropertyFilter();
				$oReleaseesSelection->addPropertyFilterEqual($oReleaseType->getFieldId('disp_reference'), $iDispId);
				$this->navibar_back();
				$this->navibar_push($oDispatch->getName(), $this->pre_lang."/admin/dispatches/dispatch_edit/".$iDispId."/");
				$this->navibar_push("Архив выпусков");
			}
			// sorting
			$oReleaseesSelection->setOrderFilter();
			$oReleaseesSelection->setOrderByProperty($oReleaseType->getFieldId('date'), false);
			$arrSelResults = umiSelectionsParser::runSelection($oReleaseesSelection);
			if (is_array($arrSelResults) && count($arrSelResults)) {
				for ($iI=0; $iI<count($arrSelResults); $iI++) {
					$params['rows'] .= self::renderRelease($arrSelResults[$iI]);
				}
			}
			return $this->parse_form("releasees_list", $params);
		}

		protected function renderRelease($iReleaseId) {
			$sResult = "";
			$oRelease = umiObjectsCollection::getInstance()->getObject($iReleaseId);
			$params = array();
			if ($oRelease instanceof iUmiObject) {
				$params['release_id'] = $oRelease->getId();
				$params['release_status'] = ($oRelease->getValue('status')? "отправлен": "не отправлен");
				$oDate = $oRelease->getValue('date');
				$sDate = "не отправлен";
				if ($oDate instanceof umiDate) {
					$sDate = $oDate->getFormattedDate("d.m.Y H:i");
				}
				$params['release_date'] = $sDate;
				$iDispId = $oRelease->getValue('disp_reference');
				$oDisp =  umiObjectsCollection::getInstance()->getObject($iDispId);
				if ($oDisp instanceof umiObject) {
					$params['disp_id'] = $oDisp->getId();
					$params['disp_name'] = $oDisp->getName();
				}
				$sResult = $this->parse_form("releasees_list_row", $params);
			}
			return $sResult;
		}

		public function release_send() {
			$sResult = "";
			$sHost = cmsController::getInstance()->getCurrentDomain()->getHost();
			$oMailer = new umiMail();
			// set tab
			$this->sheets_set_active("dispatches_list");
			$iDispId = (int) $_REQUEST['param0'];
			$oDispatch = umiObjectsCollection::getInstance()->getObject($iDispId);
			$iReleaseId = $this->getNewReleaseInstanceId($iDispId);
			$oRelease = umiObjectsCollection::getInstance()->getObject($iReleaseId);
			// mail template
			$arrMailBlocks = array();
			list($sReleaseFrm, $sMessageFrm) = def_module::loadTemplates("tpls/dispatches/release.tpl", "release_body", "release_message");
			if ($oRelease instanceof umiObject && $oDispatch instanceof umiObject) {
				// gen mail body and attach files
				$sMailBody = "";
				$oMsgsSelection = new umiSelection;
				$oMsgsSelection->setObjectTypeFilter();
				$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "message")->getId();
				$iMsgTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
				$oMsgType = umiObjectTypesCollection::getInstance()->getType($iMsgTypeId);
				$oMsgsSelection->addObjectType($iMsgTypeId);
				$oMsgsSelection->setPropertyFilter();
				$oMsgsSelection->addPropertyFilterEqual($oMsgType->getFieldId('release_reference'), $iReleaseId);
				$arrSelResults = umiSelectionsParser::runSelection($oMsgsSelection);
				$arrMailBlocks['header'] = $oDispatch->getName().", выпуск от ".date("d.m.Y");
				$arrMailBlocks['messages'] = "";
				if (is_array($arrSelResults) && count($arrSelResults)) {
					for ($iI=0; $iI<count($arrSelResults); $iI++) {
						$oNextMsg = umiObjectsCollection::getInstance()->getObject($arrSelResults[$iI]);
						if ($oNextMsg instanceof umiObject) {
							$arrMsgBlocks = array();
							$arrMsgBlocks['body'] = $oNextMsg->getValue('body');
							$arrMsgBlocks['header'] = $oNextMsg->getValue('header');
							$arrMailBlocks['messages'] .= def_module::parseTemplate($sMessageFrm, $arrMsgBlocks);
							$oNextAttach = $oNextMsg->getValue('attach_file');
							if ($oNextAttach instanceof umiFile && !$oNextAttach->getIsBroken()) {
								$oMailer->attachFile($oNextAttach);
							}
						}
					}
				} else {
					return "<span style=\"color:red;\"><b>Извините, выпуск не отправлен. В выпуске нет сообщений.</b></span>";
				}
				// add recipients
				$oSbsSelection = new umiSelection;
				$oSbsSelection->setObjectTypeFilter();
				$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "subscriber")->getId();
				$iSbsTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
				$oSbsType = umiObjectTypesCollection::getInstance()->getType($iSbsTypeId);
				$oSbsSelection->addObjectType($iSbsTypeId);
				$oSbsSelection->setPropertyFilter();
				$oSbsSelection->addPropertyFilterEqual($oSbsType->getFieldId('subscriber_dispatches'), $iDispId);
				$arrSelResults = umiSelectionsParser::runSelection($oSbsSelection);
				$oMailer->setFrom(regedit::getInstance()->getVal("//settings/email_from"));
				$oMailer->setSubject($arrMailBlocks['header']);
				$arrHTMLImages = array();
				for ($iI=0; $iI<count($arrSelResults); $iI++) {
					$oNextMailer = clone $oMailer;
					$oNextSbs = umiObjectsCollection::getInstance()->getObject($arrSelResults[$iI]);
					$sRecipientName =  $oNextSbs->getValue('lname')." ".$oNextSbs->getValue('fname')." ".$oNextSbs->getValue('father_name');
					// create umiMail
					$arrMailBlocks['unsubscribe_link'] = "http://".$sHost."/dispatches/unsubscribe/".$oNextSbs->getId();
					$sMailBody =  def_module::parseTemplate($sReleaseFrm, $arrMailBlocks);
					$oNextMailer->setContent($sMailBody);
					$oNextMailer->addRecipient($oNextSbs->getName(), $sRecipientName);
					$oNextMailer->commit();
				}
				umiMail::clearFilesCahce();
				// =============
				$oRelease->setValue('status', true);
				$oDate = new umiDate(time());
				$oRelease->setValue('date', $oDate);
				$oRelease->commit();

				$oDispatch->setValue('disp_last_release', $oDate);
				$oDispatch->commit();
				$sResult = "<b>Выпуск успешно отправлен.</b>";
			} else {
				$sResult = "<span style=\"color:red;\"><b>Выпуск не отправлен. При попытке отправить выпуск возникли ошибки.</b></span>";
			}
			return $sResult;
		}
	}
?>