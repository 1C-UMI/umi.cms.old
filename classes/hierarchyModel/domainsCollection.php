<?php
	class domainsCollection extends singleton implements iSingleton, iDomainsCollection {
		private $domains = Array(), $def_domain;

		protected function __construct() {
			$this->loadDomains();
		}

		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}

		public function addDomain($host, $lang_id, $is_default = false) {
			if($domain_id = $this->getDomainId($host)) {
				return $domain_id;
			} else {
				$sql = "INSERT INTO cms3_domains VALUES()";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				$domain_id = mysql_insert_id();

				$this->domains[] = $domain = new domain($domain_id);
				$domain->setHost($host);
				$domain->setIsDefault($is_default);
				$domain->setDefaultLangId($lang_id);
				if($is_default) $this->setDefaultDomain($domain_id);
				$domain->commit();

				return $domain_id;
			}
		}

		public function setDefaultDomain($domain_id) {
			if($this->isExists($domain_id)) {
				$sql = "UPDATE cms3_domains SET is_default = '0' WHERE is_default = '1'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				} else {
					if($def_domain = $this->getDefaultDomain()) {
						$def_domain->setIsDefault(false);
						$def_domain->commit();
					}

					$this->def_domain = $domain_id;
					$this->def_domain->setIsDefault(true);
					$this->def_domain->commit();
				}

			} else {
				return false;
			}
		}

		public function delDomain($domain_id) {
			if($this->isExists($domain_id)) {
				$domain = $this->getDomain($domain_id);
				$domain->delAllMirrows();

				if($domain->getIsDefault()) {
					$this->def_domain = false;
				}

				unset($domain);
				unset($this->domains[$domain_id]);


				$sql = "DELETE FROM cms3_hierarchy WHERE domain_id = '{$domain_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}


				$sql = "DELETE FROM cms3_domains WHERE id = '{$domain_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				} else {
					return true;
				}
			} else {
				trigger_error("Domain #{$domain_id} doesn't exists.", E_USER_WARNING);
				return false;
			}
		}

		public function getDomain($domain_id) {
			if($this->isExists($domain_id)) {
				return $this->domains[$domain_id];
			} else {
				return false;
			}
		}

		public function getDefaultDomain() {
			return ($this->def_domain) ? $this->def_domain : false;
		}

		public function getList() {
			return $this->domains;
		}

		public function isExists($domain_id) {
			return (bool) array_key_exists($domain_id, $this->domains);
		}

		public function getDomainId($host, $use_mirrows = true) {
			foreach($this->domains as $domain) {
				if($domain->getHost() == $host) {
					return $domain->getId();
				} else {
					if($use_mirrows) {
						$mirrows = $domain->getMirrowsList();
						foreach($mirrows as $domainMirrow) {
							if($domainMirrow->getHost() == $host) {
								return $domain->getId();
							}
						}
					}
				}
			}
			return false;
		}

		private function loadDomains() {
			$sql = "SELECT id FROM cms3_domains";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($domain_id) = mysql_fetch_row($result)) {
				if($domain = memcachedController::getInstance()->load($domain_id, "domain")) {
				} else {
					$domain = new domain($domain_id);
					memcachedController::getInstance()->save($domain, "domain");
				}
				$this->domains[$domain_id] = $domain;

				if($domain->getIsDefault()) {
					$this->def_domain = $domain;
				}
			}

			return true;
		}
	}
?>