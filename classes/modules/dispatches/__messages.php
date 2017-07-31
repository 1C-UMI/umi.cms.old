<?php
	abstract class __messages_messages {
		public function messages_list($iReleaseId=false) {
			$bChangeNavi = false;
			if ($iReleaseId==false) {
				$iReleaseId = (int) $_REQUEST['param0']; 
				$bChangeNavi = true;
			}
			$oRelease = umiObjectsCollection::getInstance()->getObject($iReleaseId);
			$this->sheets_set_active("dispatches_list");
			$this->load_forms();
			$params = array();
			$params['rows'] = "";
			$oMsgsSelection = new umiSelection;
			$oMsgsSelection->setObjectTypeFilter();
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "message")->getId();
			$iMsgTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oMsgType = umiObjectTypesCollection::getInstance()->getType($iMsgTypeId);
			$oMsgsSelection->addObjectType($iMsgTypeId);
			$sMessagesForm = "messages_list";
			$sMessageRowForm = "messages_list_row";
			// add curr release filter
			if ($oRelease instanceof umiObject) {
				$oMsgsSelection->setPropertyFilter();
				$oMsgsSelection->addPropertyFilterEqual($oMsgType->getFieldId('release_reference'), $iReleaseId);
				$iDispId = $oRelease->getValue('disp_reference');
				if ((bool) $oRelease->getValue('status')) {
					$sMessagesForm = "messages_list_not_editable";
					$sMessageRowForm = "messages_list_row_not_editable";
				}
				$oDispatch = umiObjectsCollection::getInstance()->getObject($iDispId);
				if ($oDispatch instanceof umiObject && $bChangeNavi) {
					$this->navibar_back();
					$this->navibar_push($oDispatch->getName(), $this->pre_lang."/admin/dispatches/dispatch_edit/".$iDispId."/");
					$this->navibar_push("Архив выпусков", $this->pre_lang."/admin/dispatches/releasees_list/".$iDispId."/");
					$oDate = $oRelease->getValue('date');
					$sDate = "не отправлен";
					if ($oDate instanceof umiDate) {
						$sDate = $oDate->getFormattedDate("d.m.Y H:i");
					}
					$this->navibar_push($oDispatch->getName()." (".$sDate.")");
				}
			}
			$arrSelResults = umiSelectionsParser::runSelection($oMsgsSelection);
			for ($iI=0; $iI<count($arrSelResults); $iI++) {
				$params['rows'] .= self::renderMessage($arrSelResults[$iI], $sMessageRowForm);
			}
			return $this->parse_form($sMessagesForm, $params);
		}
		
		protected function renderMessage($iMsgId, $sFormName="messages_list_row") {
			$sResult = "";
			$oMessage = umiObjectsCollection::getInstance()->getObject($iMsgId);
			$params = array();
			if ($oMessage instanceof iUmiObject) {
				$params['mess_id'] = $iMsgId;
				$params['mess_name'] = $oMessage->getName();
				$sResult = $this->parse_form($sFormName, $params);
			}
			return $sResult;
		}

		public function message_del() {
			$iMsgId = $_REQUEST['param0'];
			$oMsgObj = umiObjectsCollection::getInstance()->getObject($iMsgId);
			if ($oMsgObj instanceof umiObject) {
				$iReleaseId = $oMsgObj->getValue('release_reference');
				umiObjectsCollection::getInstance()->delObject($iMsgId);
				if ($iDispId) {
					$this->redirect($this->pre_lang . "/admin/dispatches/dispatch_edit/".$iDispId."/");
				}
			}
			$this->redirect($this->pre_lang . "/admin/dispatches/messages_list/");
		}
	};
?>