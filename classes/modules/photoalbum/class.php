<?php
	class photoalbum extends def_module {
		public $per_page = 10;

		public function __construct() {
        	        parent::__construct();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$this->__loadLib("__admin.php");
				$this->__implement("__photoalbum");

				$this->__loadLib("__albums.php");
				$this->__implement("__albums_photoalbum");

				$this->__loadLib("__albums_add.php");
				$this->__implement("__albums_add_photoalbum");

				$this->__loadLib("__albums_edit.php");
				$this->__implement("__albums_edit_photoalbum");


				$this->__loadLib("__photos.php");
				$this->__implement("__photos_photoalbum");

				$this->__loadLib("__photos_add.php");
				$this->__implement("__photos_add_photoalbum");

				$this->__loadLib("__photos_edit.php");
				$this->__implement("__photos_edit_photoalbum");
			} else {
				$this->__loadLib("__custom.php");
				$this->__implement("__custom_photoalbum");
			}

			if($per_page = (int) regedit::getInstance()->getVal("//modules/photoalbum/per_page")) {
				$this->per_page = $per_page;
			}
		}


		public function albums($template = "default") {
			if(!$template) $template = "default";

			list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/photoalbum/{$template}.tpl", "albums_list_block", "albums_list_block_empty", "albums_list_block_line");
			$block_arr = Array();


			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("photoalbum", "album")->getId();

			$sel = new umiSelection;
			$sel->setElementTypeFilter();
			$sel->addElementType($hierarchy_type_id);

			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);

			$lines = "";
			if($total > 0) {
				foreach($result as $element_id) {
					$line_arr = Array();

					$element = umiHierarchy::getInstance()->getElement($element_id);

					$line_arr['name'] = $element->getName();
					$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);
					templater::pushEditable("photoalbum", "album", $element_id);
					$lines .= self::parseTemplate($template_line, $line_arr, $element_id);
				}
			} else {
				return self::parseTemplate($template_block_empty, $block_arr);
			}


			$block_arr['lines'] = $lines;
			$block_arr['total'] = $total;
			return self::parseTemplate($template_block, $block_arr);
		}


		public function album($element_id = false, $template = "default", $limit = false) {
			if(!$template) $template = "default";
			$curr_page = (int) $_REQUEST['p'];
			$per_page = ($limit) ? $limit : $this->per_page;

			$element_id = $this->analyzeRequiredPath($element_id);

			list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/photoalbum/{$template}.tpl", "album_block", "album_block_empty", "album_block_line");
			$block_arr = Array();



			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("photoalbum", "photo")->getId();

			$sel = new umiSelection;
			$sel->setElementTypeFilter();
			$sel->addElementType($hierarchy_type_id);

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($element_id);

			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);

			$block_arr['album_id'] = $element_id;

			$lines = "";
			if($total > 0) {
				foreach($result as $photo_element_id) {
					$line_arr = Array();

					$photo_element = umiHierarchy::getInstance()->getElement($photo_element_id);

					$line_arr['name'] = $photo_element->getName();
					$line_arr['link'] = umiHierarchy::getInstance()->getPathById($photo_element_id);
					templater::pushEditable("photoalbum", "photo", $photo_element_id);
					$lines .= self::parseTemplate($template_line, $line_arr, $photo_element_id);
				}
			} else {
				return self::parseTemplate($template_block_empty, $block_arr, $element_id);
			}

			$block_arr['lines'] = $lines;
			$block_arr['total'] = $total;
			$block_arr['per_page'] = $per_page;
			$block_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);

			return self::parseTemplate($template_block, $block_arr, $element_id);
		}


		public function photo($element_id = false, $template = "default") {
			if(!$template) $template = "default";

			$element_id = $this->analyzeRequiredPath($element_id);


			list($template_block, $template_line) = def_module::loadTemplates("tpls/photoalbum/{$template}.tpl", "photo_block");
			$block_arr = Array();

			$element = umiHierarchy::getInstance()->getElement($element_id);

			$block_arr['id'] = $element_id;
			$block_arr['name'] = $element->getName();

			templater::pushEditable("photoalbum", "photo", $element_id);

			return self::parseTemplate($template_block, $block_arr, $element_id);
		}


		public function config () {
			if(class_exists("__photoalbum")) {
				return __photoalbum::config();
			}
		}


		public function getEditLink($element_id, $element_type) {
			$element = umiHierarchy::getInstance()->getElement($element_id);
			$parent_id = $element->getParentId();

			switch($element_type) {
				case "album": {
					$link_add = $this->pre_lang . "/admin/photoalbum/photo_add/{$element_id}/";
					$link_edit = $this->pre_lang . "/admin/photoalbum/album_edit/{$element_id}/";

					return Array($link_add, $link_edit);
					break;
				}


				case "photo": {
					$link_add = false;
					$link_edit = $this->pre_lang . "/admin/photoalbum/photo_edit/{$element_id}/";

					return Array($link_add, $link_edit);
					break;
				}

				default: {
					return false;
				}
			}
		}

	};
?>