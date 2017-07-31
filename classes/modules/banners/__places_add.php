<?php
	abstract class __places_add_banners {
		public function place_add() {
			// set tab
			$this->sheets_set_active("places_list");
			// load forms
			$this->load_forms();
			// params
			$params = array();
			
			$params['method'] = "place_add_do";
			// controlbar
			$params['control_bar'] = '<button title="Отменить" onclick="javascript: return edtCancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и выйти" onclick="javascript: return edtWithExit();" /> <submit title="Добавить" onclick="javascript: return edtWithEdit();" />';
			//
			if(cmsController::getInstance()->getModule('data')) {
				$type_id = umiObjectTypesCollection::getInstance()->getBaseType("banners", "place");
				$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($type_id);
			}

			return $this->parse_form("place_edit", $params);
		}

		public function place_add_do() {
			// set tab
			$this->sheets_set_active("places_list");
			// input:
			$sNewPlaceName = $_REQUEST['place_name'];
			$sNewPlaceDsc = $_REQUEST['place_desc'];
			$bNewPlaceShowRand = (bool) $_REQUEST['place_show_rand'];
			// add new place
			$type_id = umiObjectTypesCollection::getInstance()->getBaseType("banners", "place");
			$iObjectId = umiObjectsCollection::getInstance()->addObject($sNewPlaceName, $type_id);
			$oObject = umiObjectsCollection::getInstance()->getObject($iObjectId);
				
			$oObject->setValue('descr', $sNewPlaceDsc);
			$oObject->setValue('is_show_rand_banner', $bNewPlaceShowRand);
			// date
			$oShowTillDate = NULL;
			if (strlen($sBanShowTill)) {
				$oShowTillDate = new umiDate();
				$oShowTillDate->setDateByString($sBanShowTill);
			}
			$oObject->setValue('show_till_date', $oShowTillDate);
			$oObject->commit();
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