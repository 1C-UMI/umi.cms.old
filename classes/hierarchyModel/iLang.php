<?php
	interface iLang {
		public function getTitle();
		public function setTitle($title);

		public function getPrefix();
		public function setPrefix($prefix);

		public function getIsDefault();
		public function setIsDefault($isDefault);
	}
?>