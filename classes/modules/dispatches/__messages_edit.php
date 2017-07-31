<?php
	abstract class __messages_edit_messages {
		public function message_edit() {
			// set tab
			$this->sheets_set_active("dispatches_list");
			// load forms
			$this->load_forms();
			// params
			$params = array();
			// input
			$iMsgObjId = (int) $_REQUEST['param0'];
			$params['msg_id'] = $iMsgObjId;
			// navibar
			$oMessage = umiObjectsCollection::getInstance()->getObject($iMsgObjId);
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "message")->getId();
			$iMsgTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oMsgType = umiObjectTypesCollection::getInstance()->getType($iMsgTypeId);
			if ($oMessage instanceof umiObject) {
				if(cmsController::getInstance()->getModule('data')) {
					$iMsgBodyFldId = $oMsgType->getFieldId('body');
					$oMsgBodyFld = umiFieldsCollection::getInstance()->getField($iMsgBodyFldId);
					$params['msg_body'] = "<div>".cmsController::getInstance()->getModule('data')->renderWYSIWYGInput($oMsgBodyFld, $oMessage->getValue('body'), false)."</div>";
					$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($iMsgTypeId, $oMessage->getId(), true);
				}
				$params['method'] = "message_edit_do/".$iMsgObjId;
				$params['msg_name'] = $oMessage->getName();
				$params['msg_header'] = $oMessage->getValue('header');
				$params['cancel_redirect'] = $this->pre_lang."/admin/dispatches/dispatches_list/";
				$iReleaseId = $oMessage->getValue('release_reference');
				$oRelease = umiObjectsCollection::getInstance()->getObject($iReleaseId);
				$sDisabled = "no";
				if ($oRelease instanceof umiObject) {
					if ((bool) $oRelease->getValue('status')) {
						$sDisabled = "yes";
						$params['cancel_redirect'] = $this->pre_lang."/admin/dispatches/messages_list/".$oRelease->getId()."/";
					} else {
						$params['cancel_redirect'] = $this->pre_lang."/admin/dispatches/dispatch_edit/".$oRelease->getValue("disp_reference")."/";
					}
				}
				$params['control_bar'] = '<submit title="Отменить" onclick="javascript: return edtCancel();" />&#160;&#160;&#160;&#160;<submit disabled="'.$sDisabled.'" title="Сохранить и выйти" onclick="javascript: return edtWithExit();" /> <submit disabled="'.$sDisabled.'" title="Сохранить" onclick="javascript: return edtWithEdit();" />';
				// navibar
				$oDispatch = umiObjectsCollection::getInstance()->getObject($oRelease->getValue("disp_reference"));
				if ($oDispatch instanceof umiObject) {
					$this->navibar_back();
					$this->navibar_push($oDispatch->getName(), $this->pre_lang."/admin/dispatches/dispatch_edit/".$iDispId."/");
					$this->navibar_push("%nav_edit_message% (".$oMessage->getName().")");
				}
			}
			return $this->parse_form("message_edit", $params);
		}

		public function message_edit_do() {
			//input
			$iMsgObjId = (int) $_REQUEST['param0'];
			$sMsgName = $_REQUEST['msg_name'];
			$sMsgHeader = $_REQUEST['msg_header'];
			$sAfterSaveAct = $_REQUEST['after_save_act'];
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "message")->getId();
			$iMsgTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oMsgType = umiObjectTypesCollection::getInstance()->getType($iMsgTypeId);
			$iMsgBodyFldId = $oMsgType->getFieldId('body');
			// try get object
			$oMsgObj = umiObjectsCollection::getInstance()->getObject($iMsgObjId);
			$vDispId = "";
			if ($oMsgObj instanceof umiObject) {
				// get old object props
				$iReleaseId = $oMsgObj->getValue('release_reference');
				$oRelease = umiObjectsCollection::getInstance()->getObject($iReleaseId);
				if ($oRelease instanceof umiObject) {
					$vDispId = $oRelease->getValue('disp_reference');
				}
				// set object props
				$oMsgObj->setName($sMsgName);
				$oMsgObj->setValue('header', $sMsgHeader);
				if (isset($_REQUEST['data_values'][$iMsgBodyFldId])) {
					$oMsgObj->setValue('body', $_REQUEST['data_values'][$iMsgBodyFldId]);
				}
				// set groups
				if(cmsController::getInstance()->getModule('data')) {
					cmsController::getInstance()->getModule('data')->saveEditedGroups($oMsgObj->getId(), true);
				}
				// commit
				$oMsgObj->commit();
			}
			if (!$vDispId) $vDispId="";
			// redirect
			switch ($sAfterSaveAct) {
				case "exit": $this->redirect($this->pre_lang . "/admin/dispatches/dispatch_edit/".$vDispId); break;
				case "edit": $this->redirect($this->pre_lang . "/admin/dispatches/message_edit/".$iMsgObjId."/"); break;
				default: //do nothing
			}
		}

	};
?>