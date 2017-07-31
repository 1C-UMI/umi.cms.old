<?php
	class umiXmlImporter implements iUmiXmlImporter {
		protected	$ignore_new_fields = false,
				$ignore_new_items = false,
				$is_xml_analyzed = false;
		protected $xml;

		protected	$xml_elements = Array(),
				$xml_objects = Array(),
				$xml_types = Array();

		protected	$source_id = 1;

		public function __construct() {
		}


		public function ignoreNewFields($ignore_new_fields = NULL) {
			$old_value = $this->ignore_new_fields;

			if(!is_null($ignore_new_fields)) {
				$this->ignore_new_fields = (bool) $ignore_new_fields;
			}

			return $old_value;
		}


		public function ignoreNewItems($ignore_new_items = NULL) {
			$old_value = $this->ignore_new_items;

			if(!is_null($ignore_new_items)) {
				$this->ignore_new_items = (bool) $ignore_new_items;
			}

			return $old_value;
		}


		public function loadXmlString($xml_string) {
			$xml = simplexml_load_string($xml_string);
			return $this->loadXml($xml);
		}


		public function loadXmlFile($xml_filepath) {
			if(!is_file($xml_filepath)) {
				trigger_error("XML file {$xml_filepath} not found", E_USER_WARNING);
				return false;
			}


			if(!is_readable($xml_filepath)) {
				trigger_error("XML file {$xml_filepath} is not readable", E_USER_WARNING);
				return false;
			}


			$xml = simplexml_load_file($xml_filepath);
			return $this->loadXml($xml);
		}


		protected function loadXml($xml) {
			if(is_object($xml)) {
				$this->xml = $xml;
				return true;
			} else {
				trigger_error("Failed to read xml-content", E_USER_WARNING);
				return false;
			}
		}


		public function analyzeXml() {
			$source_id_name = (string) $this->xml->sourceId;
			$this->source_id = umiImportRelations::getInstance()->addNewSource($source_id_name);

			foreach($this->xml->element as $currentNode) {
				$this->analyzeElementNode($currentNode);
			}


			foreach($this->xml->object as $currentNode) {
				$old_object_id = (int) $currentNode->attributes()->id;

				if(array_key_exists($old_object_id, $this->xml_objects)) {
					$this->analyzeObjectNode($currentNode);
				}
			}
		}


		protected function analyzeElementNode(SimpleXMLElement $elementNode) {
			$element_id =		(int) $elementNode->attributes()->id;
			$element_parent_id =	(int) $elementNode->attributes()->parentId;
			$element_object_id =	(int) $elementNode->attributes()->objectId;
			$element_alt_name =	(string) $elementNode->altName;

			$module = $elementNode->behaviour->module;
			$method = $elementNode->behaviour->method;

			$element_hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName($module, $method);

			if($element_hierarchy_type === false) {
				trigger_error("Unknown element's module/method", E_USER_ERROR);
				return false;
			}

			$element_filepath = (string) $elementNode->templatePath;

			$element_hierarchy_type_id = $element_hierarchy_type->getId();

			$this->xml_elements[$element_id] = Array(
							"old_element_id" => $element_id,
							"old_parent_id" => $element_parent_id,
							"old_element_object_id" => $element_object_id,

							"element_hierarchy_type_id" => $element_hierarchy_type_id,
							"element_filepath" => $element_filepath,

							"old_element_alt_name" => $element_alt_name
							);

			$this->xml_objects[$element_object_id] = Array(
									"old_element_id" => $element_id,
									"element_hierarcy_type_id" => $element_hierarchy_type_id,
									"is_linked" => true
								);

		}


		protected function analyzeObjectNode(SimpleXMLElement $objectNode) {
			$object_id = (int) $objectNode->attributes()->id;
			$object_type_id = (int) $objectNode->attributes()->typeId;

			$object_info = $this->xml_objects[$object_id];

			$object_info['old_object_id'] = $object_id;
			$object_info['old_type_id'] = $object_type_id;
			$object_info['old_name'] = (string) $objectNode->name;

			$object_info['props'] = $this->analyzeObjectPropertiesBlockNode($objectNode->propertiesBlock, $object_type_id);

			$this->xml_objects[$object_id] = $object_info;
		}



		protected function analyzeObjectPropertiesBlockNode(SimpleXMLElement $object_properties_block_nodes, $object_type_id) {
			if(!array_key_exists($object_type_id, $this->xml_types)) {
				$this->xml_types[$object_type_id] = Array();
				$this->xml_types[$object_type_id]['is_base'] = true;
				$this->xml_types[$object_type_id]['props'] = Array();
			}

			$obj_props = Array();

			foreach($object_properties_block_nodes as $object_properties_block_node) {
				$props_block_title = (string) $object_properties_block_node->title;
				$props_block_name = (string) $object_properties_block_node->name;

				foreach($object_properties_block_node->property as $object_property_node) {
					$prop_title = (string) $object_property_node->title;
					$prop_name = (string) $object_property_node->name;
					$prop_tip = (string) $object_property_node->tip;

					$prop_is_multiple = (int) $object_property_node->isMultiple;
					$prop_is_indexed = (int) $object_property_node->isIndexed;
					$prop_is_filterable = (int) $object_property_node->isFilterable;
					$prop_guide_id = (string) $object_property_node->guideId;

					$prop_field_type = (string) $object_property_node->fieldType;

					$prop_values = $this->extractValues($object_property_node->values);


					$tmp_prop_title = iconv("UTF-8", "CP1251", $prop_title);
					if(!$prop_name) {
						$prop_name = translit::convert($tmp_prop_title);
					}

					$prop_info = Array();
					$prop_info['title'] = $prop_title;
					$prop_info['name'] = $prop_name;
					$prop_info['tip'] = $prop_tip;
					$prop_info['is_multiple'] = $prop_is_multiple;
					$prop_info['is_filterable'] = $prop_is_filterable;
					$prop_info['guide_id'] = $prop_guide_id;
					$prop_info['field_type'] = $prop_field_type;
					$prop_info['values'] = $prop_values;

					$prop_info['prop_block_title'] = $props_block_title;
					$prop_info['prop_block_name'] = $props_block_name;

					$this->xml_types[$object_type_id]['props'][$prop_name] = $prop_info;

					$obj_props[$prop_name] = $prop_info;
				}
			}

			return $obj_props;
		}


		protected function extractValues($values_node) {
			$res = Array();

			foreach($values_node->value as $value_node) {
				$timestamp = ((string) $value_node->timestamp[0]);

				$val = ((string) $value_node);

				if($timestamp) {
					$val = new umiDate();
					$val->setDateByTimeStamp($timestamp);
				}

				if($val) {
					$res[] = $val;
				}
			}

			return $res;
		}



		protected function detectBetterFieldType($value) {
			//TODO: Определять наибоее подходящий тип данных.
		}


		protected function detectBetterObjectType($hierarchy_type_id, $old_type_id) {
			//TODO: Определять наиболее подходящий тип данных.
			$fields = array_keys($this->xml_types[$old_type_id]['props']);

			$new_type_id = umiImportRelations::getInstance()->getNewTypeIdRelation($this->source_id, $old_type_id);

			if($new_type_id) {
				return $new_type_id;
			}

			$types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type_id);
			$base_type_id = umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($hierarchy_type_id);

			foreach($types as $type_id => $type_name) {
				$diff_count = $this->compareObjectTypeFields($type_id, $fields);

				if($diff_count == 0) {
					$new_type_id = $type_id;
					break;
				}
			}
			if(!$new_type_id) {
				$base_type_name = umiObjectTypesCollection::getInstance()->getType($base_type_id)->getName();
				$base_type_name = iconv("CP1251", "UTF-8", $base_type_name);
				$new_type_id = umiObjectTypesCollection::getInstance()->addType($base_type_id, "РџРѕРґС‚РёРї \"{$base_type_name}\" #{$old_type_id}");
			}

			umiImportRelations::getInstance()->setTypeIdRelation($this->source_id, $old_type_id, $new_type_id);

			return $new_type_id;
		}


		protected function compareObjectTypeFields($object_type_id, $fields) {
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			if($object_type === false) {
				trigger_error("Object type #{$object_type_id} not found", E_USER_ERROR);
				return false;
			}


			$diff_count = 0;
			foreach($fields as $field_name) {
				if($object_type->getFieldId($field_name) == false) {
					++$diff_count;
				}
			}

			return $diff_count;
		}



		protected function detectBetterTemplateId($filepath) {
			if($filepath) {
				foreach(templatesCollection::getInstance()->getTemplatesList as $ctpl) {
					if($ctpl->getFilename() == $filepath) {
						return $ctpl->getId();
					}
				}
			}

			return templatesCollection::getInstance()->getDefaultTemplate()->getId();
		}



		public function importXml() {
			foreach($this->xml_elements as $element_info) {
				$hierarchy_type_id = $element_info['element_hierarchy_type_id'];
				$old_object_id = $element_info['old_element_object_id'];
				$old_object_type_id = $this->xml_objects[$old_object_id]['old_type_id'];

				$element_info['old_type_id'] = $old_object_type_id;
				$element_info['new_type_id'] = $new_type_id = $this->detectBetterObjectType($hierarchy_type_id, $old_object_type_id);
				$element_info['new_tpl_id'] = $new_tpl_id = $this->detectBetterTemplateId($element_info['element_filepath']);

				$element_info['new_lang_id'] = langsCollection::getInstance()->getDefaultLang()->getId();
				$element_info['new_domain_id'] = domainsCollection::getInstance()->getDefaultDomain()->getId();

				$element_info['element_name'] = $this->xml_objects[$old_object_id]['old_name'];


				$this->importElement($element_info);
			}
		}


		protected function importElement($element_info) {
			$old_element_id = $element_info['old_element_id'];
			$old_element_parent_id = $element_info['old_parent_id'];

			$old_element_object_id = $element_info['old_element_object_id'];

			$new_name = $element_info['element_name'];
			$old_element_alt_name = $element_info['old_element_alt_name'];

			$old_object_type_id = $element_info['old_type_id'];

			if($old_element_alt_name) {
				$alt_name = $old_element_alt_name;
			} else {
				$alt_name = $new_name;
			}

			$new_element_id = umiImportRelations::getInstance()->getNewIdRelation($this->source_id, $old_element_id);

			if($old_element_parent_id == 0) {
				$new_parent_id = $old_element_parent_id;
			} else {
				$new_parent_id = umiImportRelations::getInstance()->getNewIdRelation($this->source_id, $old_element_parent_id);
			}

			if($new_element_id === false && $new_parent_id !== false) {
				$new_domain_id = $element_info['new_domain_id'];
				$new_lang_id = $element_info['new_lang_id'];
				$new_hierarchy_type_id = $element_info['element_hierarchy_type_id'];
				$new_tpl_id = $element_info['new_tpl_id'];
				$new_type_id = $element_info['new_type_id'];

				$new_element_parent_id = umiImportRelations::getInstance()->getNewIdRelation($old_element_parent_id);
				$new_element_id = umiHierarchy::getInstance()->addElement($new_parent_id, $new_hierarchy_type_id, $new_name, $alt_name, $new_type_id, $new_domain_id, $new_lang_id, $new_tpl_id);

				umiImportRelations::getInstance()->setIdRelation($this->source_id, $old_element_id, $new_element_id);
			}

			cmsController::getInstance()->getModule("users");
			if($users_inst = cmsController::getInstance()->getModule("users")) {
				$users_inst->setDefaultPermissions($new_element_id);
			}


			$new_element = umiHierarchy::getInstance()->getElement($new_element_id, true);
			$new_element->setIsActive(true);
			$new_element->setAltName($alt_name);

			$missed_props = Array();
			$props = $this->xml_objects[$old_element_object_id]['props'];

			foreach($props as $prop_name => $prop_info) {
				$prop_value = $prop_info['values'];

				$field_type = $prop_info['field_type'];
				if($field_type == "img_file") {
					if($prop_value[0]) {
						$prop_value[0] = new umiImageFile($prop_value[0]);
					}
				}

				$prop_name = strtolower(translit::convert($prop_name));

				if($new_element->getObject()->getPropByName($prop_name)) {
					$new_element->setValue($prop_name, $prop_value);
				} else {
					$missed_props[] = $prop_info;
				}
			}

			$this->addMissedProps($new_element, $missed_props, $old_object_type_id);

			foreach($missed_props as $prop_info) {
				$prop_value = $prop_info['values'];

				$field_type = $prop_info['field_type'];
				if($field_type == "img_file") {
					if($prop_value[0]) {
						$prop_value[0] = new umiImageFile($prop_value[0]);
					}
				}


				if(!$prop_info['name']) $prop_info['name'] = translit::convert($prop_info['title']);
				$prop_info['name'] = strtolower(translit::convert($prop_info['name']));

				$new_element->setValue($prop_info['name'], $prop_value);

			}

			$new_element->commit();
		}



		protected function addMissedProps(&$new_element, $missed_props, $old_object_type_id) {
			$object_type_id = $new_element->getObject()->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);


			foreach($missed_props as $missed_prop) {
				$prop_block_title = $missed_prop['prop_block_title'];
				$prop_block_name = $missed_prop['prop_block_name'];

				if(!$prop_block_name) {
					if($prop_block_title) {
						$prop_block_name = translit::convert($prop_block_title);
					} else {
						$prop_block_title = "Imported fields group";
						$prop_block_name = "imported_fields_group";
					}
				}


				if($prop_group_block = $object_type->getFieldsGroupByName($prop_block_name)) {
				} else {
					$prop_group_block_id = $object_type->addFieldsGroup($prop_block_name, $prop_block_name, true, true);
					$prop_group_block = $object_type->getFieldsGroup($prop_group_block_id);
				}

				
				$field_type_id = $this->getFieldTypeId($missed_prop['field_type'], $missed_prop['is_multiple']);

				if($field_type_id === false) continue;


				if(!$missed_prop['name']) $missed_prop['name'] = translit::convert($missed_prop['title']);
				$missed_prop['name'] = strtolower(translit::convert($missed_prop['name']));

				if($object_type_id) {
					if(umiImportRelations::getInstance()->getNewFieldId($this->source_id, $object_type_id, $missed_prop['name'])) {
						continue;
					}
				}


				if($missed_prop['field_type'] == "relation") {
					$guide_id = self::getAutoGuideId($missed_prop['title']);
				}

				$field_id = umiFieldsCollection::getInstance()->addField($missed_prop['name'], $missed_prop['title'], $field_type_id, true, false);
				$field = umiFieldsCollection::getInstance()->getField($field_id);
				$field->setTip($missed_prop['tip']);

				if($guide_id) {
					$field->setGuideId($guide_id);
				}

				$field->commit();

				$prop_group_block->attachField($field_id);

				if($object_type_id) {
					umiImportRelations::getInstance()->setFieldIdRelation($this->source_id, $object_type_id, $missed_prop['name'], $field_id);
				}
			}
		}


		protected function getFieldTypeId($data_type, $is_multiple = false) {
			$field_types = umiFieldTypesCollection::getInstance()->getFieldTypesList();

			foreach($field_types as $field_type) {
				if($field_type->getDataType() == $data_type && $field_type->getIsMultiple() == $is_multiple) {
					return $field_type->getId();
				}
			}

			return false;
		}




	public function getAutoGuideId($title) {
		$guide_name = "РЎРїСЂР°РІРѕС‡РЅРёРє РґР»СЏ РїРѕР»СЏ \"{$title}\"";

		$child_types = umiObjectTypesCollection::getInstance()->getChildClasses(7);
		foreach($child_types as $child_type_id) {
			$child_type = umiObjectTypesCollection::getInstance()->getType($child_type_id);
			$child_type_name = iconv("CP1251", "UTF-8", $child_type->getName());

			if($child_type_name == $guide_name) {
				$child_type->setIsGuidable(true);
				return $child_type_id;
			}
		}

		$guide_id = umiObjectTypesCollection::getInstance()->addType(7, $guide_name);
		$guide = umiObjectTypesCollection::getInstance()->getType($guide_id);
		$guide->setIsGuidable(true);
		$guide->setIsPublic(true);
		$guide->commit();

		return $guide_id;
	}

	};
?>