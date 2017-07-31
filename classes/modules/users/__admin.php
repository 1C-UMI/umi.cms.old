<?php

abstract class __users {

	public function user_blocking() {
			$group_id = (int) $_REQUEST['param0'];
			$user_id = (int) $_REQUEST['param1'];
			$is_active = (int) $_REQUEST['param2'];

			$object = umiObjectsCollection::getInstance()->getObject($user_id);
			if ($object instanceof umiObject) {
				$object->setValue("is_activated", $is_active);
				$object->commit();
			}

			if($_SERVER['HTTP_REFERER']) {
				$url = $_SERVER['HTTP_REFERER'];
			} else {
				$url = $this->pre_lang . "/admin/users/users_list/".($group_id ? $group_id : "");
			}


			$this->redirect($url);
	}

	public function add_group() {
		$this->sheets_set_active("groups_list");
		$params = Array();
		$this->load_forms("forms_admin.php");

		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array("%groups_list_new%", "/admin/users/groups_list/");
		cmsController::getInstance()->nav_arr[] = Array("%users_group_add%", "/admin/users/add_group/");


		if(system_is_allowed("users", "add_group_do"))
			$submit = "<p align=\"right\"><submit title=\"Добавить и выйти\" onclick=\"return save_with_exit();\"/>&#160;&#160;<submit title=\"Добавить\" onclick=\"return save_without_exit();\" /></p>";
		else
			$submit = "<p align=\"right\"><submit title=\"Добавить и выйти\" onclick=\"return save_with_exit();\" disabled=\"yes\" />&#160;&#160;<submit title=\"Добавить\" onclick=\"return save_without_exit();\" disabled=\"yes\" /></p>";


		$params['method'] = "add_group_do";
		$params['submit'] = $submit;
		$params['perms_form'] = $this->choose_perms('group');
		$params['new_unit'] = "true";

		return $this->parse_form('add_group', $params);
	}

	public function add_group_do() {
		$group_name = $_REQUEST['group_name'];

		$group_id = umiObjectsCollection::getInstance()->addObject($group_name, 6);
		$group = umiObjectsCollection::getInstance()->getObject($group_id);
		$group->setName($group_name);
		$group->setValue("name", $group_name);
		$group->commit();

		$this->save_perms($group_id);

		if($_REQUEST['exit_after_save']) {
			$this->redirect($this->pre_lang . "/admin/users/groups_list/");
		} else {
			$this->redirect($this->pre_lang . "/admin/users/group_edit/{$group_id}/");
		}
	}


	public function group_edit() {
		$this->sheets_set_active("groups_list");
		$object_id = (int) $_REQUEST['param0'];
		$this->load_forms("forms_admin.php");

		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
		cmsController::getInstance()->nav_arr[] = Array("%groups_list_new%", "/admin/users/groups_list/");

		$object = umiObjectsCollection::getInstance()->getObject($object_id);
		$name = $object->getName();

		cmsController::getInstance()->nav_arr[] = Array($name, "/admin/users/group_edit/$object_id/");


		if(system_is_allowed("users", "edit_group_do"))
			$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\"/>&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" /></p>";
		else
			$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\" disabled=\"yes\"/>&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" disabled=\"yes\" /></p>";



		$params['group_name'] = $name;
		$params['group_id'] = $object_id;
		$params['method'] = "group_edit_do";
		$params['submit'] = $submit;
		$params['new_unit'] = "false";

		$params['perms_form']= $this->choose_perms('group', $object_id);


		return $this->parse_form('add_group', $params);
	}


	public function group_edit_do() {
		$object_id = (int) $_REQUEST['param0'];
		$group_name = $_REQUEST['group_name'];


		$object = umiObjectsCollection::getInstance()->getObject($object_id);
		$object->setName($group_name);
		$object->setValue("name", $group_name);
		$object->commit();


		$this->save_perms($object_id);

		if($_REQUEST['exit_after_save']) {
			$this->redirect($this->pre_lang . "/admin/users/groups_list/");
		} else {
			$this->redirect($this->pre_lang . "/admin/users/group_edit/{$object_id}/");
		}
	}


	public function group_delete() {
		$object_id = (int) $_REQUEST['param0'];
		umiObjectsCollection::getInstance()->delObject($object_id);
		$this->redirect($this->pre_lang . "/admin/users/groups_list/");
	}





	public function add_user() {
		$this->sheets_set_active("groups_list");
		$this->load_forms("forms_admin.php");

		$preset_group_id = (int) $group_id = $_REQUEST['param0'];
		if(is_numeric($group_id)) {
			$group_object = umiObjectsCollection::getInstance()->getObject($group_id);
			$group_name = $group_object->getName();
		}
		
		if(!$group_id) {
			$group_id = "outgroup";
		}

		if($group_id == "all")
			$group_name = "%users_select_all%";
		if($group_id == "outgroup")
			$group_name = "%users_select_outgroup%";

		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array("%groups_list_new%", "/admin/users/groups_list/");
		cmsController::getInstance()->nav_arr[] = Array($group_name, "/admin/users/users_list/$group_id/");
		cmsController::getInstance()->nav_arr[] = Array("%users_new_user%", "/admin/users/add_user/$group_id/");


		if(system_is_allowed("users", "add_user_do"))
			$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\"/>&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" /></p>";
		else
			$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\" disabled=\"yes\" />&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" disabled=\"yes\" /></p>";

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");
		$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

		if(cmsController::getInstance()->getModule('data')) {
			$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($object_type_id);
		}


		$params['save_n_save'] = $submit;
		$params['s_gr_perms'] = $s_gr_perms;
		$params['s_groups'] = $s_groups;
		$params['is_active'] = 1;
		$params['acf_add'] = "acf_inputs_test[acf_inputs_test.length] = Array('upassword', 'Enter password');\r\n";
		$params['perms_form'] = $this->choose_perms('user');
		$params['method'] = "add_user_do";
		$params['new_unit'] = "true";



		$s_gr_perms = "genetic = new Array();\r\n";
		$s_groups = "s_groups = new Array();\r\n";

		$list_groups = "";

		$group_field_id = $object_type->getFieldId('groups');
		$field = umiFieldsCollection::getInstance()->getField($group_field_id);
		$guide_id = $field->getGuideId();
		$guide_items = umiObjectsCollection::getInstance()->getGuidedItems($guide_id);
		$i = 0;
		foreach($guide_items as $group_id => $group_name) {
			$is_selected = ($group_id == $preset_group_id) ? "1" : "";

			$list_groups .= <<<END
	<p>
		<checkbox selected="{$is_selected}">
			<id><![CDATA[gr_{$group_id}]]></id>
			<name><![CDATA[groups[{$i}]]]></name>
			<title><![CDATA[{$group_name}]]></title>
			<value><![CDATA[{$group_id}]]></value>

			<onclick><![CDATA[javascript: sel_group(this);]]></onclick>
		</checkbox>
	</p>

END;

			$s_groups .= "s_groups[s_groups.length] = {$group_id};\r\n";

			$sql = "SELECT * FROM cms_permissions WHERE owner_id = '{$group_id}' AND allow='1'";
			$result = mysql_query($sql);
			$s_gr_perms .= "genetic[{$group_id}] = new Array();\r\n";

			while($row = mysql_fetch_array($result)) {
				$s_gr_perms .= "genetic[{$group_id}][genetic[{$group_id}].length] = new Array('{$row['module']}', '{$row['method']}', '{$row1['allow']}');\r\n";
			}
		}
		$params['list_groups'] = $list_groups;
		$params['s_gr_perms'] = $s_gr_perms;
		$params['s_groups'] = $s_groups;
		$params['is_in_groups'] = (is_numeric($group_id)) ? "true" : "false";


		return $this->parse_form('add_user', $params);
	}



	public function add_user_do() {
		$login = $_POST['login'];
		$password = $_POST['password'];
		$groups = $_REQUEST['groups'];
		$is_active = (int) $_REQUEST['is_active'];

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");

		$object_id = umiObjectsCollection::getInstance()->addObject($login, $object_type_id);
		$object = umiObjectsCollection::getInstance()->getObject($object_id);
		$object->setValue("login", $login);
		if($password) {
		    $password = umiObjectProperty::filterInputString($password);
		    $object->setValue("password", md5($password));
		}

		$object->setValue("groups", $groups);
		$object->setValue("is_activated", $is_active);

		if(cmsController::getInstance()->getModule('data')) {
			cmsController::getInstance()->getModule('data')->saveEditedGroups($object_id, true);
		}

		$this->save_perms($object_id);

		if($_REQUEST['exit_after_save'])
			$this->redirect($this->pre_lang . "/admin/users/users_list/#user{$object_id}");
		else
			$this->redirect($this->pre_lang . "/admin/users/user_edit/{$object_id}/");
	}

	public function user_delete() {
		$user_id = (int) $_REQUEST['param0'];
		umiObjectsCollection::getInstance()->delObject($user_id);
		$this->redirect($this->pre_lang . "/admin/users/users_list/");
	}


	public function user_edit() {
		$this->load_forms("forms_admin.php");
		$this->sheets_set_active("groups_list");


		$user_id = (int) $_REQUEST['param0'];
		$user_object = umiObjectsCollection::getInstance()->getObject($user_id);
		$user_name = $user_object->getName();

		$group_id = $_REQUEST['param1'];

		if(is_numeric($group_id)) {
			$group_object = umiObjectsCollection::getInstance()->getObject($group_id);
			$group_name = $group_object->getName();
		}
		
		if($group_id == "all" || !$group_id) $group_name = "%users_select_all%";
		if($group_id == "outgroup") $group_name = "%users_select_outgroup%";



		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
		cmsController::getInstance()->nav_arr[] = Array("%groups_list_new%", "/admin/users/groups_list/");
		cmsController::getInstance()->nav_arr[] = Array($group_name, "/admin/users/users_list/$group_id/");
		cmsController::getInstance()->nav_arr[] = Array($user_name, "/admin/users/user_edit/$user_id/$group_id/");


		if(system_is_allowed("users", "edit_user_do"))
			$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\"/>&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" /></p>";
		else
			$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\" disabled=\"yes\" />&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" disabled=\"yes\" /></p>";

		$params['login'] = $user_object->getValue("login");
		$params['password'] = "";
		$params['is_active'] = $user_object->getValue("is_activated");

		if(cmsController::getInstance()->getModule('data')) {
			$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($user_object->getTypeId(), $user_id, true);
		}


		$groups = $user_object->getPropByName("groups")->getValue();

		$params['is_in_groups'] = (sizeof($groups))  ? "true" : "false";


		$s_gr_perms = "genetic = new Array();\r\n";
		$s_groups = "s_groups = new Array();\r\n";

		$list_groups = "";

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");
		$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

		$group_field_id = $object_type->getFieldId('groups');

		$field = umiFieldsCollection::getInstance()->getField($group_field_id);
		$guide_id = $field->getGuideId();
		$guide_items = umiObjectsCollection::getInstance()->getGuidedItems($guide_id);
		$i = 0;
		foreach($guide_items as $guide_group_id => $group_name) {
			$is_selected = (in_array($guide_group_id, $groups) ? 1 : "");

			$list_groups .= <<<END
	<p>
		<checkbox selected="{$is_selected}">
			<id><![CDATA[gr_{$guide_group_id}]]></id>
			<name><![CDATA[groups[{$i}]]]></name>
			<title><![CDATA[{$group_name}]]></title>
			<value><![CDATA[{$guide_group_id}]]></value>

			<onclick><![CDATA[javascript: sel_group(this);]]></onclick>
		</checkbox>
	</p>

END;
			$i++;

			$s_groups .= "s_groups[s_groups.length] = {$guide_group_id};\r\n";

			$sql = "SELECT * FROM cms_permissions WHERE owner_id = '{$guide_group_id}' AND allow='1'";
			$result = mysql_query($sql);
			$s_gr_perms .= "genetic[{$guide_group_id}] = new Array();\r\n";

			while($row = mysql_fetch_array($result)) {
				$s_gr_perms .= "genetic[{$guide_group_id}][genetic[{$guide_group_id}].length] = new Array('{$row['module']}', '{$row['method']}', '{$row1['allow']}');\r\n";
			}
		}


		$params['list_groups'] = $list_groups;
		$params['s_gr_perms'] = $s_gr_perms;
		$params['s_groups'] = $s_groups;


		$params['perms_form']= $this->choose_perms('user', $user_id);


		$params['save_n_save'] = $submit;
		$params['method'] = "edit_user_do";
		$params['user_id'] = $user_id;
		$params['group_id'] = $group_id;
		$params['new_unit'] = "true";
		return $this->parse_form('add_user', $params);
	}

	public function edit_user_do() {
		$user_id = (int) $_REQUEST['param0'];
		$group_id = $_REQUEST['param1'];

		$login = $_POST['login'];
		$password = $_POST['password'];
		$is_active = (int) $_POST['is_active'];
		$groups = $_REQUEST['groups'];


		$object = umiObjectsCollection::getInstance()->getObject($user_id);
		$object->setName($login);
		$object->setValue("login", $login);
		
		if($password) {
		    $password = umiObjectProperty::filterInputString($password);
    		    $object->setValue("password", md5($password));
		}

		$object->setValue("groups", $groups);
		$object->setValue("is_activated", $is_active);

		if(cmsController::getInstance()->getModule('data')) {
			cmsController::getInstance()->getModule('data')->saveEditedGroups($user_id, true);
		}

		$this->save_perms($user_id);

//		if($backup_inst = cmsController::getInstance()->getModule("backup")) {
//			$backup_inst->backup_save("users", "edit_user_do", $user_id);
//		}


		if($_REQUEST['exit_after_save'])
			$this->redirect($this->pre_lang . "/admin/users/users_list/" . $group_id . "/#user$user_id");
		else
			$this->redirect($this->pre_lang . "/admin/users/user_edit/" . $user_id . "/" . $group_id . "/");
	}



	public function users_list() {
		$params = Array();
		$this->load_forms("forms_admin.php");
		

		if(cmsController::getInstance()->getCurrentMethod() == "users_list") {
			$this->sheets_set_active("groups_list");
		}


		$group_id = $_REQUEST['param0'];

		if(is_numeric($group_id)) {
			$group_object = umiObjectsCollection::getInstance()->getObject($group_id);
			$group_name = $group_object->getName();
		}
		
		if($group_id == "all" || !$group_id) $group_name = "%users_select_all%";
		if($group_id == "outgroup") $group_name = "%users_select_outgroup%";


		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array("%groups_list_new%", "/admin/users/groups_list/");
		cmsController::getInstance()->nav_arr[] = Array($group_name, "/admin/users/groups_list/$filter/"); // WTF is $filter?


		$curr_page = (int) $_REQUEST['p'];
		$per_page = 50;

		$type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");

		$sel = new umiSelection;
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);
		
		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);

		$group_field_id = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldId("groups");
		$login_field_id = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldId("login");
		$sel->setPropertyFilter();
		if(is_numeric($group_id)) {
			$sel->addPropertyFilterEqual($group_field_id, $group_id);
		}

		if($group_id == "outgroup") {
			$sel->addPropertyFilterIsNull($group_field_id);
		}
		
		$sel->setOrderFilter();
		$sel->setOrderByName();
		
		if($user_search) {
			$sel->addPropertyFilterLike($login_field_id, $user_search);
			$params['user_search'] = $user_search;
		}
		
		$result = umiSelectionsParser::runSelection($sel);
		$total = umiSelectionsParser::runSelectionCounts($sel);
		
		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page, "p", Array("user_search" => $user_search));

		$sz = sizeof($result);
		for($i = 0; $i < $sz; $i++) {
			$user_id = $result[$i];

			$user_object = umiObjectsCollection::getInstance()->getObject($user_id);
			$is_active = $user_object->getValue("is_activated");
			$blocking = "";
			$group = (int) $group_id;
			if($is_active) {
				$blocking = <<<END
					<a href="%pre_lang%/admin/users/user_blocking/{$group}/{$user_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
			} else {
				$blocking = <<<END
					<a href="%pre_lang%/admin/users/user_blocking/{$group}/{$user_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
			}

			$user_info = $this->get_user_info($user_id, "<b><![CDATA[%login%]]></b><br/><![CDATA[%lname% %fname% %father_name%]]>");


			$del_link = <<<END
	<a href='%pre_lang%/admin/users/user_delete/{$user_id}/{$group_id}/' commit_unrestorable="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="%delete%" title="%delete%" /></a>
END;


			$in_groups = "";
			$user_groups = $user_object->getPropByName("groups")->getValue();
			$sz1 = sizeof($user_groups);
			for($j = 0; $j < $sz1; $j++) {
				$user_group_id = $user_groups[$j];

				$user_group_object = umiObjectsCollection::getInstance()->getObject($user_group_id);
				$in_groups .=  "<a href=\"%pre_lang%/admin/users/group_edit/{$user_group_id}\">" . $user_group_object->getName() . "</a>";
				if($sz1 > ($j + 1))  $in_groups .= ", ";
			}


			$users_list .= <<<END

	<row>
		<col style="padding: 10px; width: 310px;">

			<table width="100%" border="0">
				<tr>
					<td style="vertical-align: middle; text-align: center; width: 25px;">
						<img src="/images/cms/admin/%skin_path%/ico_user.%ico_ext%" />
					</td>

					<td valign="top" style="font-size: 11px; font-family: Tahoma;">
						<a href='%pre_lang%/admin/users/user_edit/{$user_id}/{$group_id}/'>{$user_info}</a>
					</td>
				</tr>
			</table>

		</col>
		<col style="padding: 10px;">$in_groups</col>
		<col style="text-align: center;">{$blocking}</col>
		<col style="width: 100px; text-align: center;"><a href='%pre_lang%/admin/users/user_edit/{$user_id}/{$group_id}/'><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" border="0" alt="Редактировать" title="Редактировать" /></a></col>
		<col style="padding: 10px; width: 100px" align="center">
			{$del_link}
		</col>
	</row>

END;
		}

		$this->sheets_reset();
		$this->sheets_add("%core_users_sheets_users%", "users_list_all");
		$this->sheets_add("%core_users_sheets_groups%", "groups_list");


		$params['group_id'] = $group_id;
		$params['users_list'] = $users_list;
		return $this->parse_form("users_list_new" , $params);
	}


	public function groups_list() {
		$res = "";
		$this->load_forms("forms_admin.php");

		$groups_list = "";

		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array("%groups_list_new%", "/admin/users/groups_list/");

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "users");

		$name_field_id = umiObjectTypesCollection::getInstance()->getType($object_type_id)->getFieldId("name");


		$sel = new umiSelection;

		$sel->setPropertyFilter();

		if($group_search) {
			$sel->addPropertyFilterLike($name_field_id, $group_search);
			$params['group_search'] = $group_search;
		}


		$sel->setLimitFilter();
		$sel->addLimit(50);

		$sel->setObjectTypeFilter();
		$sel->addObjectType($object_type_id);
		$result = umiSelectionsParser::runSelection($sel);


		$sz = sizeof($result);
		for($i = 0; $i < $sz; $i++) {
			$object_id = $result[$i];
			$object = umiObjectsCollection::getInstance()->getObject($object_id);

			$name = $object->getName();



			$sel = new umiSelection;
			$type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");

			$sel->setObjectTypeFilter();
			$sel->addObjectType($type_id);

			$group_field_id = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldId("groups");
			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($group_field_id, $object_id);
			$users_count = umiSelectionsParser::runSelectionCounts($sel);


			if($group_id != 1) {
				$group_icon = '<img src="/images/cms/admin/%skin_path%/ico_group.%ico_ext%" />';
				$del_link = <<<END
<a href='%pre_lang%/admin/users/group_delete/{$object_id}/' commit_unrestorable="Вы уверены?">
<img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="%users_group_del%" title="%users_group_del%" border="1" />
</a>

END;
			} else {
				$del_link = "";
				$group_icon = '<img src="/images/cms/admin/%skin_path%/ico_group.%ico_ext%" height="16" width="16" />';
			}

			$groups_list .= <<<END

	<row>
		<col style="width: 70%;">

<table border="0" width="100%">
 <tr>
  <td width="25" style="text-align: center; vertical-align: middle;">
   $group_icon
  </td>
  <td style="font-family: Tahoma; font-size: 11px; vertical-align: middle;">
   <a href='%pre_lang%/admin/users/users_list/{$object_id}/' title='%users_group_view%'>{$name} ({$users_count})</a>
  </td>
 </tr>
</table>

		</col>
		<col style="text-align: center"><a href='%pre_lang%/admin/users/group_edit/{$object_id}/'><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="%users_group_edit%" title="%users_group_edit%" border="0" /></a></col>
		<col style="text-align: center">$del_link</col>
	</row>

END;


		}


		if($sz == 0) {
			$groups_list = <<<END
	<row>
		<col colspan="3">
			<p align="center">
				<![CDATA[%users_no_groups_found%]]>
			</p>
			<br />
		</col>
	</row>


END;
		}

		$sel = new umiSelection;

		$type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");

		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);

		$group_field_id = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldId("groups");
		$sel->setPropertyFilter();

		$sel->addPropertyFilterIsNull($group_field_id);
		$out_num = umiSelectionsParser::runSelectionCounts($sel);


		$sel = new umiSelection;
		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);
		$total_num = umiSelectionsParser::runSelectionCounts($sel);

		$params = Array();
		$params['groups_list'] = $groups_list;
		$params['out_num'] = $out_num;
		$params['total_num'] = $total_num;
		$params['group_search'] = $group_search;

		$res = $this->parse_form("groups_list_new", $params);


		return $res;
	}


	public function users_list_all() {
		return $this->users_list();
	}

	public function json_change_dock() {
		$s_dock_panel = $_REQUEST['dock_panel'];
		if ($o_users = cmsController::getInstance()->getModule("users")) {
			$i_user_id = $o_users->user_id;
			$o_user = umiObjectsCollection::getInstance()->getObject($i_user_id);
			if ($o_user) {
				$o_user->setValue("user_dock", $s_dock_panel);
			}
		}
		header('HTTP/1.1 200 OK');
		header("Cache-Control: public, must-revalidate");
		header("Pragma: no-cache");
		header('Date: ' . date("D M j G:i:s T Y"));
		header('Last-Modified: ' . date("D M j G:i:s T Y"));
		header ("Content-type: text/javascript");
		exit();
	}

	public function getFavourites() {
		$i_user_id = $_REQUEST['param0'];
		$o_user = umiObjectsCollection::getInstance()->getObject($i_user_id);

		$s_favorites = "";
		$s_user_dock = "";
		if ($o_user) {
			$s_dock = (string) $o_user->getValue("user_dock");
			$arr_dock = explode(",", $s_dock);
			for ($i = 0; $i < count($arr_dock); $i++) {
				if (strlen($arr_dock[$i])) {
					$s_user_dock .= "<ditem id=\"".$arr_dock[$i]."\" />";
				}
			}
		}
		
		header('HTTP/1.1 200 OK');
		header("Cache-Control: public, must-revalidate");
		header("Pragma: no-cache");
		header('Date: ' . date("D M j G:i:s T Y"));
		header('Last-Modified: ' . date("D M j G:i:s T Y"));
		header('Content-type: text/xml');

		// default user dock
		/*
		if (!strlen($s_user_dock)) {
			$s_user_dock = <<<END
				<ditem id="content" />
				<ditem id="data" />
				<ditem id="news" />
END;
		}
*/
		$s_favorites = <<<END
<?xml version="1.0" encoding="utf-8"?>
	<ufavorites>
		<udock>
			{$s_user_dock}
		</udock>
	</ufavorites>
END;
		echo $s_favorites;
		exit();
	}
};


?>