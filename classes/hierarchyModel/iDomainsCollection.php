<?php
	interface iDomainsCollection {
		public function addDomain($host, $defaultLangId, $isDefault = false);
		public function delDomain($domainId);
		public function getDomain($domainId);

		public function getDefaultDomain();
		public function setDefaultDomain($domainId);

		public function getDomainId($host, $useMirrows = true);

		public function getList();
	}
?>