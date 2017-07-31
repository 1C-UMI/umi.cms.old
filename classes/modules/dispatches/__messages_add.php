<?php
	abstract class __messages_add_messages {
		public function message_add() {
			// input
			$iDispId = (int) $_REQUEST['param0'];
			// set tab
			$this->sheets_set_active("dispatches_list");
			// load forms
			$this->load_forms();
			// mess type
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "message")->getId();
			$iMsgTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oMsgType = umiObjectTypesCollection::getInstance()->getType($iMsgTypeId);
			// params
			$params = array();
			$params['control_bar'] = '<submit title="Отменить" onclick="javascript: return edtCancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и выйти" onclick="javascript: return edtWithExit();" /> <submit title="Добавить" onclick="javascript: return edtWithEdit();" />';
			$params['method'] = 'message_add_do/'.$iDispId;
			$params['cancel_redirect'] = $this->pre_lang."/admin/dispatches/dispatch_edit/".$iDispId."/";
			// navibar
			$oDispatch = umiObjectsCollection::getInstance()->getObject($iDispId);
			if ($oDispatch instanceof umiObject) {
				$this->navibar_back();
				$this->navibar_push($oDispatch->getName(), $this->pre_lang."/admin/dispatches/dispatch_edit/".$iDispId."/");
				$this->navibar_push("Добавление сообщения");
			}
			if(cmsController::getInstance()->getModule('data')) {
					$iMsgBodyFldId = $oMsgType->getFieldId('body');
					$oMsgBodyFld = umiFieldsCollection::getInstance()->getField($iMsgBodyFldId);
					$params['msg_body'] = "<div>".cmsController::getInstance()->getModule('data')->renderWYSIWYGInput($oMsgBodyFld, "", false)."</div>";
					$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($iMsgTypeId, null, true);
			}

			return $this->parse_form("message_edit", $params);
			/*
			$iDispId = (int) $_REQUEST['param0'];
			$iReleaseId = $this->getNewReleaseInstanceId($iDispId);
			$sMsgName = "New message";
			// add object
			$iMsgTypeId =  umiObjectTypesCollection::getInstance()->getBaseType("dispatches", "message");
			$iNewMsgObjId = umiObjectsCollection::getInstance()->addObject($sMsgName, $iMsgTypeId);
			// set default props
			$oNewMsgObj = umiObjectsCollection::getInstance()->getObject($iNewMsgObjId);
			if ($oNewMsgObj instanceof umiObject) {
				$oNewMsgObj->setValue('release_reference', $iReleaseId);
				$oNewMsgObj->setValue('header', $sMsgName);
				$oNewMsgObj->commit();
			}
			$this->redirect($this->pre_lang . "/admin/dispatches/message_edit/".$iNewMsgObjId."/");
			*/
		}

		public function message_add_do() {
			//input
			$iDispId = (int) $_REQUEST['param0'];
			$sMsgName = $_REQUEST['msg_name'];
			$sMsgHeader = $_REQUEST['msg_header'];
			$sAfterSaveAct = $_REQUEST['after_save_act'];
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "message")->getId();
			$iMsgTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oMsgType = umiObjectTypesCollection::getInstance()->getType($iMsgTypeId);
			$iMsgBodyFldId = $oMsgType->getFieldId('body');
			//add new message
			$iMsgObjId = umiObjectsCollection::getInstance()->addObject($sMsgName, $iMsgTypeId);
			// try get object
			$oMsgObj = umiObjectsCollection::getInstance()->getObject($iMsgObjId);
			if ($oMsgObj instanceof umiObject) {
				// set object props
				$iReleaseId = $this->getNewReleaseInstanceId($iDispId);
				$oMsgObj->setValue('release_reference', $iReleaseId);
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

			// redirect
			switch ($sAfterSaveAct) {
				case "exit": $this->redirect($this->pre_lang . "/admin/dispatches/dispatch_edit/".$iDispId."/"); break;
				case "edit": $this->redirect($this->pre_lang . "/admin/dispatches/message_edit/".$iMsgObjId."/"); break;
				default: //do nothing
			}
		}
	}
?>