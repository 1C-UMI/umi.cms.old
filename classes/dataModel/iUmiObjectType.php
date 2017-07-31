<?php

	interface iUmiObjectType {
		public function addFieldsGroup($name, $title, $isActive = true, $isVisible = true);
		public function delFieldsGroup($fieldGroupId);

		public function getFieldsGroupByName($fieldGroupName);

		public function getFieldsGroup($fieldGroupId);
		public function getFieldsGroupsList();

		public function getName();
		public function setName($name);

		public function setIsLocked($isLocked);
		public function getIsLocked();

		public function setIsGuidable($isGuidable);
		public function getIsGuidable();

		public function setIsPublic($isPublic);
		public function getIsPublic();

		public function setHierarchyTypeId($hierarchyTypeId);
		public function getHierarchyTypeId();

		public function getParentId();

		public function setFieldGroupOrd($groupId, $newOrd, $isLast);


		public function getFieldId($fieldName);

		public function getAllFields();
	}

?>