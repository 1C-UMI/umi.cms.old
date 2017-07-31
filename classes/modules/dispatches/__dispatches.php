<?php
	abstract class __dispatches_dispatches {
		public function dispatches_list() {
			// set tab
			$this->sheets_set_active("dispatches_list");
			$this->load_forms();
			$params = array();
			$params['rows'] = "";
			$oDispsSelection = new umiSelection;
			$oDispsSelection->setObjectTypeFilter();
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "dispatch")->getId();
			$iDispTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oDispType = umiObjectTypesCollection::getInstance()->getType($iDispTypeId);
			$oDispsSelection->addObjectType($iDispTypeId);
			
			$arrSelResults = umiSelectionsParser::runSelection($oDispsSelection);
			for ($iI=0; $iI<count($arrSelResults); $iI++) {
				$params['rows'] .= self::renderDispatch($arrSelResults[$iI]);
			}

			return $this->parse_form("dispatches_list", $params);
		}

		private function renderDispatch($iDispId) {
			$sResult = "";
			$oDispatch =  umiObjectsCollection::getInstance()->getObject($iDispId);
			$params = array();
			if ($oDispatch instanceof iUmiObject) {
				$params['disp_name'] = $oDispatch->getName();
				$params['disp_description'] = $oDispatch->getValue('disp_description');
				$sLastRelease = "выпусков нет";
				$oLastReleaseDate = $oDispatch->GetValue('disp_last_release');
				if ($oLastReleaseDate instanceof umiDate) {
					$sLastRelease = $oLastReleaseDate->getFormattedDate("d.m.Y H:i");
				}
				$params['disp_last_release'] = $sLastRelease;
				$params['disp_id'] = $iDispId;
				// subscribers
				$params['disp_subscribers'] = "";

				$sResult = $this->parse_form("dispatches_list_row", $params);
			}
			return $sResult;
		}

		public function dispatch_del() {
			$iDispId = $_REQUEST['param0'];
			umiObjectsCollection::getInstance()->delObject($iDispId);
			$this->redirect($this->pre_lang . "/admin/dispatches/dispatches_list/");
		}
	};
?>