<?php
	interface iUmiSelection {
		public function setObjectTypeFilter($isEnabled = true);
		public function setElementTypeFilter($isEnabled = true);
		public function setPropertyFilter($isEnabled = true);
		public function setLimitFilter($isEnabled = true);
		public function setHierarchyFilter($isEnabled = true);
		public function setOrderFilter($isEnabled = true);
		public function setPermissionsFilter($isEnabled = true);
		public function setNamesFilter($isEnabled = true);
		public function setActiveFilter($isEnabled = true);

		public function forceHierarchyTable($isForced = true);

		public function addObjectType($objectTypeId);
		public function addElementType($elementTypeId);

		public function addLimit($resultsPerQueryPage, $resultsPage = 0);

		public function setOrderByProperty($fieldId, $asc = true);
		public function setOrderByOrd();
		public function setOrderByRand();
		public function setOrderByName($asc = true);

		public function addHierarchyFilter($elementId, $depth = 0);

		public function addPropertyFilterBetween($fieldId, $minValue, $maxValue);
		public function addPropertyFilterEqual($fieldId, $exactValue, $caseInsencetive = true);
		public function addPropertyFilterNotEqual($fieldId, $exactValue, $caseInsencetive = true);
		public function addPropertyFilterLike($fieldId, $likeValue, $caseInsencetive = true);
		public function addPropertyFilterMore($fieldId, $val);
		public function addPropertyFilterLess($fieldId, $val);
		public function addPropertyFilterIsNull($fieldId);
		public function addActiveFilter($active);

		public function addNameFilterEquals($exactValue);
		public function addnameFilterLike($likeValue);

		public function addPermissions($userId = false);

		public function getOrderConds();
		public function getLimitConds();
		public function getPropertyConds();
		public function getObjectTypeConds();
		public function getElementTypeConds();
		public function getHierarchyConds();
		public function getPermissionsConds();
		public function getForceCond();
		public function getActiveConds();

		public function getNameConds();
		
		
		public function setConditionModeOR();
	}
?>