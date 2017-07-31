<?php
	abstract class __photos_edit_photoalbum {

		public function photo_edit() {
			$params = Array();
			$this->load_forms();

			$element_id = $_REQUEST['param0'];
			$parent_id = umiHierarchy::getInstance()->getParent($element_id);

			$parent_element = umiHierarchy::getInstance()->getElement($parent_id);
			cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
			cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
			cmsController::getInstance()->nav_arr[] = Array($parent_element->getName(), "/admin/photoalbum/photos_list/{$parent_id}/", "");
			cmsController::getInstance()->nav_arr[] = Array("%nav_photoalbum_photo_edit%", "", "");


			if(system_is_allowed("forum", "message_edit", $parent))
				$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
			else
				$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" disabled="yes" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" disabled="yes" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " disabled="yes"  />';

			$element = umiHierarchy::getInstance()->getElement($element_id);


			$params['alt_name'] = 		$element->getAltName();
			$params['is_visible'] = 	$element->getIsVisible();
			$params['is_default'] = 	$element->getIsDefault();
			$params['name'] = 		$element->getObject()->getName();
			$params['h1'] = 		$element->getValue("h1");
			$params['title'] =		$element->getValue("title");
			$params['meta'] = 		$element->getValue("meta_keywords");
			$params['description'] = 	$element->getValue("meta_descriptions");

			$params['robots_deny'] =	$element->getValue("robots_deny");
			$params['tags'] =			implode(', ', $element->getValue("tags"));
			$params['show_submenu'] =	$element->getValue("show_submenu");
			$params['expanded'] =		$element->getValue("is_expanded");
			$params['unindexed'] =		$element->getValue("is_unindexed");

			$params['is_active'] =		$element->getIsActive();

			$params['descr'] =		$element->getValue("descr");
			$params['tags'] =		implode(", ", $element->getValue("tags"));

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();



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


			$params['pre_lang'] = $_REQUEST['pre_lang'];
			$params['domain'] = $_REQUEST['target_domain'];

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


			$photo_images = new cifi("photo", "./images/cms/data/");
			$photo = $element->getValue("photo");
			$photo_path =  ($photo) ? $photo->getFileName() : "";
			$params['cifi_photo'] = $photo_images->make_div() . $photo_images->make_element($photo_path);



			if(cmsController::getInstance()->getModule('users')) {
				$params['perm_panel'] = cmsController::getInstance()->getModule('users')->get_perm_panel("photoalbum", "view", "photo_edit", $element_id);
			}

			if(cmsController::getInstance()->getModule('filemanager')) {
				$perm_panel = cmsController::getInstance()->getModule('filemanager')->upl_files("images/cms/content/");
				$params['files_panel'] = $perm_panel;
			}

			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("photoalbum", "photo");
			$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
			$params['object_types'] = putSelectBox_assoc($object_types, $element->getObject()->getTypeId(), false);


			if(cmsController::getInstance()->getModule('data')) {
				$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($element->getObject()->getTypeId(), $element_id, false);
			}

			if($backup_inst = cmsController::getInstance()->getModule("backup")) {
				$params['backup_panel'] = $backup_inst->backup_panel("photoalbum", "photo_edit_do", $element_id);
			}


			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("users", "user");
			$uobject_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
			$user_object_type = key($uobject_types);
			$params['photo_users'] = putSelectBox_assoc(umiObjectsCollection::getInstance()->getGuidedItems($user_object_type), $element->getValue("user_id"), false);

			if($photo = $element->getValue("photo")) {
				$photo_filepath = $photo->getFilePath();
				$systemModule = &system_buildin_load("system");
				$photo_thumb = $systemModule->makeThumbnail($photo_filepath, 750, 'auto', false, true);

				$params['photo_image'] = <<<END

<img src="{$photo_thumb['src']}" width="{$photo_thumb['width']}" height="{$photo_thumb['height']}" style="border: #000 1px solid;" />

END;
			}
	

			$params['parent_id'] = $parent_id;
			$params['param0'] = $element_id;
			$params['method'] = "photo_edit_do";
			$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/photoalbum/photos_list/" . $parent_id . "/";
			return $this->parse_form("photo_add", $params);
		}


		public function photo_edit_do() {
			$element_id = (int) $_REQUEST['param0'];
			$parent_id = umiHierarchy::getInstance()->getParent($element_id);

			$name = $_REQUEST['name'];
			$title = $_REQUEST['title'];
			$meta_keywords = $_REQUEST['meta_keywords'];
			$meta_description = $_REQUEST['meta_description'];
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
			$tags = (string) $_REQUEST['tags'];

			$object_type_id = (int) $_REQUEST['object_type_id'];

			$is_active = (bool) $_REQUEST['is_active'];

			$descr = $_REQUEST['descr'];
			$user_id = (int) $_POST['user_id'];



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
			$element->setValue("meta_descriptions", $meta_description);
			$element->setValue("tags", $tags);

			$element->setValue("robots_deny", $robots_deny);
			$element->setValue("tags",$tags);
			$element->setValue("show_submenu", $show_submenu);
			$element->setValue("is_expanded", $expanded);
			$element->setValue("is_unindexed", $unindexed);

			$element->setValue("descr", $descr);
			$element->setValue("user_id", $user_id);

			$select_menu_ua = $_REQUEST['select_menu_ua'];
			if(!($menu_ua = umiFile::upload("pics", "menu_ua", "./images/cms/menu/"))) $menu_ua = new umiFile("./images/cms/menu/" . $select_menu_ua);
			$element->setValue("menu_pic_ua", $menu_ua);

			$select_menu_a = $_REQUEST['select_menu_a'];
			if(!($menu_a = umiFile::upload("pics", "menu_a", "./images/cms/menu/"))) $menu_a = new umiFile("./images/cms/menu/" . $select_menu_a);
			$element->setValue("menu_pic_a", $menu_a);

			$select_headers = $_REQUEST['select_headers'];
			if(!($headers = umiFile::upload("pics", "headers", "./images/cms/headers/"))) $headers = new umiFile("./images/cms/headers/" . $select_headers);
			$element->setValue("header_pic", $headers);

			$select_photo = $_REQUEST['select_photo'];
			if(!($photo = umiFile::upload("pics", "photo", "./images/cms/data/"))) $photo = new umiFile("./images/cms/data/" . $select_photo);
			$element->setValue("photo", $photo);


			if(cmsController::getInstance()->getModule('data')) {
				cmsController::getInstance()->getModule('data')->saveEditedGroups($element_id);
			}

			if($object_type_id) {
				$element->getObject()->setTypeId($object_type_id);
			}


			$element->commit();

			if(cmsController::getInstance()->getModule('users')) {
				cmsController::getInstance()->getModule('users')->setPerms($element_id);
			}

			if($backup_inst = cmsController::getInstance()->getModule("backup")) {
				$backup_inst->backup_save("photoalbum", "photo_edit_do", $element_id);
			}


			$exit_after_save = $_REQUEST['exit_after_save'];

			if($_REQUEST['exit_after_save'] == 2) {
				$link = umiHierarchy::getInstance()->getPathById($element_id);
				$this->redirect($link);
			}

			if($exit_after_save) {
				$this->redirect($this->pre_lang . "/admin/photoalbum/photos_list/{$parent_id}/");
			} else {
				$this->redirect($this->pre_lang . "/admin/photoalbum/photo_edit/{$element_id}/");
			}
		}
	};
?>