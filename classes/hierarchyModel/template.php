<?php
	class template extends umiEntinty implements iUmiEntinty {
		private $filename, $title, $domain_id, $lang_id, $is_default;
		protected $store_type = "template";


		public function getFilename() {
			return $this->filename;
		}

		public function getTitle() {
			return $this->title;
		}

		public function getDomainId() {
			return $this->domain_id;
		}

		public function getLangId() {
			return $this->lang_id;
		}

		public function getIsDefault() {
			return $this->is_default;
		}

		public function setFilename($filename) {
			$this->filename = $filename;
			$this->setIsUpdated();
		}

		public function setTitle($title) {
			$this->title = $title;
			$this->setIsUpdated();
		}

		public function setDomainId($domain_id) {
			$domains = domainsCollection::getInstance();
			if($domains->isExists($domain_id)) {
				$this->domain_id = (int) $domain_id;
				$this->setIsUpdated();

				return true;
			} else {
				return false;
			}
		}

		public function setLangId($lang_id) {
			$langs = langsCollection::getInstance();
			if($langs->isExists($lang_id)) {
				$this->lang_id = (int) $lang_id;
				$this->setIsUpdated();

				return true;
			} else {
				return false;
			}
		}

		public function setIsDefault($is_default) {
			$this->is_default = (bool) $is_default;
			$this->setIsUpdated();
		}


		protected function loadInfo() {
			$sql = "SELECT filename, title, domain_id, lang_id, is_default FROM cms3_templates WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($filename, $title, $domain_id, $lang_id, $is_default) = mysql_fetch_row($result)) {
				$this->filename = $filename;
				$this->title = $title;
				$this->domain_id = (int) $domain_id;
				$this->lang_id = (int) $lang_id;
				$this->is_default = (bool) $is_default;

				return true;
			} else {
				return false;
			}
		}

		protected function save() {
			$filename = mysql_real_escape_string($this->filename);
			$title = mysql_real_escape_string($this->title);
			$domain_id = (int) $this->domain_id;
			$lang_id =  (int) $this->lang_id;
			$is_default = (int) $this->is_default;

			$sql = "UPDATE cms3_templates SET filename = '{$filename}', title = '{$title}', domain_id = '{$domain_id}', lang_id = '{$lang_id}', is_default = '{$is_default}' WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return true;
			}
		}
	}
?>