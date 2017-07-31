<?php
	abstract class __client_reflection_data {

		public function getEditForm($object_id, $template = "default", $groups_names = "") {
			if(!$template) $template = "default";

			if(!cmsController::getInstance()->getModule("users")->isOwnerOfObject($object_id)) {
				return "%data_edit_foregin_object%";
			}

			$groups_names = split(" ", trim($groups_names));


			list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_block", "reflection_block_empty", "reflection_group");

			if(!($object = umiObjectsCollection::getInstance()->getObject($object_id))) {
				return $template_block_empty;
			}


			$object_type_id = $object->getTypeId();
			$groups_arr = $this->getTypeFieldGroups($object_type_id);

			$groups = "";
			foreach($groups_arr as $group) {
				if(!$group->getIsActive()) {
					continue;
				}

				if(sizeof($groups_names)) {
					if(!in_array($group->getName(), $groups_names)) {
						continue;
					}
				} else {
					if(!$group->getIsActive() || !$group->getIsVisible()) {
						continue;
					}
				}

				$line_arr = Array();

				$fields_arr = $group->getFields();
				$fields = "";
				foreach($fields_arr as $field) {
					if(!$field->getIsVisible()) continue;

					$fields .= $this->renderEditField($template, $field, $object);
				}

				$line_arr['title'] = $group->getTitle();
				$line_arr['name'] = $group->getName();

				$line_arr['fields'] = $fields;

				$groups .= def_module::parseTemplate($template_line, $line_arr);
			}


			$block_arr['groups'] = $groups;

			return def_module::parseTemplate($template_block, $block_arr, false, $object_id);
		}



		public function getCreateForm($object_type_id, $template = "default", $groups_names = "") {
			if(!$template) $template = "default";

			list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_block", "reflection_block_empty", "reflection_group");

			$groups_names = split(" ", trim($groups_names));



			$groups_arr = $this->getTypeFieldGroups($object_type_id);

			$groups = "";
			foreach($groups_arr as $group) {
				if(!$group->getIsActive()) {
					continue;
				}

				if(sizeof($groups_names)) {
					if(!in_array($group->getName(), $groups_names)) {
						continue;
					}

				} else {
					if(!$group->getIsActive() || !$group->getIsVisible()) {
						continue;
					}
				}

				$line_arr = Array();

				$fields_arr = $group->getFields();
				$fields = "";
				foreach($fields_arr as $field) {
					if(!$field->getIsVisible()) continue;

					$fields .= $this->renderEditField($template, $field, $object);
				}

				$line_arr['title'] = $group->getTitle();
				$line_arr['name'] = $group->getName();

				$line_arr['fields'] = $fields;

				$groups .= def_module::parseTemplate($template_line, $line_arr);
			}


			$block_arr['groups'] = $groups;

			return def_module::parseTemplate($template_block, $block_arr);
		}



		public function getTypeFieldGroups($type_id) {
			if($type = umiObjectTypesCollection::getInstance()->getType($type_id)) {
				return $type->getFieldsGroupsList();
			} else {
				return false;
			}
		}

		public function renderEditField($template, umiField $field, $object = false) {
			$field_type_id = $field->getFieldTypeId();
			$field_type = umiFieldTypesCollection::getInstance()->getFieldType($field_type_id);
			$is_multiple = $field_type->getIsMultiple();

			$data_type = $field_type->getDataType();



			switch($data_type) {
				case "int": {
					$res = $this->renderEditFieldInt($field, $is_multiple, $object, $template);
					break;
				}


				case "string": {
					$res = $this->renderEditFieldString($field, $is_multiple, $object, $template);
					break;
				}

				case "password": {
					$res = $this->renderEditFieldPassword($field, $is_multiple, $object, $template);
					break;
				}

				case "relation": {
					$res = $this->renderEditFieldRelation($field, $is_multiple, $object, $template);
					break;
				}


				case "img_file": {
					$res = $this->renderEditFieldImageFile($field, $is_multiple, $object, $template);
					break;
				}

				case "swf_file": {
					$res = $this->renderEditFieldFile($field, $is_multiple, $object, $template);
					break;
				}

				case "file": {
					$res = $this->renderEditFieldFile($field, $is_multiple, $object, $template);
					break;
				}

				case "text": {
					$res = $this->renderEditFieldText($field, $is_multiple, $object, $template);
					break;
				}

				case "wysiwyg": {
					$res = $this->renderEditFieldWYSIWYG($field, $is_multiple, $object, $template);
					break;
				}




				default: {
					$res = "<p>I don't know, how to let you edit this field (\"{$data_type}\") yet.</p>";
					break;
				}
			}
			return $res;
		}


		public function renderEditFieldString($field, $is_multiple, $object, $template) {
			list($template_block) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_field_string");

			$block_arr = Array();

			if($is_multiple) {
				//TODO: Подумать, имеет ли смысл вводить поля на несколько строк?
			} else {
				$field_name = $field->getName();
				$block_arr['name'] = $field_name;
				$block_arr['title'] = $field->getTitle();
				$block_arr['tip'] = $field->getTip();

				$block_arr['value'] = ($object) ? $object->getValue($field->getName()) : "";

				if($object) {
					$block_arr['object_id'] = $object->getId();
				}


				$block_arr['input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			}

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function renderEditFieldText($field, $is_multiple, $object, $template) {
			list($template_block) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_field_text");

			$block_arr = Array();

			if($is_multiple) {
				//Оно тут не нужно
			} else {
				$field_name = $field->getName();
				$block_arr['name'] = $field_name;
				$block_arr['title'] = $field->getTitle();
				$block_arr['tip'] = $field->getTip();

				$block_arr['value'] = ($object) ? $object->getValue($field->getName()) : "";

				if($object) {
					$block_arr['object_id'] = $object->getId();
				}


				$block_arr['input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			}

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function renderEditFieldWYSIWYG($field, $is_multiple, $object, $template) {
			list($template_block) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_field_wysiwyg");

			$block_arr = Array();

			if($is_multiple) {
				//Оно тут не нужно
			} else {
				$field_name = $field->getName();
				$block_arr['name'] = $field_name;
				$block_arr['title'] = $field->getTitle();
				$block_arr['tip'] = $field->getTip();

				$block_arr['value'] = ($object) ? $object->getValue($field->getName()) : "";

				if($object) {
					$block_arr['object_id'] = $object->getId();
				}


				$block_arr['input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			}

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function renderEditFieldInt($field, $is_multiple, $object, $template) {
			list($template_block) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_field_int");

			$block_arr = Array();

			if($is_multiple) {
				//TODO
			} else {
				$field_name = $field->getName();
				$block_arr['name'] = $field_name;
				$block_arr['title'] = $field->getTitle();
				$block_arr['tip'] = $field->getTip();

				$block_arr['value'] = ($object) ? $object->getValue($field->getName()) : "";

				if($object) {
					$block_arr['object_id'] = $object->getId();
				}


				$block_arr['input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			}

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function renderEditFieldPassword($field, $is_multiple, $object, $template) {
			list($template_block) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_field_password");

			$block_arr = Array();

			if($is_multiple) {
				//TODO
			} else {
				$field_name = $field->getName();
				$block_arr['name'] = $field_name;
				$block_arr['title'] = $field->getTitle();
				$block_arr['tip'] = $field->getTip();

				//$block_arr['value'] = ($object) ? $object->getValue($field->getName()) : "";
				$block_arr['value'] = "";

				if($object) {
					$block_arr['object_id'] = $object->getId();
				}


				$block_arr['input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}][]" : "data[new][{$field_name}][]";
			}

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function renderEditFieldRelation($field, $is_multiple, $object, $template) {
			if($guide_id = $field->getGuideId()) {
				$guide_items = umiObjectsCollection::getInstance()->getGuidedItems($guide_id);
			} else {
				return false;
			}

			list($template_block, $template_block_line, $template_block_line_a, $template_mul_block, $template_mul_block_line, $template_mul_block_line_a) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_field_relation", "reflection_field_relation_option", "reflection_field_relation_option_a", "reflection_field_multiple_relation", "reflection_field_multiple_relation_option", "reflection_field_multiple_relation_option_a");

			$block_arr = Array();

			if($object) {
				$value = $object->getValue($field->getName());
			}

			if($is_multiple) {
				$field_name = $field->getName();
				$block_arr['name'] = $field_name;
				$block_arr['title'] = $field->getTitle();
				$block_arr['tip'] = $field->getTip();

				$options = "";
				foreach($guide_items as $item_id => $item_name) {
					$selected = (in_array($item_id, $value)) ? " selected" : "";

					if($template_block_line) {
						$line = ($selected) ? $template_mul_block_line_a : $template_mul_block_line;
						$line_arr = Array();
						$line_arr['id'] = $item_id;
						$line_arr['name'] = $item_name;

						$options .= def_module::parseTemplate($line, $line_arr, false, $item_id);
					} else {
						$options .= "<option value=\"{$item_id}\"{$selected}>{$item_name}</option>\n";
					}
				}

				if($object) {
					$block_arr['object_id'] = $object->getId();
				}


				$block_arr['options'] = $options;
				$block_arr['input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}][]" : "data[new][{$field_name}]";

				return def_module::parseTemplate($template_mul_block, $block_arr);
			} else {
				$field_name = $field->getName();
				$block_arr['name'] = $field_name;
				$block_arr['title'] = $field->getTitle();
				$block_arr['tip'] = $field->getTip();

				$options = "";
				foreach($guide_items as $item_id => $item_name) {
					$selected = ($item_id == $value) ? " selected" : "";

					if($template_block_line) {
						$line = ($selected) ? $template_block_line_a : $template_block_line;
						$line_arr = Array();
						$line_arr['id'] = $item_id;
						$line_arr['name'] = $item_name;

						$options .= def_module::parseTemplate($line, $line_arr, false, $item_id);
					} else {
						$options .= "<option value=\"{$item_id}\"{$selected}>{$item_name}</option>\n";
					}
				}

				if($object) {
					$block_arr['object_id'] = $object->getId();
				}


				$block_arr['options'] = $options;
				$block_arr['input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";

				return def_module::parseTemplate($template_block, $block_arr);
			}
		}


		public function renderEditFieldImageFile($field, $is_multiple, $object, $template) {
			list($template_block) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_field_img_file");

			$block_arr = Array();

			if($is_multiple) {
				//TODO
			} else {
				$field_name = $field->getName();
				$block_arr['name'] = $field_name;
				$block_arr['title'] = $field->getTitle();
				$block_arr['tip'] = $field->getTip();

				$value = ($object) ? $object->getValue($field->getName()) : "";
                                if($value) {
					$block_arr['value'] = $value->getFilePath();
				} else {
					$block_arr['value'] = "";
				}

				if($object) {
					$block_arr['object_id'] = $object->getId();
				}

				$block_arr['input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			}

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function renderEditFieldFile($field, $is_multiple, $object, $template) {
			list($template_block) = def_module::loadTemplates("tpls/data/reflection/{$template}.tpl", "reflection_field_file");

			$block_arr = Array();

			if($is_multiple) {
				//TODO
			} else {
				$field_name = $field->getName();
				$block_arr['name'] = $field_name;
				$block_arr['title'] = $field->getTitle();
				$block_arr['tip'] = $field->getTip();

				$value = ($object) ? $object->getValue($field->getName()) : "";
                                if($value) {
					$block_arr['value'] = $value->getFilePath();
				} else {
					$block_arr['value'] = "";
				}

				if($object) {
					$block_arr['object_id'] = $object->getId();
				}

				$block_arr['input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			}

			return def_module::parseTemplate($template_block, $block_arr);
		}



		public function saveEditedObject($object_id, $is_new = false) {
			global $_FILES;

			if(!($object = umiObjectsCollection::getInstance()->getObject($object_id))) {
				return false;
			}

			if(!cmsController::getInstance()->getModule("users")->isOwnerOfObject($object_id)) {
				return false;
			}


			$object_type_id = $object->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			$key = ($is_new) ? "new" : $object_id;

			$data = $_REQUEST['data'][$key];

			foreach($_FILES['data']['tmp_name'][$key] as $i=>$v) {
				$data[$i] = $v;
			}

			foreach($data as $field_name => $field_value) {

				if(!($field_id = $object_type->getFieldId($field_name))) {
					continue;
				}

				$field = umiFieldsCollection::getInstance()->getField($field_id);

				if(!$field->getIsVisible()) {
					continue;
				}


				$field_type = $field->getFieldType();
				$data_type = $field_type->getDataType();
				switch($data_type) {
					case "password": {
						$field_value = ($field_value[0] == $field_value[1]) ? md5($field_value[0]) : NULL;
						break;
					}

					case "img_file": {
						if($value = umiImageFile::upload("data", $field_name, "./images/cms/data/", $key)) {
							$field_value = $value;
						} else {
							$field_value = $object->getValue($field_name);
						}
						break;
					}

					case "swf_file": {
						if($value = umiFile::upload("data", $field_name, "./images/files/", $key)) {
							$field_value = $value;
						} else {
							$field_value = $object->getValue($field_name);
						}
						break;
					}

					case "file": {
						if($value = umiFile::upload("data", $field_name, "./files/", $key)) {
							$field_value = $value;
						} else {
							$field_value = $object->getValue($field_name);
						}
						break;
					}
				}

				$object->setValue($field_name, $field_value);
			}

			$object->commit();

			return true;
		}


	};
?>