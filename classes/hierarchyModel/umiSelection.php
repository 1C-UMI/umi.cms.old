<?php
	class umiSelection implements iUmiSelection {
		private	$order = Array(),
			$limit = Array(),
			$object_type = Array(),
			$element_type = Array(),
			$props = Array(),
			$hierarchy = Array(),
			$perms = Array(),
			$names = Array(),
			$active = Array(),

			$is_order = false,  $is_limit = false, $is_object_type = false, $is_element_type = false, $is_props = false, $is_hierarchy = false, $is_permissions = false, $is_forced = false, $is_names = false, $is_active = false,
			$condition_mode_or = false;

		public function setObjectTypeFilter($is_enabled = true) {
			$this->is_object_type = (bool) $is_enabled;
			if (!$is_enabled) $this->object_type = Array();
		}

		public function setElementTypeFilter($is_enabled = true) {
			$this->is_element_type = (bool) $is_enabled;
			if (!$is_enabled) $this->element_type = Array();
		}

		public function setPropertyFilter($is_enabled = true) {
			$this->is_props = (bool) $is_enabled;
			if (!$is_enabled) $this->props = Array();
		}

		public function setLimitFilter($is_enabled = true) {
			$this->is_limit = (bool) $is_enabled;
			if (!$is_enabled) $this->limit = Array();
		}

		public function setHierarchyFilter($is_enabled = true) {
			$this->is_hierarchy = (bool) $is_enabled;
			if (!$is_enabled) $this->hierarchy = Array();
		}

		public function setOrderFilter($is_enabled = true) {
			$this->is_order = (bool) $is_enabled;
			if (!$is_enabled) $this->order = Array();
		}

		public function setPermissionsFilter($is_enabled = true) {
			

			$this->is_permissions = $is_enabled;

			$user_id = $this->getCurrentUserId();
			if(cmsController::getInstance()->getModule("users")->isSv($user_id)) {
				$this->is_permissions = false;
			}
			if (!$is_enabled) $this->perms = Array();
		}

		public function setActiveFilter($is_enabled = true) {
			$this->is_active = (bool) $is_enabled;
			if (!$is_enabled) $this->is_active = Array();
		}

		public function setNamesFilter($is_enabled = true) {
			$this->is_names = (bool) $is_enabled;
			if (!$is_enabled) $this->names = Array();
		}

		public function forceHierarchyTable($isForced = true) {
			$this->is_forced = (bool) $isForced;
		}

		public function addObjectType($object_type_id) {
			if(is_array($object_type_id)) {
				foreach($object_type_id as $sub_object_type_id) {
					if(!$this->addObjectType($sub_object_type_id)) {
						return false;
					}
				}
				return true;
			}

			if(umiObjectTypesCollection::getInstance()->isExists($object_type_id)) {
				if(in_array($object_type_id, $this->object_type) === false) {
					$this->object_type[] = $object_type_id;
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function addElementType($element_type_id) {
			if(umiHierarchyTypesCollection::getInstance()->isExists($element_type_id)) {
				if(in_array($element_type_id, $this->element_type) === false) {
					$this->element_type[] = $element_type_id;
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function addLimit($per_page, $page = 0) {
			$this->limit = Array($per_page, $page);
		}

		public function addActiveFilter($active) {
			$this->active = Array($active);
		}

		public function setOrderByProperty($field_id, $asc = true) {
			$data_type = $this->getDataByFieldId($field_id);

			$filter = Array("field_id" => $field_id, "asc" => $asc, "type" => $data_type, "native_field" => false);

			if(in_array($filter, $this->order) === false) {
				$this->order[] = $filter;
				return true;
			} else {
				return false;
			}
		}

		public function setOrderByOrd() {
			$filter = Array("type" => "native", "native_field" => "ord", "asc" => true);

			if(in_array($filter, $this->order) === false) {
				$this->order[] = $filter;
				return true;
			} else {
				return false;
			}
		}
		
		public function setOrderByRand() {
			$filter = Array("type" => "native", "native_field" => "rand", "asc" => true);

			if(in_array($filter, $this->order) === false) {
				$this->order[] = $filter;
				return true;
			} else {
				return false;
			}
		}

		public function setOrderByName($asc = true) {
			$filter = Array("type" => "native", "native_field" => "name", "asc" => $asc);

			if(in_array($filter, $this->order) === false) {
				$this->order[] = $filter;
				return true;
			} else {
				return false;
			}
		}


		public function addHierarchyFilter($element_id, $depth = 0) {
			if(umiHierarchy::getInstance()->isExists($element_id) || (is_numeric($element_id) && $element_id == 0)) {
				if(in_array($element_id, $this->hierarchy) === false || $element_id == 0) {
					$this->hierarchy[] = (int) $element_id;
				}

				if($depth > 0) {
					$childs = umiHierarchy::getInstance()->getChilds($element_id, true, true, 1);

					foreach($childs as $element_id => $nl) {
						$this->addHierarchyFilter($element_id, ($depth - 1));
					}
				}
			} else {
				return false;
			}
		}


		public function addPropertyFilterBetween($field_id, $min, $max) {
			$data_type = $this->getDataByFieldId($field_id);

			$filter = Array("type" => $data_type, "field_id" => $field_id, "filter_type" => "between", "min" => $min, "max" => $max);
			$this->props[] = $filter;
		}

		public function addPropertyFilterEqual($field_id, $value, $case_insencetive = true) {
			$data_type = $this->getDataByFieldId($field_id);

			$filter = Array("type" => $data_type, "field_id" => $field_id, "filter_type" => "equal", "value" => $value, "case_insencetive" => $case_insencetive);
			$this->props[] = $filter;
		}

		public function addPropertyFilterNotEqual($field_id, $value, $case_insencetive = true) {
			$data_type = $this->getDataByFieldId($field_id);

			$filter = Array("type" => $data_type, "field_id" => $field_id, "filter_type" => "not_equal", "value" => $value, "case_insencetive" => $case_insencetive);
			$this->props[] = $filter;
		}


		public function addPropertyFilterLike($field_id, $value, $case_insencetive = true) {
			$data_type = $this->getDataByFieldId($field_id);

			$filter = Array("type" => $data_type, "field_id" => $field_id, "filter_type" => "like", "value" => $value, "case_insencetive" => $case_insencetive);
			$this->props[] = $filter;
		}

		public function addPropertyFilterMore($field_id, $value) {
			$data_type = $this->getDataByFieldId($field_id);

			$filter = Array("type" => $data_type, "field_id" => $field_id, "filter_type" => "more", "value" => $value);
			$this->props[] = $filter;
		}

		public function addPropertyFilterLess($field_id, $value) {
			$data_type = $this->getDataByFieldId($field_id);

			$filter = Array("type" => $data_type, "field_id" => $field_id, "filter_type" => "less", "value" => $value);
			$this->props[] = $filter;
		}

		public function addPropertyFilterIsNull($field_id) {
			$data_type = $this->getDataByFieldId($field_id);

			$filter = Array("type" => $data_type, "field_id" => $field_id, "filter_type" => "null");
			$this->props[] = $filter;
		}


		public function addPermissions($user_id = false) {
			if($user_id === false) $user_id = $this->getCurrentUserId();
			$owners = $this->getOwnersByUser($user_id);
			$this->perms = $owners;
		}

		public function addNameFilterEquals($value) {
			$value = Array("value" => $value, "type" => "exact");

			if(!in_array($value, $this->names)) {
				$this->names[] = $value;
			}
		}
		
		public function addNameFilterLike($value) {
			$value = Array("value" => $value, "type" => "like");

			if(!in_array($value, $this->names)) {
				$this->names[] = $value;
			}
		}
		
		





		public function getOrderConds() {
			return ($this->is_order) ? $this->order : false;
		}

		public function getLimitConds() {
			return ($this->is_limit) ? $this->limit : false;
		}

		public function getActiveConds() {
			return ($this->is_active) ? $this->active : false;
		}

		public function getPropertyConds() {
			return ($this->is_props) ? $this->props : false;
		}

		public function getObjectTypeConds() {
			return ($this->is_object_type) ? $this->object_type : false;
		}

		public function getElementTypeConds() {
			return ($this->is_element_type) ? $this->element_type : false;
		}

		public function getHierarchyConds() {
			$this->hierarchy = array_unique($this->hierarchy);
			return ($this->is_hierarchy) ? $this->hierarchy : false;
		}

		public function getPermissionsConds() {
			return ($this->is_permissions) ? $this->perms : false;
		}

		public function getForceCond() {
			return $this->is_forced;
		}

		public function getNameConds() {
			return ($this->is_names) ? $this->names : false;
		}


		private function getDataByFieldId($field_id) {
			if($field = umiFieldsCollection::getInstance()->getField($field_id)) {
				$field_type_id = $field->getFieldTypeId();

				if($field_type = umiFieldTypesCollection::getInstance()->getFieldType($field_type_id)) {
					if($data_type = $field_type->getDataType()) {
						return umiFieldType::getDataTypeDB($data_type);
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		private function getCurrentUserId() {
			if($users = cmsController::getInstance()->getModule("users")) {
				return $users->user_id;
			} else {
				return false;
			}
		}

		private function getOwnersByUser($user_id) {
			if($user = umiObjectsCollection::getInstance()->getObject($user_id)) {
				$groups = $user->getPropByName("groups")->getValue();
				$groups[] = $user_id;
				return $groups;
			} else {
				return false;
			}
		}
		
		
		public function setConditionModeOr() {
			$this->condition_mode_or = true;
		}
		
		
		public function getConditionModeOr() {
			return $this->condition_mode_or;
		}
	};
?>
