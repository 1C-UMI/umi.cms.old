<?php

	abstract class __add_shared_file {
		public function add_shared_file() {
			$params = Array();
			$this->load_forms();

			$this->sheets_set_active("shared_files");

			$lang_id = langsCollection::getInstance()->getDefaultLang()->getId();
			$domain_id = domainsCollection::getInstance()->getDefaultDomain()->getId();

			if(system_is_allowed("filemanager", "edit_shared_file"))
				$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и посмотреть" onclick="javascript: return save_with_redirect();" /> <submit title="Добавить страницу" onclick="javascript: return save_with_exit();" />';
			else
				$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и посмотреть" onclick="javascript: return save_with_redirect();" disabled="yes"/> <submit title="Добавить страницу" onclick="javascript: return save_with_exit();" disabled="yes"/>';


			$params['is_active'] = 1;
			$params['is_visible'] = 1;
			$params['meta'] = $pkey;
			$params['description'] = $pdesc;
			$params['h1'] = "";
			$params['tags'] = "";

			$params['downloads'] = 0;

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

			
			$fs_files = new cifi("fs_files", "./files/", false);

			$arr_extfiles = array();
			$s_extfile_name = "";
			if (isset($_REQUEST['param0'])) {
				$s_path = base64_decode($_REQUEST['param0']);
				$s_root_path = ini_get("include_path");

				$s_path = str_replace("\\", "/", $s_path);
				$s_path = str_replace("//", "/", $s_path);
				
				if(strpos($s_path, $s_root_path) === false || strpos($s_path, "..") !== false || strpos($s_path, "./") !== false) {
					$s_path = $s_root_path;
				}

				$o_file = new umiFile($s_path);
				if (!$o_file->getIsBroken()) {
					$s_extfile_name = "/".str_replace($s_root_path, "", $o_file->getFilePath());
					$arr_extfile_parts = explode("/", str_replace("\\", "/", $s_extfile_name));
					array_shift($arr_extfile_parts);
					if (isset($arr_extfile_parts[0], $arr_extfile_parts[1]) && count($arr_extfile_parts) == 2 && $arr_extfile_parts[0] == "files") {
						$s_extfile_name = $arr_extfile_parts[1];
					} else {
						$arr_extfiles[] = $s_extfile_name;
					}
				}
			}

			$params['cifi_shared_file'] = $fs_files->make_div().$fs_files->make_element($s_extfile_name, $arr_extfiles);

			$params['cifi_menu_ua'] = $menu_ua_images->make_div() . $menu_ua_images->make_element();
			$params['cifi_menu_a'] = $menu_a_images->make_div() . $menu_a_images->make_element();
			$params['cifi_headers'] = $headers_images->make_div() . $headers_images->make_element();


			if(cmsController::getInstance()->getModule('users')) {
				$params['perm_panel'] = cmsController::getInstance()->getModule('users')->get_perm_panel("filemanager", "view", "shared_file_edit");
			}

			if(cmsController::getInstance()->getModule('filemanager')) {
				$perm_panel = cmsController::getInstance()->getModule('filemanager')->upl_files("images/cms/content/");
				$params['files_panel'] = $perm_panel;
			}

			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("filemanager", "shared_file");

			$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
			$params['object_types'] = putSelectBox_assoc($object_types, 0, false);

			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("users", "user");
			$uobject_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
			$user_object_type = key($uobject_types);
			$params['fs_users'] = putSelectBox_assoc(umiObjectsCollection::getInstance()->getGuidedItems($user_object_type), cmsController::getInstance()->getModule("users")->user_id, false);

			if(cmsController::getInstance()->getModule('data')) {
				reset($object_types);
				$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups(key($object_types));
			}


			$params['method'] = "add_shared_file_do";
			return $this->parse_form("edit_shared_file", $params);
		}

		public function add_shared_file_do() {
			global $HTTP_POST_FILES;


			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("filemanager", "shared_file")->getId();
			$lang_id = langsCollection::getInstance()->getDefaultLang()->getId();
			$domain_id = domainsCollection::getInstance()->getDefaultDomain()->getId();


			$parent_id = 0;

			$name = $_REQUEST['name'];
			$title = $_REQUEST['title'];
			$meta_keywords = $_REQUEST['meta_keywords'];
			$meta_description = $_REQUEST['meta_descriptions'];
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

			$downloads = (int) $_REQUEST['downloads_counter'];


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

			$element->setValue("content", $descr);
			$element->setValue("downloads_counter", $downloads);


			$select_fs_file = $_REQUEST['select_fs_files'];

			$s_file_path = "";
			if (strpos($select_fs_file, "/") === 0) {
				$s_file_path = ini_get("include_path").$select_fs_file;
			} else {
				$s_file_path = "./files/".$select_fs_file;
			}

			if(!($fs_file = umiFile::upload("pics", "fs_files", "./files/"))) $fs_file = new umiFile($s_file_path);
			$element->setValue("fs_file", $fs_file);

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

			if($_REQUEST['exit_after_save'] == 2) {
				$link = umiHierarchy::getInstance()->getPathById($element_id);
				$this->redirect($link);
			}

			if($exit_after_save) {
				$this->redirect($this->pre_lang . "/admin/filemanager/shared_files/");
			} else {
				$this->redirect($this->pre_lang . "/admin/filemanager/edit_shared_file/" . $element->getId() . "/");
			}
		}
	}

?>