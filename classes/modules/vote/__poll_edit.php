<?php
	abstract class __poll_edit_vote {
		public function edit_poll() {
			$params = Array();
			$this->load_forms();

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

			$element_id = (int) $_REQUEST['param0'];
			$element = umiHierarchy::getInstance()->getElement($element_id);

			//Setting templates
			$templates = "";
			$templates_list = templatesCollection::getInstance()->getTemplatesList($domain_id, $lang_id);
			foreach($templates_list as $template) {
				$tpl_id = $template->getId();
				$tpl_name = $tpl_name = $template->getTitle();

				if($tpl_id == $element->getTplId())
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


			//Setting permissions
			if(cmsController::getInstance()->getModule('users')) {
				$files_panel = cmsController::getInstance()->getModule('users')->get_perm_panel("vote", "poll", "edit_poll", $element_id);
				$params['perm_panel'] = $files_panel;
			}


			//Setting hierarchy type
			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("vote", "poll");
			$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
			$params['object_types'] = putSelectBox_assoc($object_types, $element->getObject()->getTypeId(), false);


			//Setting cifi-inputs
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


			//Setting defaults
			$params['alt_name'] = 		$element->getAltName();
			$params['is_visible'] = 	$element->getIsVisible();
			$params['is_default'] = 	$element->getIsDefault();
			$params['name'] = 		$element->getObject()->getName();
			$params['h1'] = 		$element->getValue("h1");
			$params['title'] =		$element->getValue("title");
			$params['meta'] = 		$element->getValue("meta_keywords");
			$params['description'] = 	$element->getValue("meta_descriptions");
			$params['dev_desc'] =		$element->getValue("readme");

			$params['robots_deny'] =	$element->getValue("robots_deny");
			$params['tags'] =			implode(', ', $element->getValue("tags"));
			$params['show_submenu'] =	$element->getValue("show_submenu");
			$params['expanded'] =		$element->getValue("is_expanded");
			$params['unindexed'] =		$element->getValue("is_unindexed");

			$params['is_active'] =		$element->getIsActive();

			//Setting custom
			$params['is_closed'] =		$element->getValue("is_closed");
			$params['question'] =		$element->getValue("question");

			$params['posttime'] =		(is_object($element->getValue("publish_time"))) ? $element->getValue("publish_time")->getFormattedDate() : "";

			//Setting dynamic fields
			if(cmsController::getInstance()->getModule('data')) {
				$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($element->getObject()->getTypeId(), $element_id);
			}



			$item_type_id = umiObjectTypesCollection::getInstance()->getBaseType("vote", "poll_item");
			$item_type = umiObjectTypesCollection::getInstance()->getType($item_type_id);
			$rel_field_id = $item_type->getFieldId("poll_rel");


			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("vote", "poll_item")->getId();
			$item_type_id = umiObjectTypesCollection::getInstance()->getBaseType("vote", "poll_item");

			$sel = new umiSelection();

			$sel->setObjectTypeFilter();
			$sel->addObjectType($item_type_id);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($rel_field_id, $element->getObject()->getId());

			$result = umiSelectionsParser::runSelection($sel);

			$rows = "";

			foreach($result as $item_id) {
				$item = umiObjectsCollection::getInstance()->getObject($item_id);
				$item_name = $item->getName();
				$item_count = $item->getPropByName("count")->getValue();

				$rows .= <<<END
			<row>
				<col>
					<input quant='no' class='' style='width: 525px; vertical-align: middle;' name='item_names[{$item_id}]'>
						<name><![CDATA[item_names[{$item_id}]]]></name>
						<value><![CDATA[{$item_name}]]></value>
					</input>
				</col>

				<col align="left">
					{$item_count}
				</col>

				<col align="center">
					<checkbox name="dels[]" value="{$item_id}">
						<name><![CDATA[dels[]]]></name>
						<value><![CDATA[{$item_id}]]></value>
					</checkbox>
				</col>
			</row>

END;
			}

			if($backup_inst = cmsController::getInstance()->getModule("backup")) {
				$params['backup_panel'] = $backup_inst->backup_panel("vote", "edit_poll_do", $element_id);
			}


			$params['rows'] = $rows;


			$params['poll_id'] = $element_id;
			$params['method'] = "edit_poll_do";

			if(system_is_allowed("vote", "edit_poll", $element_id))
				$params['save_n_save'] = '<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
			else
				$params['save_n_save'] = '<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" disabled="yes" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" disabled="yes" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " disabled="yes"  />';

			return $this->parse_form("add_poll", $params);
		}



		public function edit_poll_do() {
			global $HTTP_POST_FILES;

			$element_id = (int) $_REQUEST['param0'];
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
			$is_default = (bool) $_REQUEST['is_default'];
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

			$posttime = $_REQUEST['posttime'];
			$publish_time = new umiDate();
			$publish_time->setDateByString($posttime);


			//Getting element
			$hierarchy = umiHierarchy::getInstance();
			$element = $hierarchy->getElement($element_id);


			//Setting default fields
			$element->setIsVisible($is_visible);
			$element->setIsActive($is_active);
			$element->setTplId($tpl_id);
			$element->setAltName($alt_name);
			$element->setIsDefault($is_default);

			$element->getObject()->setName($name);

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

			$element->setValue("publish_time", $publish_time);


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


			//Setting permissions
			if(cmsController::getInstance()->getModule('users')) {
				cmsController::getInstance()->getModule('users')->setPerms($element_id);
			}

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


			$item_names = $_REQUEST['item_names'];

			foreach($item_names as $item_id => $item_name) {
				$item = umiObjectsCollection::getInstance()->getObject($item_id);
				$item->setName($item_name);
				$item->getPropByName("poll_rel")->setValue($element->getObject()->getId());
				$item->commit();
			}

			$dels = $_REQUEST['dels'];
			foreach($dels as $item_id) {
				umiObjectsCollection::getInstance()->delObject($item_id);
			}

			if($backup_inst = cmsController::getInstance()->getModule("backup")) {
				$backup_inst->backup_save("vote", "edit_poll_do", $element_id);
			}



			//Preparing redirect
			$exit_after_save = $_REQUEST['exit_after_save'];

			if($_REQUEST['exit_after_save'] == 2) {
				$link = umiHierarchy::getInstance()->getPathById($element_id);
				$this->redirect($link);
			}
			if($exit_after_save) {
				$this->redirect($this->pre_lang . "/admin/vote/");
			} else {
				$this->redirect($this->pre_lang . "/admin/vote/edit_poll/" . $element_id . "/");
			}
		}

	};
?>