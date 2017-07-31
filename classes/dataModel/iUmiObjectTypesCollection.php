<?php
	interface iUmiObjectTypesCollection {
		public function addType($parentId, $name, $isLocked = false);
		public function delType($typeId);

		public function getType($typeId);
		public function getSubTypesList($typeId);

		public function getParentClassId($typeId);
		public function getChildClasses($typeId);

		public function getGuidesList($publicOnly = false);

		public function getTypesByHierarchyTypeId($hierarchyTypeId);
		public function getTypeByHierarchyTypeId($hierarchyTypeId);

		public function getBaseType($typeName, $typeExt = "");
	}
?>