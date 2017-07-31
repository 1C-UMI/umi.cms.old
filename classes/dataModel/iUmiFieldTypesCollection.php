<?php
	interface iUmiFieldTypesCollection {
		public function addFieldType($name, $dataType = "string", $isMultiple = false, $isUnsigned = false);
		public function delFieldType($fieldTypeId);
		public function getFieldType($fieldTypeId);

		public function getFieldTypesList();
	}
?>