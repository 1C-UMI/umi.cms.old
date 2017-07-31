<?php
	interface iUmiFieldType {
		public function getName();
		public function setName($name);

		public function getIsMultiple();
		public function setIsMultiple($isMultiple);

		public function getIsUnsigned();
		public function setIsUnsigned($isUnsigned);

		public function getDataType();
		public function setDataType($dataTypeStr);

		public static function getDataTypes();
		public static function getDataTypeDB($dataType);
		public static function isValidDataType($dataTypeStr);
	}
?>