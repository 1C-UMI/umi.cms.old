<?php
	interface iUmiField {
		public function getName();
		public function setName($name);

		public function getTitle();
		public function setTitle($title);

		public function getIsLocked();
		public function setIsLocked($isLocked);

		public function getIsInheritable();
		public function setIsInheritable($isInheritable);

		public function getIsVisible();
		public function setIsVisible($isVisible);

		public function getFieldTypeId();
		public function setFieldTypeId($fieldTypeId);

		public function getFieldType();

		public function getGuideId();
		public function setGuideId($guideId);

		public function getIsInSearch();
		public function setIsInSearch($isInSearch);

		public function getIsInFilter();
		public function setIsInFilter($isInFilter);

		public function getTip();
		public function setTip($tip);
	}
?>