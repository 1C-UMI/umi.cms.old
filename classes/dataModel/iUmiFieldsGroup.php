<?php
	interface iUmiFieldsGroup {
		public function getName();
		public function setName($name);

		public function getTitle();
		public function setTitle($title);

		public function getTypeId();
		public function setTypeId($typeId);

		public function getOrd();
		public function setOrd($ord);

		public function getIsActive();
		public function setIsActive($isActive);

		public function getIsVisible();
		public function setIsVisible($isVisible);

		public function getIsLocked();
		public function setIsLocked($isLocked);

		public function getFields();

		public function attachField($fieldId);
		public function detachField($fieldId);

		public function moveFieldAfter($fieldId, $beforeFieldId, $group_id, $is_last);
	}
?>