<?php

class catalog extends def_module implements iCatalog {
	private $curr_path = "";
	public  $objects_cache = Array();

	private $skip_mode = true;

	public $per_page;

	public $fav_items = Array();

	public function __construct() {
		parent::__construct();

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$this->__loadLib("__admin.php");
			$this->__implement("__catalog");

			$this->__loadLib("__tree.php");
			$this->__implement("__catalog_tree");

			$this->__loadLib("__tree_sections.php");
			$this->__implement("__catalog_tree_sections");

			$this->__loadLib("__tree_objects.php");
			$this->__implement("__catalog_tree_objects");


			$this->__loadLib("__matrix.php");
			$this->__implement("__catalog_matrix");

//			$this->sheets_add("Матрицы подбора", "matrix");
		} else {
			$this->per_page = regedit::getInstance()->getVal("//modules/catalog/per_page");

			$this->__loadLib("__search.php");
			$this->__implement("__search_catalog");

//			$this->__loadLib("__compare.php");
//			$this->__implement("__compare_catalog");

			$this->__loadLib("__custom.php");
			$this->__implement("__custom_catalog");

			$this->autoDetectAttributes();
		}
	}

	public function category($template = "default", $element_path = false) {
		if(!$template) $template = "default";
		list($template_block) = def_module::loadTemplates("tpls/catalog/{$template}.tpl", "category");

		$category_id = $this->analyzeRequiredPath($element_path);

		$block_arr = Array();
		$block_arr['category_id'] = $category_id;
		$block_arr['category_path'] = $element_path;
		$block_arr['link'] = umiHierarchy::getInstance()->getPathById($category_id);

		templater::pushEditable("catalog", "category", $category_id);
		return self::parseTemplate($template_block, $block_arr, $category_id);
	}

	public function getCategoryList($template = "default", $category_id, $limit=false) {
		if(!$template) $template = "default";
		list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/catalog/{$template}.tpl", "category_block", "category_block_empty", "category_block_line");

		$category_id = $this->analyzeRequiredPath($category_id);

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("catalog", "category")->getId();

		$per_page = ($limit) ? $limit : $this->per_page;
		$curr_page = (int) $_REQUEST['p'];

		
		$sel = new umiSelection;
		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);
		$sel->setHierarchyFilter();
		$sel->addHierarchyFilter($category_id);
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$sel->setPermissionsFilter();
		$sel->addPermissions();
		$result = umiSelectionsParser::runSelection($sel);


		if(($sz = sizeof($result)) > 0) {
			$block_arr = Array();

			$lines = "";
			for($i = 0; $i < $sz; $i++) {
				if ($i < $limit || $limit === false) {
					$element_id = $result[$i];
					$element = umiHierarchy::getInstance()->getElement($element_id);

					if(!$element) continue;

					$line_arr = Array();
					$line_arr['id'] = $element_id;
					$line_arr['text'] = $element->getName();
					$line_arr['alt_name'] = $element->getAltName();
					$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);


					$lines .= self::parseTemplate($template_line, $line_arr, $element_id);
					
				}
			}

			$block_arr['lines'] = $lines;
			return self::parseTemplate($template_block, $block_arr, $category_id);
		} else {
			return $template_block_empty;
		}
	}

	public function getObjectsList($template = "default", $category_id, $limit = false) {
		if(!$template) $template = "default";

		list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/catalog/{$template}.tpl", "objects_block", "objects_block_empty", "objects_block_line");

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("catalog", "object")->getId();

		$category_id = $this->analyzeRequiredPath($category_id);

		$category_element = umiHierarchy::getInstance()->getElement($category_id);

		$per_page = ($limit) ? $limit : $this->per_page;
		$curr_page = (int) $_REQUEST['p'];

		$sel = new umiSelection;
		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);
		$sel->setHierarchyFilter();
		$sel->addHierarchyFilter($category_id);
		$sel->setPermissionsFilter();
		$sel->addPermissions();

		$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getType($hierarchy_type_id);
		$type_id = umiObjectTypesCollection::getInstance()->getBaseType($hierarchy_type->getName(), $hierarchy_type->getExt());


		$type_id = umiHierarchy::getInstance()->getDominantTypeId($category_id);

		$this->autoDetectOrders($sel, $type_id);
		$this->autoDetectFilters($sel, $type_id);

		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$result = umiSelectionsParser::runSelection($sel);
		$total = umiSelectionsParser::runSelectionCounts($sel);

		if(($sz = sizeof($result)) > 0) {
			$block_arr = Array();

			$lines = "";
			for($i = 0; $i < $sz; $i++) {
				$element_id = $result[$i];
				$element = umiHierarchy::getInstance()->getElement($element_id);

				if(!$element) continue;

				$line_arr = Array();
				$line_arr['id'] = $element_id;
				$line_arr['text'] = $element->getName();
				$line_arr['alt_name'] = $element->getAltName();
				$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);

				$lines .= self::parseTemplate($template_line, $line_arr, $element_id);
				
				umiHierarchy::getInstance()->unloadElement($element_id);

				templater::pushEditable("catalog", "object", $element_id);
			}

			$block_arr['lines'] = $lines;
			$block_arr['numpages'] = umiPagenum::generateNumPage($total, $per_page);
			$block_arr['total'] = $total;
			$block_arr['per_page'] = $per_page;
			$block_arr['category_id'] = $category_id;

			return self::parseTemplate($template_block, $block_arr, $category_id);
		} else {
			$block_arr['numpages'] = umiPagenum::generateNumPage(0, 0);
			$block_arr['lines'] = "";
			$block_arr['total'] = 0;
			$block_arr['per_page'] = 0;
			$block_arr['category_id'] = $category_id;

			return self::parseTemplate($template_block_empty, $block_arr, $category_id);;
		}
	}


	public function object($template = "default", $element_path = false) {
		if(!$template) $template = "default";

		$element_id = $this->analyzeRequiredPath($element_path);

		templater::pushEditable("catalog", "object", $element_id);
		return $this->viewObject($element_id, $template);
	}

	public function viewObject($element_id, $template = "default") {
		if(!$template) $template = "default";

		$element_id = $this->analyzeRequiredPath($element_id);

		$element = umiHierarchy::getInstance()->getElement($element_id);

		if(!$element) {
//			trigger_error("Can't find element #{$element_id} in hierarchy", E_USER_NOTICE);
			return "";
		}

		$block_arr = Array();
		list($template_block) = self::loadTemplates("tpls/catalog/{$template}.tpl", "view_block");

		$block_arr['id'] = $element_id;
		$block_arr['name'] = $element->getName();
		$block_arr['alt_name'] = $element->getAltName();
		$block_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);

		templater::pushEditable("catalog", "object", $element_id);
		return self::parseTemplate($template_block, $block_arr, $element_id);
	}


	public function search($category_id, $group_names, $template = "default") {
		if(!$template) $template = "default";


		$category_id = $this->analyzeRequiredPath($category_id);

		if(!$category_id) return "";

		list($template_block, $template_block_line, $template_block_line_text, $template_block_line_relation, $template_block_line_price, $template_block_line_boolean) = self::loadTemplates("tpls/catalog/{$template}.tpl", "search_block", "search_block_line", "search_block_line_text", "search_block_line_relation", "search_block_line_price", "search_block_line_boolean");
		$block_arr = Array();

		$type_id = umiHierarchy::getInstance()->getDominantTypeId($category_id);

		if(is_null($type_id)) return "";

		if(!($type = umiObjectTypesCollection::getInstance()->getType($type_id))) {
			trigger_error("Failed to load type", E_USER_WARNING);
			return "";
		}

		$group_names_arr = split(" ", $group_names);
		$fields = Array();

		foreach($group_names_arr as $group_name) {
			if(!($fields_group = $type->getFieldsGroupByName($group_name))) {
			} else {
				$fields = array_merge($fields, $fields_group->getFields());
			}
		}


		//TODO: Parse filters (inputs + default values)
		$lines = "";
		foreach($fields as $field_id => $field) {
			if(!$field->getIsVisible()) continue;
			if(!$field->getIsInFilter()) continue;

			$line_arr = Array();

			$field_type_id = $field->getFieldTypeId();
			$field_type = umiFieldTypesCollection::getInstance()->getFieldType($field_type_id);

			$data_type = $field_type->getDataType();

			$line = "";
			switch($data_type) {
				case "relation": {
					$line = $this->parseSearchRelation($field, $template_block_line_relation);
					break;
				}

				case "text": {
					$line = $this->parseSearchText($field, $template_block_line_text);
					break;
				}

				case "string": {
					$line = $this->parseSearchText($field, $template_block_line_text);
					break;
				}

				case "wysiwyg": {
					$line = $this->parseSearchText($field, $template_block_line_text);
					break;
				}

				case "price": {
					$line = $this->parseSearchPrice($field, $template_block_line_price);
					break;
				}

				case "boolean": {
					$line = $this->parseSearchBoolean($field, $template_block_line_boolean);
					break;
				}



				default: {
					$line = "[search filter for \"{$data_type}\" not specified]";
					break;
				}
			}

			$line_arr['selector'] = $line;

			$lines .= self::parseTemplate($template_block_line, $line_arr);
		}
		$block_arr['lines'] = $lines;

		return self::parseTemplate($template_block, $block_arr);
	}
	
	public function config() {
			return __catalog::config();
	}


	public function getEditLink($element_id, $element_type) {
		$element = umiHierarchy::getInstance()->getElement($element_id);
		$parent_id = $element->getParentId();

		switch($element_type) {
			case "category": {
				$link_add = $this->pre_lang . "/admin/catalog/tree_object_add/{$element_id}/";
				$link_edit = $this->pre_lang . "/admin/catalog/tree_section_edit/{$parent_id}/{$element_id}/";

				return Array($link_add, $link_edit);
				break;
			}

			case "object": {
				$link_edit = $this->pre_lang . "/admin/catalog/tree_object_edit/{$parent_id}/{$element_id}/";

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