<?php

class news extends def_module implements iNews {
	public $per_page;

	public function __construct() {
		parent::__construct($CMS_ENV);

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$this->__loadLib("__admin.php");
			$this->__implement("__news");
			
			$this->sheets_add("Новости и ленты новостей", "lists");
			$this->sheets_add("Последние новости", "last_lists");
			$this->sheets_add("Сюжеты", "subjects");

			$this->__loadLib("__rubrics_add.php");
			$this->__implement("__rubrics_add_news");

			$this->__loadLib("__rubrics_edit.php");
			$this->__implement("__rubrics_edit_news");

			$this->__loadLib("__items_add.php");
			$this->__implement("__items_add_news");

			$this->__loadLib("__items_edit.php");
			$this->__implement("__items_edit_news");

			$this->__loadLib("__subjects.php");
			$this->__implement("__subjects_news");
		} else {
			$this->__loadLib("__custom.php");
			$this->__implement("__custom_news");

			$this->per_page = regedit::getInstance()->getVal("//modules/news/per_page");
		}
	}

	public function lastlist($path = "", $template = "default", $per_page = false) {
		if(!$template) $template = "default";
		list($template_block, $template_block_empty, $template_line, $template_archive) = def_module::loadTemplates("tpls/news/{$template}.tpl", "lastlist_block", "lastlist_block_empty", "lastlist_item", "lastlist_archive");
		if(!$per_page) $per_page = $this->per_page;
		$curr_page = (int) $_REQUEST['p'];


		$parent_id = $this->analyzeRequiredPath($path);


		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("news", "item")->getId();

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("news", "item");
		$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
		$publish_time_field_id = $object_type->getFieldId('publish_time');


		$sel = new umiSelection;
		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);

		$sel->setHierarchyFilter();
		$sel->addHierarchyFilter($parent_id);

		$sel->setPermissionsFilter();
		$sel->addPermissions();

		$sel->setOrderFilter();
		$sel->setOrderByProperty($publish_time_field_id, false);

		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$result = umiSelectionsParser::runSelection($sel);
		$total = umiSelectionsParser::runSelectionCounts($sel);

		if(($sz = sizeof($result)) > 0) {
			$block_arr = Array();

			$lines = "";
			for($i = 0; $i < $sz; $i++) {
				$line_arr = Array();
				$element_id = $result[$i];
				$element = umiHierarchy::getInstance()->getElement($element_id);

				$line_arr['id'] = $element_id;
				$line_arr['name'] = $element->getName();
				$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);
				$line_arr['header'] = $lines_arr['name'] = $element->getName();

				$lent_id = $element->getParentId();
				if($lent_element = umiHierarchy::getInstance()->getElement($lent_id)) {
					$lent_name = $lent_element->getName();
					$lent_link = umiHierarchy::getInstance()->getPathById($lent_id);
				} else {
					$lent_name = "";
					$lent_link = "";
				}

				$line_arr['lent_id'] = $lent_id;
				$line_arr['lent_name'] = $lent_name;
				$line_arr['lent_link'] = $lent_link;

				$lines .= self::parseTemplate($template_line, $line_arr, $element_id);

				templater::pushEditable("news", "item", $element_id);
				
				umiHierarchy::getInstance()->unloadElement($element_id);
			}
			
			if(is_array($parent_id)) {
				list($parent_id) = $parent_id;
			}

			$block_arr['items'] = $block_arr['lines'] = $lines;
			$block_arr['archive'] = ($total > ($i)) ? $template_archive : "";
			$block_arr['archive_link'] = umiHierarchy::getInstance()->getPathById($parent_id);

			$block_arr['total'] = $total;
			$block_arr['per_page'] = $per_page;


			return self::parseTemplate($template_block, $block_arr, $parent_id);
		} else {
			return $template_block_empty;
		}
	}

	public function rubric($path = "", $template = "default") {
		if(!$template) $template = "default";


		$element_id = cmsController::getInstance()->getCurrentElementId();

		templater::pushEditable("news", "rubric", $element_id);
		return $this->lastlents($element_id, $template) . $this->lastlist($element_id, $template);
	}

	public function view($element_id, $template = "default") {
		if(!$template) $template = "default";
		list($template_block) = def_module::loadTemplates("tpls/news/{$template}.tpl", "view");

		$element_id = $this->analyzeRequiredPath($element_id);

		$block_arr = Array();
		$element = umiHierarchy::getInstance()->getElement($element_id);

		$block_arr['id'] = $element_id;

		templater::pushEditable("news", "item", $element_id);

		return self::parseTemplate($template_block, $block_arr);
	}

	public function related_links($element_id, $template = "default", $limit = 3) {
		if(!$template) $template = "default";
		list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/news/{$template}.tpl", "related_block", "related_block_empty", "related_line");

		$element_id = $this->analyzeRequiredPath($element_id);

		$element = umiHierarchy::getInstance()->getElement($element_id);

		if(!$element) return $template_block_empty;
		$subjects = $element->getValue("subjects");

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("news", "item")->getId();

		$sel = new umiSelection;
		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);


		$subjects_field_id = $element->getFieldId('subjects');
		$sel->setPropertyFilter();
		$sel->addPropertyFilterEqual($subjects_field_id, $subjects);

		$sel->setPermissionsFilter();
		$sel->addPermissions();

		$sel->setLimitFilter();
		$sel->addLimit($limit);

		$result = umiSelectionsParser::runSelection($sel);
//		$total = umiSelectionsParser::runSelectionCounts($sel);

		if(($sz = sizeof($result)) > 0) {
			$block_arr = Array();
			$lines = "";

			$sz--;
			for($i = 0; $i < $sz; $i++) {
				$line_arr = Array();
				$rel_element_id = $result[$i];

				if($rel_element_id == $element_id) {
					$sz++;
					continue;
				}

				$rel_element = umiHierarchy::getInstance()->getElement($rel_element_id);

				$line_arr['id'] = $rel_element_id;
				$line_arr['name'] = $rel_element->getName();
				$line_arr['link'] = umiHierarchy::getInstance()->getPathById($rel_element_id);

				$lines .= self::parseTemplate($template_line, $line_arr, $rel_element_id);
			}
			
			if(!$lines) {
				return "";
			}

			$block_arr['lines'] = $block_arr['related_links'] = $lines;
			return self::parseTemplate($template_block, $block_arr);
		} else {
			return $template_block_empty;
		}
	}


	public function config() {
			return __news::config();
	}


	private function checkPath($path) {
		if(is_numeric($path)) {
			return (umiHierarchy::getInstance()->isExists((int) $path)) ? (int) $path : false;
		} else {
			if(trim($path)) {
				$rel_id = umiHierarchy::getInstance()->getIdByPath($path);
				return ($rel_id !== false) ? $rel_id : false;
			} else {
				return false;
			}
		}
	}

	public function item() {
		$element_id = (int) cmsController::getInstance()->getCurrentElementId();
		$element = umiHierarchy::getInstance()->getElement($element_id);

		return $this->view($element_id);
	}
	
	
	public function listlents($element_id, $template = "default", $per_page = false) {
		return $this->lastlents($element_id, $template, $per_page);
	}


	public function lastlents($element_id, $template = "default", $per_page = false) {
		if(!$template) $template = "default";
		list($template_block, $template_block_empty, $template_line, $template_archive) = def_module::loadTemplates("tpls/news/{$template}.tpl", "listlents_block", "listlents_block_empty", "listlents_item");
		if(!$per_page) $per_page = $this->per_page;
		$curr_page = (int) $_REQUEST['p'];


		$parent_id = $this->analyzeRequiredPath($path);


		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("news", "rubric")->getId();

		$sel = new umiSelection;
		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);

		$sel->setHierarchyFilter();
		$sel->addHierarchyFilter($parent_id);

		$sel->setPermissionsFilter();
		$sel->addPermissions();

		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$result = umiSelectionsParser::runSelection($sel);
		$total = umiSelectionsParser::runSelectionCounts($sel);

		if(($sz = sizeof($result)) > 0) {
			$block_arr = Array();

			$lines = "";
			for($i = 0; $i < $sz; $i++) {
				$line_arr = Array();
				$element_id = $result[$i];
				$element = umiHierarchy::getInstance()->getElement($element_id);

				$line_arr['id'] = $element_id;
				$line_arr['name'] = $element->getName();
				$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);
				$line_arr['header'] = $lines_arr['name'] = $element->getName();

				$lines .= self::parseTemplate($template_line, $line_arr, $element_id);

				templater::pushEditable("news", "rubric", $element_id);
			}
			
			if(is_array($parent_id)) {
				list($parent_id) = $parent_id;
			}

			$block_arr['items'] = $block_arr['lines'] = $lines;

			$block_arr['total'] = $total;
			$block_arr['per_page'] = $per_page;


			return self::parseTemplate($template_block, $block_arr, $parent_id);
		} else {
			return $template_block_empty;
		}
	}


	public function getEditLink($element_id, $element_type) {
		$element = umiHierarchy::getInstance()->getElement($element_id);
		$parent_id = $element->getParentId();

		switch($element_type) {
			case "rubric": {
				$link_add = $this->pre_lang . "/admin/news/add_item/{$element_id}/";
				$link_edit = $this->pre_lang . "/admin/news/edit_list/{$parent_id}/{$element_id}/";

				return Array($link_add, $link_edit);
				break;
			}

			case "item": {
				$link_edit = $this->pre_lang . "/admin/news/edit_item/{$parent_id}/{$element_id}/";

				return Array(false, $link_edit);
				break;
			}

			default: {
				return false;
			}
		}
	}

};
?>