<?php
	class umiHierarchyType extends umiEntinty implements iUmiEntinty, iUmiHierarchyType {
		private $name, $title, $ext;
		protected $store_type = "element_type";

		public function getName() {
			return $this->name;
		}

		public function getTitle() {
			return $this->title;
		}

		public function getExt() {
			return $this->ext;
		}

		public function setName($name) {
			$this->name = self::filterInputString($name);
			$this->setIsUpdated();
		}

		public function setTitle($title) {
			$this->title = self::filterInputString($title);
			$this->setIsUpdated();
		}

		public function setExt($ext) {
			$this->ext = self::filterInputString($ext);
			$this->setIsUpdated();
		}


		protected function loadInfo() {
			$sql = "SELECT name, title, ext FROM cms3_hierarchy_types WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($name, $title, $ext) = mysql_fetch_row($result)) {
				$this->name = $name;
				$this->title = $title;
				$this->ext = $ext;

				return true;
			} else {
				return false;
			}
		}

		protected function save() {
			$name = mysql_escape_string($this->name);
			$title = mysql_escape_string($this->title);
			$ext = mysql_escape_string($this->ext);

			$sql = "UPDATE cms3_hierarchy_types SET name = '{$name}', title = '{$title}', ext = '{$ext}' WHERE id = '{$this->id}'";
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