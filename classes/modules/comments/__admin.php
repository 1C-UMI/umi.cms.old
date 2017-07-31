<?php

abstract class __comments {

	public function view_comments() {
		$params = Array();
		$this->load_forms();

		$curr_page = (int) $_REQUEST['p'];
		$parent_id = $_REQUEST['param0'];
		$filter_author_id = (int) $_REQUEST['filter_author_id'];

		$per_page = $this->per_page;

		$block_arr = Array();

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("comments", "comment")->getId();


		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("comments", "comment");
		$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);


		$sel = new umiSelection;

		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);
		if ($parent_id) {
			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($parent_id);
		}


		if($filter_author_id) {
			$sel->setPropertyFilter();
			$field_id = $object_type->getFieldId("author_id");
			$sel->addPropertyFilterEqual($field_id, $filter_author_id);
		}


		$sel->setPermissionsFilter();
		$sel->addPermissions();

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("comments", "comment");
		$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

		$publish_time_field_id = $object_type->getFieldId('publish_time');

		$sel->setOrderFilter();
		$sel->setOrderByProperty($publish_time_field_id, false);

		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$result = umiSelectionsParser::runSelection($sel);
		$total = umiSelectionsParser::runSelectionCounts($sel);

		$params['rows'] = "";
		foreach($result as $element_id) {
			$params['rows'] .= $this->renderComment($element_id);
		}

		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);


		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("users", "author")->getId();
		list($type_id) = array_keys(umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type_id));

		$authors_arr = umiObjectsCollection::getInstance()->getGuidedItems($type_id);
		$authors = putSelectBox_assoc($authors_arr, $filter_author_id, true);
		$params['authors'] = $authors;

		$params['parent_id'] = $parent_id;
		return $this->parse_form("view_comments", $params);

	}

	public function view_noactive_comments() {

		$this->sheets_set_active("view_noactive_comments");

		$params = Array();
		$this->load_forms();

		$curr_page = (int) $_REQUEST['p'];

		$parent_id = $_REQUEST['param0'];

		$per_page = $this->per_page;

		$block_arr = Array();

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("comments", "comment")->getId();

		$sel = new umiSelection;

		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);
		if ($parent_id) {
			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($parent_id);
		}
		$sel->setPermissionsFilter();
		$sel->addPermissions();

		// active filter
		$sel->setActiveFilter();
		$sel->addActiveFilter(false);

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("comments", "comment");
		$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

		$publish_time_field_id = $object_type->getFieldId('publish_time');

		$sel->setOrderFilter();
		$sel->setOrderByProperty($publish_time_field_id, false);

		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$result = umiSelectionsParser::runSelection($sel);
		$total = umiSelectionsParser::runSelectionCounts($sel);

		$params['rows'] = "";
		foreach($result as $element_id) {
			$params['rows'] .= $this->renderComment($element_id, 1);
		}

		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

		return $this->parse_form("view_comments", $params);
	}

	public function renderComment($element_id, $unactive=0) {
		$result = "";
		$params = array();
		$this->load_forms();

		$element = umiHierarchy::getInstance()->getElement($element_id);
		if ($element) {
			$params['element_id'] = $element_id;
			$params['comment_title'] = $element->getName();
			$params['message'] = str_replace("<br />", "\r\n", $element->getValue("message"));
			$o_publish_time = $element->getValue("publish_time");
			if ($o_publish_time instanceof umiDate) {
				$params['publish_time'] = $o_publish_time->getFormattedDate("Y-m-d h:i");
			}

			$parent_id =umiHierarchy::getInstance()->getParent($element_id);
			$parent = umiHierarchy::getInstance()->getElement($parent_id);
			if ($parent) {
				$params['parent_id'] = $parent_id;
				$params['parent_name'] = $parent->getName();
			}

			$author_id = (int) $element->getValue("author_id");
			
			$author = umiObjectsCollection::getInstance()->getObject($author_id);
			if ($author) {
				if($author->getValue("is_registrated")) {
					$user_id = $author->getValue("user_id");
					$author = umiObjectsCollection::getInstance()->getObject($user_id);
					$author_name = "";
					if ($author) {
						$author_name = $author->getValue("lname")." ".$author->getValue("fname")." (".$author->getValue("login").")";
						$author_ip = $author->getValue("ip");

						$user_blocking = "";
						if($user_id != 14 && $user = umiObjectsCollection::getInstance()->getObject($user_id)) {
							if($user->getValue("is_activated")) {
								$user_blocking = <<<END

<a href="%pre_lang%/admin/users/user_blocking/all/{$user_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать пользователя" title="Заблокировать пользователя" /></a>
&nbsp;
END;
							} else {
								$user_blocking = <<<END

<a href="%pre_lang%/admin/users/user_blocking/all/{$user_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать пользователя" title="Разблокировать пользователя" /></a>
&nbsp;
END;

							}
						}
					}
					$author_ip = (strlen($author_ip)? "ip: ".$author_ip : "");

					$params['author'] = $user_blocking . "<a href=\"".$this->pre_lang."/admin/users/user_edit/".$user_id."/\">".$author_name."</a>" . $author_ip;
				} else {
					$author_nickname = $author->getValue("nickname");
					$author_email = $author->getValue("email");
					$author_email = (strlen($author_email)? "e-mail: ".$author_email : "");
					$author_ip = $author->getValue("ip");
					$author_ip = (strlen($author_ip)? "ip: ".$author_ip : "");
					$params['author'] = $author_nickname." (Гость) ".$author_email." ".$author_ip;
				}
			}

			$subj_id = (int) $_REQUEST['param0'];
			if($element->getIsActive()) {
				$params['blocking'] = <<<END
					<a href="%pre_lang%/admin/comments/comment_blocking/{$subj_id}/{$element_id}/0/$unactive"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
			} else {
				$params['blocking'] = <<<END
					<a href="%pre_lang%/admin/comments/comment_blocking/{$subj_id}/{$element_id}/1/$unactive"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
			}

			$result = $this->parse_form("view_comments_row", $params);
		}
		return $result;
	}

	public function comment_del() {
		$element_id = $_REQUEST['param0'];
		umiHierarchy::getInstance()->delElement($element_id);
		$this->redirect($this->pre_lang . "/admin/comments/view_comments/");
	}

	public function comment_blocking() {
		$parent_id = (int) $_REQUEST['param0'];
		$element_id = (int) $_REQUEST['param1'];
		$is_active = (bool) $_REQUEST['param2'];
		$unactive = (bool) $_REQUEST['param3'];

		$element = umiHierarchy::getInstance()->getElement($element_id);
		$element->setIsActive($is_active);
		$element->commit();
		$s_parent_id = $parent_id>0? $parent_id : "";
		$method = $unactive? "view_noactive_comments" : "view_comments";
		$this->redirect($this->pre_lang . "/admin/comments/".$method."/" . $s_parent_id . "/");
	}

	public function comment_edit() {
			$this->sheets_set_active("view_comments");
			
			$params = Array();
			$this->load_forms();

			$parent = $_REQUEST['param0'];
			$element_id = $_REQUEST['param1'];
			if(system_is_allowed("comments", "comment_edit", $parent))
				$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
			else
				$params['save_n_save'] = '<button title="Отменить" onclick="javascript: return edit_cancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и посмотреть" onclick="javascript: return save_with_redirect();" disabled="yes" />&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" disabled="yes" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " disabled="yes"  />';

			$element = umiHierarchy::getInstance()->getElement($element_id);


			$params['alt_name'] = 		$element->getAltName();
			$params['is_visible'] = 	$element->getIsVisible();
			$params['is_default'] = 	$element->getIsDefault();
			$params['name'] = 			$element->getObject()->getName();
			$params['h1'] = 			$element->getValue("h1");
			$params['title'] =			$element->getValue("title");
			$params['meta'] = 			$element->getValue("meta_keywords");
			$params['description'] = 	$element->getValue("meta_descriptions");

			$params['robots_deny'] =	$element->getValue("robots_deny");
			$params['tags'] =			implode(', ', $element->getValue("tags"));
			$params['show_submenu'] =	$element->getValue("show_submenu");
			$params['expanded'] =		$element->getValue("is_expanded");
			$params['unindexed'] =		$element->getValue("is_unindexed");

			$params['is_active'] =		$element->getIsActive();

			$params['descr'] =			$element->getValue("descr");
			$params['message'] =		$element->getValue("message");

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

			if(cmsController::getInstance()->getModule('users')) {
				$params['perm_panel'] = cmsController::getInstance()->getModule('users')->get_perm_panel("comments", "view", "comment_edit", $element_id);
			}

			if(cmsController::getInstance()->getModule('filemanager')) {
				$perm_panel = cmsController::getInstance()->getModule('filemanager')->upl_files("images/cms/content/");
				$params['files_panel'] = $perm_panel;
			}

			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("comments", "comment");
			$object_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type->getId());
			$params['object_types'] = putSelectBox_assoc($object_types, $element->getObject()->getTypeId(), false);


			if(cmsController::getInstance()->getModule('data')) {
				$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($element->getObject()->getTypeId(), $element_id, false);
			}

			if($backup_inst = cmsController::getInstance()->getModule("backup")) {
				$params['backup_panel'] = $backup_inst->backup_panel("comments", "comment_edit_do", $element_id);
			}



			$params['parent_id'] = $parent;
			$params['element_id'] = $element_id;
			$params['method'] = "comment_edit_do";
			$params['edit_cancel_redirect'] = $this->pre_lang . "/admin/comments/" . $parent;
			return $this->parse_form("comment_edit", $params);
	}

	public function comment_edit_do() {
			$parent_id = (int) $_REQUEST['param0'];
			$element_id = (int) $_REQUEST['param1'];

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
			$tags =(string) $_REQUEST['tags'];

			$object_type_id = (int) $_REQUEST['object_type_id'];

			$is_active = (bool) $_REQUEST['is_active'];

			$message = $_REQUEST['message'];



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

			$element->setValue("robots_deny", $robots_deny);
			$element->setValue("tags",$tags);
			$element->setValue("show_submenu", $show_submenu);
			$element->setValue("is_expanded", $expanded);
			$element->setValue("is_unindexed", $unindexed);
			
			$element->setValue("message", $message);

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


			if(cmsController::getInstance()->getModule('users')) {
				cmsController::getInstance()->getModule('users')->setPerms($element_id);
			}

			if($backup_inst = cmsController::getInstance()->getModule("backup")) {
				$backup_inst->backup_save("comments", "comment_edit_do", $element_id);
			}


			$exit_after_save = $_REQUEST['exit_after_save'];

			if($_REQUEST['exit_after_save'] == 2) {
				$link = umiHierarchy::getInstance()->getPathById($parent_id);
				$this->redirect($link);
			}

			if($exit_after_save) {
				$this->redirect($this->pre_lang . "/admin/comments/view_comments/");
			} else {
				$this->redirect($this->pre_lang . "/admin/comments/comment_edit/{$parent_id}/{$element_id}/");
			}
	}


	public function config() {
		$params = Array();
		$this->load_forms();
		$regedit = regedit::getInstance();

		$params['per_page']  = $regedit->getVal("//modules/comments/per_page");
		$params['moderated'] = $regedit->getVal("//modules/comments/moderated");
		$params['allow_guest'] = $regedit->getVal("//modules/comments/allow_guest");

		return $this->parse_form("config", $params);
	}


	public function config_do() {

		$regedit = regedit::getInstance();
		$regedit->setVar("//modules/comments/per_page", (int) $_REQUEST['per_page']);
		$regedit->setVar("//modules/comments/moderated", (int) $_REQUEST['moderated']);
		$regedit->setVar("//modules/comments/allow_guest", (int) $_REQUEST['allow_guest']);

		$this->redirect("admin", "comments", "config");
	}
}


?>