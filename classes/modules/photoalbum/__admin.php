<?php
	abstract class __photoalbum {
		public function config() {
			$params = Array();
			$this->sheets_reset();
			$this->load_forms();

			$regedit = regedit::getInstance();

			$arr1 = Array();
			$arr2 = Array();

			$params['per_page'] = $regedit->getVal("//modules/photoalbum/per_page");
			$params['groups_list'] = putSelectBox($arr2, $arr1, $params['def_group']);
			return $this->parse_form("config", $params);
		}


		public function config_do() {
			$regedit = regedit::getInstance();

			$per_page = (int) $_REQUEST['per_page'];
			$regedit->setVar("//modules/photoalbum/per_page", $per_page);

			$this->redirect($this->pre_lang . "/admin/photoalbum/config/");
		}


		public function album_blocking() {
			$element_id = (int) $_REQUEST['param0'];
			$is_active = (bool) $_REQUEST['param1'];

			$element = umiHierarchy::getInstance()->getElement($element_id);
			$element->setIsActive($is_active);
			$element->commit();

			$this->redirect($this->pre_lang . "/admin/photoalbum/albums_list/");
		}


		public function photo_blocking() {
			$element_id = (int) $_REQUEST['param0'];
			$is_active = (bool) $_REQUEST['param1'];

			$element = umiHierarchy::getInstance()->getElement($element_id);
			$element->setIsActive($is_active);
			$element->commit();

			$parent_id = $element->getParentId();

			$this->redirect($this->pre_lang . "/admin/photoalbum/photos_list/" . $parent_id . "/");
		}


		public function album_del() {
			$element_id = (int) $_REQUEST['param0'];

			umiHierarchy::getInstance()->delElement($element_id);

			$this->redirect($this->pre_lang . "/admin/photoalbum/albums_list/");
		}


		public function photo_del() {
			$element_id = (int) $_REQUEST['param0'];

			umiHierarchy::getInstance()->delElement($element_id);

			$parent_id = umiHierarchy::getInstance()->getParent($element_id);

			$this->redirect($this->pre_lang . "/admin/photoalbum/photos_list/" . $parent_id . "/");
		}
	};
?>