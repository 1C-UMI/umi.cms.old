<?php
	class domain extends umiEntinty implements iUmiEntinty, iDomainMirrow, iDomain {
		private	$host, $default_lang_id, $mirrows = Array();
		protected $store_type = "domain";

		protected function loadInfo() {
			$sql = "SELECT SQL_CACHE host, is_default, default_lang_id FROM cms3_domains WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($host, $is_default, $default_lang_id) = mysql_fetch_row($result)) {
				$this->host = $host;
				$this->is_default = (bool) $is_default;
				$this->default_lang_id = (int) $default_lang_id;

				return $this->loadMirrows();
			} else {
				return false;
			}
		}

		public function getHost() {
			return $this->host;
		}

		public function getIsDefault() {
			return $this->is_default;
		}

		public function setHost($host) {
			$this->host = $host;
			$this->setIsUpdated();
		}

		public function setIsDefault($is_default) {
			$this->is_default = (bool) $is_default;
			$this->setIsUpdated();
		}


		public function getDefaultLangId() {
			return $this->default_lang_id;
		}

		public function setDefaultLangId($lang_id) {
			if(langsCollection::getInstance()->isExists($lang_id)) {
				$this->default_lang_id = $lang_id;
				$this->setIsUpdated();

				return true;
			} else {
				trigger_error("Language #{$lang_id} doesn't exists", E_USER_WARNING);
				return false;
			}
		}


		public function addMirrow($mirrow_host) {
			if($mirrow_id = $this->getMirrowId($mirrow_host)) {
				return $mirrow_id;
			} else {
				$sql = "INSERT INTO cms3_domain_mirrows (rel) VALUES('{$this->id}')";
				mysql_query($sql);

				if($err = mysql_fetch_row($result)) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				$mirrow_id = mysql_insert_id();

				$mirrow = new domainMirrow($mirrow_id);
				$mirrow->setHost($mirrow_host);
				$mirrow->commit();

				$this->mirrows[$mirrow_id] = $mirrow;

				return $mirrow_id;
			}
		}

		public function delMirrow($mirrow_id) {
			if($this->isMirrowExists($mirrow_id)) {
				$sql = "DELETE FROM cms3_domain_mirrows WHERE id = '{$mirrow_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				} else {
					unset($this->mirrows[$mirrow_id]);
					return true;
				}
			} else {
				return false;
			}
		}

		public function delAllMirrows() {
			$sql = "DELETE FROM cms3_domain_mirrows WHERE rel = '{$this->id}'";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return true;
			}
		}

		public function getMirrowId($mirrow_host) {
			foreach($this->mirrows as $mirrow) {
				if($mirrow->getHost() == $mirrow_host) {
					return $mirrow->getId();
				}
			}
			return false;
		}

		public function getMirrow($mirrow_id) {
			if($this->isMirrowExists($mirrow_id)) {
				return $this->mirrows[$mirrow_id];
			} else {
				return false;
			}
		}

		public function isMirrowExists($mirrow_id) {
			return (bool) array_key_exists($mirrow_id, $this->mirrows);
		}

		public function getMirrowsList() {
			return $this->mirrows;
		}

		private function loadMirrows() {
			$sql = "SELECT SQL_CACHE id FROM cms3_domain_mirrows WHERE rel = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($mirrow_id) = mysql_fetch_row($result)) {
				$this->mirrows[$mirrow_id] = new domainMirrow($mirrow_id);
			}

			return true;
		}

		protected function save() {
			$host = mysql_real_escape_string($this->host);
			$is_default = (int) $this->is_default;
			$default_lang_id = (int) $this->default_lang_id;

			$sql = "UPDATE cms3_domains SET host = '{$host}', is_default = '{$is_default}', default_lang_id = '{$default_lang_id}' WHERE id = '{$this->id}'";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return true;
			}
		}
	}
?>
