<?php
	class umiXmlExporter implements iUmiXmlExporter {
		private $objects, $elements, $dump;


		public function __construct() {
			//(void)
		}

		public function setElements($elements_arr) {
			if(is_array($elements_arr)) {
				$this->elements = $elements_arr;
				$this->fillObjects($elements_arr);
				return true;
			} else {
				trigger_error("First argument must be an array.", E_USER_WARNING);
				return false;
			}
		}

		public function setObjects($objects_arr) {
			if(is_array($objects_arr)) {
				$this->objects = $objects_arr;
				return true;
			} else {
				trigger_error("First argument must be an array.", E_USER_WARNING);
				return false;
			}
		}

		public function run() {
			$elements = $this->parseElements();
			$objects = $this->parseObjects();

			$domain		= $this->getDomainPath() . "/";
			$site_name	= $this->getSiteName();
			$generatetime	= new umiDate(time());
			$generatetime_timestamp	= $generatetime->getFormattedDate("U");
			$generatetime_rfc		= $generatetime->getFormattedDate("r");
			$generatetime_utc		= $generatetime->getFormattedDate(DATE_ATOM);
			
			$source_id = strtoupper(md5($site_name));


			$dump = <<<END
<?xml version="1.0" encoding="utf-8"?>
<umicmsDump>
	<siteName><![CDATA[{$site_name}]]></siteName>
	<domain>{$domain}</domain>
	<sourceId><![CDATA[{$source_id}]]></sourceId>

	<generateTime>
			<timestamp><![CDATA[{$generatetime_timestamp}]]></timestamp>
			<RFC><![CDATA[{$generatetime_rfc}]]></RFC>
			<UTC><![CDATA[{$generatetime_utc}]]></UTC>
	</generateTime>

{$elements}

{$objects}

</umicmsDump>
END;
			$this->dump = iconv("CP1251", "UTF-8//IGNORE", $dump);
		}

		public function getResultFile() {
			return $this->dump;
		}

		public function saveResultFile($filepath) {
			file_put_contents($filepath, $this->dump);
			chmod($filepath, 0777);
		}



		protected function parseObjects() {
			$objects = "";

			for($i = 0; $i < sizeof($this->objects); $i++) {
				$object_id = $this->objects[$i];

				$objects .= $this->parseObject($object_id);
			}

			return $objects;
		}

		protected function parseObject($object_id) {
			if(!($object = umiObjectsCollection::getInstance()->getObject($object_id))) {
				trigger_error("Can't load object #{$object_id}", E_USER_WARNING);
				return false;
			}

			$object_name		= $object->getName();
			$object_type_id		= $object->getTypeId();
			$object_is_locked	= (int) $object->getIsLocked();

			$properties_blocks = $this->parsePropertyBlocks($object);

			$object_str = <<<END
	<object id="{$object_id}" typeId="{$object_type_id}" isLocked="{$object_is_locked}">
		<name><![CDATA[{$object_name}]]></name>

{$properties_blocks}
	</object>


END;

			return $object_str;
		}


		protected function parsePropertyBlocks(umiObject $object) {
			$object_type_id = $object->getTypeId();
			
			if(!$object_type_id) return false;
			
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			$groups = $object_type->getFieldsGroupsList();
			$groups_block = "";

			foreach($groups as $group) {
				$group_name		= $group->getName();
				$group_title		= $group->getTitle();
				$group_is_locked	= (int) $group->getIsLocked();
				$group_is_public	= (int) $group->getIsVisible();

				$properties_block = $this->parseProperty($object, $group);

				$groups_block .= <<<END
		<propertiesBlock isLocked="{$group_is_locked}" isPublic="{$group_is_public}">
			<name><![CDATA[{$group_name}]]></name>
			<title><![CDATA[{$group_title}]]></title>

{$properties_block}
		</propertiesBlock>


END;
			}

			return $groups_block;
		}


		protected function parseProperty(umiObject $object, umiFieldsGroup $group) {
			$properties = "";

			$fields = $group->getFields();

			foreach($fields as $field) {
				$field_name		= $field->getName();
				$field_title		= $field->getTitle();
				$field_tip		= $field->getTip();

				$field_is_locked	= (int) $field->getIsLocked();
				$field_is_public	= (int) $field->getIsVisible();
				$field_is_indexed	= (int) $field->getIsInSearch();
				$field_is_filterable	= (int) $field->getIsInFilter();

				$field_guide_id		= $field->getGuideId();

				$field_type		= $field->getFieldType();
				$field_is_multiple	= (int) $field_type->getIsMultiple();
				$field_data_type	= (string) $field_type->getDataType();
				

				$values = $this->parseValues($object, $field);

				$properties .= <<<END

			<property isLocked="{$field_is_locked}" isPublic="{$field_is_public}">
				<name><![CDATA[{$field_name}]]></name>
				<title><![CDATA[{$field_title}]]></title>

				<fieldType><![CDATA[{$field_data_type}]]></fieldType>
				<isMultiple>{$field_is_multiple}</isMultiple>
				<isIndexed>{$field_is_indexed}</isIndexed>
				<isFilterable>{$field_is_filterable}</isFilterable>

				<guideId>{$field_guide_id}</guideId>

				<tip><![CDATA[{$field_tip}]]></tip>

				<values>
{$values}
				</values>
			</property>


END;
			}

			return $properties;
		}


		protected function parseValues(umiObject $object, umiField $field) {
			$field_type		= $field->getFieldType();
			$field_is_multiple	= (int) $field_type->getIsMultiple();
			$field_data_type	= (string) $field_type->getDataType();

			$values = $object->getValue($field->getName());
			$values = (is_array($values)) ? $values : Array($values);

			switch($field_data_type) {
				case "img_file": {
					$values_arr = Array();

					foreach($values as $cval) {
						if(!$cval) continue;

						$values_arr[] = Array	(
										"value"	=> $cval->getFilePath()
									);
					}

					break;
				}

				case "relation": {
					$values_arr = Array();

					foreach($values as $cval) {
						$cval = umiObjectsCollection::getInstance()->getObject($cval);
						if(!$cval) continue;

						$obj_id = $cval->getId();

						if(!in_array($obj_id, $this->objects)) {
							$this->objects[] = $obj_id;
						}


						$values_arr[] = Array	(
										"value"	=> $cval->getName(),
										"id"	=> $cval->getId()
									);
					}
					break;
				}

				case "symlink": {
					$values_arr = Array();

					foreach($values as $cval) {
						if(!$cval) continue;

						$values_arr[] = Array	(
										"value"	=> $cval->getName(),
										"id"	=> $cval->getId(),
										"link"	=> $this->getDomainPath() . umiHierarchy::getInstance()->getPathById($cval->getId())
									);
					}
					break;
				}

				case "date": {
					$values_arr = Array();

					foreach($values as $cval) {
						if(!$cval) continue;

						$values_arr[] = Array	(
										"timestamp"	=> $cval->getFormattedDate("U"),
										"RFC"		=> $cval->getFormattedDate("r"),
										"UTC"		=> $cval->getFormattedDate(DATE_ATOM)
									);
					}
					break;
				}


				default: {
					$values_arr = Array();
					foreach($values as $cval) {
						$values_arr[] = Array	(
										"value"	=> $cval,
										"id"	=> NULL
									);
					}
					break;
				}
			}

			$values = "";
			foreach($values_arr as $val) {
				$value = $val['value'];

				if(is_null($val['id'])) {
					if($val['timestamp']) {

						$values .= <<<END
					<value>
						<timestamp><![CDATA[{$val['timestamp']}]]></timestamp>
						<RFC><![CDATA[{$val['RFC']}]]></RFC>
						<UTC><![CDATA[{$val['UTC']}]]></UTC>
					</value>

END;
					} else {
						$values .= <<<END
					<value><![CDATA[{$value}]]></value>

END;
					}

				} else {
					if(!$value) continue;
					$id = $val['id'];
					$link = array_key_exists("link", $val) ? " link=\"{$val['link']}\"" : "";

					$values .= <<<END
					<value id="{$id}"{$link}><![CDATA[{$value}]]></value>

END;
				}
			}

			return $values;
		}


		protected function getDomainPath() {
			return "http://" . $_SERVER['HTTP_HOST'];
		}


		protected function getSiteName() {
			$regedit = regedit::getInstance();
			return $regedit->getVal("//settings/site_name");
		}


		protected function fillObjects($elements_arr) {
			foreach($elements_arr as $element_id) {
				$element = umiHierarchy::getInstance()->getElement($element_id);
				if (!$element) continue;
				$object_id = $element->getObject()->getId();

				if(!in_array($object_id, $this->objects)) {
					$this->objects[] = $object_id;
				}
			}
		}

		protected function parseElements() {
			$elements_arr = $this->elements;

			$elements = "";

			foreach($elements_arr as $element_id) {
				$element = umiHierarchy::getInstance()->getElement($element_id);
				if(!$element) continue;

				$name			= $element->getName();
				$alt_name		= $element->getAltName();
				$link			= $this->getDomainPath() . umiHierarchy::getInstance()->getPathById($element_id);
				$parent_id		= $element->getParentId();
				$hierarchy_type_id	= $element->getTypeId();

				$hierarchy_type		= umiHierarchyTypesCollection::getInstance()->getType($hierarchy_type_id);
				$behaviour_title	= $hierarchy_type->getTitle();
				$behaviour_module	= $hierarchy_type->getName();
				$behaviour_method	= $hierarchy_type->getExt();

				$tpl_id			= $element->getTplId();
				$tpl_path		= templatesCollection::getInstance()->getTemplate($tpl_id)->getFilename();

				$lang_id		= $element->getLangId();
				$lang			= langsCollection::getInstance()->getLang($lang_id);
				$lang_title		= $lang->getTitle();
				$lang_prefix		= $lang->getPrefix();

				$domain_id		= $element->getDomainId();
				$domain			= domainsCollection::getInstance()->getDomain($domain_id);
				$domain_host		= $domain->getHost();

				$object_id		= $element->getObject()->getId();
				
				$is_visible		= (int) $element->getIsVisible();

				$updatetime		= new umiDate($element->getUpdateTime());
				$updatetime_timestamp	= $updatetime->getFormattedDate("U");
				$updatetime_rfc		= $updatetime->getFormattedDate("r");
				$updatetime_utc		= $updatetime->getFormattedDate(DATE_ATOM);

				$elements .= <<<END
	<element id="{$element_id}" parentId="{$parent_id}" objectId="{$object_id}" is_visible="{$is_visible}">
		<name><![CDATA[{$name}]]></name>
		<link><![CDATA[{$link}]]></link>
		<altName><![CDATA[{$alt_name}]]></altName>

		<templateId><![CDATA[{$tpl_id}]]></templateId>
		<templatePath><![CDATA[{$tpl_path}]]></templatePath>
		<lang prefix="{$lang_prefix}"><![CDATA[{$lang_title}]]></lang>
		<domain><![CDATA[{$domain_host}]]></domain>

		<behaviour>
			<title><![CDATA[{$behaviour_title}]]></title>
			<module><![CDATA[{$behaviour_module}]]></module>
			<method><![CDATA[{$behaviour_method}]]></method>
		</behaviour>

		<updateTime>
				<timestamp><![CDATA[{$updatetime_timestamp}]]></timestamp>
				<RFC><![CDATA[{$updatetime_rfc}]]></RFC>
				<UTC><![CDATA[{$updatetime_utc}]]></UTC>
		</updateTime>
	</element>


END;
			}

			return $elements;
		}
	};
?>