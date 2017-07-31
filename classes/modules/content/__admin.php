<?php

abstract class __content {

	public function sitetree() {
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array("%content_sitetree%", "/admin/content/");

		$regedit = cmsController::getInstance();

		if($_REQUEST['direction'])
			$this->move_page();

		$res = "";

		$domains = domainsCollection::getInstance()->getList();


		if(is_array($domains)) {
			foreach($domains as $domain) {
				if(system_is_allowed("content", str_replace(".", "_", $domain->getHost()))) {
					$domainName = $domain->getHost();
					$res .= <<<END
	<contentTree>
		<domainName>{$domainName}</domainName>
		<preLang><![CDATA[{$_REQUEST['pre_lang']}]]></preLang>
	</contentTree>

END;
				}
			}
		}

		$this->load_forms();
		$params = Array("all_domains" => $res);
		$res = $this->parse_form("sitetree", $params);

		return $res;
	}



	public function parse_site_tree($tree, $level = 0) {
		$res = "";

		$i = 0;
		foreach($tree as $element_id => $childs) {
			$i++;

			$element = umiHierarchy::getInstance()->getElement($element_id);

			$element_name = $element->getObject()->getName();
			$element_rel = $element->getParentId();

			//
			if($inc == 1)			$place = " place='first'";
			if(sizeof($src) == $inc)	$place = " place='last'";
			if(sizeof($src) == 1)		$place = " place='first_n_last'";

			//
			$has_childs = (sizeof($childs)) ? ' children="ya"' : "";

			//
			$bg = dechex(15856887 - (920328 * $level));

			$element_name = str_replace("\"", "", $element_name);

			$res .= <<<PAGE

<page	title="{$element_name}" id="{$element_id}" rel="{$element_rel}"
	b="{$bg}" pre_lang="{$this->pre_lang}"
	{$has_childs} {$place}>

PAGE;

			if(sizeof($childs)) {
				$res .= $this->parse_site_tree($childs, ($level + 1));
			}

			$res .= "</page>\n";
		}

		return $res;
	}






	public function add_page() {
		if(defined("CURRENT_VERSION_LINE")) {
			if(CURRENT_VERSION_LINE == "free") {
				$count = umiHierarchy::getInstance()->getElementsCount("content");

				if($count >= 10) {
					return "%error_free_max_pages%";
				}
			}
		}


		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
		cmsController::getInstance()->nav_arr[] = Array("%content_sitetree%", "/admin/content/");
		cmsController::getInstance()->nav_arr[] = Array("%content_newpage%", "/admin/content/");

		$lang_prefix = cmsController::getInstance()->getCurrentLang()->getPrefix();
		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
		$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

		$parent = (int) $_REQUEST['parent'];

		$params = Array();
		$params['pid'] = $parent;
		$params['method'] = "add_page_do";

		if(system_is_allowed("content", "edit_page", $parent))
			$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и посмотреть" onclick="javascript: return save_with_redirect();" /> <submit title="Добавить и выйти" onclick="javascript: return save_with_exit();" /> <submit title="Добавить" onclick="javascript: return save_without_exit();" />';
		else
			$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и посмотреть" onclick="javascript: return save_with_redirect();" disabled="yes"/> <submit title="Добавить и выйти" onclick="javascript: return save_with_exit();" disabled="yes"/> <submit title="Добавить" onclick="javascript: return save_with_exit();" disabled="yes"/>';

		$this->CMS_ENV['flud']['save_n_save'] = $params['save_n_save'];

		if(!is_numeric($_REQUEST['parent'])) {
			$domain_id = domainsCollection::getInstance()->getDomainId($_REQUEST['parent']);
		} else {
			$parent_element = umiHierarchy::getInstance()->getElement($_REQUEST['parent']);
			$domain_id = $parent_element->getDomainId();
		}


		//получим настройки род. страницы, если она существует
		if($parent != 0) {
		} else {
			$pkey = regedit::getInstance()->getVal("//domains/" . $_REQUEST['target_domain'] . "/keywords_" . $lang_prefix);
			$pdesc = regedit::getInstance()->getVal("//domains/" . $_REQUEST['target_domain'] . "/describtion_" . $lang_prefix);
		}

		$params['is_active'] = 1;
		$params['is_visible'] = 1;
		$params['show_submenu'] = 1;
		$params['meta'] = $pkey;
		$params['describtion'] = $pdesc;
		$params['h1'] = "";
		$params['tags'] = "";

		$templates = "";

		$templates_list = templatesCollection::getInstance()->getTemplatesList($domain_id, $lang_id);
		foreach($templates_list as $template) {
			$tpl_id = $template->getId();
			$tpl_name = $tpl_name = $template->getTitle();

			if($template->getIsDefault())
				$selected = " selected=\"yes\"";
			else
				$selected = "";

			$templates .= "<item $selected>
			<title><![CDATA[$tpl_name]]></title>
			<value><![CDATA[$tpl_id]]></value>
		</item>";
		}

		$params['templates'] = $templates;


		$params['pre_lang'] = $_REQUEST['pre_lang'];

		if($parent == 0) {
			$params['domain'] = $_REQUEST['parent'];
		} else {
			$params['domain'] = "";
		}

		$menu_ua_images = new cifi("menu_ua", "./images/cms/menu/");
		$menu_a_images = new cifi("menu_a", "./images/cms/menu/");
		$headers_images = new cifi("headers", "./images/cms/headers/");

		$params['cifi_menu_ua'] = $menu_ua_images->make_div() . $menu_ua_images->make_element();
		$params['cifi_menu_a'] = $menu_a_images->make_div() . $menu_a_images->make_element();
		$params['cifi_headers'] = $headers_images->make_div() . $headers_images->make_element();


		if($users_inst = cmsController::getInstance()->getModule("users")) {
			$params['perm_panel'] = $users_inst->get_perm_panel("content", "content", "edit_page");
		}

		//if($users_inst = cmsController::getInstance()->getModule("filemanager")) {
		//	$perm_panel = $users_inst->upl_files("images/cms/content/");
		//	$params['files_panel'] = $perm_panel;
		//}

		$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("content");
		$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
		$params['object_types'] = putSelectBox_assoc($object_types, 0, false);


		$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/content/";

		$this->load_forms();
		return $this->parse_form('add_page', $params);
	}

	public function add_page_do() {
		global $HTTP_POST_FILES;

		if(defined("CURRENT_VERSION_LINE")) {
			if(CURRENT_VERSION_LINE == "free") {
				$count = umiHierarchy::getInstance()->getElementsCount("content");

				if($count >= 10) {
					return "%error_free_max_pages%";
				}
			}
		}


		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("content")->getId();
		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
		$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

		$parent_id = (int) $_REQUEST['param0'];

		$name = $_REQUEST['name'];
		$title = $_REQUEST['title'];
		$meta_keywords = $_REQUEST['meta_keywords'];
		$meta_descriptions = $_REQUEST['meta_descriptions'];
		$content = $_REQUEST['content'];
		$alt_name = $_REQUEST['alt_name'];
		$h1 = $_REQUEST['h1'];
		$tpl_id = (int) $_REQUEST['tpl'];

		$is_visible = (bool) $_REQUEST['is_visible'];
		$is_default = (bool) $_REQUEST['def'];
		$show_submenu = (bool) $_REQUEST['show_submenu'];
		$expanded = (bool) $_REQUEST['expanded'];
		$unindexed = (bool) $_REQUEST['unindexed'];
		$index_item = (bool) $_REQUEST['index_item'];
		$robots_deny = (bool) $_REQUEST['robots_deny'];
		$tags = (string) $_REQUEST['tags'];
		$object_type_id = (int) $_REQUEST['object_type_id'];

		$is_active = (bool) $_REQUEST['is_active'];

		if(!$_REQUEST['param0']) {
			$domain_id = domainsCollection::getInstance()->getDomainId($_REQUEST['target_domain']);
		} else {
			$parent_element = umiHierarchy::getInstance()->getElement($_REQUEST['param0']);
			$domain_id = $parent_element->getDomainId();
		}



		$hierarchy = umiHierarchy::getInstance();
		$element_id = $hierarchy->addElement($parent_id, $hierarchy_type_id, $name, $alt_name, $object_type_id, $domain_id, $lang_id, $tpl_id);

		if($users_inst = cmsController::getInstance()->getModule("users")) {
			$users_inst->setPerms($element_id);
		}

		$element = $hierarchy->getElement($element_id);

		$element->setIsVisible($is_visible);
		$element->setIsActive($is_active);
		$element->setIsDefault($is_default);

		$element->setValue("h1", $h1);
		$element->setValue("title", $title);

		$element->setValue("meta_keywords", $meta_keywords);
		$element->setValue("meta_descriptions", $meta_descriptions);
		$element->setValue("content", $content);

		$element->setValue("robots_deny", $robots_deny);
		$element->setValue("tags", $tags);
		$element->setValue("show_submenu", $show_submenu);
		$element->setValue("is_expanded", $expanded);
		$element->setValue("is_unindexed", $unindexed);

		$element->commit();

		if($_REQUEST['exit_after_save'] == 2) {
			$link = umiHierarchy::getInstance()->getPathById($element_id);
			$this->redirect($link);
		}
		if($_REQUEST['exit_after_save']) {
			$this->redirect($this->pre_lang . "/admin/content/");
		} else {
			$this->redirect($this->pre_lang . "/admin/content/edit_page/" . $element_id . "/");
		}
	}

	public function del_page() {
		$element_id = $_REQUEST['pid'];

		if(!system_is_allowed("content", "edit_page", $element_id)) {
			return "Нет прав на удаление этой страницы";
		}

		umiHierarchy::getInstance()->delElement($element_id);
		$this->redirect($this->pre_lang . "/admin/content/");

		return "Страница удалена";
	}


	public function edit_page() {
		$res = "";

		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
		cmsController::getInstance()->nav_arr[] = Array("%content_sitetree%", "/admin/content/");


		if(!system_is_allowed("content", "content", $_REQUEST['param0']))
			return "Конкретно эту страницу вам просматривать нельзя.";

		$params = Array();

		$element_id = $_REQUEST['param0'];


		$element = umiHierarchy::getInstance()->getElement($element_id);

		cmsController::getInstance()->nav_arr[] = Array($element->getObject()->getName(), "/admin/content/");

		$params['alt_name'] = 		$element->getAltName();
		$params['is_visible'] = 	$element->getIsVisible();
		$params['is_default'] = 	$element->getIsDefault();
		$params['name'] = 		$element->getObject()->getName();
		$params['h1'] = 		$element->getValue("h1");
		$params['title'] =		$element->getValue("title");
		$params['meta_keywords'] = 	$element->getValue("meta_keywords");
		$params['meta_descriptions'] = 	$element->getValue("meta_descriptions");

		$content = $element->getValue("content");
		$content = str_replace("%", "&#037;", $content);
		$params['content'] =		$content;

		$params['robots_deny'] =	$element->getValue("robots_deny");
		$params['show_submenu'] =	$element->getValue("show_submenu");
		$params['expanded'] =		$element->getValue("is_expanded");
		$params['unindexed'] =		$element->getValue("is_unindexed");

		$params['tags'] =			implode(', ', $element->getValue("tags"));
		$params['is_active'] =		$element->getIsActive();


		$lang_id = $element->getLangId();
		$domain_id = $element->getDomainId();

		$templates = "";
		$templates_list = templatesCollection::getInstance()->getTemplatesList($domain_id, $lang_id);
		foreach($templates_list as $template) {
			$tpl_id = $template->getId();
			$tpl_name = $tpl_name = $template->getTitle();

			if($tpl_id == $element->getTplId())
				$selected = " selected=\"yes\"";
			else
				$selected = "";

			$templates .= "<item $selected>
			<title><![CDATA[$tpl_name]]></title>
			<value><![CDATA[$tpl_id]]></value>
		</item>";
		}
		$params['templates'] = $templates;


		$menu_ua_images = new cifi("menu_ua", "./images/cms/menu/");
		$menu_pic_ua = $element->getValue("menu_pic_ua");
		$menu_pic_ua_path =  ($menu_pic_ua) ? $menu_pic_ua->getFileName() : "";
		$params['cifi_menu_ua'] = $menu_ua_images->make_div() . $menu_ua_images->make_element($menu_pic_ua_path);





		$menu_a_images = new cifi("menu_a", "./images/cms/menu/");
		$menu_pic_a = $element->getValue("menu_pic_a");
		$menu_pic_a_path =  ($menu_pic_a) ? $menu_pic_a->getFileName() : "";
		$params['cifi_menu_a'] = $menu_a_images->make_div() . $menu_a_images->make_element($menu_pic_a_path);

		$headers_images = new cifi("headers", "./images/cms/headers/");
		$header_pic = $element->getValue("header_pic");
		$header_pic_path =  ($header_pic) ? $header_pic->getFileName() : "";
		$params['cifi_headers'] = $headers_images->make_div() . $headers_images->make_element($header_pic_path);

		if($data_inst = cmsController::getInstance()->getModule("data")) {
			$params['data_field_groups'] = $data_inst->renderEditableGroups($element->getObject()->getTypeId(), $element_id);
		}


		$object_types = "";

		$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("content");
		$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
		$params['object_types'] = putSelectBox_assoc($object_types, $element->getObject()->getTypeId(), false);


		if($users_inst = cmsController::getInstance()->getModule("users")) {
			$params['perm_panel'] = $users_inst->get_perm_panel("content", "content", "edit_page", $element_id);
		}

		if($backup_inst = cmsController::getInstance()->getModule("backup")) {
			$params['backup_panel'] = $backup_inst->backup_panel("content", "edit_page_do", $element_id);
		}



		if(system_is_allowed("content", "edit_page", $element_id))
			$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
		else
			$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" disabled="yes" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" disabled="yes" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " disabled="yes"  />';

		$params['pid'] = $element_id;
		$params['method'] = "edit_page_do";
		$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/content/";
		$this->load_forms();
		return $this->parse_form('add_page', $params);
	}


	public function edit_page_do() {
		$element_id = (int) $_REQUEST['param0'];

		if(!system_is_allowed("content", "edit_page", $element_id))
			return "Конкретно эту страницу вам редактировать нельзя.";


		$name = $_REQUEST['name'];
		$title = $_REQUEST['title'];
		$meta_keywords = $_REQUEST['meta_keywords'];
		$meta_descriptions = $_REQUEST['meta_descriptions'];
		$content = $_REQUEST['content'];
		$alt_name = $_REQUEST['alt_name'];
		$h1 = $_REQUEST['h1'];


		$tpl_id = (int) $_REQUEST['tpl'];
		$is_default = (bool) $_REQUEST['def'];

		$is_visible = (bool) $_REQUEST['is_visible'];
		$show_submenu = (bool) $_REQUEST['show_submenu'];
		$expanded = (bool) $_REQUEST['expanded'];
		$unindexed = (bool) $_REQUEST['unindexed'];
		$index_item = (bool) $_REQUEST['index_item'];
		$robots_deny = (bool) $_REQUEST['robots_deny'];
		$tags =(string) $_REQUEST['tags'];
		$object_type_id = (int) $_REQUEST['object_type_id'];

		$is_active = (bool) $_REQUEST['is_active'];

		$element = umiHierarchy::getInstance()->getElement($element_id);

		$element->setIsVisible($is_visible);
		$element->setTplId($tpl_id);
		$element->setAltName($alt_name);
		$element->setIsDefault($is_default);

		$element->setIsActive($is_active);

		$element->getObject()->setName($name);

		$element->setValue("h1", $h1);
		$element->setValue("title", $title);
		$element->setValue("meta_keywords", $meta_keywords);
		$element->setValue("meta_descriptions", $meta_descriptions);
		$element->setValue("content", $content);

		$element->setValue("robots_deny", $robots_deny);
		$element->setValue("tags",$tags);
		$element->setValue("show_submenu", $show_submenu);
		$element->setValue("is_expanded", $expanded);
		$element->setValue("is_unindexed", $unindexed);

		$element->setValue("tags", $tags);

		$select_menu_ua = $_REQUEST['select_menu_ua'];
		if(!($menu_ua = umiFile::upload("pics", "menu_ua", "./images/cms/menu/"))) $menu_ua = new umiFile("./images/cms/menu/" . $select_menu_ua);
		$element->setValue("menu_pic_ua", $menu_ua);

		$select_menu_a = $_REQUEST['select_menu_a'];
		if(!($menu_a = umiFile::upload("pics", "menu_a", "./images/cms/menu/"))) $menu_a = new umiFile("./images/cms/menu/" . $select_menu_a);
		$element->setValue("menu_pic_a", $menu_a);

		$select_headers = $_REQUEST['select_headers'];
		if(!($headers = umiFile::upload("pics", "headers", "./images/cms/headers/"))) $headers = new umiFile("./images/cms/headers/" . $select_headers);
		$element->setValue("header_pic", $headers);


		if($data_inst = cmsController::getInstance()->getModule("data")) {
			$data_inst->saveEditedGroups($element_id);
		}

		if($object_type_id) {
			$element->getObject()->setTypeId($object_type_id);
		}


		if($users_inst = cmsController::getInstance()->getModule("users")) {
			$users_inst->setPerms($element_id);
		}

		$exit_after_save = $_REQUEST['exit_after_save'];

		if($backup_inst = cmsController::getInstance()->getModule("backup")) {
			$backup_inst->backup_save("content", "edit_page_do", $element_id);
		}

		if($_REQUEST['exit_after_save'] == 2) {
			$link = umiHierarchy::getInstance()->getPathById($element_id);
			$this->redirect($link);
		}

		if($exit_after_save) {
			$this->redirect($this->pre_lang . "/admin/content/");
		} else {
			$this->redirect($this->pre_lang . "/admin/content/edit_page/" . $element_id . "/");
		}
	}








	public function config() {
		$res = "";
		$res1 = "";

		$this->sheets_reset();
		$this->sheets_add("Шаблоны", "config");
		$this->sheets_add("SEO (Умолчания)", "edit_domain");

		$domains = domainsCollection::getInstance()->getList();
		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();

		foreach($domains as $domain) {

			$host = $domain->getHost();
			$domain_id = $domain->getId();

			$templates = templatesCollection::getInstance()->getTemplatesList($domain_id, $lang_id);

			$res1 .=
<<<END

<tablegroup>
 <hrow>
  <hcol colspan="4" style="text-align: center;">
  	<![CDATA[{$host}]]>
  </hcol>
 </hrow>

 <hrow>
  <hcol>Название шаблона</hcol>
  <hcol>Имя файла</hcol>
  <hcol style="text-align: center;">Основной</hcol>
  <hcol style="text-align: center;">Удалить</hcol>
 </hrow>

END;

		foreach($templates as $template) {
			$tpl_id = $template->getId();
			$tpl_name = $template->getTitle();
			$tpl_path = $template->getFilename();
			$tpl_is_default = $template->getIsDefault();
			$s_disabled = $tpl_is_default? "disabled=\"1\"": "";
			$res1 .=
<<<END
<row>

 <col>
 	<input quant='no' style='width: 95%'>
		<name><![CDATA[tpl_names[$domain_id][$tpl_id]]]></name>
		<value><![CDATA[$tpl_name]]></value>
	</input>

 </col>

 <col>
  	<input   quant='no' style='width: 95%'>
		<name><![CDATA[tpl_paths[$domain_id][$tpl_id]]]></name>
		<value><![CDATA[$tpl_path]]></value>
	</input>

 </col>

 <col style='text-align: center; vertical-align: middle'>
  	<radio selected='$tpl_is_default'>
		<name><![CDATA[tpl_default[$domain_id]]]></name>
		<value><![CDATA[$tpl_id]]></value>
	</radio>

 </col>

 <col style='text-align: center; vertical-align: middle'>
	<checkbox {$s_disabled}>
		<name><![CDATA[tpl_del[$domain_id][$tpl_id]]]></name>
	</checkbox>

 </col>

</row>
END;
		}
		$res1 .=
<<<END
<row>
  <col>
  	<input quant='no' style='width: 95%'>
		<name><![CDATA[tpl_new_name[$domain_id]]]></name>
	</input>
  </col>
  <col>
  	<input quant='no' style='width: 95%'>
		<name><![CDATA[tpl_new_path[$domain_id]]]></name>
	</input>
  </col>
  <col style='text-align: center; vertical-align: middle'>
  	<radio selected="">
					<name><![CDATA[tpl_default[$domain_id]]]></name>
					<value><![CDATA[new]]></value>
  	</radio>
  </col>
  <col style='text-align: center; vertical-align: middle'></col>
 </row>
</tablegroup>
<br/>
END;
	}

		$params = Array();
		$params['templates_list'] = $res1;
		$params['pre_lang'] = $_REQUEST['pre_lang'];

		$this->load_forms();
		$res = $this->parse_form('config', $params);

		return $res;
	}

	public function templates_do() {
		$tpl_paths = $_REQUEST['tpl_paths'];
		$tpl_names = $_REQUEST['tpl_names'];

		$tpl_new_name = $_REQUEST['tpl_new_name'];
		$tpl_new_path = $_REQUEST['tpl_new_path'];

		$tpl_default = $_REQUEST['tpl_default'];
		$tpl_del = $_REQUEST['tpl_del'];

		//$lang_id = langsCollection::getInstance()->getDefaultLang()->getId();
		//$domain_id = domainsCollection::getInstance()->getDefaultDomain()->getId();

		$domains = domainsCollection::getInstance()->getList();
		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();

		foreach($domains as $domain) {

			$domain_id = $domain->getId();

			if(is_array($tpl_names[$domain_id])) {
				foreach($tpl_names[$domain_id] as $id => $name) {
					$name = utf8_1251($name);
					$path = utf8_1251($tpl_paths[$domain_id][$id]);

					$template = templatesCollection::getInstance()->getTemplate($id);
					$template->setTitle($name);
					$template->setFilename($path);
					$template->commit();
				}

			}
			//error_log('$domain_id = '.$domain_id.'; new_path=  '.$tpl_new_path[$domain_id].'; new_name = '.$tpl_new_name[$domain_id].';');
			if($tpl_new_name[$domain_id] && $tpl_new_path[$domain_id]) {
				$tpl_new_name[$domain_id] = utf8_1251($tpl_new_name[$domain_id]);
				$tpl_new_path[$domain_id] = utf8_1251($tpl_new_path[$domain_id]);

				templatesCollection::getInstance()->addTemplate($tpl_new_path[$domain_id], $tpl_new_name[$domain_id], $domain_id, $lang_id, (($tpl_default[$domain_id] == "new") ? true : false));
			}

			if($tpl_default[$domain_id] != "new") {
				templatesCollection::getInstance()->setDefaultTemplate($tpl_default[$domain_id],$domain_id, $lang_id);
			}

			if(is_array($tpl_del[$domain_id])) {
				foreach($tpl_del[$domain_id] as $tpl_id => $tpl_val) {
					templatesCollection::getInstance()->delTemplate($tpl_id);
				}
			}
		}

		$this->redirect($this->pre_lang . "/admin/content/config/");
	}

	public function move_page($pid = 0, $direction = "") {
		if(!$pid) {
			$pid = (int) $_GET['pid'];
			$direction = $_REQUEST['direction'];
		}

		$pid = (int) $pid;

//		return $pid . $this->sitetree();

		//let's get his parent
		$sql = "SELECT rel, ord, lang, domain FROM cms_content WHERE id=$pid";
		$result = mysql_query($sql);

		list($parent, $curr_ord, $plang, $pdomain) = mysql_fetch_row($result);

		//let us know our new position
		if($direction == "up")
			$sql = "SELECT id, ord FROM cms_content WHERE rel='$parent' AND ord < '$curr_ord' AND lang='" . $plang . "' AND domain='" . $pdomain . "' ORDER BY ord DESC";

		if($direction == "down")
			$sql = "SELECT id, ord FROM cms_content WHERE rel='$parent' AND ord > '$curr_ord' AND lang='" . $plang . "' AND domain='" . $pdomain . "' ORDER BY ord ASC";

		$result = mysql_query($sql);


		if(mysql_num_rows($result) == 0)
			return $this->CMS_ENV['move_called'];

		list($r_id, $r_ord) = mysql_fetch_row($result);

		//now - move page
		$sql1 = "UPDATE cms_content SET ord='$r_ord' WHERE id='$pid' LIMIT 1";
		$sql2 = "UPDATE cms_content SET ord='$curr_ord' WHERE id='$r_id' LIMIT 1";

		mysql_query($sql1);
		mysql_query($sql2);

		$this->site_tree = false;
		$this->rec_tree();

	}

	public function treelink() {
		header("Content-type: text/html; charset=utf-8");
		$this->load_forms();

		$lines = "";

		$params = Array();

		$res = $this->parse_form("tree_link", $params);
		$this->flush($res);
	}

	public function edit_domain() {
		$res = "<form action='%pre_lang%/admin/content/edit_domain_do/'>\r\n";

		$this->sheets_reset();
		$this->sheets_add("Шаблоны", "config");
		$this->sheets_add("SEO (Умолчания)", "edit_domain");
		$this->load_forms();
		$regedit = regedit::getInstance();

		$domains = domainsCollection::getInstance()->getList();
		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();

		foreach($domains as $domain) {
			$params = Array();

			$domain_name = $domain->getHost();
			$domain_id = $domain->getId();

			$templates = templatesCollection::getInstance()->getTemplatesList($domain_id, $lang_id);

			$params['domain_name'] = $domain_name;
			$params['domain_id'] = $domain_id;

			$params['title_prefix'] = $regedit->getVal("//settings/title_prefix/{$lang_id}/{$domain_id}");
			$params['keywords'] = $regedit->getVal("//settings/meta_keywords/{$lang_id}/{$domain_id}");
			$params['description'] = $regedit->getVal("//settings/meta_description/{$lang_id}/{$domain_id}");

			$res .= $this->parse_form("edit_domain", $params);
		}

		$res .= '<p><submit title=\'Сохранить\'/></p>';
		$res .= '</form>';

		return $res;
	}

	public function edit_domain_do() {
		$res = $this->UC();

		$title_prefix = $_REQUEST['title_prefix'];
		$keywords = $_REQUEST['keywords'];
		$description = $_REQUEST['description'];

		$regedit = regedit::getInstance();
		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();

		foreach($title_prefix as $domain_id => $tmp) {

			// удалить 3 строки ниже, кагда реестр будет пахать нормально!
			$regedit->setVar("//settings/title_prefix/{$lang_id}", '');
			$regedit->setVar("//settings/meta_keywords/{$lang_id}", '');
			$regedit->setVar("//settings/meta_description/{$lang_id}", '');

			$regedit->setVar("//settings/title_prefix/{$lang_id}/{$domain_id}", utf8_1251($title_prefix[$domain_id]));
			$regedit->setVar("//settings/meta_keywords/{$lang_id}/{$domain_id}", utf8_1251($keywords[$domain_id]));
			$regedit->setVar("//settings/meta_description/{$lang_id}/{$domain_id}", utf8_1251($description[$domain_id]));
		}

		$this->redirect($this->pre_lang . "/admin/content/edit_domain/");
	}

	public function insertimage() {
		header("Content-type: text/html; charset=utf-8");
		$this->load_forms();

		$lines = "";

		$dir = "images/cms/content/";

		$cifi_f = new cifi("void", $dir);
		$arr = $cifi_f->read_files();
		sort($arr);
		$sz = sizeof($arr);
		for($i = 0; $i < $sz; $i++) {
			$o = $arr[$i];
			$lines .= "\t<option value='/" . $dir . $o . "'>" . $o . "</option>\r\n";
		}



		$params = Array();
		$params['lines'] = &$lines;

		$res = $this->parse_form("insertimage", $params);
		$this->flush($res);
	}


	public function insertimage_do() {
		umiImageFile::upload("pics", "new", "images/cms/content/");
		$this->redirect($this->pre_lang . "/admin/content/insertimage/");
	}

	public function insertmacros() {
		$res = "";
		$this->load_forms();
		header("Content-type: text/html; charset=utf-8");

		$lines = "";
		$mc = Array();

		$regedit = regedit::getInstance();
		$r = $regedit->getList("//modules");
		foreach($r as $m) {
			$m = $m[0];
			//echo $m . "<br />";
			if(system_module_prepared($m, $this->CMS_ENV)) {
				$rm = $this->CMS_ENV['modules'][$m]->genMacroses();
				if(is_array($rm)) {
					$mc = array_merge($mc, $rm);
				}
			}
		}


		$sz = sizeof($mc);
		for($i = 0; $i < $sz; $i++) {
			$o = $mc[$i][1];
			$cm = $mc[$i][0];
			$cm = str_replace("%", "&#037;", $cm);
			$lines .= "\t<option value='" . $cm . "'>" . $o . "</option>\r\n";
		}



		$params = Array();
		$params['lines'] = &$lines;

		$res = $this->parse_form("insertimage", $params);
		$this->flush($res, "text/html");

		return $res;
	}


	public function replace() {
		$id = (int) $_REQUEST['id'];
		$rel = $_REQUEST['rel'];
		if(!is_numeric($rel)) {
			$domain = $rel;
		}
		$rel = (int) $rel;
		$before = $_REQUEST['before'];


		umiHierarchy::getInstance()->moveBefore($id, $rel, (($before) ? $before : false));
		$this->flush();
	}
};

?>