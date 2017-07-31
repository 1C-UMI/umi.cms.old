<?php
	interface iUmiObjectProperty {
		public function getValue();
		public function setValue($value);
		public function resetValue();

		public function getName();
		public function getTitle();

		public function getIsMultiple();
		public function getIsUnsigned();
		public function getDataType();
		public function getIsLocked();
		public function getIsInheritable();
		public function getIsVisible();

		public static function filterOutputString($string);
		public static function filterCDATA($string);
	}
?>