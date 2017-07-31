<?php
	class lang extends umiEntinty implements iUmiEntinty, iLang {
		private $prefix, $is_default, $title;
		protected $store_type = "lang";

		protected function loadInfo() {
			$sql = "SELECT SQL_CACHE prefix, is_default, title FROM cms3_langs WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) trigger_error($err, E_USER_WARNING);

			if(list($prefix, $is_default, $title) = mysql_fetch_row($result)) {
				$this->prefix = $prefix;
				$this->title = $title;
				$this->is_default = (bool) $is_default;

				return true;
			} else {
				return false;
			}
		}


		public function getTitle() {
			return $this->title;
		}

		public function getPrefix() {
			return $this->prefix;
		}

		public function getIsDefault() {
			return $this->is_default;
		}


		public function setTitle($title) {
			$this->title = $title;
			$this->setIsUpdated();
		}

		public function setPrefix($prefix) {
			$this->prefix = $prefix;
			$this->setIsUpdated();
		}

		public function setIsDefault($is_default) {
			$this->is_default = (bool) $is_default;
			$this->setIsUpdated();
		}

		protected function save() {
			$title = self::filterInputString($this->title);
			$prefix = self::filterInputString($this->prefix);
			$is_default = (int) $this->is_default;

			$sql = "UPDATE cms3_langs SET prefix = '{$prefix}', is_default = '{$is_default}', title = '{$title}' WHERE id = '{$this->id}'";
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