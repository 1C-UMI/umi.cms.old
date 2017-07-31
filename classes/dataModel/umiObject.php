<?php
	class umiObject extends umiEntinty implements iUmiEntinty, iUmiObject {
		private $name, $type_id, $is_locked, $owner_id = false,
			$type, $properties = Array(), $prop_groups = Array();
		protected $store_type = "object";

		public function getName() {
			return $this->name;
		}

		public function getTypeId() {
			return $this->type_id;
		}

		public function getIsLocked() {
			return $this->is_locked;
		}


		public function setName($name) {
			$this->name = umiObjectProperty::filterInputString($name);
			$this->setIsUpdated();
		}

		public function setTypeId($type_id) {
			$this->type_id = $type_id;
			return true;
		}

		public function setIsLocked($is_locked) {
			$this->is_locked = (bool) $is_locked;
			$this->setIsUpdated();
		}

		public function setOwnerId($ownerId) {

			if(!is_null($ownerId) and umiObjectsCollection::getInstance()->isExists($ownerId)) {
				$this->owner_id = $ownerId;
				return true;
			}
			else {
				$this->owner_id = NULL;
				return false;
			}
		}

		public function getOwnerId() {
			return $this->owner_id;
		}

		protected function save() {
			$name = $this->name;
			$type_id = (int) $this->type_id;
			$is_locked = (int) $this->is_locked;
			$owner_id = (int) $this->owner_id;
			$sql = "UPDATE cms3_objects SET name = '{$name}', type_id = '{$type_id}', is_locked = '{$is_locked}', owner_id = '{$owner_id}' WHERE id = '{$this->id}'";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			foreach($this->properties as $prop) {
				if(is_object($prop)) {
					$prop->commit();
				}
			}

			return true;
		}

		protected function loadInfo() {
			$sql = "SELECT SQL_CACHE name, type_id, is_locked, owner_id FROM cms3_objects WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($name, $type_id, $is_locked, $owner_id) = mysql_fetch_row($result)) {
				if(!$type_id) {	//Foregin keys check failed, or manual queries made. Delete this object.
					umiObjectsCollection::getInstance()->delObject($this->id);
					return false;
				}
			
				$this->name = $name;
				$this->type_id = (int) $type_id;
				$this->is_locked = (bool) $is_locked;
				$this->owner_id = (int) $owner_id;

				return $this->loadType();
			} else {
				return false;
			}
		}

		private function loadType() {
			$type = umiObjectTypesCollection::getInstance()->getType($this->type_id);

			if(!$type) {
				trigger_error("Can't load type in object's init", E_USER_WARNING);
				return false;
			}

			$this->type = $type;
			return $this->loadProperties();
		}

		private function loadProperties() {
			$type = $this->type;
			$groups_list = $type->getFieldsGroupsList();
			foreach($groups_list as $group) {
				if($group->getIsActive() == false) continue;

				$fields = $group->getFields();

				$this->prop_groups[$group->getId()] = Array();

				foreach($fields as $field) {
					$this->properties[$field->getId()] = $field->getName();
					$this->prop_groups[$group->getId()][] = $field->getId();
				}
			}
		}

		public function getPropByName($prop_name) {
			$prop_name = strtolower($prop_name);

			foreach($this->properties as $field_id => $prop) {
				if(is_object($prop)) {
					if($prop->getName() == $prop_name) {
						return $prop;
					}
				} else {
					if($prop == $prop_name) {
						$prop = new umiObjectProperty($this->id, $field_id);
						$this->properties[$field_id] = $prop;
						return $prop;
					}
				}
			}
			return NULL;
		}

		public function getPropById($field_id) {
			if(!$this->isPropertyExists($field_id)) {
				return NULL;
			} else {
				if(!is_object($this->properties)) {
					$this->properties[$field_id] = new umiObjectProperty($this->id, $field_id);
				}
				return $this->properties[$field_id];
			}
		}

		public function isPropertyExists($field_id) {
			return (bool) array_key_exists($field_id, $this->properties);
		}

		public function isPropGroupExists($prop_group_id) {
			return (bool) array_key_exists($prop_group_id, $this->prop_groups);
		}

		public function getPropGroupId($prop_group_name) {
			$groups_list = $this->type->getFieldsGroupsList();
			foreach($groups_list as $group) {
				if($group->getName() == $prop_group_name) {
					return $group->getId();
				}
			}
			return false;
		}

		public function getPropGroupByName($prop_group_name) {
			$groups_list = $this->type->getFieldsGroupsList();

			if($group_id = $this->getPropGroupId($prop_group_name)) {
				return $this->getPropGroupById($group_id);
			} else {
				return false;
			}
		}

		public function getPropGroupById($prop_group_id) {
			if($this->isPropGroupExists($prop_group_id)) {
				return $this->prop_groups[$prop_group_id];
			} else {
				return false;
			}
		}


		public function getValue($prop_name) {
			if($prop = $this->getPropByName($prop_name)) {
				return $prop->getValue();
			} else {
				return false;
			}
		}

		public function setValue($prop_name, $prop_value) {
			if($prop = $this->getPropByName($prop_name)) {
				return $prop->setValue($prop_value);
			} else {
				return false;
			}
		}
	}
?>