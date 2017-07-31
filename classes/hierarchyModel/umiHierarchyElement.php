<?php
	class umiHierarchyElement extends umiEntinty implements iUmiEntinty, iUmiHierarchyElement {
		private	$rel, $alt_name, $ord, $object_id,
			$type_id, $domain_id, $lang_id, $tpl_id,
			$is_deleted = false, $is_active = true, $is_visible = true, $is_default = false, $name,
			$update_time,
			$object,
			$is_broken = false;
		protected $store_type = "element";

		public function getIsDeleted() {
			return $this->is_deleted;
		}

		public function getIsActive() {
			return $this->is_active;
		}

		public function getIsVisible() {
			return $this->is_visible;
		}

		public function getLangId() {
			return $this->lang_id;
		}

		public function getDomainId() {
			return $this->domain_id;
		}

		public function getTplId() {
			return $this->tpl_id;
		}

		public function getTypeId() {
			return $this->type_id;
		}

		public function getUpdateTime() {
			return $this->update_time;
		}

		public function getOrd() {
			return $this->ord;
		}

		public function getRel() {
			return $this->rel;
		}

		public function getAltName() {
			return $this->alt_name;
		}

		public function getIsDefault() {
			return $this->is_default;
		}


		public function getObject() {
			if($this->object) {
				return $this->object;
			} else {
				$this->object = umiObjectsCollection::getInstance()->getObject($this->object_id);
				return $this->object;
			}
		}

		public function getParentId() {
			return $this->rel;
		}

		public function getName() {
			return $this->name;	//read-only
		}


		public function getValue($prop_name) {
			return $this->getObject()->getValue($prop_name);
		}

		public function setValue($prop_name, $prop_value) {
			return $this->getObject()->setValue($prop_name, $prop_value);
		}



		public function setIsVisible($is_visible = true) {
			$this->is_visible = (bool) $is_visible;
			$this->setIsUpdated();
		}

		public function setIsActive($is_active = true) {
			$this->is_active = (bool) $is_active;
			$this->setIsUpdated();
		}

		public function setIsDeleted($is_deleted = false) {
			$this->is_deleted = (bool) $is_deleted;
			$this->setIsUpdated();
		}

		public function setTypeId($type_id) {
			$this->type_id = (int) $type_id;
			$this->setIsUpdated();
		}

		public function setLangId($lang_id) {
			$this->lang_id = (int) $lang_id;
			$this->setIsUpdated();
		}

		public function setTplId($tpl_id) {
			$this->tpl_id = (int) $tpl_id;
			$this->setIsUpdated();
		}

		public function setDomainId($domain_id) {
			$childs = umiHierarchy::getInstance()->getChilds($this->id, true, true);

			foreach($childs as $child_id => $nl) {
				$child = umiHierarchy::getInstance()->getElement($child_id);
				$child->setDomainId($domain_id);
				unset($child);
			}


			$this->domain_id = (int) $domain_id;
			$this->setIsUpdated();
		}

		public function setUpdateTime($update_time = 0) {
			if($update_time == 0) {
				$update_time = umiHierarchy::getTimeStamp();
			}
			$this->update_time = (int) $update_time;
			$this->setIsUpdated();
		}

		public function setOrd($ord) {
			$this->ord = (int) $ord;
			$this->setIsUpdated();
		}

		public function setRel($rel) {
			$this->rel = (int) $rel;
			$this->setIsUpdated();
		}

		public function setObject(umiObject $object) {
			$this->object = $object;
			$this->object_id = $object->getId();
			$this->setIsUpdated();
		}

		public function setAltName($alt_name, $auto_convert = true) {
			if($auto_convert) {
				$alt_name = umiHierarchy::convertAltName($alt_name);
			}

			$this->alt_name = $this->getRightAltName(umiObjectProperty::filterInputString($alt_name));
			if(!$this->alt_name) {
				$this->alt_name = $alt_name;
			}
			$this->setIsUpdated();
		}

		private function getRightAltName($alt_name) {
			if(empty($alt_name)) $alt_name = '1';

			// массив для совпадающих значений переменной alt_name
			$exists_alt_names =  array();

			preg_match  ("/^([a-z0-9_.-]*\D)(\d*)$/", $alt_name, $regs);
			$alt_digit = $regs[2];
			$alt_string = $regs[1];
			
			$lang_id = $this->getLangId();
			$domain_id = $this->getDomainId();

			$sql = "SELECT alt_name FROM cms3_hierarchy WHERE rel={$this->getRel()} AND id <> {$this->getId()} AND is_deleted = '0' AND lang_id = '{$lang_id}' AND domain_id = '{$domain_id}' AND alt_name LIKE '{$alt_string}%';";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			// cуществующие совпадения с alt_name помещаем в массив
			while(list($item) = mysql_fetch_row($result)) $exists_alt_names[] = $item;

			//  Вычисляем значение $increment;
			if(!empty($exists_alt_names) and in_array($alt_name,$exists_alt_names)){

			foreach($exists_alt_names as $next_alt_name){
					preg_match  ("/(\D*)(\d*)/", $next_alt_name, $regs);
					if (!empty($regs[2])) $alt_digit = max($alt_digit,$regs[2]);
			}
			++$alt_digit;
		}

			return $alt_string. $alt_digit;
		}


		public function setIsDefault($is_default = true) {
			$this->is_default = (int) $is_default;
			$this->setIsUpdated();
		}


		public function getFieldId($field_name) {
			return umiObjectTypesCollection::getInstance()->getType($this->getObject()->getTypeId())->getFieldId($field_name);
		}

		protected function loadInfo() {
			$sql = "SELECT SQL_CACHE h.rel, h.type_id, h.lang_id, h.domain_id, h.tpl_id, h.obj_id, h.ord, h.alt_name, h.is_active, h.is_visible, h.is_deleted, h.updatetime, h.is_default, o.name FROM cms3_hierarchy h, cms3_objects o WHERE h.id = '{$this->id}' AND o.id = h.obj_id";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($rel, $type_id, $lang_id, $domain_id, $tpl_id, $obj_id, $ord, $alt_name, $is_active, $is_visible, $is_deleted, $updatetime, $is_default, $name) = mysql_fetch_row($result)) {
				if(!$obj_id) {	//Really bad, foregin check didn't worked out :(, let's delete it itself
					umiHierarchy::getInstance()->delElement($this->id);
					$this->is_broken = true;
					return false;
				}
			
				$this->rel = (int) $rel;
				$this->type_id = (int) $type_id;
				$this->lang_id = (int) $lang_id;
				$this->domain_id = (int) $domain_id;
				$this->tpl_id = (int) $tpl_id;
				$this->object_id = (int) $obj_id;
				$this->ord = (int) $ord;
				$this->alt_name = $alt_name;
				$this->is_active = (bool) $is_active;
				$this->is_visible = (bool) $is_visible;
				$this->is_deleted = (bool) $is_deleted;
				$this->is_default = (bool) $is_default;

				$this->name = $name;	//read-only

				if(!$updatetime) $updatetime = umiHierarchy::getTimeStamp();
				$this->update_time = (int) $updatetime;

				return true;
			} else {
				$this->is_broken = true;
				return false;
			}
		}

		protected function save() {
			$rel = (int) $this->rel;
			$type_id = (int) $this->type_id;
			$lang_id = (int) $this->lang_id;
			$domain_id = (int) $this->domain_id;
			$tpl_id = (int) $this->tpl_id;
			$object_id = (int) $this->object_id;
			$ord = (int) $this->ord;
			$alt_name = mysql_real_escape_string($this->alt_name);
			$is_active = (int) $this->is_active;
			$is_visible = (int) $this->is_visible;
			$is_deleted = (int) $this->is_deleted;
			$update_time = (int) $this->update_time;
			$is_default = (int) $this->is_default;


			if($is_default) {
				$sql ="UPDATE cms3_hierarchy SET is_default = '0' WHERE is_default = '1' AND lang_id = '{$lang_id}' AND domain_id = '{$domain_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}
			}

			$sql = "UPDATE cms3_hierarchy SET rel = '{$rel}', type_id = '{$type_id}', lang_id = '{$lang_id}', domain_id = '{$domain_id}', tpl_id = '{$tpl_id}', obj_id = '{$object_id}', ord = '{$ord}', alt_name = '{$alt_name}', is_active = '{$is_active}', is_visible = '{$is_visible}', is_deleted = '{$is_deleted}', updatetime = '{$update_time}', is_default = '{$is_default}' WHERE id = '{$this->id}'";
			mysql_query($sql);



			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {

				if(cmsController::getInstance()->getModule('search')) {
					__index_search::index_item($this->id);
				}

				return true;
			}
		}

		protected function setIsUpdated($is_updated = true) {
			parent::setIsUpdated($is_updated);
			$this->update_time = time();
			umiHierarchy::getInstance()->addUpdatedElementId($this->id);
			if($this->rel) {
				umiHierarchy::getInstance()->addUpdatedElementId($this->rel);
			}
		}
		
		
		public function getIsBroken() {
			return $this->is_broken;
		}
	};
?>