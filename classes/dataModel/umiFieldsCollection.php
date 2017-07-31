<?php
	class umiFieldsCollection extends singleton implements iSingleton, iUmiFieldsCollection {
		private	$fields = Array();

		protected function __construct() {
		}

		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}


		public function getField($field_id) {
			if($this->isExists($field_id)) {
				return $this->fields[$field_id];
			} else {
				return $this->loadField($field_id);
			}
		}

		public function delField($field_id) {
			if($this->isExists($field_id)) {
				$sql = "DELETE FROM cms3_object_fields WHERE id = '{$field_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				unset($this->fields[$field_id]);
				return true;
			} else {
				return false;
			}
		}

		public function addField($name, $title, $field_type_id, $is_visible = true, $is_locked = false, $is_inheritable = false) {
			$sql = "INSERT INTO cms3_object_fields VALUES()";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$field_id = mysql_insert_id();

			$field = new umiField($field_id);

			$field->setName($name);
			$field->setTitle($title);
			if(!$field->setFieldTypeId($field_type_id)) return false;
			$field->setIsVisible($is_visible);
			$field->setIsLocked($is_locked);
			$field->setIsInheritable($is_inheritable);

			if(!$field->commit()) return false;

			$this->fields[$field_id] = $field;

			return $field_id;
		}

		public function isExists($field_id) {
			return (bool) array_key_exists($field_id, $this->fields);
		}

		private function loadField($field_id) {
			$sql = "SELECT SQL_CACHE COUNT(*) FROM cms3_object_fields WHERE id = '{$field_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNINIG);
				return false;
			} else {
				list($count) = mysql_fetch_row($result);
			}

			if($count) {
				if($field = memcachedController::getInstance()->load($field_id, "field")) {
				} else {
					$field = new umiField($field_id);
					memcachedController::getInstance()->save($field, "field");
				}
				$this->fields[$field_id] = $field;
				return $this->fields[$field_id];
			} else {
				return false;
			}
		}
	}
?>