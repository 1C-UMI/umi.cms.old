<?php

class data extends def_module implements iData {
	public $alowed_source = Array(
						Array("forum", "topic"),
						Array("forum", "conf"),
						Array("news", "rubric")
					);

	public function __construct() {
		parent::__construct();

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$this->__loadLib("__admin.php");
			$this->__implement("__data");

			$this->__loadLib("__json.php");
			$this->__implement("__json_data");

			$this->__loadLib("__reflection.php");
			$this->__implement("__reflection_data");

			$this->__loadLib("__trash.php");
			$this->__implement("__trash_data");

			$this->__loadLib("__guides.php");
			$this->__implement("__guides_data");


			$this->sheets_add("%data_sheets_types%", "types");
			$this->sheets_add("%data_sheets_guides%", "guides");
		} else {
			$this->__loadLib("__client_reflection.php");
			$this->__implement("__client_reflection_data");

			$this->__loadLib("__rss.php");
			$this->__implement("__rss_data");

			$this->__loadLib("__custom.php");
			$this->__implement("__custom_data");
		}
	}


	public function getProperty($element_id, $prop_id, $template = "default", $is_random = false) {
		if(!$template) $template = "default";

		if($element = umiHierarchy::getInstance()->getElement($element_id)) {
			if($prop = (is_numeric($prop_id)) ? $element->getObject()->getPropById($prop_id) : $element->getObject()->getPropByName($prop_id)) {
				return self::parseTemplate($this->renderProperty($prop, $template, $is_random), Array(), $element_id);
			} else {
				return "";
			}
		} else {
			return "";
		}
	}


	public function getPropertyGroup($element_id, $group_id, $template = "default") {
		if(!$template) $template = "default";

		if(strstr($group_id, " ") !== false) {
			$group_ids = split(" ", $group_id);
			$res = "";
			foreach($group_ids as $group_id) {
				if(!($group_id = trim($group_id))) continue;
				$res .= $this->getPropertyGroup($element_id, $group_id, $template);
			}
			return $res;
		}

		if($element = umiHierarchy::getInstance()->getElement($element_id)) {
			if(!is_numeric($group_id)) $group_id = $element->getObject()->getPropGroupId($group_id);

			$type_id = $element->getObject()->getTypeId();
			if($group = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldsGroup($group_id)) {
				if($group->getIsActive() == false) return "";
				list($template_block, $template_line) = self::loadTemplates("tpls/data/{$template}.tpl", "group", "group_line");

				$lines = "";
				$props = $element->getObject()->getPropGroupById($group_id);
				$sz = sizeof($props);
				for($i = 0; $i < $sz; $i++) {
					$prop_id = $props[$i];

					$line_arr = Array();
					$line_arr['id'] = $element_id;
					$line_arr['prop_id'] = $prop_id;

					if($prop_val = $this->getProperty($element_id, $prop_id, $template)) {
						$line_arr['prop'] = $prop_val;
					} else {
						continue;
					}

					$lines .= self::parseTemplate($template_line, $line_arr);
				}
				if(!$lines) return "";	//TODO: check

				$block_arr = Array();
				$block_arr['name'] = $group->getName();
				$block_arr['title'] = $group->getTitle();
				$block_arr['lines'] = $lines;
				$block_arr['template'] = $template;

				return self::parseTemplate($template_block, $block_arr);
			} else {
				return "";
			}
		} else {
			return "";
		}

	}


	public function getAllGroups($element_id, $template = "default") {
		if(!$template) $template = "default";

		if($element = umiHierarchy::getInstance()->getElement($element_id)) {
			list($template_block, $template_line) = self::loadTemplates("tpls/data/{$template}.tpl", "groups_block", "groups_line");

			$block_arr = Array();

			$object_type_id = $element->getObject()->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$groups = $object_type->getFieldsGroupsList();

			$lines = "";
			foreach($groups as $group_id => $group) {
				if(!$group->getIsActive() || !$group->getIsVisible()) continue;

				$line_arr = Array();
				$line_arr['group_id'] = $group_id;
				$line_arr['group_name'] = $group->getName();

				$lines .= self::parseTemplate($template_line, $line_arr);
			}


			$block_arr['lines'] = $lines;
			$block_arr['id'] = $element_id;
			$block_arr['template'] = $template;
			return self::parseTemplate($template_block, $block_arr);
		} else {
			return "";
		}
	}


	/*	Of-object block. TODO: refactoring with element-block.		*/

	public function getPropertyOfObject($object_id, $prop_id, $template = "default", $is_random = false) {
		if(!$template) $template = "default";

		if($object = umiObjectsCollection::getInstance()->getObject($object_id)) {
			if($prop = (is_numeric($prop_id)) ? $object->getPropById($prop_id) : $object->getPropByName($prop_id)) {
				return $this->renderProperty($prop, $template, $is_random);
			} else {
				return "";
			}
		} else {
			return "";
		}
	}


	public function getPropertyGroupOfObject($object_id, $group_id, $template = "default") {
		if(!$template) $template = "default";

		if(strstr($group_id, " ") !== false) {
			$group_ids = split(" ", $group_id);
			$res = "";
			foreach($group_ids as $group_id) {
				if(!($group_id = trim($group_id))) continue;
				$res .= $this->getPropertyGroupOfObject($object_id, $group_id, $template);
			}
			return $res;
		}


		if($object = umiObjectsCollection::getInstance()->getObject($object_id)) {
			if(!is_numeric($group_id)) $group_id = $object->getPropGroupId($group_id);

			$type_id = $object->getTypeId();
			if($group = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldsGroup($group_id)) {
				if($group->getIsActive() == false) return "";
				list($template_block, $template_line) = self::loadTemplates("tpls/data/{$template}.tpl", "group", "group_line");

				$lines = "";
				$props = $object->getPropGroupById($group_id);
				$sz = sizeof($props);
				for($i = 0; $i < $sz; $i++) {
					$prop_id = $props[$i];

					$line_arr = Array();
					$line_arr['id'] = $object_id;
					$line_arr['prop_id'] = $prop_id;

					if($prop_val = $this->getPropertyOfObject($object_id, $prop_id, $template)) {
						$line_arr['prop'] = $prop_val;
					} else {
						continue;
					}

					$lines .= self::parseTemplate($template_line, $line_arr);
				}

				

				$block_arr = Array();
				$block_arr['name'] = $group->getName();
				$block_arr['title'] = $group->getTitle();
				$block_arr['lines'] = $lines;
				$block_arr['template'] = $template;
				return self::parseTemplate($template_block, $block_arr);
			} else {
				return "";
			}
		} else {
			return "";
		}

	}


	public function getAllGroupsOfObject($object_id, $template = "default") {
		if(!$template) $template = "default";

		if($object = umiObjectsCollection::getInstance()->getObject($object_id)) {
			list($template_block, $template_line) = self::loadTemplates("tpls/data/{$template}.tpl", "groups_block", "groups_line");

			$block_arr = Array();

			$object_type_id = $object->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$groups = $object_type->getFieldsGroupsList();

			$lines = "";
			foreach($groups as $group_id => $group) {
				if(!$group->getIsActive() || !$group->getIsVisible()) continue;

				$line_arr = Array();
				$line_arr['group_id'] = $group_id;
				$line_arr['group_name'] = $group->getName();

				$lines .= self::parseTemplate($template_line, $line_arr);
			}


			$block_arr['lines'] = $lines;
			$block_arr['id'] = $object_id;
			$block_arr['template'] = $template;
			return self::parseTemplate($template_block, $block_arr);
		} else {
			return "";
		}
	}





	private function renderProperty(umiObjectProperty &$property, $template, $is_random = false) {
		$data_type = $property->getDataType();

		switch($data_type) {
			case "string": {
				return $this->renderString($property, $template);
			}

			case "text": {
				return $this->renderString($property, $template);
			}

			case "wysiwyg": {
				return $this->renderString($property, $template);
			}

			case "int": {
				return $this->renderInt($property, $template);
			}

			case "price": {
				return $this->renderPrice($property, $template);
			}

			case "boolean": {
				return $this->renderBoolean($property, $template);
			}

			case "img_file": {
				return $this->renderImageFile($property, $template);
			}

			case "relation": {
				return $this->renderRelation($property, $template);
			}

			case "symlink": {
				return $this->renderSymlink($property, $template, false, $is_random);
			}

			case "swf_file": {
				return $this->renderFile($property, $template);
			}

			case "file": {
				return $this->renderFile($property, $template);
			}



			default: {
				return "I don't know, how to render this sort of property (\"{$data_type}\") :(";
			}
		}
	}

	private function renderString(umiObjectProperty &$property, $template, $showNull = false) {
		$name = $property->getName();
		$title = $property->getTitle();
		$value = $property->getValue();

		

		if($property->getIsMultiple() === false) {
			list($tpl, $tpl_empty) = self::loadTemplates("tpls/data/{$template}.tpl", "string", "string_empty");
			
			if(empty($value) && !$showNull) {
				return $tpl_empty;
			}

			$arr = Array();
			$arr['name'] = $name;
			$arr['title'] = $title;
			$arr['value'] = $value;
			return self::parseTemplate($tpl, $arr);
		} else {
			list($tpl_block, $tpl_empty, $tpl_item, $tpl_quant) = self::loadTemplates("tpls/data/{$template}.tpl", "string_mul_block", "string_mul_block_empty", "string_mul_item", "string_mul_quant");
			
			if(empty($value) && !$showNull) {
				return $tpl_empty;
			}

			$items = "";
			$sz = sizeof($value);

			for($i = 0; $i < $sz; $i++) {
				$arr_item = Array();
				$arr_item['value'] = $value[$i];
				$arr_item['quant'] = ($sz != ($i + 1)) ? $tpl_quant : "";

				$items .= self::parseTemplate($tpl_item, $arr_item);
			}


			$arr_block = Array();
			$arr_block['name'] = $name;
			$arr_block['title'] = $title;
			$arr_block['items'] = $items;
			$arr_block['template'] = $template;
			return self::parseTemplate($tpl_block, $arr_block);
		}
	}


	private function renderInt(umiObjectProperty &$property, $template, $showNull = false) {
		$name = $property->getName();
		$title = $property->getTitle();
		$value = $property->getValue();

		

		if($property->getIsMultiple() === false) {
			list($tpl, $tpl_empty) = self::loadTemplates("tpls/data/{$template}.tpl", "int", "int_empty");
			
			if(empty($value) && !$showNull) {
				return $tpl_empty;
			}

			$arr = Array();
			$arr['name'] = $name;
			$arr['title'] = $title;
			$arr['value'] = $value;
			return self::parseTemplate($tpl, $arr);
		} else {
			list($tpl_block, $tpl_empty, $tpl_item, $tpl_quant) = self::loadTemplates("tpls/data/{$template}.tpl", "int_mul_block", "int_mul_block_empty", "int_mul_item", "int_mul_quant");
			
			if(empty($value) && !$showNull) {
				return $tpl_empty;
			}

			$items = "";
			$sz = sizeof($value);

			for($i = 0; $i < $sz; $i++) {
				$arr_item = Array();
				$arr_item['value'] = $value[$i];
				$arr_item['quant'] = ($sz != ($i + 1)) ? $tpl_quant : "";

				$items .= self::parseTemplate($tpl_item, $arr_item);
			}


			$arr_block = Array();
			$arr_block['name'] = $name;
			$arr_block['title'] = $title;
			$arr_block['items'] = $items;
			$arr_block['template'] = $template;
			return self::parseTemplate($tpl_block, $arr_block);
		}
	}


	private function renderPrice(umiObjectProperty &$property, $template, $showNull = false) {
		$name = $property->getName();
		$title = $property->getTitle();
		$value = $property->getValue();

		if(empty($value) && !$showNull) return "";

		if($property->getIsMultiple() === false) {
			list($tpl) = self::loadTemplates("tpls/data/{$template}.tpl", "price");

			$arr = Array();
			$arr['name'] = $name;
			$arr['title'] = $title;
			$arr['value'] = $value;
			return self::parseTemplate($tpl, $arr);
		} else {
			list($tpl_block, $tpl_item, $tpl_quant) = self::loadTemplates("tpls/data/{$template}.tpl", "price_mul_block", "price_mul_item", "price_mul_quant");

			$items = "";
			$sz = sizeof($value);

			for($i = 0; $i < $sz; $i++) {
				$arr_item = Array();
				$arr_item['value'] = $value[$i];
				$arr_item['quant'] = ($sz != ($i + 1)) ? $tpl_quant : "";

				$items .= self::parseTemplate($tpl_item, $arr_item);
			}


			$arr_block = Array();
			$arr_block['name'] = $name;
			$arr_block['title'] = $title;
			$arr_block['items'] = $items;
			$arr_block['template'] = $template;
			return self::parseTemplate($tpl_block, $arr_block);
		}
	}


	private function renderBoolean(umiObjectProperty &$property, $template, $showNull = false) {
		$name = $property->getName();
		$title = $property->getTitle();
		$value = $property->getValue();

		if(empty($value) && !$showNull) return "";

		if($property->getIsMultiple() === false) {
			list($tpl_yes, $tpl_no) = self::loadTemplates("tpls/data/{$template}.tpl", "boolean_yes", "boolean_no");

			$tpl = ($value) ? $tpl_yes : $tpl_no;

			$arr_block = Array();
			$arr_block['name'] = $name;
			$arr_block['title'] = $title;
			$arr_block['template'] = $template;
			return self::parseTemplate($tpl, $arr_block);

		} else {
			//а зачем? O_o
			return "";
		}
	}

	private function renderImageFile(umiObjectProperty &$property, $template, $showNull = false) {
		$name = $property->getName();
		$title = $property->getTitle();
		$value = $property->getValue();



		if($property->getIsMultiple() === false) {
			list($tpl, $tpl_empty) = self::loadTemplates("tpls/data/{$template}.tpl", "img_file", "img_file_empty");

			if(empty($value) && !$showNull) {
				return $tpl_empty;
			}

			$arr = Array();
			$arr['name'] = $name;
			$arr['title'] = $title;
			$arr['size'] = $value->getSize();
			$arr['filename'] = $value->getFileName();
			$arr['filepath'] = $value->getFilePath();
			$arr['src'] = $value->getFilePath(true);
			$arr['ext'] = $value->getExt();

			if(strtolower($value->getExt()) == "swf") {
				list($tpl) = self::loadTemplates("tpls/data/{$template}.tpl", "swf_file");
			}

			$arr['width'] = $value->getWidth();
			$arr['height'] = $value->getHeight();

			$arr['template'] = $template;

			return self::parseTemplate($tpl, $arr);
		} else {
		}
	}

	private function renderRelation(umiObjectProperty &$property, $template, $showNull = false) {
		$name = $property->getName();
		$title = $property->getTitle();
		$value = $property->getValue();


		if($property->getIsMultiple() === false) {
			list($tpl, $tpl_empty) = self::loadTemplates("tpls/data/{$template}.tpl", "relation", "relation_empty");
			
			if(empty($value) && !$showNull) {
				return $tpl_empty;
			}

			$arr = Array();
			$arr['name'] = $name;
			$arr['title'] = $title;
			$arr['object_id'] = $value;
			$arr['value'] = umiObjectsCollection::getInstance()->getObject($value)->getName();;
			return self::parseTemplate($tpl, $arr);
		} else {
			list($tpl_block, $tpl_block_empty, $tpl_item, $tpl_quant) = self::loadTemplates("tpls/data/{$template}.tpl", "relation_mul_block", "relation_mul_block_empty", "relation_mul_item", "relation_mul_quant");
			
			if(empty($value) && !$showNull) {
				return $tpl_empty;
			}

			$items = "";
			$sz = sizeof($value);

			for($i = 0; $i < $sz; $i++) {
				$arr_item = Array();
				$arr_item['object_id'] = $value[$i];
				$arr_item['value'] = umiObjectsCollection::getInstance()->getObject($value[$i])->getName();
				$arr_item['quant'] = ($sz != ($i + 1)) ? $tpl_quant : "";

				$items .= self::parseTemplate($tpl_item, $arr_item);
			}


			$arr_block = Array();
			$arr_block['name'] = $name;
			$arr_block['title'] = $title;
			$arr_block['items'] = $items;
			$arr_block['template'] = $template;
			return self::parseTemplate($tpl_block, $arr_block);
		}
	}


	private function renderSymlink(umiObjectProperty &$property, $template, $showNull = false, $is_random = false) {
		$name = $property->getName();
		$title = $property->getTitle();
		$value = $property->getValue();

		if(empty($value) && !$showNull) return "";
		$is_random = ($is_random) ? true : false;

		list($tpl_block, $tpl_empty, $tpl_item, $tpl_quant) = self::loadTemplates("tpls/data/{$template}.tpl", "symlink_block", "symlink_block_empty", "symlink_item", "symlink_quant");
		
		if(empty($value) && !$showNull) {
			return $tpl_empty;
		}

		if($is_random) {
			$value = $value[rand(0, sizeof($value) - 1)];
			$value = Array($value);
		}


		$items = "";
		$sz = sizeof($value);

		for($i = 0; $i < $sz; $i++) {
			$arr_item = Array();

			$element = $value[$i];
			$element_id = $element->getId();

			$arr_item['id'] = $element_id;
			$arr_item['object_id'] = $element->getObject()->getId();
			$arr_item['value'] = $element->getName();
			$arr_item['link'] = umiHierarchy::getInstance()->getPathById($element_id);
			$arr_item['quant'] = ($sz != ($i + 1)) ? $tpl_quant : "";

			$items .= self::parseTemplate($tpl_item, $arr_item);
		}


		$arr_block = Array();
		$arr_block['name'] = $name;
		$arr_block['title'] = $title;
		$arr_block['items'] = $items;
		$arr_block['template'] = $template;

		return self::parseTemplate($tpl_block, $arr_block);
	}

	private function renderFile(umiObjectProperty &$property, $template, $showNull = false) {
		$name = $property->getName();
		$title = $property->getTitle();
		$value = $property->getValue();

		

		if($property->getIsMultiple() === false) {
			list($tpl, $tpl_empty) = self::loadTemplates("tpls/data/{$template}.tpl", "file", "file_empty");
			
			if(empty($value) && !$showNull) {
				return $tpl_empty;
			}

			$arr = Array();
			$arr['name'] = $name;
			$arr['title'] = $title;
			$arr['size'] = $value->getSize();
			$arr['filename'] = $value->getFileName();
			$arr['filepath'] = $value->getFilePath();
			$arr['src'] = $value->getFilePath(true);
			$arr['ext'] = $value->getExt();
			$arr['modifytime'] = $value->getModifyTime();

			$arr['template'] = $template;

			return self::parseTemplate($tpl, $arr);
		} else {
		}
	}



	public function config() {
		if(class_exists("__data")) {
			return __data::config();
		}
	}

};


?>