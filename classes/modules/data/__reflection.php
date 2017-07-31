<?php

class __reflection_data {
	public function renderEditableGroups($type_id, $element_id = false, $is_object = false) {
		$res = "";

		if($element_id) {
			if($is_object) {
				$object = umiObjectsCollection::getInstance()->getObject($element_id);
			} else {
				$object = umiHierarchy::getInstance()->getElement($element_id)->getObject();
			}
		}

		$groups_list = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldsGroupsList();
		foreach($groups_list as $fields_group) {
			if($fields_group->getIsVisible() == false) continue;
			$group_name = $fields_group->getName();
			$group_title = $fields_group->getTitle();

			$res .= <<<END
				<setgroup name="{$group_title}" id="data_{$group_name}" form="no">
END;

			$fields_list = $fields_group->getFields();
			foreach($fields_list as $field) {
				if($field->getIsVisible() == false) continue;

				$res .= "<div style=\"width: 49%; height: auto; float: left;\">";
				$res .= $this->renderEditableField($field, $object);
				$res .= "</div>";
			}


			$res .= <<<END
<div style="clear: both;"></div>
<p align="right">%save_n_save%</p>
				</setgroup>
END;
		}

		return $res;
	}

	public function renderEditableField(&$field, &$object) {
		$field_title = $field->getTitle();
		$field_name = $field->getName();

		$field_type = umiFieldTypesCollection::getInstance()->getFieldType($field->getFieldTypeId());

		$data_type = $field_type->getDataType();
		$is_multiple = $field_type->getIsMultiple();

		$val = (is_object($object)) ? $val = $object->getValue($field_name) : false;

		switch($data_type) {
			case "string": {
				$res = $this->renderStringInput($field, $val, $is_multiple);
				break;
			}

			case "int": {
				$res = $this->renderIntegerInput($field, $val, $is_multiple);
				break;
			}

			case "price": {
				$res = $this->renderIntegerInput($field, $val, $is_multiple);
				break;
			}

			case "boolean": {
				$res = $this->renderBooleanInput($field, $val, $is_multiple);
				break;
			}

			case "text": {
				$res = $this->renderTextInput($field, $val, $is_multiple);
				break;
			}

			case "wysiwyg": {
				$res = $this->renderWYSIWYGInput($field, $val, $is_multiple);
				break;
			}

			case "img_file": {
				$res = $this->renderImageFileInput($field, $val, $is_multiple);
				break;
			}

			case "relation": {
				$res = $this->renderRelationInput($field, $val, $is_multiple);
				break;
			}

			case "date": {
				$res = $this->renderDateInput($field, $val, $is_multiple);
				break;
			}

			case "tags": {
				$res = $this->renderTagsInput($field, $val, $is_multiple);
				break;
			}

			case "symlink": {
				$res = $this->renderSymlinkInput($field, $val, $is_multiple);
				break;
			}

			case "swf_file": {
				$res = $this->renderFileInput($field, $val, $is_multiple);
				break;
			}


			case "file": {
				$res = $this->renderFileInput($field, $val, $is_multiple);
				break;
			}





			default: {
				$res = "?" . $data_type . "?";
				break;
			}
		}

		return $res;
	}


	public function renderStringInput(&$field, $val, $is_multiple) {
		$res = "";

		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$tip = $field->getTip();


		if($is_multiple == false) {
			$res = <<<END
<input type="text" title="{$field_title}" br="yes" name="data_values[{$field_id}]" style="width: 355px; margin-bottom: 2px;">
				<name><![CDATA[data_values[{$field_id}]]]></name>
				<title><![CDATA[{$field_title}]]></title>
				<value><![CDATA[{$val}]]></value>
				<tip><![CDATA[{$tip}]]></tip>
		</input>

END;
		} else {
			$res = "";

			foreach($val as $cval) {
				$res .= <<<END
<input type="text" title="{$field_title}" br="yes" name="data_values[{$field_id}][]" style="width: 355px;">
				<name><![CDATA[data_values[{$field_id}]]]></name>
				<title><![CDATA[{$field_title}]]></title>
				<value><![CDATA[{$cval}]]></value>
		</input><br /><br />

END;

			}

			$res .= <<<END
<input type="text" title="{$field_title}" br="yes" name="data_values[{$field_id}][]" style="width: 355px;">
				<name><![CDATA[data_values[{$field_id}][]]]></name>
				<title><![CDATA[{$field_title}]]></title>
				<tip><![CDATA[{$tip}]]></tip>
		</input>

END;
		}

		return $res;
	}


	public function renderIntegerInput(&$field, $val, $is_multiple) {
		return $this->renderStringInput($field, $val, $is_multiple);
	}


	public function renderBooleanInput(&$field, $val, $is_multiple) {
		$res = "";

		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$tip = $field->getTip();


		if($is_multiple == false) {
			$val = ($val) ? $val : "";
			$res = <<<END
<div style="margin: 3px; height: 34px;">
	<br />
	<checkbox br="yes" selected="{$val}">
				<name><![CDATA[data_values[{$field_id}]]]></name>
				<title><![CDATA[{$field_title}]]></title>
				<value><![CDATA[1]]></value>
				<tip><![CDATA[{$tip}]]></tip>
	</checkbox>
	<br />
</div>
END;
		} else {
			// Действительно, зачем? O_o
		}

		return $res;
	}


	public function renderTextInput(&$field, $val, $is_multiple) {
		$res = "";

		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$tip = $field->getTip();


		if($is_multiple == false) {
			$val = self::filterOutputString($val);

			$res = <<<END
<div style="margin: 3px;">{$field_title}:<br /><textarea name="data_values[{$field_id}]" style="width: 363px; height: 120px;">{$val}</textarea></div>
END;
		} else {
			foreach($val as $cval) {
				$cval = self::filterOutputString($cval);

				$res .= <<<END
<div style="margin: 3px;">{$field_title}:<br /><textarea name="data_values[{$field_id}][]" style="width: 363px; height: 120px;">{$cval}</textarea></div>
END;
			}
			$res .= <<<END
<div style="margin: 3px;">{$field_title}:<br /><textarea name="data_values[{$field_id}][]" style="width: 363px; height: 120px;"></textarea></div>
END;
		}

		return $res;
	}


	public function renderWYSIWYGInput(&$field, $val, $is_multiple) {
		$res = "";

		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$tip = $field->getTip();

		if($is_multiple == false) {
			$val = self::filterOutputString($val);

			$res = <<<END
</div><div style="clear: both;" /><div>

<div style="width: 95%; margin: 3px;">
	<wysiwyg name="data_values[{$field_id}]" id="data_values[{$field_id}]" style="height: 120px;">
		<title><![CDATA[{$field_title}]]></title>
		<value><![CDATA[{$val}]]></value>
		<tip><![CDATA[{$tip}]]></tip>
	</wysiwyg>
</div>

END;
		} else {
			//TODO
		}

		return $res;
	}


	public function renderImageFileInput(&$field, $val, $is_multiple) {
		$res = "";

		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$field_name = $field->getName();

		if($is_multiple == false) {
			$cifi = new cifi("data_values__{$field_id}", "./images/cms/data/");
			$file_path =  (is_object($val)) ? $val->getFileName() : "";
			$cifi_str = $cifi->make_div() . $cifi->make_element($file_path);

			$res = <<<END
<span class="ftext">{$field_title}</span>
{$cifi_str}
END;
		} else {
			//TODO
		}

		return $res;
	}

	public function renderRelationInput(&$field, $val, $is_multiple) {
		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$field_name = $field->getName();
		$tip = $field->getTip();

		$guide_id = $field->getGuideId();
		if(!($guide_type = umiObjectTypesCollection::getInstance()->getType($guide_id))) {
			return "";
		}

		$guide_items = umiObjectsCollection::getInstance()->getGuidedItems($guide_id);
		$is_guidable = $guide_type->getIsGuidable();
		$is_public = $guide_type->getIsPublic();
		$res = '';
		
		// варианты
		if($is_guidable) {
			if($is_multiple) {
				if($is_public) { // multipleGuideInput
					$rows = putSelectBox_assoc($guide_items, $val);
					$res = 
<<<END
<multipleGuideInput width="370" height="130" quant="no" br="yes">
	<id>{$field_id}</id>
	<name>data_values[{$field_id}][]</name>
	<title>{$field_title}</title>
	<tip><![CDATA[{$tip}]]></tip>
	{$rows}
</multipleGuideInput>
END;
				}
				else { // multiple
					$rows = putSelectBox_assoc($guide_items, $val);
					$res =
<<<END
<multiple name="data_values[{$field_id}][]" title="{$field_title}" style="width: 370px; height:100px" quant="no" br="yes">{$rows}</multiple>
END;
				}
			}
			else {
				if($is_public) { //singleGuideInput
					$rows = putSelectBox_assoc($guide_items, $val);
					$res = 
<<<END
<singleGuideInput width="370" quant="no" br="yes">
	<id>{$field_id}</id>
	<name>data_values[{$field_id}][]</name>
	<title>{$field_title}</title>
	<tip><![CDATA[{$tip}]]></tip>
	{$rows}
</singleGuideInput>
END;
				}
				else { // select
					$rows = putSelectBox_assoc($guide_items, $val, true);
					$res = 
<<<END
<select name="data_values[{$field_id}]" title="{$field_title}" style="width: 370px;" class="std_select" quant="no" br="yes">{$rows}</select>
END;
				}
			}
		}

		return $res;
	}

	public function renderDateInput(&$field, $val, $is_multiple) {
		$res = "";

		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$tip = $field->getTip();

		if(is_null($val) || $val === false) {
			$val = new umiDate();
		}

		$val = $val->getFormattedDate();


		if($is_multiple == false) {
			$res = <<<END
<input br="yes">
	<title><![CDATA[{$field_title}]]></title>
	<name><![CDATA[data_values[{$field_id}]]]></name>
	<value><![CDATA[{$val}]]></value>
	<style><![CDATA[width: 355px;]]></style>
	<tip><![CDATA[{$tip}]]></tip>
</input>
END;
		} else {
			$res = "";

			foreach($val as $cval) {
				$res .= <<<END
<input type="text" title="{$field_title}" br="yes" name="data_values[{$field_id}][]" style="width: 355px;">{$cval}</input><br /><br />
END;

			}

			$res .= <<<END
<input type="text" title="{$field_title}" br="yes" name="data_values[{$field_id}][]" style="width: 355px;"></input>
END;
		}

		return $res;
	}

	public function renderTagsInput(&$field, $val, $is_multiple) {
		$res = "";

		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$tip = $field->getTip();

		$val = implode(", ", $val);

		$res = <<<END
<input type="text" br="yes" style="width: 355px;">
	<name><![CDATA[data_values[{$field_id}][]]]></name>
	<title><![CDATA[[{$field_title}]]></title>
	<value><![CDATA[{$val}]]></value>
	<tip><![CDATA[{$tip}]]></tip>
</input>
END;
		return $res;
	}


	public function renderSymlinkInput(&$field, $val, $is_multiple) {
		$res = "";

		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$field_name = $field->getName();
		$tip = $field->getTip();

//		$rows = putSelectBox_assoc($guide_items, $val);
		$values = "";

		foreach($val as $element) {
			$element_id = $element->getId();
			$element_name = $element->getName();
			
			$element_name = mysql_escape_string($element_name);
			
		$values .= <<<END
<value id="{$element_id}">
	<title><![CDATA[{$element_name}]]></title>
</value>

END;
		}

		$res = <<<END
</div><div style="clear: both;" /><div>

<div style="width: 95%; margin: 3px;">

<symlinkInput id="{$field_id}" style="width: 370px; height:100px" quant="no" br="yes">
	<title><![CDATA[{$field_title}]]></title>
	<tip><![CDATA[{$tip}]]></tip>
	<values>
	{$values}
	</values>
</symlinkInput>
</div>
END;

		return $res;
	}


	public function renderFileInput(&$field, $val, $is_multiple) {
		$res = "";

		$field_id = $field->getId();
		$field_title = $field->getTitle();
		$field_name = $field->getName();

		if($is_multiple == false) {
			$cifi = new cifi("data_values__{$field_id}", "./files/", false);
			$file_path =  (is_object($val)) ? $val->getFileName() : "";
			$cifi_str = $cifi->make_div() . $cifi->make_element($file_path);

			$res = <<<END
<span class="ftext">{$field_title}</span>
{$cifi_str}
END;
		} else {
			//TODO
		}

		return $res;
	}





	public function saveEditedGroups($element_id, $is_object = false) {
		if($is_object) {
			$object = umiObjectsCollection::getInstance()->getObject($element_id);
		} else {
			$element = umiHierarchy::getInstance()->getElement($element_id);
			$object = $element->getObject();
		}
			$type_id = $object->getTypeId();


		$groups_list = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldsGroupsList();
		foreach($groups_list as $fields_group) {
			if($fields_group->getIsVisible() == false) continue;

			$fields_list = $fields_group->getFields();
			foreach($fields_list as $field) {
				if($field->getIsVisible() == false) continue;
				$field_id = $field->getId();
				$value = $_REQUEST['data_values'][$field_id];

				$data_type = umiFieldTypesCollection::getInstance()->getFieldType($field->getFieldTypeId())->getDataType();

				switch($data_type) {
					case "file":
						$select = $_REQUEST['select_data_values__' . $field_id];
						if(!($value = umiFile::upload("pics", "data_values__" . $field_id, "./files/"))) $value = new umiFile("./files/" . $select);

						if($prop = $object->getPropById($field_id)) {
							$prop->setValue($value);
						}

						break;

					case "img_file":
						$select = $_REQUEST['select_data_values__' . $field_id];
						if(!($value = umiImageFile::upload("pics", "data_values__" . $field_id, "./images/cms/data/"))) $value = new umiImageFile("./images/cms/data/" . $select);

						if($prop = $object->getPropById($field_id)) {
							$prop->setValue($value);
						}

						break;

					case "swf_file":
						$select = $_REQUEST['select_data_values__' . $field_id];
						if(!($value = umiFile::upload("pics", "data_values__" . $field_id, "./files/"))) $value = new umiImageFile("./files/" . $select);

						if($prop = $object->getPropById($field_id)) {
							$prop->setValue($value);
						}

						break;


					case "file":
						$select = $_REQUEST['select_data_values__' . $field_id];
						if(!($value = umiFile::upload("pics", "data_values__" . $field_id, "./files/"))) $value = new umiImageFile("./files/" . $select);

						if($prop = $object->getPropById($field_id)) {
							$prop->setValue($value);
						}

						break;


					case "date":
						$dateValue = new umiDate();

						$dateValue->setDateByString($value);

						if($prop = $object->getPropById($field_id)) {
							$prop->setValue($dateValue);
						}
						break;


					default:
						if($prop = $object->getPropById($field_id)) {
							$prop->setValue($value);
						}
						break;
				}
			}
		}
	}

	public static function filterOutputString($str) {
		return str_replace("%", "&#037;", $str);
	}
}
?>