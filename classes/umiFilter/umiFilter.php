<?php
	
	class umiFilter implements iUmiFilter {
		private	$sFilterString = "",
					$bFilterByName = false,
					$bFilterByActive = null,
					$iAncestorId = null,
					$iHierarchyTypeId = null,
					$oHierarchyType = null,
					$oBaseObjectType = null,
					$arrObjectTypes = array(),
					$arrHierarchyTypes = array(),
					$arrFilteredProperties = array(),
					$bIsObjectFilter = array(),
					$oSortedProperty = null,
					$bIsDescSorting = false;

		public function __construct($iHierarchyTypeId, $iObjectTypeId = false, $bIsObjectFilter = false) {
			$oHierarchyType = umiHierarchyTypesCollection::getInstance()->getType($iHierarchyTypeId);
			$this->bIsObjectFilter = $bIsObjectFilter;
			if ($oHierarchyType instanceof umiHierarchyType) {
				$this->oHierarchyType = $oHierarchyType;
				
				if (!$iObjectTypeId) {
					$sModule = $this->oHierarchyType->getName();
					$sMethod = $this->oHierarchyType->getExt();
					$iBaseObjectTypeId = umiObjectTypesCollection::getInstance()->getBaseType($sModule, $sMethod);
					$oBaseObjectType = umiObjectTypesCollection::getInstance()->getType($iBaseObjectTypeId);
				} else {
					$oBaseObjectType = umiObjectTypesCollection::getInstance()->getType($iObjectTypeId);
				}

				if($oBaseObjectType instanceof umiObjectType) {
					$this->oBaseObjectType = $oBaseObjectType;
				} else {
					trigger_error("Can't detect base object type for hierarchy type id #{$iHierarchyTypeId}", E_USER_WARNING);
				}
			} else {
				trigger_error("Wrong hierarchy type id #{$iHierarchyTypeId}", E_USER_WARNING);
			}
		}


		public function setFilterString($sFilterString) {

			$this->sFilterString = stripslashes(umiObjectProperty::filterInputString($sFilterString));
		}

		public function setSortedProperty($sPropName, $bDesc = false) {
			if (!strlen($sPropName)) return false;

			$iPropId = $this->oBaseObjectType->getFieldId($sPropName);
			$this->oSortedProperty = umiFieldsCollection::getInstance()->getField($iPropId);
			$this->bIsDescSorting = (bool) $bDesc;

			return $this->oSortedProperty instanceof umiObjectProperty;
		}

		public function addPropertyFilter($sPropertyName, $iValue = 1, $bEnabled = true) {
			if ($bEnabled) {
				$sFieldName = umiObjectProperty::filterInputString($sFieldName);
				$this->arrFilteredProperties[$sPropertyName] = $iValue;
			} elseif (isset($this->arrFilteredProperties[$sPropertyName])) {
				unset($this->arrFilteredProperties[$sPropertyName]);
			}
		}

		public function addFilterByName($bEnabled = true) {
			$this->bFilterByName = (bool) $bEnabled;
		}

		public function addFilterByActive($bActive, $bEnabled = true) {
			$this->bFilterByActive = $bEnabled? (bool) $bActive : null;
		}

		public function addFilterByAncestor($iElementId) {
			$this->iAncestorId = (int) $iElementId;
		}

		public function addObjectTypeFilter($iObjectTypeId, $bEnabled = true) {
			if ($bEnabled) {
				$this->arrObjectTypes[(int) $iObjectTypeId] = 1;
			} elseif (isset($this->arrObjectTypes[(int) $iObjectTypeId])) {
				unset($this->arrObjectTypes[(int) $iObjectTypeId]);
			}
		}

		public function addHierarchyTypeFilter($iHierarchyTypeId, $bEnabled = true) {
			if ($bEnabled) {
				$this->arrHierarchyTypes[(int) $iHierarchyTypeId] = 1;
			} elseif (isset($this->arrHierarchyTypes[(int) $iHierarchyTypeId])) {
				unset($this->arrHierarchyTypes[(int) $iHierarchyTypeId]);
			}
		}

		public function getBaseObjectType() {
			return $this->oBaseObjectType;
		}

		public function getHierarchyType() {
			return $this->oHierarchyType;
		}

		public function getFilterString() {
			return $this->sFilterString;
		}

		public function getFilterArray() {
			if (!strlen($this->sFilterString)) return array();
			
			$arrResult = array();
			$arrFilter = preg_split("/[\s,]+/", $this->sFilterString);

			for ($iI = 0; $iI < count($arrFilter); $iI++) {
				$sNextFilter = $arrFilter[$iI];
				$vFltrVal = $sNextFilter;
				$sFltrType = "like";

				switch (true) {
					// equal
					case ($sNextFilter{0} == "'" && substr($sNextFilter, -1) == "'") || ($sNextFilter{0} == "\"" && substr($sNextFilter, -1) == "\""):
						$sFltrType = "equal";
						$vFltrVal = trim($sNextFilter, "\"'");
					break;
					// not equal
					case $sNextFilter{0} == "!":
						$sFltrType = "notequal";
						$vFltrVal = substr($sNextFilter, 1);
					break;
					// less
					case $sNextFilter{0} == "<":
						$sFltrType = "less";
						$vFltrVal = (float) substr($sNextFilter, 1);
					break;
					// more
					case $sNextFilter{0} == ">":
						$sFltrType = "more";
						$vFltrVal = (float) substr($sNextFilter, 1);
					break;
					// between
					case $sNextFilter{0} == "[" && substr($sNextFilter, -1) == "]" && strpos($sNextFilter, "-") !== false:
						$sFltrType = "between";
						$sNextFilter = trim($sNextFilter, "[]");
						list($iMin, $iMax) = explode("-", $sNextFilter);
						$vFltrVal = array("min" => (float) $iMin, "max" => (float) $iMax);
					break;
					default:
						$sFltrType = "like";
						$vFltrVal = $sNextFilter;
				}
				$arrResult[] = array("type" => $sFltrType, "value" => $vFltrVal);
			}

			return $arrResult;
		}

		public function getFiltredProperties() {
			return $this->arrFilteredProperties;
		}

		public function getFilterByName() {
			return $this->bFilterByName;
		}

		public function getFilterByActive() {
			return $this->bFilterByActive;
		}

		public function getFilterByAncestor() {
			return $this->iAncestorId;
		}

		public function getObjectTypesFilter() {
			return $this->arrObjectTypes;
		}

		public function getHierarchyTypesFilter() {
			return $this->arrHierarchyTypes;
		}

		public function getIsObjectFilter() {
			return (bool) $this->bIsObjectFilter;
		}

		public function getSortedProperty() {
			return $this->oSortedProperty;
		}

		public function getIsDescSorting() {
			return $this->bIsDescSorting;
		}

	}

?>