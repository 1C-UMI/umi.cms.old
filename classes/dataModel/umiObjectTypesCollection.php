<?php
	class umiObjectTypesCollection extends singleton implements iSingleton, iUmiObjectTypesCollection {
		private $types = Array();

		protected function __construct() {
		}

		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}


		public function getType($type_id) {
			if($this->isLoaded($type_id)) {
				return $this->types[$type_id];
			} else {
				if($this->isExists($type_id)) {
					$this->loadType($type_id);
					return $this->types[$type_id];
				} else {
					return false;
				}
			}
			trigger_error("Unknow error", E_USER_FATAL);
		}

		public function addType($parent_id, $name, $is_locked = false) {
			$parent_id = (int) $parent_id;

			$sql = "INSERT INTO cms3_object_types (parent_id) VALUES('{$parent_id}')";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$type_id = mysql_insert_id();

			//Making types inheritance...
			$sql = "SELECT * FROM cms3_object_field_groups WHERE type_id = '{$parent_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while($row = mysql_fetch_assoc($result)) {
				$sql = "INSERT INTO cms3_object_field_groups (name, title, type_id, is_active, is_visible, ord, is_locked) VALUES ('" . mysql_real_escape_string($row['name']) . "', '" . mysql_real_escape_string($row['title']) . "', '{$type_id}', '{$row['is_active']}', '{$row['is_visible']}', '{$row['ord']}', '{$row['is_locked']}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				$old_group_id = $row['id'];
				$new_group_id = mysql_insert_id();

				$sql = "INSERT INTO cms3_fields_controller SELECT ord, field_id, '{$new_group_id}' FROM cms3_fields_controller WHERE group_id = '{$old_group_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}
			}



			$type = new umiObjectType($type_id);
			$type->setName($name);
			$type->setIsLocked($is_locked);
			$type->commit();

			$this->types[$type_id] = $type;

			return $type_id;
		}

		public function delType($type_id) {
			if($this->isExists($type_id)) {
				$childs = $this->getChildClasses($type_id);

				$sz = sizeof($childs);
				for($i = 0; $i < $sz; $i++) {
					$child_type_id = $childs[$i];

					if($this->isExists($child_type_id)) {
						$sql = "DELETE FROM cms3_objects WHERE type_id = '{$child_type_id}'";
						mysql_query($sql);

						$sql = "DELETE FROM cms3_object_types WHERE id = '{$child_type_id}'";
						mysql_query($sql);

						if($err = mysql_error()) {
							trigger_error($err, E_USER_WARNING);
							return false;
						}
						unset($this->types[$child_type_id]);
					}
				}

				$type_id = (int) $type_id;

				$sql = "DELETE FROM cms3_objects WHERE type_id = '{$type_id}'";
				mysql_query($sql);

				$sql = "DELETE FROM cms3_object_types WHERE id = '{$type_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				unset($this->types[$type_id]);
				return true;
			} else {
				return false;
			}
		}

		public function isExists($type_id) {
			if($this->isLoaded($type_id)) {
				return true;
			} else {
				$type_id = (int) $type_id;

				$sql = "SELECT COUNT(*) FROM cms3_object_types WHERE id = '$type_id'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				} else {
					list($count) = mysql_fetch_row($result);
				}

				return (bool) $count;
			}
		}

		private function isLoaded($type_id) {
			return (bool) array_key_exists($type_id, $this->types);
		}

		private function loadType($type_id) {
			if($this->isLoaded($type_id)) {
				return true;
			} else {
				if($type = memcachedController::getInstance()->load($type_id, "object_type")) {
				} else {
					$type = new umiObjectType($type_id);
					memcachedController::getInstance()->save($type, "object_type");
				}
				
				if(is_object($type)) {
					$this->types[$type_id] = $type;
					return true;
				} else {
					return false;
				}
			}
		}

		public function getSubTypesList($type_id) {
			if(!is_numeric($type_id)) {
				trigger_error("Type id must be numeric", E_USER_WARNING);
				return false;
			}

			$type_id = (int) $type_id;

			$sql = "SELECT SQL_CACHE id FROM cms3_object_types WHERE parent_id = '{$type_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$res = Array();
			while(list($type_id) = mysql_fetch_row($result)) {
				$res[] = (int) $type_id;
			}
			return $res;
		}

		public function getParentClassId($type_id) {
			if($this->isLoaded($type_id)) {
				return $this->getType($type_id)->getParentId();
			} else {
				$type_id = (int) $type_id;
				$sql = "SELECT SQL_CACHE parent_id FROM cms3_object_types WHERE id = '{$type_id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				if(list($parent_type_id) = mysql_fetch_row($result)) {
					return (int) $parent_type_id;
				} else {
					return false;
				}
			}
		}

		public function getChildClasses($type_id, $childs = false) {
			$res = Array();
			if(!$childs) $childs = Array();

			$type_id = (int) $type_id;

			$sql = "SELECT SQL_CACHE id FROM cms3_object_types WHERE parent_id = '{$type_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($id) = mysql_fetch_row($result)) {
				$res[] = $id;

				if(!in_array($id, $childs)) $res = array_merge($res, $this->getChildClasses($id, $res));
			}
			$res = array_unique($res);
			return $res;
		}

		public function getGuidesList($public_only = false) {
			$res = Array();

			if($public_only) {
				$sql = "SELECT SQL_CACHE id, name FROM cms3_object_types WHERE is_guidable = '1' AND is_public = '1'";
			} else {
				$sql = "SELECT SQL_CACHE id, name FROM cms3_object_types WHERE is_guidable = '1'";
			}

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($id, $name) = mysql_fetch_row($result)) {
				$res[$id] = $name;
			}
			return $res;
		}

		public function getTypesByHierarchyTypeId($hierarchy_type_id) {
			$hierarchy_type_id = (int) $hierarchy_type_id;

			$sql = "SELECT SQL_CACHE id, name FROM cms3_object_types WHERE hierarchy_type_id = '{$hierarchy_type_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$res = Array();

			while(list($id, $name) = mysql_fetch_row($result)) {
				$res[$id] = $name;
			}

			return $res;
		}

		public function getTypeByHierarchyTypeId($hierarchy_type_id) {
			$hierarchy_type_id = (int) $hierarchy_type_id;

			$sql = "SELECT SQL_CACHE id FROM cms3_object_types WHERE hierarchy_type_id = '{$hierarchy_type_id}' LIMIT 1";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($id) = mysql_fetch_row($result)) {
				return $id;
			} else {
				return false;
			}
		}

		public function getBaseType($name, $ext = "") {
			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName($name, $ext);

			if($hierarchy_type) {
				$hierarchy_type_id = $hierarchy_type->getId();
				$type_id = $this->getTypeByHierarchyTypeId($hierarchy_type_id);
				return $type_id;
			} else {
				return false;
			}
		}
	}
?>
