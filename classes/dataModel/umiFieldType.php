<?php
	class umiFieldType extends umiEntinty implements iUmiEntinty, iUmiFieldType {
		private $name, $data_type, $is_multiple = false, $is_unsigned = false;
		protected $store_type = "field_type";

		public function getName() {
			return $this->name;
		}

		public function getIsMultiple() {
			return $this->is_multiple;
		}

		public function getIsUnsigned() {
			return $this->is_multiple;
		}

		public function getDataType() {
			return $this->data_type;
		}


		public function setName($name) {
			$this->name = $name;
			$this->setIsUpdated();
		}

		public function setIsMultiple($is_multiple) {
			$this->is_multiple = (bool) $is_multiple;
			$this->setIsUpdated();
		}

		public function setIsUnsigned($is_unsigned) {
			$this->is_unsigned = (bool) $is_unsigned;
			$this->setIsUpdated();
		}

		public function setDataType($data_type) {
			if(self::isValidDataType($data_type)) {
				$this->data_type = $data_type;
				$this->setIsUpdated();
				return true;
			} else {
				return false;
			}
		}

		public static function getDataTypes() {
			return

			Array	(
				"int",
				"string",
				"text",
				"relation",
				"file",
				"img_file",
				"swf_file",
				"date",
				"boolean",
				"wysiwyg",
				"password",
				"tags",
				"symlink",
				"price"
				);
		}

		public static function getDataTypeDB($data_type) {
			$rels = Array	(
				"int"		=> "int_val",
				"string"	=> "varchar_val",
				"text"		=> "text_val",
				"relation"	=> "rel_val",
				"file"		=> "varchar_val",
				"img_file"	=> "varchar_val",
				"swf_file"	=> "varchar_val",
				"date"		=> "int_val",
				"boolean"	=> "int_val",
				"wysiwyg"	=> "text_val",
				"password"	=> "varchar_val",
				"tags"		=> "varchar_val",
				"symlink"	=> "tree_val",
				"price"		=> "int_val"
				);

			if(array_key_exists($data_type, $rels) === false) {
				return false;
			} else {
				return $rels[$data_type];
			}
		}

		public static function isValidDataType($data_type) {
			return in_array($data_type, self::getDataTypes());
		}


		protected function loadInfo() {
			$sql = "SELECT SQL_CACHE name, data_type, is_multiple, is_unsigned FROM cms3_object_field_types WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($name, $data_type, $is_multiple, $is_unsigned) = mysql_fetch_row($result)) {
				if(!self::isValidDataType($data_type)) {
					trigger_error("Wrong data type given for filed type #{$this->id}", E_USER_WARNING);
					return false;
				}

				$this->name = $name;
				$this->data_type = $data_type;
				$this->is_multiple= (bool) $is_multiple;
				$this->is_unsigned = (bool) $is_unsigned;

				return true;
			} else {
				return false;
			}
		}

		protected function save() {
			$name = mysql_real_escape_string($this->name);
			$data_type = mysql_real_escape_string($this->data_type);
			$is_multiple = (int) $this->is_multiple;
			$is_unsigned = (int) $this->is_unsigned;

			$sql = "UPDATE cms3_object_field_types SET name = '{$name}', data_type = '{$data_type}', is_multiple = '{$is_multiple}', is_unsigned = '{$is_unsigned}' WHERE id = '{$this->id}'";
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