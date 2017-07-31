<?php
	class umiFieldTypesCollection extends singleton implements iSingleton, iUmiFieldTypesCollection {
		private $field_types = Array();

		protected function __construct() {
			$this->loadFieldTypes();
		}

		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}


		public function addFieldType($name, $data_type = "string", $is_multiple = false, $is_unsigned = false) {
			if(!umiFieldType::isValidDataType($data_type)) {
				trigger_error("Not valid data type given", E_USER_WARNING);
				return false;
			}

			$sql = "INSERT INTO cms3_object_field_types (data_type) VALUES('{$data_type}')";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$field_type_id = mysql_insert_id();

			$field_type = new umiFieldType($field_type_id);

			$field_type->setName($name);
			$field_type->setDataType($data_type);
			$field_type->setIsMultiple($is_multiple);
			$field_type->setIsUnsigned($is_unsigned);
			$field_type->commit();

			$this->field_types[$field_type_id] = $field_type;

			return $field_type_id;
		}

		public function delFieldType($field_type_id) {
			if($this->isExists($field_type_id)) {
				$field_type_id = (int) $field_type_id;
				$sql = "DELETE FROM cms3_object_field_types WHERE id = '{$field_type_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				unset($this->field_types[$field_type_id]);
				return true;
			} else {
				return false;
			}
		}

		public function getFieldType($field_type_id) {
			if($this->isExists($field_type_id)) {
				return $this->field_types[$field_type_id];
			} else {
				return true;
			}
		}

		public function isExists($field_type_id) {
			return (bool) array_key_exists($field_type_id, $this->field_types);
		}


		private function loadFieldTypes() {
			$sql = "SELECT SQL_CACHE id FROM cms3_object_field_types ORDER BY name ASC";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return $err;
			}

			while(list($field_type_id) = mysql_fetch_row($result)) {
				if($field_type = memcachedController::getInstance()->load($field_type_id, "field_type")) {
				} else {
					$field_type = new umiFieldType($field_type_id);
					memcachedController::getInstance()->save($field_type, "field_type");
				}
				$this->field_types[$field_type_id] = $field_type;
			}

			return true;
		}

		public function getFieldTypesList() {
			return $this->field_types;
		}
	}
?>