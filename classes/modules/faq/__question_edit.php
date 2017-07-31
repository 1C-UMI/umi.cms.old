<?php

abstract class __faq_question_edit {
	public function question_blocking() {
		$parent_id = (int) $_REQUEST['param0'];
		$element_id = (int) $_REQUEST['param1'];
		$is_active = (int) $_REQUEST['param2'];

		$element = umiHierarchy::getInstance()->getElement($element_id);
		if ($element instanceof umiHierarchyElement) {
			$element->setIsActive($is_active);
			$element->commit();
		}
		$this->redirect($this->pre_lang . "/admin/faq/questions_list/{$parent_id}/");
	}


	public function question_edit() {
		$params = Array();
		$this->load_forms();
		$this->sheets_set_active("projects_list");

		$parent_id = $_REQUEST['param0'];
		$element_id = $_REQUEST['param1'];


		$element = umiHierarchy::getInstance()->getElement($element_id);
		
		$this->navibar_back();
		$this->navibar_push("%nav_question_edt% (" . $element->getName() . ")", "/admin/faq/question_edit/" . $parent_id . "/" . $element_id);


		$params['alt_name'] = 		$element->getAltName();
		$params['is_visible'] = 	$element->getIsVisible();
		$params['is_default'] = 	$element->getIsDefault();
		$params['name'] = 		$element->getObject()->getName();
		$params['h1'] = 		$element->getValue("h1");
		$params['title'] =		$element->getValue("title");
		$params['meta_keywords'] = 		$element->getValue("meta_keywords");
		$params['meta_descriptions'] = 	$element->getValue("meta_descriptions");
		$params['dev_desc'] =		$element->getValue("readme");

		$params['robots_deny'] =	$element->getValue("robots_deny");
		$params['tags'] =		implode(', ', $element->getValue("tags"));
		$params['show_submenu'] =	$element->getValue("show_submenu");
		$params['expanded'] =		$element->getValue("is_expanded");
		$params['unindexed'] =		$element->getValue("is_unindexed");
		$params['question'] =		$element->getValue("question");
		$params['content'] =		$element->getValue("content");

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

			$templates .=
<<<END
							<item $selected>
								<title><![CDATA[$tpl_name]]></title>
								<value><![CDATA[$tpl_id]]></value>
							</item>
END;
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

		if(cmsController::getInstance()->getModule('data')) {
			$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($element->getObject()->getTypeId(), $element_id);
		}


		$object_types = "";

		$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "question");
		$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
		$params['object_types'] = putSelectBox_assoc($object_types, $element->getObject()->getTypeId(), false);


		if(cmsController::getInstance()->getModule('users')) {
			$params['perm_panel'] = cmsController::getInstance()->getModule('users')->get_perm_panel("faq", "view", "question_edit", $element_id);
		}

		if(system_is_allowed("faq", "question_edit", $element_id))
			$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
		else
			$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" disabled="yes" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" disabled="yes" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " disabled="yes"  />';

		if($backup_inst = cmsController::getInstance()->getModule("backup")) {
			$params['backup_panel'] = $backup_inst->backup_panel("faq", "question_edit_do", $element_id);
		}


		$params['parent_id'] = $parent_id;
		$params['element_id'] = $element_id;
		$params['method'] = "question_edit_do";
		$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/faq/questions_list/" . $parent_id . "/";
		return $this->parse_form('question_edit', $params);
	}

	public function question_edit_do() {
		global $HTTP_POST_FILES;

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "question")->getId();

		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
		$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

		$parent_id = (int) $_REQUEST['param0'];
		$element_id = (int) $_REQUEST['param1'];

		$name = $_REQUEST['name'];
		$meta_keywords = $_REQUEST['meta_keywords'];
		$meta_description = $_REQUEST['meta_description'];
		$alt_name = $_REQUEST['alt_name'];
		$h1 = $_REQUEST['h1'];
		$title = $_REQUEST['title'];
		$tpl_id = (int) $_REQUEST['tpl'];

		$is_visible = (bool) $_REQUEST['is_visible'];
		$is_default = (bool) $_REQUEST['is_default'];
		$show_submenu = (bool) $_REQUEST['show_submenu'];
		$expanded = (bool) $_REQUEST['expanded'];
		$unindexed = (bool) $_REQUEST['unindexed'];
		$index_item = (bool) $_REQUEST['index_item'];
		$robots_deny = (bool) $_REQUEST['robots_deny'];
		$tags = (string) $_REQUEST['tags'];

		$dev_desc = $_REQUEST['dev_desc'];
		$source = $_REQUEST['source'];
		$source_url = $_REQUEST['source_url'];

		$object_type_id = (int) $_REQUEST['object_type_id'];

		$is_active = (bool) $_REQUEST['is_active'];

		$content = (string) $_REQUEST['content'];
		$question = (string) $_REQUEST['question'];


		$hierarchy = umiHierarchy::getInstance();
		$element = $hierarchy->getElement($element_id);

		$element->getObject()->setName($name);

		$element->setAltName($alt_name);
		$element->setIsVisible($is_visible);
		$element->setIsActive($is_active);
		$element->setIsDefault($is_default);
		$element->setTplId($tpl_id);

		$element->setValue("h1", $h1);
		$element->setValue("title", $title);
		$element->setValue("meta_keywords", $meta_keywords);
		$element->setValue("meta_descriptions", $meta_description);

		$element->setValue("readme", $dev_desc);

		$element->setValue("robots_deny", $robots_deny);
		$element->setValue("tags",$tags);
		$element->setValue("show_submenu", $show_submenu);
		$element->setValue("is_expanded", $expanded);
		$element->setValue("is_unindexed", $unindexed);
		$element->setValue("content", $content);
		$element->setValue("question", $question);



		$select_menu_ua = $_REQUEST['select_menu_ua'];
		if(!($menu_ua = umiFile::upload("pics", "menu_ua", "./images/cms/menu/"))) $menu_ua = new umiFile("./images/cms/menu/" . $select_menu_ua);
		$element->setValue("menu_pic_ua", $menu_ua);

		$select_menu_a = $_REQUEST['select_menu_a'];
		if(!($menu_a = umiFile::upload("pics", "menu_a", "./images/cms/menu/"))) $menu_a = new umiFile("./images/cms/menu/" . $select_menu_a);
		$element->setValue("menu_pic_a", $menu_a);

		$select_headers = $_REQUEST['select_headers'];
		if(!($headers = umiFile::upload("pics", "headers", "./images/cms/headers/"))) $headers = new umiFile("./images/cms/headers/" . $select_headers);
		$element->setValue("header_pic", $headers);

		if(cmsController::getInstance()->getModule('data')) {
			cmsController::getInstance()->getModule('data')->saveEditedGroups($element_id);
		}


		$element->commit();

		if(cmsController::getInstance()->getModule('users')) {
			cmsController::getInstance()->getModule('users')->setPerms($element_id);
		}

		if($backup_inst = cmsController::getInstance()->getModule("backup")) {
			$backup_inst->backup_save("faq", "question_edit_do", $element_id);
		}


		$exit_after_save = $_REQUEST['exit_after_save'];

		if($_REQUEST['exit_after_save'] == 2) {
			$link = umiHierarchy::getInstance()->getPathById($element_id);
			$this->redirect($link);
		}


		if($exit_after_save) {
			$this->redirect($this->pre_lang . "/admin/faq/questions_list/".$parent_id."/");
		} else {
			$this->redirect($this->pre_lang . "/admin/faq/question_edit/" . $parent_id . "/" . $element_id . "/");
		}
	}
}
?>