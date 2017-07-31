<?php
	interface iDomain {
//REM: Inheritance from iDomainMirrow interface for a while
//		public function getHost();
//		public function setHost(string $host);

		public function getIsDefault();
		public function setIsDefault($isDefault);

		public function addMirrow($mirrowHost);
		public function delMirrow($mirrowId);

		public function getMirrowId($mirrowHost);
		public function getMirrow($mirrowId);

		public function getMirrowsList();
		public function delAllMirrows();


		public function isMirrowExists($mirrowId);

		public function getDefaultLangId();
		public function setDefaultLangId($langId);
	}
?>