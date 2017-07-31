<?php
	interface iLangsCollection {
		public function addLang($prefix, $title, $isDefault = false);
		public function delLang($langId);

		public function getDefaultLang();
		public function setDefault($langId);

		public function getLangId($prefix);
		public function getLang($langId);

		public function getList();

		public function getAssocArray();
	}
?>