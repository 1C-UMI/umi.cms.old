<?php
	interface iTemplate {
		public function getFilename();
		public function setFilename($filename);

		public function getTitle();
		public function setTitle($title);

		public function getDomainId();
		public function setDomainId($domainId);

		public function getLangId();
		public function setLangId($langId);

		public function getIsDefault();
		public function setIsDefault($isDefault);
	}
?>