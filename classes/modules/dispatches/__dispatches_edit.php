<?php
	abstract class __dispatches_edit_dispatches {
		public function dispatch_edit() {
			// set tab
			$this->sheets_set_active("dispatches_list");
			// load forms
			$this->load_forms();
			// params
			$params = array();
			// input
			$iDispObjId = (int) $_REQUEST['param0'];
			$params['disp_id'] = $iDispObjId;
			// get current props
			$oDispatch = umiObjectsCollection::getInstance()->getObject($iDispObjId);
			if ($oDispatch instanceof umiObject) {
				// toolbar
				$arrTBParams = array('disp_id'=>$oDispatch->getId());
				$params['dispatch_toolbar'] = $this->parse_form('dispatch_toolbar', $arrTBParams);
				// release messages
				$arrReleaseParams = array('disp_id'=>$iDispObjId, 'messages_list'=>$this->messages_list($this->getNewReleaseInstanceId($iDispObjId)));
				$params['release_messages'] = $this->parse_form('release_messages', $arrReleaseParams);;
				$params['method'] = 'dispatch_edit_do/'.$iDispObjId;
				$params['disp_name'] = $oDispatch->getName();
				$params['disp_description'] = $oDispatch->getValue('disp_description');
				$params['control_bar'] = '<submit title="Отменить" onclick="javascript: return edtCancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return edtWithExit();" /> <submit title="Сохранить" onclick="javascript: return edtWithEdit();" />';
				// navibar
				$this->navibar_back();
				$this->navibar_push($oDispatch->getName());
			}
			return $this->parse_form("dispatch_edit", $params);
		}

		public function dispatch_edit_do() {
			//input
			$iDispObjId = (int) $_REQUEST['param0'];
			$sDispName = $_REQUEST['disp_name'];
			$sDispDescription = $_REQUEST['disp_description'];
			$sAfterSaveAct = $_REQUEST['after_save_act'];
			// try get object
			$oDispObj = umiObjectsCollection::getInstance()->getObject($iDispObjId);
			if ($oDispObj instanceof umiObject) {
				// set object props
				$oDispObj->setName($sDispName);
				$oDispObj->setValue('disp_description', $sDispDescription);
				// commit
				$oDispObj->commit();
			}
			// redirect
			switch ($sAfterSaveAct) {
				case "exit": $this->redirect($this->pre_lang . "/admin/dispatches/dispatches_list/"); break;
				case "edit": $this->redirect($this->pre_lang . "/admin/dispatches/dispatch_edit/".$iDispObjId."/"); break;
				default: //do nothing
			}
		}

	};
?>