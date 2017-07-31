<?php

abstract class __faq_category_add {
	public function category_add() {
		$params = Array();
		$this->load_forms();
		$this->sheets_set_active("projects_list");
		$parent_id = (int) $_REQUEST['param0'];
		
		$params['parent_id'] = $parent_id;

		$parent = umiHierarchy::getInstance()->getElement($parent_id);

		$this->navibar_back();
		$this->navibar_push($parent->getName(), "/admin/faq/categories_list/" . $parent_id);
		$this->navibar_push("%nav_cat_add%");

		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
		$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

		if($parent_id) {
			$parent_element = umiHierarchy::getInstance()->getElement($parent_id);
			$domain_id = $parent_element->getDomainId();
		}



		if(cmsController::getInstance()->getModule('users')) {
			$files_panel = cmsController::getInstance()->getModule('users')->get_perm_panel_adding("faq", Array("categories_list", "category_add"));
			$params['perm_panel'] = $files_panel;
		}

		$types_arr = Array();
		$params['types'] = putSelectBox_assoc($types_arr);

		$params['is_active'] = 1;



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



		$menu_ua_images = new cifi("menu_ua", "./images/cms/menu/");
		$menu_a_images = new cifi("menu_a", "./images/cms/menu/");
		$headers_images = new cifi("headers", "./images/cms/headers/");

		$params['cifi_menu_ua'] = $menu_ua_images->make_div() . $menu_ua_images->make_element();
		$params['cifi_menu_a'] = $menu_a_images->make_div() . $menu_a_images->make_element();
		$params['cifi_headers'] = $headers_images->make_div() . $headers_images->make_element();


		if(cmsController::getInstance()->getModule('data')) {
			$type_id = umiObjectTypesCollection::getInstance()->getBaseType("faq", "category");
			$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($type_id);
		}

		$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "category");
		$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
		$params['object_types'] = putSelectBox_assoc($object_types, 0, false);


		if(cmsController::getInstance()->getModule('users')) {
			$params['perm_panel'] = cmsController::getInstance()->getModule('users')->get_perm_panel("faq", "category", "category_add");
		}



		$params['is_active'] = 1;
		$params['parent_id'] = $parent_id;
		$params['title'] = "";
		$params['method'] = "category_add_do";
		$params['save_n_save'] = '<submit title="Добавить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Добавить" onclick="javascript: return save_without_exit(); " />';
		return $this->parse_form("category_edit", $params);
	}

	public function category_add_do() {
		global $HTTP_POST_FILES;


		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "category")->getId();
		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
		$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

		$parent_id = (int) $_REQUEST['param0'];

		if($parent_id) {
			$parent_element = umiHierarchy::getInstance()->getElement($parent_id);
			$domain_id = $parent_element->getDomainId();
		}


		$name = $_REQUEST['name'];
		$title = $_REQUEST['title'];
		$meta_keywords = $_REQUEST['meta_keywords'];
		$meta_descriptions = $_REQUEST['meta_descriptions'];
		$content = $_REQUEST['content'];
		$alt_name = $_REQUEST['alt_name'];
		$h1 = $_REQUEST['h1'];
		$seo_prefix = $_REQUEST['seo_prefix'];
		$is_active = (bool) $_REQUEST['is_active'];
		$tpl_id = (int) $_REQUEST['tpl'];

		$is_visible = (bool) $_REQUEST['is_visible'];
		$show_submenu = (bool) $_REQUEST['show_submenu'];
		$expanded = (bool) $_REQUEST['expanded'];
		$unindexed = (bool) $_REQUEST['unindexed'];
		$index_item = (bool) $_REQUEST['index_item'];
		$robots_deny = (bool) $_REQUEST['robots_deny'];
		$tags = (string) $_REQUEST['tags'];
		$object_type_id = (int) $_REQUEST['object_type_id'];
		$content = (string) $_REQUEST['content'];

		$hierarchy = umiHierarchy::getInstance();
		$element_id = $hierarchy->addElement($parent_id, $hierarchy_type_id, $name, $alt_name, $object_type_id, $domain_id, $lang_id, $tpl_id);

		if(cmsController::getInstance()->getModule('data')) {
			cmsController::getInstance()->getModule('data')->saveEditedGroups($element_id);
		}

		$element = $hierarchy->getElement($element_id);


		$element->setIsActive($is_active);
		$element->setIsVisible($is_visible);
		$element->setTplId($tpl_id);
		$element->setAltName($alt_name);
		$element->setIsDefault($is_default);

		$element->getObject()->setName($name);

		$element->setValue("h1", $h1);
		$element->setValue("title", $title);
		$element->setValue("meta_keywords", $meta_keywords);
		$element->setValue("meta_descriptions", $meta_descriptions);

		$element->setValue("robots_deny", $robots_deny);
		$element->setValue("tags", $tags);
		$element->setValue("show_submenu", $show_submenu);
		$element->setValue("is_expanded", $expanded);
		$element->setValue("is_unindexed", $unindexed);
		$element->setValue("content", $content);

		$select_menu_ua = $_REQUEST['select_menu_ua'];
		if(!($menu_ua = umiFile::upload("pics", "menu_ua", "./images/cms/menu/"))) $menu_ua = new umiFile("./images/cms/menu/" . $select_menu_ua);
		$element->setValue("menu_pic_ua", $menu_ua);

		$select_menu_a = $_REQUEST['select_menu_a'];
		if(!($menu_a = umiFile::upload("pics", "menu_a", "./images/cms/menu/"))) $menu_a = new umiFile("./images/cms/menu/" . $select_menu_a);
		$element->setValue("menu_pic_a", $menu_a);

		$select_headers = $_REQUEST['select_headers'];
		if(!($headers = umiFile::upload("pics", "headers", "./images/cms/headers/"))) $headers = new umiFile("./images/cms/headers/" . $select_headers);
		$element->setValue("header_pic", $headers);



		if($object_type_id) {
			$element->getObject()->setTypeId($object_type_id);
		}

		$element->commit();


		if(cmsController::getInstance()->getModule('users')) {
			cmsController::getInstance()->getModule('users')->setPerms($element_id);
		}


		if($_REQUEST['exit_after_save']) {
			$this->redirect($this->pre_lang . "/admin/faq/categories_list/".$parent_id);
		} else {
			$this->redirect($this->pre_lang . "/admin/faq/category_edit/" . $parent_id . "/" . $element_id . "/");
		}
	}
}
?>