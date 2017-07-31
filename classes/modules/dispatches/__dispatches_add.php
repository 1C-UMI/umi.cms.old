<?php
	abstract class __dispatches_add_dispatches {
		public function dispatch_add() {
			// set tab
			$this->sheets_set_active("dispatches_list");
			// load forms
			$this->load_forms();
			// params
			$params = array();
			$params['control_bar'] = '<submit title="Отменить" onclick="javascript: return edtCancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и выйти" onclick="javascript: return edtWithExit();" /> <submit title="Добавить" onclick="javascript: return edtWithEdit();" />';
			$params['method'] = 'dispatch_add_do';
			return $this->parse_form("dispatch_edit", $params);
		}

		public function dispatch_add_do() {
			//input
			$sDispName = $_REQUEST['disp_name'];
			$sDispDescription = $_REQUEST['disp_description'];
			$sAfterSaveAct = $_REQUEST['after_save_act'];
			//add new dispatch
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "dispatch")->getId();
			$iDispTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$iDispId = umiObjectsCollection::getInstance()->addObject($sDispName, $iDispTypeId);
			$oDispatch = umiObjectsCollection::getInstance()->getObject($iDispId);
			if ($oDispatch instanceof umiObject) {
				$oDispatch->setValue('disp_description', $sDispDescription);
				// commit
				$oDispatch->commit();
			}
			// redirect
			switch ($sAfterSaveAct) {
				case "exit": $this->redirect($this->pre_lang . "/admin/dispatches/dispatches_list/"); break;
				case "edit": $this->redirect($this->pre_lang . "/admin/dispatches/dispatch_edit/".$iDispId."/"); break;
				default: //do nothing
			}
		}
	}
?>