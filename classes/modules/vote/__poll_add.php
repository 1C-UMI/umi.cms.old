<?php
	abstract class __poll_add_vote {

		public function add_poll() {
			$params = Array();
			$this->load_forms();

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();


			//Setting templates
			$templates = "";
			$templates_list = templatesCollection::getInstance()->getTemplatesList($domain_id, $lang_id);

			foreach($templates_list as $template) {
			$tpl_id = $template->getId();
			$tpl_name = $tpl_name = $template->getTitle();

				if($template->getIsDefault())
					$selected = " checked=\"yes\"";
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


			//Setting permissions
			if(cmsController::getInstance()->getModule('users')) {
				$files_panel = cmsController::getInstance()->getModule('users')->get_perm_panel("vote", "poll", "edit_poll");
				$params['perm_panel'] = $files_panel;
			}


			//Setting hierarchy type
			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("vote", "poll");
			$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
			$params['object_types'] = putSelectBox_assoc($object_types, 0, false);

			//Setting cifi-inputs
			$menu_ua_images = new cifi("menu_ua", "./images/cms/menu/");
			$menu_a_images = new cifi("menu_a", "./images/cms/menu/");
			$headers_images = new cifi("headers", "./images/cms/headers/");

			$params['cifi_menu_ua'] = $menu_ua_images->make_div() . $menu_ua_images->make_element();
			$params['cifi_menu_a'] = $menu_a_images->make_div() . $menu_a_images->make_element();
			$params['cifi_headers'] = $headers_images->make_div() . $headers_images->make_element();


			//Setting defaults
			$params['is_active'] = 1;
			$params['is_visible'] = 1;
			$params['meta'] = "";
			$params['description'] = "";
			$params['h1'] = "";
            $params['tags'] = "";

			$params['method'] = "add_poll_do";

			if(system_is_allowed("vote", "edit_poll", $parent_id))
				$params['save_n_save'] = '<submit title="Добавить и посмотреть" onclick="javascript: return save_with_redirect();" /> <submit title="Добавить страницу" onclick="javascript: return save_with_exit();" />';
			else
				$params['save_n_save'] = '<submit title="Добавить и посмотреть" onclick="javascript: return save_with_redirect();" disabled="yes"/> <submit title="Добавить страницу" onclick="javascript: return save_with_exit();" disabled="yes"/>';

			return $this->parse_form("add_poll", $params);
		}


		public function add_poll_do() {
			global $HTTP_POST_FILES;

			$parent_id = 0;

			//Hierarchy defines
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("vote", "poll")->getId();

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

			//Getting default fields
			$name = $_REQUEST['name'];
			$meta_keywords = $_REQUEST['meta_keywords'];
			$meta_description = $_REQUEST['meta_description'];
			$alt_name = $_REQUEST['alt_name'];
			$h1 = $_REQUEST['h1'];
			$title = $_REQUEST['title'];
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


			//Getting custom fields
			$is_closed = (bool) $_REQUEST['is_closed'];
			$question = $_REQUEST['question'];


			//Creating empty element
			$hierarchy = umiHierarchy::getInstance();
			$element_id = $hierarchy->addElement($parent_id, $hierarchy_type_id, $name, $alt_name, $object_type_id, $domain_id, $lang_id, $tpl_id);

			//Setting permissions
			if(cmsController::getInstance()->getModule('users')) {
				cmsController::getInstance()->getModule('users')->setPerms($element_id);
			}

			$element = $hierarchy->getElement($element_id);


			//Setting default fields
			$element->setIsVisible($is_visible);
			$element->setIsActive($is_active);

			$element->setValue("h1", $h1);
			$element->setValue("title", $title);
			$element->setValue("meta_keywords", $meta_keywords);
			$element->setValue("meta_descriptions", $meta_description);
			$element->setValue("readme", $dev_desc);

			$element->setValue("robots_deny", $robots_deny);
			$element->setValue("tags", $tags);
			$element->setValue("show_submenu", $show_submenu);
			$element->setValue("is_expanded", $expanded);
			$element->setValue("is_unindexed", $unindexed);


			//Setting custom fields
			$element->setValue("is_closed", $is_closed);
			$element->setValue("question", $question);


			//Setting images fields
			$select_menu_ua = $_REQUEST['select_menu_ua'];
			if(!($menu_ua = umiFile::upload("pics", "menu_ua", "./images/cms/menu/"))) $menu_ua = new umiFile("./images/cms/menu/" . $select_menu_ua);
			$element->setValue("menu_pic_ua", $menu_ua);

			$select_menu_a = $_REQUEST['select_menu_a'];
			if(!($menu_a = umiFile::upload("pics", "menu_a", "./images/cms/menu/"))) $menu_a = new umiFile("./images/cms/menu/" . $select_menu_a);
			$element->setValue("menu_pic_a", $menu_a);

			$select_headers = $_REQUEST['select_headers'];
			if(!($headers = umiFile::upload("pics", "headers", "./images/cms/headers/"))) $headers = new umiFile("./images/cms/headers/" . $select_headers);
			$element->setValue("header_pic", $headers);


			//Setting dynamic fields
			if(cmsController::getInstance()->getModule('data')) {
				cmsController::getInstance()->getModule('data')->saveEditedGroups($element_id);
			}

			//Saving changes
			$element->commit();



			//Appending answers
			$items_name_new = $_REQUEST['items_name_new'];

			if($items_name_new) {
				$answer_type_id = umiObjectTypesCollection::getInstance()->getBaseType("vote", "poll_item");


				$item_id = umiObjectsCollection::getInstance()->addObject($items_name_new, $answer_type_id);
				$item = umiObjectsCollection::getInstance()->getObject($item_id);
				$item->setValue("count", 0);
				$item->setValue("poll_rel", $element->getObject()->getId());
				$item->commit();
			}



			//Preparing redirect
			$exit_after_save = $_REQUEST['exit_after_save'];
			if($exit_after_save) {
				$this->redirect($this->pre_lang . "/admin/vote/");
			} else {
				$this->redirect($this->pre_lang . "/admin/vote/edit_poll/" . $element_id . "/");
			}
		}

	};
?>