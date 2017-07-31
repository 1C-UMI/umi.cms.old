<?php
	class umiObjectProperty extends umiEntinty implements iUmiEntinty, iUmiObjectProperty {
		private $object_id, $field_id, $field, $field_type,
			$value = Array();

		public function __construct($id, $field_id) {
			$this->setId($id);
			$this->object_id = (int) $id;

			$this->field = umiFieldsCollection::getInstance()->getField($field_id);
			$this->field_id = $field_id;
			$this->loadInfo();
		}

		public function getValue() {
			if($this->getIsMultiple() === false) {
				if(sizeof($this->value) > 0) {
					list($value) = $this->value;
				} else {
					return NULL;
				}
			} else {
				$value = $this->value;
			}
			return $value;
		}

		public function setValue($value) {
			if(!is_array($value)) {
				$value = Array($value);
			}

			$this->value = $value;
			$this->setIsUpdated();
		}

		public function resetValue() {
			$this->value = Array();
			$this->setIsUpdated();
		}

		public function getName() {
			return $this->field->getName();
		}

		public function getTitle() {
			return $this->field->getTitle();
		}


		protected function loadInfo() {
			$field = $this->field;
			$field_types = umiFieldTypesCollection::getInstance();

			$field_type_id = $field->getFieldTypeId();

			$field_type = $field_types->getFieldType($field_type_id);
			$this->field_type = $field_type;

			return $this->loadValue();
		}

		public function getIsMultiple() {
			return $this->field_type->getIsMultiple();
		}

		public function getIsUnsigned() {
			return $this->field_type->getIsUnsigned();
		}

		public function getDataType() {
			return $this->field_type->getDataType();
		}

		public function getIsLocked() {
			return $this->field->getIsLocked();
		}

		public function getIsInheritable() {
			return $this->field->getIsInheritable();
		}

		public function getIsVisible() {
			return $this->field->getIsVisible();
		}


		/***
			The way we load values
		***/

		private function loadValue() {
			$data_type = $this->getDataType();

			switch($data_type) {
				case "int": {
					$value = $this->loadIntValue();
					break;
				}

				case "price": {
					$value = $this->loadIntValue();
					break;
				}

				case "string": {
					$value = $this->loadStringValue();
					break;
				}

				case "text": {
					$value = $this->loadTextValue();
					break;
				}

				case "wysiwyg": {
					$value = $this->loadWYSIWYGValue();
					break;
				}

				case "boolean": {
					$value = $this->loadBooleanValue();
					break;
				}


				case "relation": {
					$value = $this->loadRelationValue();
					break;
				}

				case "img_file": {
					$value = $this->loadImgFileValue();
					break;
				}

				case "password": {
					$value = $this->loadStringValue();
					break;
				}

				case "date": {
					$value = $this->loadDateValue();
					break;
				}

				case "tags": {
					$value = $this->loadTagsValue();
					break;
				}

				case "symlink": {
					$value = $this->loadSymlinkValue();
					break;
				}

				case "file": {
					$value = $this->loadFileValue();
					break;
				}

				case "swf_file": {
					$value = $this->loadImgFileValue();
					break;
				}


				default: {
					trigger_error("Unkown data type \"{$data_type}\"", E_USER_WARNING);
					break;
				}
			}
			$this->value = $value;
			//TODO: maybe, we should create standalone class for `file` object to use like in object-relation field
		}


		private function loadIntValue() {
			$res = Array();
			$field_id = $this->field_id;

			if($this->getIsMultiple()) {
				$sql = "SELECT  SQL_CACHE int_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND int_val IS NOT NULL";
			} else {
				$sql = "SELECT  SQL_CACHE int_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND int_val IS NOT NULL LIMIT 1";
			}

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				$res[] = (int) $val;
			}

			return $res;
		}


		private function loadStringValue() {
			$res = Array();
			$field_id = $this->field_id;

			if($this->getIsMultiple()) {
				$sql = "SELECT  SQL_CACHE varchar_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND varchar_val IS NOT NULL";
			} else {
				$sql = "SELECT  SQL_CACHE varchar_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND varchar_val IS NOT NULL LIMIT 1";
			}

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				$res[] = self::filterOutputString((string) $val);
			}

			return $res;
		}


		private function loadTextValue() {
			$res = Array();
			$field_id = $this->field_id;

			if($this->getIsMultiple()) {
				$sql = "SELECT  SQL_CACHE text_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND text_val IS NOT NULL";
			} else {
				$sql = "SELECT  SQL_CACHE text_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND text_val IS NOT NULL LIMIT 1";
			}

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				$res[] = self::filterOutputString((string) $val);
			}

			return $res;
		}

		private function loadWYSIWYGValue() {
			$res = Array();
			$field_id = $this->field_id;

			if($this->getIsMultiple()) {
				$sql = "SELECT  SQL_CACHE text_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND text_val IS NOT NULL";
			} else {
				$sql = "SELECT  SQL_CACHE text_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND text_val IS NOT NULL LIMIT 1";
			}

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				if(str_replace("&nbsp;", "", trim($val)) == "") continue;
				$res[] = self::filterOutputString((string) $val);
			}

			return $res;
		}

		private function loadBooleanValue() {
			return $this->loadIntValue();	//TODO
		}

		private function loadRelationValue() {
			$res = Array();
			$field_id = $this->field_id;

			if($this->getIsMultiple()) {
				$sql = "SELECT  SQL_CACHE rel_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND rel_val IS NOT NULL";
			} else {
				$sql = "SELECT  SQL_CACHE rel_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND rel_val IS NOT NULL LIMIT 1";
			}

			$result = mysql_query($sql);
			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				if(umiObjectsCollection::getInstance()->isExists($val)) {
					$res[] = $val;
				}
			}
			return $res;
		}

		private function loadImgFileValue() {
			$res = Array();
			$field_id = $this->field_id;

			if($this->getIsMultiple()) {
				$sql = "SELECT  SQL_CACHE text_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND text_val IS NOT NULL";
			} else {
				$sql = "SELECT  SQL_CACHE text_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND text_val IS NOT NULL LIMIT 1";
			}

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				$img = new umiImageFile(self::filterOutputString($val));
				if($img->getIsBroken()) continue;
				$res[] = $img;
			}
			return $res;
		}

		private function loadDateValue() {
			$res = Array();
			$field_id = $this->field_id;

			if($this->getIsMultiple()) {
				$sql = "SELECT  SQL_CACHE int_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND int_val IS NOT NULL";
			} else {
				$sql = "SELECT  SQL_CACHE int_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND int_val IS NOT NULL LIMIT 1";
			}

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				$res[] = new umiDate((int) $val);
			}

			return $res;
		}

		private function loadTagsValue() {
			$res = Array();
			$field_id = $this->field_id;

			$sql = "SELECT  SQL_CACHE varchar_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND varchar_val IS NOT NULL";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				$res[] = self::filterOutputString((string) $val);
			}
			return $res;
		}

		private function loadSymlinkValue() {
			$res = Array();
			$field_id = $this->field_id;

			$sql = "SELECT  SQL_CACHE tree_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND tree_val IS NOT NULL";

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				$element = umiHierarchy::getInstance()->getElement( (int) $val );
				if($element === false) continue;
				if($element->getIsActive() == false) continue;

				$res[] = $element;
			}

			return $res;
		}

		private function loadFileValue() {
			$res = Array();
			$field_id = $this->field_id;

			if($this->getIsMultiple()) {
				$sql = "SELECT  SQL_CACHE text_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND text_val IS NOT NULL";
			} else {
				$sql = "SELECT  SQL_CACHE text_val FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$field_id}' AND text_val IS NOT NULL LIMIT 1";
			}

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($val) = mysql_fetch_row($result)) {
				$file = new umiFile($val);

				if($file->getIsBroken()) continue;

				$res[] = $file;
			}
			return $res;
		}






		protected function save() {
			$data_type = $this->getDataType();

			switch($data_type) {
				case "int": {
					return $this->saveIntValue();
					break;
				}

				case "price": {
					return $this->saveIntValue();
					break;
				}

				case "string": {
					return $this->saveStringValue();
					break;
				}

				case "text": {
					return $this->saveTextValue();
					break;
				}

				case "relation": {
					return $this->saveRelationValue();
					break;
				}

				case "wysiwyg": {
					return $this->saveTextValue();
					break;
				}

				case "boolean": {
					return $this->saveIntValue();
					break;
				}

				case "img_file": {
					return $this->saveImgFileValue();
					break;
				}

				case "password": {
					return $this->savePasswordValue();
					break;
				}

				case "date": {
					return $this->saveDateValue();
					break;
				}

				case "tags": {
					return $this->saveTagsValue();
					break;
				}

				case "symlink": {
					return $this->saveSymlinkValue();
					break;
				}

				case "file": {
					return $this->saveFileValue();
					break;
				}

				case "swf_file": {
					return $this->saveImgFileValue();
					break;
				}



				default: {
					trigger_error("Can't save object's property, becase \"{$data_type}\" data type is unknown", E_USER_WARNING);
					break;
				}
			}
		}

		private function deleteCurrentRows() {
			$sql = "DELETE FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$this->field_id}'";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}
		}

		private function saveIntValue() {
			$this->deleteCurrentRows();

			foreach($this->value as $val) {
				if($val === false || $val === "") continue;
				$val = (int) $val;

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, int_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}

		private function saveStringValue() {
			$this->deleteCurrentRows();

			foreach($this->value as $val) {
				if(strlen($val) == 0) continue;

				$val = self::filterInputString($val);

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, varchar_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}

		private function saveTextValue() {
			$this->deleteCurrentRows();

			foreach($this->value as $val) {

				$val = self::filterInputString($val);

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, text_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}

		private function saveRelationValue() {
			$this->deleteCurrentRows();

			if(is_null($this->value)) {
				return;
			}

			foreach($this->value as $val) {
				if(!$val) continue;

				if(is_object($val)) {
					$val = $val->getId();
				} else {
					if(is_numeric($val)) {
						$val = (int) $val;
					} else {
						if($guide_id = $this->field->getGuideId()) {
							$val_name = self::filterInputString($val);

							$sql = "SELECT id FROM cms3_objects WHERE type_id = '{$guide_id}' AND name = '{$val_name}'";

							$result = mysql_query($sql);

							if($err = mysql_error()) {
								trigger_error($err, E_USER_ERROR);
								return false;
							}

							if(mysql_num_rows($result)) {
								list($val) = mysql_fetch_row($result);
							} else {
								if($val = umiObjectsCollection::getInstance()->addObject($val, $guide_id)) {
									$val = (int) $val;
								} else {
									trigger_error("Can't create guide item", E_USER_ERROR);
									return false;
								}
							}
						} else {
							continue;
						}
					}
				}

				if(!$val) continue;

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, rel_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}


		private function saveImgFileValue() {
			$this->deleteCurrentRows();

			if(is_null($this->value)) {
				return;
			}

			foreach($this->value as $val) {
				if(!$val) continue;
				$val = mysql_real_escape_string($val->getFilePath());

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, text_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}

		private function savePasswordValue() {
			foreach($this->value as $val) {
				if(strlen($val) == 0) continue;

				$this->deleteCurrentRows();

				$val = self::filterInputString($val);

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, varchar_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}

		private function saveDateValue() {
			$this->deleteCurrentRows();

			foreach($this->value as $val) {
				if($val === false || $val === "") continue;
				$val = (is_object($val)) ? (int) $val->timestamp : (int) $val;

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, int_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}

		private function saveTagsValue() {
			$this->deleteCurrentRows();

			if(sizeof($this->value) == 1) {
				$value = split(",", trim($this->value[0], ","));
			} else {
				$value = $this->value;
			}

			foreach($value as $val) {
				$val = trim($val);
				if(strlen($val) == 0) continue;

				$val = self::filterInputString($val);

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, varchar_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}




		private function fillNull() {
			$sql = "SELECT COUNT(*) FROM cms3_object_content WHERE obj_id = '{$this->object_id}' AND field_id = '{$this->field_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				list($c) = mysql_fetch_row($result);
			}

			if($c == 0) {
				$sql = "INSERT INTO cms3_object_content (obj_id, field_id) VALUES('{$this->object_id}', '{$this->field_id}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				} else {
					return true;
				}
			}
			return true;
		}

		private function saveSymlinkValue() {
			$this->deleteCurrentRows();

			foreach($this->value as $val) {
				if($val === false || $val === "") continue;

				if(is_object($val)) {
					$val = (int) $val->getId();
				}

				if(is_numeric($val)) {
					$val = (int) $val;
				}

				if(!$val) continue;

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, tree_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}

		private function saveFileValue() {
			$this->deleteCurrentRows();

			if(is_null($this->value)) {
				return;
			}

			foreach($this->value as $val) {
				if(!$val) continue;
				$val = mysql_real_escape_string($val->getFilePath());

				$sql = "INSERT INTO cms3_object_content (obj_id, field_id, text_val) VALUES('{$this->object_id}', '{$this->field_id}', '{$val}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}
			}
			$this->fillNull();
		}




		public static function filterInputString($string) {
			if(cmsController::getInstance()->getCurrentMode() == "admin") {
//				$string = iconv("UTF-8", "CP1251//TRANSLIT", $string);
			} else {
				//nothing to do for now
			}
			$string = mysql_real_escape_string($string);
			$string = umiObjectProperty::filterCDATA($string);
			return $string;
		}


		public static function filterOutputString($string) {
			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$string = str_replace("%", "&#037;", $string);
			} else {
				$string = str_replace("&#037;", "%", $string);
			}
			return $string;
		}
		
		
		public static function filterCDATA($string) {
			$string = str_replace("]]>", "]]&gt;", $string);
			return $string;
		}
	}
?>