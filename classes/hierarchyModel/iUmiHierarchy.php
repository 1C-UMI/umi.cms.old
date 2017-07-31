<?php
	interface iUmiHierarchy {
		public function addElement($relId, $hierarchyTypeId, $name, $alt_name, $objectTypeId = false, $domainId = false, $langId = false, $templateId = false);
		public function getElement($elementId, $ignorePermissions = false, $ignoreDeleted = false);
		public function delElement($elementId);

		public function copyElement($elementId, $newRelId, $copySubPages = false);
		public function cloneElement($elementId, $newRelId, $copySubPages = false);


		public function getDeletedList();

		public function restoreElement($elementId);
		public function removeDeletedElement($elementId);
		public function removeDeletedAll();


		public function getParent($elementId);
		public function getAllParents($elementsId, $selfInclude = false);

		public function getChilds($elementId, $allowUnactive = true, $allowUnvisible = true, $depth = 0, $hierarchyTypeId = false, $domainId = false);

		public function getPathById($elementId, $ignoreLang = false);
		public function getIdByPath($elementPath, $showDisabled = false);

		public static function compareStrings($string1, $string2);
		public static function convertAltName($alt_name);
		public static function getTimeStamp();

		public function getDefaultElementId($langId = false, $domainId = false);

		public function moveBefore($elementId, $relId, $beforeId = false);
		public function moveFirst($elementId, $relId);

		public function getDominantTypeId($elementId);

		//public function applyFilter(umiHierarchyFilter);
		
		public function addUpdatedElementId($elementId);
		public function getUpdatedElements();
		
		public function unloadElement($elementId);
		
		public function getElementsCount($module, $method = "");
	}

?>