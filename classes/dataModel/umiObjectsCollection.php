<?php
	class umiObjectsCollection extends singleton implements iSingleton, iUmiObjectsCollection {
		private	$objects = Array();

		protected function __construct() {
		}

		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}


		private function isLoaded($object_id) {
			return (bool) array_key_exists($object_id, $this->objects);
		}

		public function isExists($object_id) {
			$object_id = (int) $object_id;

			$sql = "SELECT COUNT(*) FROM cms3_objects WHERE id = '{$object_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				list($count) = mysql_fetch_row($result);
			}
			return ($count > 0) ? true : false;
		}

		public function getObject($object_id) {
			if($this->isLoaded($object_id)) {
				return $this->objects[$object_id];
			}

			if($object = memcachedController::getInstance()->load($object_id, "object")) {
			} else {
				$object = new umiObject($object_id);
				memcachedController::getInstance()->save($object, "object");
			}
			
			if(is_object($object)) {
				$this->objects[$object_id] = $object;
				return $this->objects[$object_id];
			} else {
				return false;
			}
		}

		public function delObject($object_id) {
			if($this->isExists($object_id)) {
				$object_id = (int) $object_id;

				$sql = "DELETE FROM cms3_objects WHERE id = '{$object_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				if($this->isLoaded($object_id)) {
					unset($this->objects[$object_id]);
				}

				return true;
			} else {
				return false;
			}
		}

		public function addObject($name, $type_id, $is_locked = false) {
			$type_id = (int) $type_id;

			$sql = "INSERT INTO cms3_objects (type_id) VALUES('$type_id')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$object_id = mysql_insert_id();

			$object = new umiObject($object_id);

			$object->setName($name);
			$object->setIsLocked($is_locked);

			//Set current user
			if($users_inst = cmsController::getInstance()->getModule("users")) {
				if($users_inst->is_auth()) {
					$user_id = cmsController::getInstance()->getModule("users")->user_id;
					$object->setOwnerId($user_id);
				}
			} else {
			    $object->setOwnerId(NULL);
			}

			$object->commit();

			$this->objects[$object_id] = $object;

			$this->resetObjectProperties($object_id);
			return $object_id;
		}

		public function getGuidedItems($guide_id) {
			$res = Array();

			$guide_id = (int) $guide_id;

			$sql = "SELECT SQL_CACHE id, name FROM cms3_objects WHERE type_id = '{$guide_id}' ORDER BY name ASC";
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
		
		
		protected function resetObjectProperties($object_id) {
			$object = $this->getObject($object_id);
			$object_type_id = $object->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$object_fields = $object_type->getAllFields();
			foreach($object_fields as $object_field) {
				$object->setValue($object_field->getName(), Array());
			}
		}
	}
?>