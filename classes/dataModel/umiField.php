<?php
	class umiField extends umiEntinty implements iUmiEntinty, iUmiField {
		private	$name, $title, $is_locked = false, $is_inheritable = false, $is_visible = true, $field_type_id, $guide_id;
		private $is_in_search = true, $is_in_filter = true, $tip = NULL;
		protected $store_type = "field";


		public function getName() {
			return $this->name;
		}

		public function getTitle() {
			return $this->title;
		}

		public function getIsLocked() {
			return $this->is_locked;
		}

		public function getIsInheritable() {
			return $this->is_inheritable;
		}

		public function getIsVisible() {
			return $this->is_visible;
		}

		public function getFieldTypeId() {
			return $this->field_type_id;
		}

		public function getFieldType() {
			return umiFieldTypesCollection::getInstance()->getFieldType($this->field_type_id);
		}

		public function getGuideId() {
			return $this->guide_id;
		}

		public function getIsInSearch() {
			return $this->is_in_search;
		}

		public function getIsInFilter() {
			return $this->is_in_filter;
		}

		public function getTip() {
			return $this->tip;
		}


		public function setName($name) {
			$this->name = umiObjectProperty::filterInputString($name);
			$this->setIsUpdated();
		}

		public function setTitle($title) {
			$this->title = umiObjectProperty::filterInputString($title);
			$this->setIsUpdated();
		}

		public function setIsLocked($is_locked) {
			$this->is_locked = (bool) $is_locked;
			$this->setIsUpdated();
		}

		public function setIsInheritable($is_inheritable) {
			$this->is_inheritable = (bool) $is_inheritable;
			$this->setIsUpdated();
		}

		public function setIsVisible($is_visible) {
			$this->is_visible = (bool) $is_visible;
			$this->setIsUpdated();
		}

		public function setFieldTypeId($field_type_id) {
			$this->field_type_id = (int) $field_type_id;
			$this->setIsUpdated();
			return true;
		}

		public function setGuideId($guide_id) {
			$this->guide_id = (int) $guide_id;
			$this->setIsUpdated();
		}

		public function setIsInSearch($is_in_search) {
			$this->is_in_search = (bool) $is_in_search;
			$this->setIsUpdated();
		}

		public function setIsInFilter($is_in_filter) {
			$this->is_in_filter = (bool) $is_in_filter;
			$this->setIsUpdated();
		}

		public function setTip($tip) {
			$this->tip = umiObjectProperty::filterInputString($tip);
			$this->setIsUpdated();
		}


		protected function loadInfo() {
			$sql = "SELECT SQL_CACHE name, title, is_locked, is_inheritable, is_visible, field_type_id, guide_id, in_search, in_filter, tip FROM cms3_object_fields WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($name, $title, $is_locked, $is_inheritable, $is_visible, $field_type_id, $guide_id, $in_search, $in_filter, $tip) = mysql_fetch_row($result)) {
				$this->name = $name;
				$this->title = $title;
				$this->is_locked = (bool) $is_locked;
				$this->is_inheritable = (bool) $is_inheritable;
				$this->is_visible = (bool) $is_visible;
				$this->field_type_id = (int) $field_type_id;
				$this->guide_id = (int) $guide_id;
				$this->is_in_search = (bool) $in_search;
				$this->is_in_filter = (bool) $in_filter;
				$this->tip = (string) $tip;
			} else {
				return false;
			}
		}

		protected function save() {
			$name = mysql_real_escape_string($this->name);
			$title = mysql_real_escape_string($this->title);
			$is_locked = (int) $this->is_locked;
			$is_inheritable = (int) $this->is_inheritable;
			$is_visible = (int) $this->is_visible;
			$field_type_id = (int) $this->field_type_id;
			$guide_id = (int) $this->guide_id;
			$in_search = (int) $this->is_in_search;
			$in_filter = (int) $this->is_in_filter;
			$tip = (string) $this->tip;

			$sql = "UPDATE cms3_object_fields SET name = '{$name}', title = '{$title}', is_locked = '{$is_locked}', is_inheritable = '{$is_inheritable}', is_visible = '{$is_visible}', field_type_id = '{$field_type_id}', guide_id = '{$guide_id}', in_search = '{$in_search}', in_filter = '{$in_filter}', tip = '{$tip}' WHERE id = '{$this->id}'";
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