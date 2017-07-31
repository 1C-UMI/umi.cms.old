<?php
	abstract class __places_banners {
		public function places_list() {
			// set tab
			$this->sheets_set_active("places_list");
			// load forms
			$this->load_forms();
			// params
			$params = array();
			// gen places list
			$oPlacesSelection = new umiSelection;
			$oPlacesSelection->setObjectTypeFilter();
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("banners", "place")->getId();
			$arrPlaceTypes = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($iHierarchyTypeId);
			foreach($arrPlaceTypes as $type_id => $nop) {
				$oPlacesSelection->addObjectType($type_id);
			}
			$arrSelResults = umiSelectionsParser::runSelection($oPlacesSelection);
			
			for ($iI=0; $iI<count($arrSelResults); $iI++) {
				$params['rows'] .= self::renderPlace($arrSelResults[$iI]);
			}

			return $this->parse_form("lists_place", $params);
		}

		protected function renderPlace($iObjId) {
			$oPlace =  umiObjectsCollection::getInstance()->getObject($iObjId);
			$params = array();
			$params['banner_name'] = $oPlace->getName();
			$params['place_id'] = $oPlace->getId();
			$params['place_name'] = $oPlace->getName();
			$params['place_dsc'] = $oPlace->getValue('descr');
			return $this->parse_form("banners_place_row", $params);
			//
		}

		public function place_del() {
			$iPlaceId = $_REQUEST['param0'];
			umiObjectsCollection::getInstance()->delObject($iPlaceId);
			$this->redirect($this->pre_lang . "/admin/banners/places_list/");
		}
	};
?>