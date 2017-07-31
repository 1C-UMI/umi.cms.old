<?php
	class umiObjectType extends umiEntinty implements iUmiEntinty, iUmiObjectType {
		private $name, $parent_id, $is_locked = false,
			$field_groups = Array(), $is_guidable = false, $is_public = false, $hierarchy_type_id;
		protected $store_type = "object_type";


		public function getName() {
			return $this->name;
		}

		public function setName($name) {
			$this->name = umiObjectProperty::filterInputString($name);
			$this->setIsUpdated();
		}

		public function getIsLocked() {
			return $this->is_locked;
		}


		public function setIsLocked($is_locked) {
			$this->is_locked = (bool) $is_locked;
			$this->setIsUpdated();
		}

		public function getParentId() {
			return $this->parent_id;
		}


		public function getIsGuidable() {
			return $this->is_guidable;
		}

		public function setIsGuidable($is_guidable) {
			$this->is_guidable = (bool) $is_guidable;
			$this->setIsUpdated();
		}

		public function getIsPublic() {
			return $this->is_public;
		}

		public function setIsPublic($is_public) {
			$this->is_public = (bool) $is_public;
			$this->setIsUpdated();
		}

		public function getHierarchyTypeId() {
			return $this->hierarchy_type_id;
		}

		public function setHierarchyTypeId($hierarchy_type_id) {
			$this->hierarchy_type_id = (int) $hierarchy_type_id;
			$this->setIsUpdated();
		}


		public function addFieldsGroup($name, $title, $is_active = true, $is_visible = true) {
			$sql = "SELECT MAX(ord) FROM cms3_object_field_groups WHERE type_id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($ord) = mysql_fetch_row($result)) {
				$ord = ((int) $ord) + 5;
			} else {
				$ord = 1;
			}

			$sql = "INSERT INTO cms3_object_field_groups (type_id, ord) VALUES('{$this->id}', '{$ord}')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$field_group_id = mysql_insert_id();

			$field_group = new umiFieldsGroup($field_group_id);
			$field_group->setName($name);
			$field_group->setTitle($title);
			$field_group->setIsActive($is_active);
			$field_group->setIsVisible($is_visible);
			$field_group->commit();

			$this->field_groups[$field_group_id] = $field_group;


			$child_types = umiObjectTypesCollection::getInstance()->getSubTypesList($this->id);
			$sz = sizeof($child_types);
			for($i = 0; $i < $sz; $i++) {
				$child_type_id = $child_types[$i];
					
				if($type = umiObjectTypesCollection::getInstance()->getType($child_type_id)) {
					$type->addFieldsGroup($name, $title, $is_active, $is_visible);
				} else {
					trigger_error("Can't load object type #{$child_type_id}", E_USER_WARNING);
				}
			}


			return $field_group_id;
		}

		public function delFieldsGroup($field_group_id) {
			if($this->isFieldsGroupExists($field_group_id)) {
				$field_group_id = (int) $field_group_id;
				$sql = "DELETE FROM cms3_object_field_groups WHERE id = '{$field_group_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				unset($this->field_groups[$field_group_id]);

				return true;

			} else {
				return false;
			}
		}

		public function getFieldsGroupByName($field_group_name) {
			$groups = $this->getFieldsGroupsList();
			foreach($groups as $group_id => $group) {
				if($group->getName()  == $field_group_name) {
					return $group;
				}
			}
			return false;
		}

		public function getFieldsGroup($field_group_id) {
			if($this->isFieldsGroupExists($field_group_id)) {
				return $this->field_groups[$field_group_id];
			} else {
				return false;
			}
		}

		public function getFieldsGroupsList() {
			return $this->field_groups;
		}


		private function isFieldsGroupExists($field_group_id) {
			return (bool) array_key_exists($field_group_id, $this->field_groups);
		}

		protected function loadInfo() {
			$sql = "SELECT SQL_CACHE name, parent_id, is_locked, is_guidable, is_public, hierarchy_type_id FROM cms3_object_types WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($name, $parent_id, $is_locked, $is_guidable, $is_public, $hierarchy_type_id) = mysql_fetch_row($result)) {
				$this->name = $name;
				$this->parent_id = (int) $parent_id;
				$this->is_locked = (bool) $is_locked;
				$this->is_guidable = (bool) $is_guidable;
				$this->is_public = (bool) $is_public;
				$this->hierarchy_type_id = (int) $hierarchy_type_id;

				return $this->loadFieldsGroups();
			} else {
				return false;
			}
		}

		private function loadFieldsGroups() {
			$sql = "SELECT SQL_CACHE id FROM cms3_object_field_groups WHERE type_id = '{$this->id}' ORDER BY ord ASC";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($field_group_id) = mysql_fetch_row($result)) {
				$field_group = new umiFieldsGroup($field_group_id);
				$this->field_groups[$field_group_id] = $field_group;
			}
			return true;
		}

		protected function save() {
			$name = $this->name;
			$parent_id = (int) $this->parent_id;
			$is_locked = (int) $this->is_locked;
			$is_guidable = (int) $this->is_guidable;
			$is_public = (int) $this->is_public;
			$hierarchy_type_id = (int) $this->hierarchy_type_id;

			$sql = "UPDATE cms3_object_types SET name = '{$name}', parent_id = '{$parent_id}', is_locked = '{$is_locked}', is_guidable = '{$is_guidable}', is_public = '{$is_public}', hierarchy_type_id = '{$hierarchy_type_id}' WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return false;
			}
		}

		public function setFieldGroupOrd($group_id, $neword, $is_last) {
			$neword = (int) $neword;
			$group_id = (int) $group_id;

			if(!$is_last) {
				$sql = "SELECT type_id FROM cms3_object_field_groups WHERE id = '{$group_id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}


				if(!(list($type_id) = mysql_fetch_row($result))) {
					return false;
				}	

				$sql = "UPDATE cms3_object_field_groups SET ord = (ord + 1) WHERE type_id = '{$type_id}' AND ord >= '{$neword}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}
			}

			$sql = "UPDATE cms3_object_field_groups SET ord = '{$neword}' WHERE id = '{$group_id}'";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}
			return true;
		}


		public function getAllFields() {
			$fields = Array();

			$groups = $this->getFieldsGroupsList();
			foreach($groups as $group) {
				$fields = array_merge($fields, $group->getFields());
			}

			return $fields;
		}

		public function getFieldId($field_name) {
			$groups = $this->getFieldsGroupsList();
			foreach($groups as $group_id => $group) {
				if(!$group->getIsActive()) continue;

				$fields = $group->getFields();

				foreach($fields as $field_id => $field) {
					if($field->getName() == $field_name) {
						return $field->getId();
					}
				}
			}
			return false;
		}
	}
?>