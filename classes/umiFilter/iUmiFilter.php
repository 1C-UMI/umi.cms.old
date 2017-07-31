<?php

	interface iUmiFilter {
		public function __construct($iHierarchyTypeId);

		public function setFilterString($sFilterString);
		public function addPropertyFilter($sPropertyName, $iValue = 1, $bEnabled = true);
		public function addFilterByName($bEnabled = true);
		public function addFilterByActive($bActive, $bEnabled = true);
		public function addFilterByAncestor($iElementId);
		public function addObjectTypeFilter($iObjectTypeId, $bEnabled = true);
		public function addHierarchyTypeFilter($iHierarchyTypeId, $bEnabled = true);

		public function getHierarchyType();
		public function getBaseObjectType();
		public function getFilterString();
		public function getFilterArray();
		public function getFiltredProperties();
		public function getFilterByName();
		public function getFilterByActive();
		public function getFilterByAncestor();
		public function getObjectTypesFilter();
		public function getHierarchyTypesFilter();
		public function getIsObjectFilter();

		public function setSortedProperty($sPropName, $bDesc = false);
		public function getSortedProperty();
		public function getIsDescSorting();
	};

?>