<?php
	class umiFieldsGroup extends umiEntinty implements iUmiEntinty, iUmiFieldsGroup {
		private	$name, $title,
			$type_id, $ord,
			$is_active = true, $is_visible = true, $is_locked = false,

			$autoload_fields = true,

			$fields = Array();


		public function getName() {
			return $this->name;
		}

		public function getTitle() {
			return $this->title;
		}

		public function getTypeId() {
			return $this->type_id;
		}

		public function getOrd() {
			return $this->ord;
		}

		public function getIsActive() {
			return $this->is_active;
		}

		public function getIsVisible() {
			return $this->is_visible;
		}

		public function getIsLocked() {
			return $this->is_locked;
		}


		public function setName($name) {
			$this->name = umiObjectProperty::filterInputString($name);
			$this->setIsUpdated();
		}

		public function setTitle($title) {
			$this->title = umiObjectProperty::filterInputString($title);
			$this->setIsUpdated();
		}

		public function setTypeId($type_id) {
			$types = umiObjectTypesCollection::getInstance();
			if($types->isExists($type_id)) {
				$this->type_id = $type_id;
				return true;
			} else {
				return false;
			}
		}

		public function setOrd($ord) {
			$this->ord = $ord;
			$this->setIsUpdated();
		}

		public function setIsActive($is_active) {
			$this->is_active = (bool) $is_active;
			$this->setIsUpdated();
		}

		public function setIsVisible($is_visible) {
			$this->is_visible = (bool) $is_visible;
			$this->setIsUpdated();
		}

		public function setIsLocked($is_locked) {
			$this->is_locked = (bool) $is_locked;
			$this->setIsUpdated();
		}


		protected function loadInfo() {
			$sql = "SELECT SQL_CACHE name, title, type_id, is_active, is_visible, is_locked, ord FROM cms3_object_field_groups WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($name, $title, $type_id, $is_active, $is_visible, $is_locked, $ord) = mysql_fetch_row($result)) {
				if(!umiObjectTypesCollection::getInstance()->isExists($type_id)) {
					return false;
				}

				$this->name = $name;
				$this->title = $title;
				$this->type_id = $type_id;
				$this->is_active = (bool) $is_active;
				$this->is_visible = (bool) $is_visible;
				$this->is_locked = (bool) $is_locked;
				$this->ord = (int) $ord;

				if($this->autoload_fields) {
					return $this->loadFields();
				} else {
					return true;
				}
			} else {
				return false;
			}
		}


		protected function save() {
			$name = mysql_real_escape_string($this->name);
			$title = mysql_real_escape_string($this->title);
			$type_id = (int) $this->type_id;
			$is_active = (int) $this->is_active;
			$is_visible = (int) $this->is_visible;
			$ord = (int) $this->ord;
			$is_locked = (int) $this->is_locked;

			$sql = "UPDATE cms3_object_field_groups SET name = '{$name}', title = '{$title}', type_id = '{$type_id}', is_active = '{$is_active}', is_visible = '{$is_visible}', ord = '{$ord}', is_locked = '{$is_locked}' WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return true;
			}
		}

		private function loadFields() {
			$sql = "SELECT SQL_CACHE field_id FROM cms3_fields_controller WHERE group_id = '{$this->id}' ORDER BY ord ASC";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$fields = umiFieldsCollection::getInstance();

			while(list($field_id) = mysql_fetch_row($result)) {
				if($field = $fields->getField($field_id)) {
					$this->fields[$field_id] = $field;
				}
			}
		}

		public function getFields() {
			return $this->fields;
		}

		private function isLoaded($field_id) {
			return (bool) array_key_exists($field_id, $this->fields);
		}

		public function attachField($field_id) {
			if($this->isLoaded($field_id)) {
				return true;
			} else {
				$field_id = (int) $field_id;

				$sql = "SELECT MAX(ord) FROM cms3_fields_controller WHERE group_id = '{$this->id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				list($ord) = mysql_fetch_row($result);
				$ord += 5;

				$sql = "INSERT INTO cms3_fields_controller (field_id, group_id, ord) VALUES('{$field_id}', '{$this->id}', '{$ord}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				$fields = umiFieldsCollection::getInstance();
				$field = $fields->getField($field_id);
				$this->fields[$field_id] = $field;
			}
		}

		public function detachField($field_id) {
			if($this->isLoaded($field_id)) {
				$field_id = (int) $field_id;

				$sql = "DELETE FROM cms3_fields_controller WHERE field_id = '{$field_id}' AND group_id = '{$this->id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				unset($this->fields[$field_id]);

				$sql = "SELECT COUNT(*) FROM cms3_fields_controller WHERE field_id = '{$field_id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				if(list($c) = mysql_fetch_row($result)) {
					return ($c == 0) ? umiFieldsCollection::getInstance()->delField($field_id) : true;
				} else return false;

				return true;
			} else {
				return false;
			}
		}

		public function moveFieldAfter($field_id, $after_field_id, $group_id, $is_last) {
			if($after_field_id == 0) {
				$neword = 0;
			} else {
				$sql = "SELECT ord FROM cms3_fields_controller WHERE group_id = '{$group_id}' AND field_id = '{$after_field_id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				} else {
					list($neword) = mysql_fetch_row($result);
				}
			}

			if($is_last) {
				$sql = "UPDATE cms3_fields_controller SET ord = (ord + 1) WHERE group_id = '{$this->id}' AND ord >= '{$neword}'";

				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}
			} else {
				$sql = "SELECT MAX(ord) FROM cms3_fields_controller WHERE group_id = '{$group_id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				list($neword) = mysql_fetch_row($result);
				++$neword;
			}

			$sql = "UPDATE cms3_fields_controller SET ord = '{$neword}', group_id = '$group_id' WHERE group_id = '{$this->id}' AND field_id = '{$field_id}'";
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
