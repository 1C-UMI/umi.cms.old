<?php
	class forum extends def_module {
		public $per_page = 10;

		public function __construct() {
        	        parent::__construct();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$this->sheets_add("Конференции", "confs_list");
				$this->sheets_add("Последние сообщения", "last_messages");


				$this->__loadLib("__admin.php");
				$this->__implement("__forum");

				$this->__loadLib("__confs.php");
				$this->__implement("__confs_forum");

				$this->__loadLib("__confs_add.php");
				$this->__implement("__confs_add_forum");

				$this->__loadLib("__confs_edit.php");
				$this->__implement("__confs_edit_forum");

				$this->__loadLib("__topics.php");
				$this->__implement("__topics_forum");


				$this->__loadLib("__topics_add.php");
				$this->__implement("__topics_add_forum");


				$this->__loadLib("__topics_edit.php");
				$this->__implement("__topics_edit_forum");


				$this->__loadLib("__messages.php");
				$this->__implement("__messages_forum");


				$this->__loadLib("__messages_add.php");
				$this->__implement("__messages_add_forum");


				$this->__loadLib("__messages_edit.php");
				$this->__implement("__messages_edit_forum");

			} else {
				$this->__loadLib("__custom.php");
				$this->__implement("__custom_forum");
			}

			if($per_page = (int) regedit::getInstance()->getVal("//modules/forum/per_page")) {
				$this->per_page = $per_page;
			}
		}


		public function conf($templates = "default") {
			if(!$template) $template = "default";
			list($template_block, $template_line) = self::loadTemplates("tpls/forum/{$template}.tpl", "topics_block", "topics_block_line");

			$element_id = cmsController::getInstance()->getCurrentElementId();
			$element = umiHierarchy::getInstance()->getElement($element_id);

			templater::pushEditable("forum", "conf", $element_id);

			$per_page = $this->per_page;
			$curr_page = $_REQUEST['p'];

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$topic_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "topic")->getId();
			$sel->addElementType($topic_hierarchy_type_id);

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($element_id);

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("forum", "topic");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$publish_time_field_id = $object_type->getFieldId('publish_time');

			$sel->setOrderFilter();
			$sel->setOrderByProperty($publish_time_field_id, false);


			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);

			unset($sel);

			$message_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "message")->getId();

			$block_arr = Array();

			$lines = "";
			foreach($result as $topic_element_id) {
				$line_arr = Array();
				$topic_element = umiHierarchy::getInstance()->getElement($topic_element_id);

				$sel = new umiSelection;
				$sel->setElementTypeFilter();
				$sel->addElementType($message_hierarchy_type_id);

				$sel->setHierarchyFilter();
				$sel->addHierarchyFilter($topic_element_id);

				$sel->setPermissionsFilter();
				$sel->addPermissions();

				$messages_count = umiSelectionsParser::runSelectionCounts($sel);


				$line_arr['messages_count'] = $messages_count;
				$line_arr['id'] = $topic_element_id;
				$line_arr['name'] = $topic_element->getName();
				$line_arr['link'] = umiHierarchy::getInstance()->getPathById($topic_element_id);

				$lines .= self::parseTemplate($template_line, $line_arr, $topic_element_id);

				templater::pushEditable("forum", "topic", $topic_element_id);
			}

			$block_arr['id'] = $element_id;
			$block_arr['lines'] = $lines;
			$block_arr['total'] = $total;
			$block_arr['per_page'] = $per_page;


			return self::parseTemplate($template_block, $block_arr, $element_id);
		}


		public function topic($template = "default") {
			if(!$template) $template = "default";
			list($template_block, $template_line) = self::loadTemplates("./tpls/forum/{$template}.tpl", "messages_block", "messages_block_line");

			$element_id = cmsController::getInstance()->getCurrentElementId();
			$element = umiHierarchy::getInstance()->getElement($element_id);


			templater::pushEditable("forum", "topic", $element_id);

			$per_page = $this->per_page;
			$curr_page = $_REQUEST['p'];

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$topic_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "message")->getId();
			$sel->addElementType($topic_hierarchy_type_id);

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($element_id);

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("forum", "message");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$publish_time_field_id = $object_type->getFieldId('publish_time');

			$sel->setOrderFilter();
			$sel->setOrderByProperty($publish_time_field_id, true);


			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);

			unset($sel);

			$lines = "";
			$sz = sizeof($result);
			for($i = 0; $i < $sz; $i++) {
				$message_element_id = $result[$i];

				$message_element = umiHierarchy::getInstance()->getElement($message_element_id);

				$line_arr = Array();
				$line_arr['id'] = $message_element_id;
				$line_arr['name'] = $message_element->getName();
				$line_arr['num'] = ($per_page * $curr_page) + $i + 1;
				$line_arr['author_id'] = $message_element->getValue("author_id");
				$line_arr['message'] = self::formatMessage($message_element->getValue("message"));
				$lines .= self::parseTemplate($template_line, $line_arr, $message_element_id);

				templater::pushEditable("forum", "message", $message_element_id);
				
				umiHierarchy::getInstance()->unloadElement($element_id);
			}

			$block_arr = Array();
			$block_arr['id'] = $element_id;
			$block_arr['lines'] = $lines;
			$block_arr['total'] = $total;
			$block_arr['per_page'] = $per_page;
			return self::parseTemplate($template_block, $block_arr, $element_id);
		}




		public function confs_list($template = "default") {
			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				return __confs_forum::confs_list();
			}
			if(!$template) $template = "default";
			list($template_block, $template_line) = self::loadTemplates("./tpls/forum/{$template}.tpl", "confs_block", "confs_block_line");

			$per_page = $this->per_page;
			$curr_page = $_REQUEST['p'];

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "conf")->getId();
			$sel->addElementType($hierarchy_type_id);

			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);


			unset($sel);

			$lines = "";
			$sz = sizeof($result);
			for($i = 0; $i < $sz; $i++) {
				$conf_element_id = $result[$i];
				$conf_element = umiHierarchy::getInstance()->getElement($conf_element_id);

				$sel = new umiSelection;

				$sel->setElementTypeFilter();
				$topic_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "topic")->getId();
				$sel->addElementType($topic_hierarchy_type_id);

				$sel->setHierarchyFilter();
				$sel->addHierarchyFilter($conf_element_id);

				$sel->setPermissionsFilter();
				$sel->addPermissions();

				$topics_count = umiSelectionsParser::runSelectionCounts($sel);

				$sel = new umiSelection;


				$sel->setElementTypeFilter();
				$topic_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "message")->getId();
				$sel->addElementType($topic_hierarchy_type_id);

				$sel->setHierarchyFilter();
				$sel->addHierarchyFilter($conf_element_id, 1);

				$sel->setPermissionsFilter();
				$sel->addPermissions();

				$messages_count = umiSelectionsParser::runSelectionCounts($sel);


				$line_arr = Array();
				$line_arr['id'] = $conf_element_id;
				$line_arr['name'] = $conf_element->getName();
				$line_arr['link'] = umiHierarchy::getInstance()->getPathById($conf_element_id);
				$line_arr['topics_count'] = $topics_count;
				$line_arr['messages_count'] = $messages_count;
				$lines .= self::parseTemplate($template_line, $line_arr, $conf_element_id);

				templater::pushEditable("forum", "conf", $conf_element_id);
			}

			$block_arr = Array();
			$block_arr['lines'] = $lines;
			$block_arr['total'] = $total;
			$block_arr['per_page'] = $per_page;
			return self::parseTemplate($template_block, $block_arr);
		}


		public function message() {
			$element_id = cmsController::getInstance()->getCurrentElementId();
			$element = umiHierarchy::getInstance()->getElement($element_id);

			$per_page = $this->per_page;

			$parent_id = $element->getParentId();
			$parent_element = umiHierarchy::getInstance()->getElement($parent_id);

			if($element->getValue("publish_time"))
			$publish_time = $element->getValue("publish_time")->getFormattedDate("U");



			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$topic_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "message")->getId();
			$sel->addElementType($topic_hierarchy_type_id);

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($parent_id);

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("forum", "message");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$publish_time_field_id = $object_type->getFieldId('publish_time');

			$sel->setOrderFilter();
			$sel->setOrderByProperty($publish_time_field_id, true);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterLess($publish_time_field_id, $publish_time);

			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$total = umiSelectionsParser::runSelectionCounts($sel);

			$p = ceil($total / $this->per_page) - 1;
			if($p < 0) $p = 0;


			$url = umiHierarchy::getInstance()->getPathById($parent_id) . "?p={$p}#" . $element_id;
			$this->redirect($url);
		}


		public function topic_last_message($element_id, $template = "default") {
			if(!$template) $template = "default";
			list($template_block) = self::loadTemplates("./tpls/forum/{$template}.tpl", "topic_last_message");

			$element_id = $this->analyzeRequiredPath($element_id);

			$sel = new umiSelection;

			$sel->setLimitFilter();
			$sel->addLimit(1);

			$sel->setElementTypeFilter();
			$element_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "message")->getId();
			$sel->addElementType($element_hierarchy_type_id);

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($element_id);

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("forum", "message");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$publish_time_field_id = $object_type->getFieldId('publish_time');

			$sel->setOrderFilter();
			$sel->setOrderByProperty($publish_time_field_id, false);


			$sel->setPermissionsFilter();
			$sel->addPermissions();

			list($message_element_id) = umiSelectionsParser::runSelection($sel);

			$block_arr = Array();

			if($message_element_id) {
				$message_element = umiHierarchy::getInstance()->getElement($message_element_id);

				$block_arr['id'] = $message_element_id;
				$block_arr['name'] = $message_element->getName();
				$block_arr['link'] = $this->getMessageLink($message_element_id);
				$block_arr['author_id'] = $message_element->getValue("author_id");
				//$block_arr['publish_time'] = $message_element->getValue("publish_time");
			} else {
				return "";
			}

			templater::pushEditable("forum", "message", $message_element_id);

			return self::parseTemplate($template_block, $block_arr, $message_element_id);
		}


		public function conf_last_message($element_id, $template = "default") {
			if(!$template) $template = "default";
			list($template_block) = self::loadTemplates("./tpls/forum/{$template}.tpl", "conf_last_message");

			$element_id = $this->analyzeRequiredPath($element_id);

			$sel = new umiSelection;

			$sel->setLimitFilter();
			$sel->addLimit(1);

			$sel->setElementTypeFilter();
			$element_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "message")->getId();
			$sel->addElementType($element_hierarchy_type_id);

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($element_id, 1);

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("forum", "message");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$publish_time_field_id = $object_type->getFieldId('publish_time');

			$sel->setOrderFilter();
			$sel->setOrderByProperty($publish_time_field_id, false);


			$sel->setPermissionsFilter();
			$sel->addPermissions();

			list($message_element_id) = umiSelectionsParser::runSelection($sel);

			$block_arr = Array();

			if($message_element_id) {
				$message_element = umiHierarchy::getInstance()->getElement($message_element_id);

				$block_arr['id'] = $message_element_id;
				$block_arr['name'] = $message_element->getName();
				$block_arr['link'] = $this->getMessageLink($message_element_id);
				$block_arr['author_id'] = $message_element->getValue("author_id");
			} else {
				return "";
			}

			templater::pushEditable("forum", "message", $message_element_id);

			return self::parseTemplate($template_block, $block_arr, $message_element_id);
		}


		public function topic_post($element_id) {
			if(!$template) $template = "default";
			
			list($template_block_user, $template_block_guest) = self::loadTemplates("./tpls/forum/{$template}.tpl", "add_topic_user", "add_topic_guest");

			$element_id = $this->analyzeRequiredPath($element_id);

			if($users_inst = cmsController::getInstance()->getModule("users")) {
				if($users_inst->is_auth()) {
					$template = $template_block_user;
				} else {
					if(!(regedit::getInstance()->getVal("//modules/forum/allow_guest"))) {
						return "";
					}
					
					$template = $template_block_guest;
				}
			}

			$block_arr = Array();

			if($element = umiHierarchy::getInstance()->getElement($element_id)) {
				$block_arr['id'] = $element_id;
				$block_arr['name'] = $element->getName();
				$block_arr['action'] = $this->pre_lang . "/forum/topic_post_do/" . $element_id . "/";
			} else {
				//TODO: exception
			}

			//TODO: captcha

			return self::parseTemplate($template, $block_arr, $element_id);
		}

		public function message_post($element_id, $template = "default") {
			if(!$template) $template = "default";
			list($template_block_user, $template_block_guest) = self::loadTemplates("./tpls/forum/{$template}.tpl", "add_message_user", "add_message_guest");

			$element_id = $this->analyzeRequiredPath($element_id);

			if($users_inst = cmsController::getInstance()->getModule("users")) {
				if($users_inst->is_auth()) {
					$template = $template_block_user;
				} else {
					if(!(regedit::getInstance()->getVal("//modules/forum/allow_guest"))) {
						return "";
					}

					$template = $template_block_guest;
				}
			}

			$block_arr = Array();

			if($element = umiHierarchy::getInstance()->getElement($element_id)) {
				$block_arr['id'] = $element_id;
				$block_arr['name'] = $element->getName();
				$block_arr['action'] = $this->pre_lang . "/forum/message_post_do/" . $element_id . "/";
			}

			//TODO: captcha

			return self::parseTemplate($template, $block_arr, $element_id);
		}


		public function topic_post_do() {
			if($users_inst = cmsController::getInstance()->getModule("users")) {
				if(!$users_inst->is_auth()) {
					if(!(regedit::getInstance()->getVal("//modules/forum/allow_guest"))) {
						return "%forum_not_allowed_post%";
					}
				}
			}


			$parent_id = (int) $_REQUEST['param0'];
			$parent_element = umiHierarchy::getInstance()->getElement($parent_id);

			$title = $_REQUEST['title'];
			$body = $_REQUEST['body'];
			
			$title = strip_tags($title);
			$body = strip_tags($body);

			$nickname = $_REQUEST['nickname'];
			$email = $_REQUEST['email'];

			$ip = $_SERVER['REMOTE_ADDR'];

			$publish_time = new umiDate(time());

			// check captcha
			$referer_url = $_SERVER['HTTP_REFERER'];
			if (isset($_REQUEST['captcha'])) {
				$_SESSION['user_captcha'] = md5((int) $_REQUEST['captcha']);
			}
			if (!umiCaptcha::checkCaptcha()) {
				$this->redirect($referer_url);
				exit();
			}

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();
			$tpl_id = $parent_element->getTplId();
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "topic")->getId();
			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("forum", "topic");

			if($users_inst = cmsController::getInstance()->getModule("users")) {
				if($user_id = $users_inst->user_id) {
					$author_id = $users_inst->createAuthorUser($user_id);
				} else {
					$author_id = $users_inst->createAuthorGuest($nickname, $email, $ip);
				}
			}

			$element_id = umiHierarchy::getInstance()->addElement($parent_id, $hierarchy_type_id, $title, $title, $object_type_id, $domain_id, $lang_id, $tpl_id);

			cmsController::getInstance()->getModule("users")->setDefaultPermissions($element_id);

			$element = umiHierarchy::getInstance()->getElement($element_id);
			$element->setIsVisible(false);
			$element->setIsActive(true);
			
			$element->getObject()->setName($title);

			$element->setValue("meta_descriptions", $meta_descriptions);
			$element->setValue("meta_keywords", $meta_keywords);
			$element->setValue("h1", $title);
			$element->setValue("title", $title);
			$element->setValue("is_expanded", false);
			$element->setValue("show_submenu", false);
			$element->setValue("publish_time", $publish_time);
			$element->setValue("author_id", $author_id);

			$element->commit();


			$_REQUEST['param0'] = $element_id;
			$this->message_post_do();
		}

		public function message_post_do() {
			if($users_inst = cmsController::getInstance()->getModule("users")) {
				if(!$users_inst->is_auth()) {
					if(!(regedit::getInstance()->getVal("//modules/forum/allow_guest"))) {
						return "%forum_not_allowed_post%";
					}
				}
			}


			$title = $_REQUEST['title'];
			$body = $_REQUEST['body'];
			
			$title = strip_tags($title);
			$body = strip_tags($body);

			$nickname = $_REQUEST['nickname'];
			$email = $_REQUEST['email'];

			$ip = $_SERVER['REMOTE_ADDR'];

			$publish_time = new umiDate(time());


			$parent_id = (int) $_REQUEST['param0'];
			$parent_element = umiHierarchy::getInstance()->getElement($parent_id);

			// check captcha
			$referer_url = $_SERVER['HTTP_REFERER'];
			if (isset($_REQUEST['captcha'])) {
				$_SESSION['user_captcha'] = md5((int) $_REQUEST['captcha']);
			}
			if (!umiCaptcha::checkCaptcha() || !$parent_element) {
				$this->redirect($referer_url);
				exit();
			}

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();
			$tpl_id = $parent_element->getTplId();
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "message")->getId();
			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("forum", "message");

			if($users_inst = cmsController::getInstance()->getModule("users")) {
				if($user_id = $users_inst->user_id) {
					$author_id = $users_inst->createAuthorUser($user_id);
				} else {
					$author_id = $users_inst->createAuthorGuest($nickname, $email, $ip);
				}
			}


			$element_id = umiHierarchy::getInstance()->addElement($parent_id, $hierarchy_type_id, $title, $title, $object_type_id, $domain_id, $lang_id, $tpl_id);
			cmsController::getInstance()->getModule("users")->setDefaultPermissions($element_id);

			$element = umiHierarchy::getInstance()->getElement($element_id);
			$element->setIsVisible(false);
			$element->setIsActive(true);
			
			$element->getObject()->setName($title);

			$element->setValue("meta_descriptions", "");
			$element->setValue("meta_keywords", "");
			$element->setValue("h1", $title);
			$element->setValue("title", $title);
			$element->setValue("is_expanded", false);
			$element->setValue("show_submenu", false);
			$element->setValue("publish_time", $publish_time);
			$element->setValue("author_id", $author_id);
			$element->setValue("message", $body);

			$element->commit();

			$path = $this->getMessageLink($element_id);
			$this->redirect($path);
		}


		public function getMessageLink($element_id) {
			$element_id = $this->analyzeRequiredPath($element_id);

			$element = umiHierarchy::getInstance()->getElement($element_id);

			$parent_id = $element->getParentId();
			$parent_element = umiHierarchy::getInstance()->getElement($parent_id);

			if($element->getValue("publish_time"))
			$publish_time = $element->getValue("publish_time")->getFormattedDate("U");



			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$topic_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "message")->getId();
			$sel->addElementType($topic_hierarchy_type_id);

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($parent_id);

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("forum", "message");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$publish_time_field_id = $object_type->getFieldId('publish_time');

			$sel->setOrderFilter();
			$sel->setOrderByProperty($publish_time_field_id, true);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterLess($publish_time_field_id, $publish_time);

			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$total = umiSelectionsParser::runSelectionCounts($sel);

			$p = ceil($total / $this->per_page) - 1;
			if($p < 0) $p = 0;


			return umiHierarchy::getInstance()->getPathById($parent_id) . "?p={$p}#" . $element_id;
		}


		public function config () {
			if(class_exists("__forum")) {
				return __forum::config();
			}
		}


		public function getEditLink($element_id, $element_type) {
			$element = umiHierarchy::getInstance()->getElement($element_id);
			$parent_id = $element->getParentId();

			switch($element_type) {
				case "conf": {
					$link_add = $this->pre_lang . "/admin/forum/topic_add/{$element_id}/";
					$link_edit = $this->pre_lang . "/admin/forum/conf_edit/{$element_id}/";

					return Array($link_add, $link_edit);
					break;
				}


				case "topic": {
					$link_add = $this->pre_lang . "/admin/forum/message_add/{$element_id}/";
					$link_edit = $this->pre_lang . "/admin/forum/topic_edit/{$parent_id}/{$element_id}/";

					return Array($link_add, $link_edit);
					break;
				}


				case "message": {
					$link_add = false;
					$link_edit = $this->pre_lang . "/admin/forum/message_edit/{$parent_id}/{$element_id}/";

					return Array($link_add, $link_edit);
					break;
				}

				default: {
					return false;
				}
			}
		}

	};

?>