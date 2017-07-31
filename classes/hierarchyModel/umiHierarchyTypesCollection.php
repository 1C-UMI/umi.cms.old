<?php
	class umiHierarchyTypesCollection extends singleton implements iSingleton, iUmiHierarchyTypesCollection {
		private $types;

		protected function __construct() {
			$this->loadTypes();
		}


		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}

		public function getType($type_id) {
			if($this->isExists($type_id)) {
				return $this->types[$type_id];
			} else {
				return false;
			}
		}


		public function getTypeByName($name, $ext = false) {
			foreach($this->types as $type) {
				if($type->getName() == $name && !$ext) return $type;
				if($type->getName() == $name && $type->getExt() == $ext && $ext) return $type;
			}
			return false;
		}


		public function addType($name, $title, $ext = "") {
			$sql = "INSERT INTO cms3_hierarchy_types VALUES()";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$type_id = mysql_insert_id();

			$type = new umiHierarchyType($type_id);
			$type->setName($name);
			$type->setTitle($title);
			$type->setExt($ext);
			$type->commit();

			$this->types[$type_id] = $type;


			return $type_id;
		}


		public function delType($type_id) {
			if($this->isExists($type_id)) {
				unset($this->types[$type_id]);

				$type_id = (int) $type_id;
				$sql = "DELETE FROM cms3_hierarchy_types WHERE id = '{$type_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				return true;
			} else {
				return false;
			}
		}


		public function isExists($type_id) {
			return (bool) array_key_exists($type_id, $this->types);
		}


		private function loadTypes() {
			$sql = "SELECT SQL_CACHE id FROM cms3_hierarchy_types ORDER BY name, ext";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($id) = mysql_fetch_row($result)) {
				if($type = memcachedController::getInstance()->load($id, "element_type")) {
				} else {
					$type = new umiHierarchyType($id);
					memcachedController::getInstance()->save($type, "element_type");
				}
				$this->types[$id] = $type;
			}
			return true;
		}

		public function getTypesList() {
			return $this->types;
		}
	}
?>