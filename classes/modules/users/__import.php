<?php

abstract class __imp__users {



	public function get_perm_panel($module_name, $method_view, $method_edit, $element_id = false) {
		$res = "";
		$this->load_forms("import_forms.php");


                $is_all_read_groups = 1;
                $is_all_edit_groups = 1;
                $is_all_read_users = 1;
                $is_all_edit_users = 1;

		$perms_users = "";

		$type_id = 4;

		$sel = new umiSelection;
		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);
		
		$group_field_id = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldId("groups");
		$sel->setPropertyFilter();
		$sel->addPropertyFilterIsNull($group_field_id);
		$result = umiSelectionsParser::runSelection($sel);

		$geneitc_arr = "";
		$sz = sizeof($result);
		for($i = 0; $i < $sz; $i++) {
			$user_id = $result[$i];

			$user = umiObjectsCollection::getInstance()->getObject($user_id);
			$groups = $user->getValue("groups");

			if(sizeof($groups) > 0) continue;

			$login = $user->getName();


			$genetic_arr .= "genetic_arr[{$user_id}] = \"0\";\r\n";

			if($element_id) {
				list($r, $e) = cmsController::getInstance()->getModule('users')->isAllowedObject($user_id, $element_id);
			} else {
				$r = cmsController::getInstance()->getModule('users')->isAllowedMethod($user_id, $module_name, $method_view);
				$e = cmsController::getInstance()->getModule('users')->isAllowedMethod($user_id, $module_name, $method_edit);
			}

			if(!$r)
				$is_all_read_users = 0;
			if(!$e)
				$is_all_edit_users = 0;



			$perms_users .= "
 <row>
  <col style='vertical-align: middle'>{$login}</col>
  <col style='text-align: center; vertical-align: middle;'>
  	<checkbox selected='{$r}'>
			<id><![CDATA[pur{$user_id}]]></id>
			<name><![CDATA[perms_users_read[{$user_id}]]]></name>
			<onclick><![CDATA[javascript:perm_switchu(\"read\", this)]]></onclick>
			<value><![CDATA[1]]></value>
	</checkbox>

  </col>
  <col style='text-align: center; vertical-align: middle;'>
  	<checkbox selected='{$e}'>
			<id><![CDATA[pue{$user_id}]]></id>
			<name><![CDATA[perms_users_edit[{$user_id}]]]></name>
			<onclick><![CDATA[javascript:perm_switchu(\"edit\", this)]]></onclick>
			<value><![CDATA[1]]></value>
	</checkbox>

  </col>
 </row>
";


		}






		$perms_groups = "";

		$sel = new umiSelection;
		$sel->setObjectTypeFilter();
		$sel->addObjectType(6);
		$result = umiSelectionsParser::runSelection($sel);

		$gr_arr = "";
		$i = 0;
		foreach($result as $group_id) {
			$group = umiObjectsCollection::getInstance()->getObject($group_id);
			$group_name = $group->getName();

			$gr_arr .= "groups_arr[{$i}] = {$group_id};\r\n";
			++$i;


			if($element_id) {
				list($r, $e) = cmsController::getInstance()->getModule('users')->isAllowedObject($group_id, $element_id);
			} else {
				$r = cmsController::getInstance()->getModule('users')->isAllowedMethod($group_id, $module_name, $method_view);
				$e = cmsController::getInstance()->getModule('users')->isAllowedMethod($group_id, $module_name, $method_edit);
			}

			if(!$r) {
				$is_all_read_groups = 0;
			}

			if(!$e) {
				$is_all_edit_groups = 0;
			}

			$perms_groups .= "
 <row>
  <col style='vertical-align: middle'>" . $group_name . "</col>
  <col style='text-align: center; vertical-align: middle;'>
  	<checkbox selected='{$r}'>
			<id><![CDATA[pgr{$group_id}]]></id>
			<name><![CDATA[perms_groups_read[{$group_id}]]]></name>
			<onclick><![CDATA[javascript:perm_switch(\"read\", this)]]></onclick>
			<value><![CDATA[1]]></value>
	</checkbox>
  </col>
  <col style='text-align: center; vertical-align: middle;'>
  	<checkbox selected='{$e}'>
			<id><![CDATA[pge{$group_id}]]></id>
			<name><![CDATA[perms_groups_edit[{$group_id}]]]></name>
			<onclick><![CDATA[javascript:perm_switch(\"edit\", this)]]></onclick>
			<value><![CDATA[1]]></value>
	</checkbox>

  </col>
 </row>
";
		}


		$params['perms_users'] = $perms_users;
		$params['perms_groups'] = $perms_groups;
		$params['genetic_arr'] = $genetic_arr;
		$params['gr_arr'] = $gr_arr;

		$params['is_all_read_groups'] = ($is_all_read_groups) ? "1" : "";
		$params['is_all_edit_groups'] = ($is_all_edit_groups) ? "1" : "";
		$params['is_all_read_users'] = ($is_all_read_users) ? "1" : "";
		$params['is_all_edit_users'] = ($is_all_edit_users) ? "1" : "";

                $params['pre_lang'] = $_REQUEST['pre_lang'];

		$params['save_n_save'] =  $this->CMS_ENV['flud']['save_n_save'];

		$res = $this->parse_form("perm_panel", $params);

		return $res;
	}





	public function setPerms($element_id) {
		$perms_groups_read = $_REQUEST['perms_groups_read'];
		$perms_groups_edit = $_REQUEST['perms_groups_edit'];
		$perms_users_read = $_REQUEST['perms_users_read'];
		$perms_users_edit = $_REQUEST['perms_users_edit'];

		mysql_query("SET AUTOCOMMIT=0");

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}


		$sql = "DELETE FROM cms3_permissions WHERE rel_id = '{$element_id}'";
		mysql_query($sql);

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}

		foreach($perms_groups_read as $group_id => $allow) {
			$level = 1;
			if($perms_groups_edit[$group_id]) $level = 2;

			$sql = "INSERT INTO cms3_permissions (level, owner_id, rel_id) VALUES('{$level}', '{$group_id}', '{$element_id}')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
			}
		}

		foreach($perms_users_read as $user_id => $allow) {
			$level = 1;
			if($perms_users_edit[$user_id]) $level = 2;

			$sql = "INSERT INTO cms3_permissions (level, owner_id, rel_id) VALUES('{$level}', '{$user_id}', '{$element_id}')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
			}
		}


		mysql_query("COMMIT");

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}

		mysql_query("SET AUTOCOMMIT=1");

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}
	}



	public function choose_perms($select = "group", $id = 0) {
		$res = "";


		$res .= "
<tablegroup from=\"no\">
 <header>
  <hcol><![CDATA[Модуль]]></hcol>
  <hcol><![CDATA[Права на использование]]></hcol>
  <hcol><![CDATA[Прочие права]]></hcol>
 </header>";

		$regedit = regedit::getInstance();
		$modules_arr = $regedit->getList("//modules");
		
		if(!$id) $id = 2373;


		$content_domains = "";
		$domains = domainsCollection::getInstance()->getList();
		foreach($domains as $domain_id => $domain) {
			$dom_name = $domain->getHost();
			$cname = str_replace(".", "_", $dom_name);

			$is_allowed = $this->isAllowedMethod($id, "content", $cname);

			$content_domains .= <<<END

<p>
	<checkbox selected='{$is_allowed}'>
			<id><![CDATA[content__{$cname}]]></id>
			<name><![CDATA[content[{$cname}]]]></name>
			<value><![CDATA[1]]></value>
	</checkbox>
 	{$dom_name}</p>

END;
		}

		$content_domains .= "<br/>";





		foreach($modules_arr as $md) {
			$module = $md[0];	// - имя модуля

			$func_list = $regedit->getList("//modules/" . $module . "/func_perms");	//листаем ф-ии, на которые надо расставить права

			if(!system_is_allowed($module)) continue;

			$module_name = cmsController::getInstance()->langs[$module]['module_name'];

			if(is_array($func_list)) {
			$ico = $regedit->getVal("//modules/" . $module . "/ico");

			$ico .= "." . ICO_EXT;

			$res .= <<<ROW
<row>
<col style="vertical-align: top;">
	<table cellspacing='0' cellpadding='0' border='0' width='100%'>
		<tr>
			<td>
				<img src="/images/cms/admin/%skin_path%/{$ico}" />
			</td>

			<td style='vertical-align: middle; padding-left: 5px; text-align: left; width: 100%;'>
				<span class="permissionsModuleName">
					{$module_name}
				</span>
			</td>
		</tr>
	</table>
</col>

ROW;


			$is_allowed = $this->isAllowedModule($id, $module);

			$res .= <<<COL
<col align="center">
	<passthru name="ps_m_perms[{$module}]">{$module}</passthru>
	<checkbox selected='{$is_allowed}'>
			<id><![CDATA[{$module}__]]></id>
			<name><![CDATA[m_perms[]]]></name>
			<value><![CDATA[{$module}]]></value>
	</checkbox>

</col>

COL;

			$res .= "<col align=\"left\">\n";


			if($module == "content") $res .= $content_domains;

				foreach($func_list as $func) {
					$f = $func[0];
					$d = $func[1];

					if(!system_is_allowed($module, $f)) continue;

					$is_allowed = $this->isAllowedMethod($id, $module, $f);

					$res .= <<<ROW
<p>
	<checkbox selected='{$is_allowed}'>
			<id><![CDATA[{$module}__{$f}]]></id>
			<name><![CDATA[{$module}[{$f}]]]></name>
			<value><![CDATA[1]]></value>
	</checkbox>
	{$d}
</p>

ROW;


				}

			$res .= <<<ROW
	</col>
</row>
ROW;

			}

		}


		$res .= "</tablegroup>";

		return $res;
	}


	public function save_perms($owner_id) {
		$owner = $this->getOwnerType($owner_id);

		mysql_query("SET AUTOCOMMIT=0");

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}

		$sql = "DELETE FROM cms_permissions WHERE owner_id = '{$owner_id}'";
		mysql_query($sql);

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}



		foreach($_REQUEST['ps_m_perms'] as $module => $nl) {
			if(in_array($module, $_REQUEST['m_perms'])) {
				$sql = "INSERT INTO cms_permissions (module, owner_id, allow) VALUES('{$module}', '{$owner_id}', 1)";
				mysql_query($sql);
			}

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
			}

			foreach($_REQUEST[$module] as $method => $is_allowed) {
				$sql = "INSERT INTO cms_permissions (module, method, owner_id, allow) VALUES('{$module}', '{$method}', '{$owner_id}', 1)";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
				}


				$mod_subfuncs = regedit::getInstance()->getList("//modules/{$module}/func_perms/{$method}");

				if(is_array($mod_subfuncs)) {
					foreach($mod_subfuncs as $subfunc) {
						$sub_method = $subfunc[0];

						if(!$sub_method || $sub_method == 'NULL') continue;

						$sql = "INSERT INTO cms_permissions (module, method, owner_id, allow) VALUES('{$module}', '{$sub_method}', '{$owner_id}', 1)";
						mysql_query($sql);

						if($err = mysql_error()) {
							trigger_error($err, E_USER_WARNING);
						}
					}
				}

			}
		}

		mysql_query("COMMIT");

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}

		mysql_query("SET AUTOCOMMIT=1");

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}
	}

};

?>