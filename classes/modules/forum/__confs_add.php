<?php
	abstract class __confs_add_forum {
		public function conf_add() {
			$this->sheets_set_active("confs_list");

			$params = Array();
			$this->load_forms();

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

			$parent = (int) $_REQUEST['param0'] = 0;

			if(system_is_allowed("forum", "conf_edit", $parent))
				$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и посмотреть" onclick="javascript: return save_with_redirect();" /> <submit title="Добавить страницу" onclick="javascript: return save_with_exit();" />';
			else
				$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и посмотреть" onclick="javascript: return save_with_redirect();" disabled="yes"/> <submit title="Добавить страницу" onclick="javascript: return save_with_exit();" disabled="yes"/>';


			$params['is_active'] = 1;
			$params['is_visible'] = 1;
			$params['meta'] = $pkey;
			$params['description'] = $pdesc;
			$params['h1'] = "";
			$params['tags'] = "";

			$posttime = new umiDate();
			$params['posttime'] = $posttime->getFormattedDate();


			$templates = "";
			$templates_list = templatesCollection::getInstance()->getTemplatesList($domain_id, $lang_id);
			foreach($templates_list as $template) {
				$tpl_id = $template->getId();
				$tpl_name = $tpl_name = $template->getTitle();

				if($template->getIsDefault())
					$selected = " selected=\"yes\"";
				else
					$selected = "";

				$templates .= <<<END
							<item $selected>
								<title><![CDATA[$tpl_name]]></title>
								<value><![CDATA[$tpl_id]]></value>
							</item>
END;
			}

			$params['templates'] = $templates;


	                $params['pre_lang'] = $_REQUEST['pre_lang'];
			$params['domain'] = $_REQUEST['target_domain'];

			$menu_ua_images = new cifi("menu_ua", "./images/cms/menu/");
			$menu_a_images = new cifi("menu_a", "./images/cms/menu/");
			$headers_images = new cifi("headers", "./images/cms/headers/");

			$params['cifi_menu_ua'] = $menu_ua_images->make_div() . $menu_ua_images->make_element();
			$params['cifi_menu_a'] = $menu_a_images->make_div() . $menu_a_images->make_element();
			$params['cifi_headers'] = $headers_images->make_div() . $headers_images->make_element();


			if(cmsController::getInstance()->getModule('users')) {
				$params['perm_panel'] = cmsController::getInstance()->getModule('users')->get_perm_panel("forum", "view", "conf_edit");
			}

			if(cmsController::getInstance()->getModule('filemanager')) {
				$perm_panel = cmsController::getInstance()->getModule('filemanager')->upl_files("images/cms/content/");
				$params['files_panel'] = $perm_panel;
			}

			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "conf");
			$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
			$params['object_types'] = putSelectBox_assoc($object_types, 0, false);

			if(cmsController::getInstance()->getModule('data')) {
				reset($object_types);
				$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups(key($object_types));
			}


			$params['method'] = "conf_add_do";
			return $this->parse_form("conf_add", $params);
		}

		public function conf_add_do() {
			global $HTTP_POST_FILES;


			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "conf")->getId();

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();


			$parent_id = 0;

			$name = $_REQUEST['name'];
			$title = $_REQUEST['title'];
			$meta_keywords = $_REQUEST['meta_keywords'];
			$meta_description = $_REQUEST['meta_description'];
			$alt_name = $_REQUEST['alt_name'];
			$h1 = $_REQUEST['h1'];
			$tpl_id = (int) $_REQUEST['tpl'];

			$is_visible = (bool) $_REQUEST['is_visible'];
			$show_submenu = (bool) $_REQUEST['show_submenu'];
			$expanded = (bool) $_REQUEST['expanded'];
			$unindexed = (bool) $_REQUEST['unindexed'];
			$index_item = (bool) $_REQUEST['index_item'];
			$robots_deny = (bool) $_REQUEST['robots_deny'];
			$tags = (string) $_REQUEST['tags'];
			$object_type_id = (int) $_REQUEST['object_type_id'];

			$is_active = (bool) $_REQUEST['is_active'];

			$descr = $_REQUEST['descr'];


			$hierarchy = umiHierarchy::getInstance();
			$element_id = $hierarchy->addElement($parent_id, $hierarchy_type_id, $name, $alt_name, $object_type_id, $domain_id, $lang_id, $tpl_id);

			if(cmsController::getInstance()->getModule('users')) {
				cmsController::getInstance()->getModule('users')->setPerms($element_id);
			}

			$element = $hierarchy->getElement($element_id);

			$element->setIsVisible($is_visible);
			$element->setTplId($tpl_id);
			$element->setIsActive($is_active);

			$element->setValue("h1", $h1);
			$element->setValue("title", $title);
			$element->setValue("meta_keywords", $meta_keywords);
			$element->setValue("meta_descriptions", $meta_description);

			$element->setValue("robots_deny", $robots_deny);
			$element->setValue("tags", $tags);
			$element->setValue("show_submenu", $show_submenu);
			$element->setValue("is_expanded", $expanded);
			$element->setValue("is_unindexed", $unindexed);

			$element->setValue("descr", $descr);




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

			if($object_type_id) {
				$element->getObject()->setTypeId($object_type_id);
			}


			$element->commit();



			$exit_after_save = $_REQUEST['exit_after_save'];

			if($exit_after_save) {
				$this->redirect($this->pre_lang . "/admin/forum/confs_list/");
			} else {
				$this->redirect($this->pre_lang . "/admin/forum/conf_edit/" . $element_id . "/");
			}
		}
	};
?>