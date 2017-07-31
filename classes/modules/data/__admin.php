<?php

class __data {
	public $per_page = 10;

	public function main() {
	}

	public function fill_navibar($parent_type_id) {
		$this->sheets_set_active("types");

		$types = umiObjectTypesCollection::getInstance();
		$nav_parent_type_id = $parent_type_id;
		$nav_parents = Array();
		do {
			if($nav_parent_type_id) $nav_parents[] = $nav_parent_type_id;
			$nav_parent_type_id = $types->getParentClassId($nav_parent_type_id);
		} while($nav_parent_type_id);
		$nav_parents = array_reverse($nav_parents);
		foreach($nav_parents as $curr_parent_id) {
			$curr_parent = $types->getType($curr_parent_id);
			
			if(!$curr_parent->getName()) continue;

			$this->navibar_push($curr_parent->getName(), "/admin/data/types/" . $curr_parent_id);
		}
	}

	public function types() {
		$parent_type_id = (int) $_REQUEST['param0'];
		$curr_page = (int) $_REQUEST['p'];
		$per_page = $this->per_page;

		$this->load_forms();
		$params = Array();

		$types = umiObjectTypesCollection::getInstance();
		$sub_types = $types->getSubTypesList($parent_type_id);

		$this->fill_navibar($parent_type_id);


		$total = sizeof($sub_types);

		$sub_types = array_slice($sub_types, $curr_page * $per_page, $per_page, true);

		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

		$rows = "";
		$sz = sizeof($sub_types);
		for($i = 0; $i < $sz; $i++) {
			$type_id = $sub_types[$i];
			$type = $types->getType($type_id);
			$type_name = $type->getName();

			$edit_link = "<a href='%pre_lang%/admin/data/type_edit/{$type_id}'><img src='/images/cms/admin/%skin_path%/ico_edit.gif' /></a>";

			if($type->getIsLocked()) {
				$del_link = "";
			} else {
				$del_link = "<a href='%pre_lang%/admin/data/type_del/{$type_id}/{$parent_type_id}/' commit_unrestorable='Вы уверены?'><img src='/images/cms/admin/%skin_path%/ico_del.gif' alt='Удалить' title='Удалить' /></a>";
			}

			$rows .= <<<ROW
<row>
	<col><a href="%pre_lang%/admin/data/types/{$type_id}/">{$type_name}</a></col>
	<col style="text-align: center;">
		<a href="%pre_lang%/admin/data/types/{$type_id}/">
			<img src='/images/cms/admin/%skin_path%/ico_subitems.gif' title="Содержание" alt="Содержание" border="0" />
		</a>
	</col>
	<col style="text-align: center;">{$edit_link}</col>
	<col style="text-align: center;">{$del_link}</col>
</row>

ROW;
		}

		$params['types_rows'] = $rows;
		$params['parent_type_id'] = $parent_type_id;
		return $this->parse_form("types", $params);
	}


	public function type_add() {
		$parent_type_id = (int) $_REQUEST['param0'];

		$objectTypes = umiObjectTypesCollection::getInstance();
		$type_id = $objectTypes->addType($parent_type_id, "Новый тип данных");

		$this->redirect($this->pre_lang . "/admin/data/type_edit/" . $type_id . "/");
	}


	public function type_edit() {
		$type_id = (int) $_REQUEST['param0'];

		$this->navibar_back();
		$this->fill_navibar($type_id);
		$this->navibar_push("%nav_type_edit%", "/admin/data/type_edit/" . $type_id);

		$this->load_forms();
		$params = Array();

		$field_types = umiFieldTypesCollection::getInstance();
		$field_types_list = $field_types->getFieldTypesList();

		$field_types_rows = "";
		foreach($field_types_list as $field_type) {
			$field_type_id = $field_type->getId();
			$field_type_name = $field_type->getName();

			$field_types_rows .= "field_types[field_types.length] = new Array('{$field_type_id}', '{$field_type_name}');\n";
		}
		$params['field_types'] = $field_types_rows;

		$types = umiObjectTypesCollection::getInstance();
		$type = $types->getType($type_id);
		$parent_type_id = $type->getParentId();
		$params['name'] = $type->getName();
		$params['type_id'] = $type_id;
		$params['is_guidable'] = $type->getIsGuidable();
		$params['is_public'] = $type->getIsPublic();


		$groups_list = $type->getFieldsGroupsList();
		$groups_list_rows = "";
		$fields_rows = "";
		foreach($groups_list as $group) {
			$group_id = $group->getId();
			$group_name = $group->getName();
			$group_title = $group->getTitle();
			$group_is_visible = (int) $group->getIsVisible();
			$group_is_locked = (int) $group->getIsLocked();

			$groups_list_rows .= "field_groups[field_groups.length] = new Array('{$group_id}', '{$group_name}', '{$group_title}', '{$group_is_visible}', '{$group_is_locked}');\n";


			//Getting fields list
			$fields_list = $group->getFields();
			foreach($fields_list as $field) {
				$field_id = $field->getId();
				$field_type_id = $field->getFieldTypeId();
				$field_name = $field->getName();
				$field_title = $field->getTitle();

				$field_is_locked = (int) $field->getIsLocked();
				$field_is_visible = (int) $field->getIsVisible();

				$fields_rows .= "fields[fields.length] = new Array('{$field_id}', '{$group_id}', '{$field_type_id}', '{$field_name}', '{$field_title}', '{$type_id}', '{$field_is_visible}', '{$field_is_locked}');\n";
			}
		}
        	$params['field_groups'] = $groups_list_rows;
		$params['fields'] = $fields_rows;
		$params['type_id'] = $type_id;


		$hierarchy_types_objs = umiHierarchyTypesCollection::getInstance()->getTypesList();
		$hierarchy_types_arr = Array();
		foreach($hierarchy_types_objs as $id => $hierarchy_type) {
			$hierarchy_types_arr[$id] = $hierarchy_type->getTitle();
		}


		$hierarchy_type_id = $type->getHierarchyTypeId();

		if(!$hierarchy_type_id) {
			if($parent_type = umiObjectTypesCollection::getInstance()->getType($type->getParentId())) {
				$hierarchy_type_id = $parent_type->getHierarchyTypeId();
			}
		}

		$hierarchy_types = putSelectBox_assoc($hierarchy_types_arr, $hierarchy_type_id, true);
		$params['hierarchy_types'] = $hierarchy_types;

		$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();"  />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); "  />';
		$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/data/types/" . $parent_type_id . "/";

		return $this->parse_form("type_edit", $params);
	}


	public function type_edit_do() {
		$type_id = (int) $_REQUEST['param0'];
		$name = $_REQUEST['name'];
		$is_guidable = (bool) $_REQUEST['is_guidable'];
		$is_public = (bool) $_REQUEST['is_public'];
		$hierarchy_type_id = (int) $_REQUEST['hierarchy_type_id'];

		$exit_after_save = (int) $_REQUEST['exit_after_save'];

		$type = umiObjectTypesCollection::getInstance()->getType($type_id);
		$type->setName($name);
		$type->setIsGuidable($is_guidable);
		$type->setIsPublic($is_public);
		$type->setHierarchyTypeId($hierarchy_type_id);
		$type->commit();

		if($exit_after_save) {
			$this->redirect($this->pre_lang . "/admin/data/types/" . $type->getParentId() . "/");
		} else {
			$this->redirect($this->pre_lang . "/admin/data/type_edit/" . $type_id . "/");
		}
	}


	public function type_del() {
		$type_id = (int) $_REQUEST['param0'];
		$parent_type_id = (int) $_REQUEST['param1'];

		umiObjectTypesCollection::getInstance()->delType($type_id);

		$this->redirect($this->pre_lang . "/admin/data/types/" . $parent_type_id . "/");
	}


	public function type_field_add() {
		$group_id = (int) $_REQUEST['param0'];
		$type_id = (int) $_REQUEST['param1'];

		$this->navibar_back();
		$this->fill_navibar($type_id);
		$this->navibar_push("%nav_type_edit%", "/admin/data/type_edit/" . $type_id);
		$this->navibar_push("%nav_type_field_add%", "/admin/data/type_field_add/" . $group_id . "/" . $type_id);


		$this->load_forms();
		$params = Array();

		$params['is_visible'] = 1;

		$field_types_list = umiFieldTypesCollection::getInstance()->getFieldTypesList();
		$field_types = "";
		foreach($field_types_list as $curr_field_type) {
			$curr_field_type_name = $curr_field_type->getName();
			$curr_field_type_id = $curr_field_type->getId();

			$field_types .= <<<END
<item>
	<value><![CDATA[{$curr_field_type_id}]]></value>
	<title><![CDATA[{$curr_field_type_name}]]></title>
</item>

END;
		}

		$params['guides_allowed'] = putSelectBox_assoc(umiObjectTypesCollection::getGuidesList(), "", true);

		$params['field_types'] = $field_types;

		$params['field_id'] = $group_id;
		$params['type_id'] = $type_id;
		$params['method'] = "type_field_add_do";

		$params['in_search'] = "";
		$params['in_filter'] = "";

		$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и выйти" onclick="javascript: return save_with_exit();"  />&#160;&#160;<submit title="Добавить" onclick="javascript: return save_without_exit(); "  />';
		$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/data/type_edit/" . $type_id . "/";

		return $this->parse_form("type_field_edit", $params);
	}


	public function type_field_add_do() {
		$group_id = (int) $_REQUEST['param0'];
		$type_id = (int) $_REQUEST['param1'];

		$name = $_REQUEST['name'];
		$title = $_REQUEST['title'];
		$field_type = (int) $_REQUEST['field_type'];
		$is_visible = (int) $_REQUEST['is_visible'];
		$is_inheritable = (int) $_REQUEST['is_inheritable'];
		$guide_id = (int) $_REQUEST['guide_id'];

		$in_search = (int) $_REQUEST['in_search'];
		$in_filter = (int) $_REQUEST['in_filter'];

		$tip = (string) $_REQUEST['tip'];

		$exit_after_save = (int) $_REQUEST['exit_after_save'];



		$field_type_obj = umiFieldTypesCollection::getInstance()->getFieldType($field_type);
		$field_data_type = $field_type_obj->getDataType();

		if($field_data_type == "relation" && $guide_id == 0) {
			$guide_id = self::getAutoGuideId($title);
		}


		$field_id = umiFieldsCollection::getInstance()->addField($name, $title, $field_type, $is_visible, false, $is_inheritable);
		$field = umiFieldsCollection::getInstance()->getField($field_id);
		$field->setGuideId($guide_id);
		$field->setIsInSearch($in_search);
		$field->setIsInFilter($in_filter);
		$field->setTip($tip);

		$field->commit();

		umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldsGroup($group_id)->attachField($field_id);
		$group_name = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldsGroup($group_id)->getName();


		$childs = umiObjectTypesCollection::getInstance()->getChildClasses($type_id);
		$sz = sizeof($childs);


		for($i = 0; $i < $sz; $i++) {
			$child_type_id = $childs[$i];

			if($type = umiObjectTypesCollection::getInstance()->getType($child_type_id)) {
				if($group = $type->getFieldsGroupByName($group_name)) {
					$group->attachField($field_id);
				}
			} else {
				trigger_error("Can't find type #{$child_type_id}", E_USER_WARNING);
			}
		}


		if($exit_after_save) {
			$this->redirect($this->pre_lang . "/admin/data/type_edit/" . $type_id . "/");
		} else {
			$this->redirect($this->pre_lang . "/admin/data/type_field_edit/" . $field_id . "/" . $type_id . "/");
		}
	}


	public function type_field_edit() {
		$field_id = (int) $_REQUEST['param0'];
		$type_id = (int) $_REQUEST['param1'];

		$this->navibar_back();
		$this->fill_navibar($type_id);
		$this->navibar_push("%nav_type_edit%", "/admin/data/type_edit/" . $type_id);
		$this->navibar_push("%nav_type_field_edit%", "/admin/data/type_field_edit/" . $field_id . "/" . $type_id);


		$this->load_forms();
		$params = Array();

		$field = umiFieldsCollection::getInstance()->getField($field_id);

		$params['name'] = $field->getName();
		$params['title'] = $field->getTitle();
		$params['is_visible'] = $field->getIsVisible();
		$params['is_inheritable'] = $field->getIsInheritable();
		$params['in_search'] = $field->getIsInSearch();
		$params['in_filter'] = $field->getIsInFilter();
		$params['tip'] = $field->getTip();

		$field_type_id = $field->getFieldTypeId();


		$field_types_list = umiFieldTypesCollection::getInstance()->getFieldTypesList();
		$field_types = "";
		foreach($field_types_list as $curr_field_type) {
			$curr_field_type_name = $curr_field_type->getName();
			$curr_field_type_id = $curr_field_type->getId();

			if($curr_field_type_id == $field_type_id) {
				$checked = " selected='yes'";
			} else $checked = "";

			$field_types .= <<<END
<item {$checked}>
	<title>{$curr_field_type_name}</title>
	<value>{$curr_field_type_id}</value>
</item>

END;
		}

		$params['field_types'] = $field_types;

		$params['guides_allowed'] = putSelectBox_assoc(umiObjectTypesCollection::getGuidesList(), $field->getGuideId(), true);

		$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();"  />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); "  />';
		$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/data/type_edit/" . $type_id . "/";

		$params['field_id'] = $field_id;
		$params['type_id'] = $type_id;
		$params['method'] = "type_field_edit_do";

		return $this->parse_form("type_field_edit", $params);
	}

	public function type_field_edit_do() {
		$field_id = (int) $_REQUEST['param0'];
		$type_id = (int) $_REQUEST['param1'];

		$name = $_REQUEST['name'];
		$title = $_REQUEST['title'];
		$field_type = (int) $_REQUEST['field_type'];
		$is_visible = (int) $_REQUEST['is_visible'];
		$is_inheritable = (int) $_REQUEST['is_inheritable'];
		$guide_id = (int) $_REQUEST['guide_id'];

		$in_search = (int) $_REQUEST['in_search'];
		$in_filter = (int) $_REQUEST['in_filter'];

		$tip = (string) $_REQUEST['tip'];

		$exit_after_save = (int) $_REQUEST['exit_after_save'];

		$field_type_obj = umiFieldTypesCollection::getInstance()->getFieldType($field_type);
		$field_data_type = $field_type_obj->getDataType();

		if($field_data_type == "relation" && !$guide_id) {
			$guide_id = self::getAutoGuideId($title);
		}


		$field = umiFieldsCollection::getInstance()->getField($field_id);
		$field->setName($name);
		$field->setTitle($title);
		$field->setFieldTypeId($field_type);
		$field->setIsVisible($is_visible);
		$field->setIsInheritable($is_inheritable);
		$field->setGuideId($guide_id);
		$field->setIsInSearch($in_search);
		$field->setIsInFilter($in_filter);
		$field->setTip($tip);
		$field->commit();

		if($exit_after_save) {
			$this->redirect($this->pre_lang . "/admin/data/type_edit/" . $type_id . "/");
		} else {
			$this->redirect($this->pre_lang . "/admin/data/type_field_edit/" . $field_id . "/" . $type_id . "/");
		}
	}


	public function type_group_add() {
		$type_id = (int) $_REQUEST['param0'];
		$this->load_forms();
		$params = Array();

		$this->navibar_back();
		$this->fill_navibar($type_id);
		$this->navibar_push("%nav_type_edit%", "/admin/data/type_edit/" . $type_id);
		$this->navibar_push("%nav_type_group_add%", "/admin/data/type_group_add/" . $type_id);


		$params['is_visible'] = 1;
		$params['is_active'] = 1;

		$params['group_id'] = 0;
		$params['type_id'] = $type_id;
		$params['method'] = "type_group_add_do";

		$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и выйти" onclick="javascript: return save_with_exit();"  />&#160;&#160;<submit title="Добавить" onclick="javascript: return save_without_exit(); "  />';
		$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/data/type_edit/" . $type_id . "/";

		return $this->parse_form("type_group_edit", $params);
	}


	public function type_group_add_do() {
		$type_id = (int) $_REQUEST['param1'];

		$name = $_REQUEST['name'];
		$title = $_REQUEST['title'];
		$is_active = (int) $_REQUEST['is_active'];
		$is_visible = (int) $_REQUEST['is_visible'];

		$exit_after_save = (int) $_REQUEST['exit_after_save'];

                $group_id = umiObjectTypesCollection::getInstance()->getType($type_id)->addFieldsGroup($name, $title, $is_active, $is_visible);

		if($exit_after_save) {
			$this->redirect($this->pre_lang . "/admin/data/type_edit/" . $type_id . "/");
		} else {
			$this->redirect($this->pre_lang . "/admin/data/type_group_edit/" . $group_id . "/" . $type_id . "/");
		}
	}


	public function type_group_edit() {
		$group_id = (int) $_REQUEST['param0'];
		$type_id = (int) $_REQUEST['param1'];

		$this->navibar_back();
		$this->fill_navibar($type_id);
		$this->navibar_push("%nav_type_edit%", "/admin/data/type_edit/" . $type_id);
		$this->navibar_push("%nav_type_group_edit%", "/admin/data/type_group_edit/" . $group_id . "/" . $type_id);


		$this->load_forms();
		$params = Array();

		$group = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldsGroup($group_id);

		$params['name'] = $group->getName();
		$params['title'] = $group->getTitle();
		$params['is_visible'] = $group->getIsVisible();
		$params['is_active'] = $group->getIsActive();

		$params['group_id'] = $group_id;
		$params['type_id'] = $type_id;
		$params['method'] = "type_group_edit_do";

		$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();"  />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); "  />';
		$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/data/type_edit/" . $type_id . "/";

		return $this->parse_form("type_group_edit", $params);
	}


	public function type_group_edit_do() {
		$group_id = (int) $_REQUEST['param0'];
		$type_id = (int) $_REQUEST['param1'];

		$name = $_REQUEST['name'];
		$title = $_REQUEST['title'];
		$is_active = (int) $_REQUEST['is_active'];
		$is_visible = (int) $_REQUEST['is_visible'];

		$exit_after_save = (int) $_REQUEST['exit_after_save'];


		$group = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldsGroup($group_id);
		$group->setName($name);
		$group->setTitle($title);
		$group->setIsActive($is_active);
		$group->setIsVisible($is_visible);
		$group->commit();

		if($exit_after_save) {
			$this->redirect($this->pre_lang . "/admin/data/type_edit/" . $type_id . "/");
		} else {
			$this->redirect($this->pre_lang . "/admin/data/type_group_edit/" . $group_id . "/" . $type_id . "/");
		}
	}




	public function config() {
		$params = Array();

		$hierarchy_types = umiHierarchyTypesCollection::getInstance()->getTypesList();
		$this->load_forms();
		$this->sheets_reset();

		$rows = "";
		foreach($hierarchy_types as $id => $type) {
			$title = $type->getTitle();
			$name = $type->getName();
			$ext = $type->getExt();


			$rows .= <<<END

<row>
	<col>
		<input   quant='no' style='width: 95%'>
			<name><![CDATA[titles[{$id}]]]></name>
			<value><![CDATA[{$title}]]></value>
		</input>

	</col>

	<col>
		<input   quant='no' style='width: 95%'>
			<name><![CDATA[names[{$id}]]]></name>
			<value><![CDATA[{$name}]]></value>
		</input>

	</col>

	<col>
		<input   quant='no' style='width: 95%'>{$ext}
			<name><![CDATA[exts[{$id}]]]></name>
			<value><![CDATA[{$ext}]]></value>
		</input>

	</col>

	<col style="text-align: center;">
		<checkbox>
					<name><![CDATA[dels[]]]></name>
					<value><![CDATA[{$id}]]></value>
		</checkbox>
	</col>
</row>

END;
		}


		$params['rows'] = $rows;
		return $this->parse_form("config", $params);
	}

	public function config_do() {
		$titles = $_REQUEST['titles'];
		$names = $_REQUEST['names'];
		$exts = $_REQUEST['exts'];

		$title_new = $_REQUEST['title_new'];
		$name_new = $_REQUEST['name_new'];
		$ext_new = $_REQUEST['ext_new'];

		$dels = $_REQUEST['dels'];


		foreach($titles as $id => $title) {
			$name = $names[$id];
			$ext = $exts[$id];

			$type = umiHierarchyTypesCollection::getInstance()->getType($id);
			$type->setTitle($title);
			$type->setName($name);
			$type->setExt($ext);
			$type->commit();
		}

		foreach($dels as $id) {
			umiHierarchyTypesCollection::getInstance()->delType($id);
		}

		if($title_new) {
			umiHierarchyTypesCollection::getInstance()->addType($name_new, $title_new, $ext_new);
		}

		$this->redirect($this->pre_lang . "/admin/data/config/");
	}


	public function getEditLink($type_id) {
		$link_add = false;
		$link_edit = $this->pre_lang . "/admin/data/type_edit/{$type_id}/";

		return Array($link_add, $link_edit);
		break;
	}


	public function getAutoGuideId($title) {
		$guide_name = "РЎРїСЂР°РІРѕС‡РЅРёРє РґР»СЏ РїРѕР»СЏ \"{$title}\"";

		$child_types = umiObjectTypesCollection::getInstance()->getChildClasses(7);
		foreach($child_types as $child_type_id) {
			$child_type = umiObjectTypesCollection::getInstance()->getType($child_type_id);

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