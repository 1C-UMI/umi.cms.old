<?php
	abstract class __places_edit_banners {
		public function place_edit() {
			// set tab
			$this->sheets_set_active("places_list");
			// load forms
			$this->load_forms();
			// params
			$params = array();
			// curr place
			$iPlaceObjId = (int) $_REQUEST['param0'];
			$oPlaceObj = umiObjectsCollection::getInstance()->getObject($iPlaceObjId);
			if ($oPlaceObj instanceof umiObject) {
				// method
				$params['method'] = "place_edit_do/".$iPlaceObjId;
				//
				$params['place_name'] = $oPlaceObj->getName();
				$params['place_desc'] = $oPlaceObj->getValue('descr');
				$params['place_show_rand'] = $oPlaceObj->getValue('is_show_rand_banner');
				// controlbar
				$params['control_bar'] = '<button title="Отменить" onclick="javascript: return edtCancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return edtWithExit();" /> <submit title="Сохранить" onclick="javascript: return edtWithEdit();" />';
				// groups
				if(cmsController::getInstance()->getModule('data')) {
					$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($oPlaceObj->getTypeId(), $iPlaceObjId, true);
				}
			}
			return $this->parse_form("place_edit", $params);
		}

		public function place_edit_do() {
			$iObjectId = $_REQUEST['param0'];
			$sNewPlaceName = $_REQUEST['place_name'];
			$sNewPlaceDsc = $_REQUEST['place_desc'];
			$bNewPlaceShowRand = (bool) $_REQUEST['place_show_rand'];

			$oObject = umiObjectsCollection::getInstance()->getObject($iObjectId);
			if ($oObject instanceof umiObject) {
				$oObject->setName($sNewPlaceName);
				$oObject->setValue('descr', $sNewPlaceDsc);
				$oObject->setValue('is_show_rand_banner', $bNewPlaceShowRand);
				$oObject->commit();
			}
			// redirect
			$sAfterSaveAct = $_REQUEST['after_save_act'];
			switch ($sAfterSaveAct) {
				case "exit": $this->redirect($this->pre_lang . "/admin/banners/places_list/"); break;
				case "edit": $this->redirect($this->pre_lang . "/admin/banners/place_edit/".$iObjectId."/"); break;
				default: //do nothing
			}
		}
	};
?>