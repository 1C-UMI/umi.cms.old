<?php
	interface iUmiHierarchyTypesCollection {
		public function addType($name, $title, $ext = "");
		public function getType($typeId);
		public function delType($typeId);
		public function getTypeByName($typeName, $extName = false);

		public function getTypesList();
	}
?>