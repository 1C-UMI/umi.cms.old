<?php

	class comments extends def_module {
		public function __construct() {
			parent::__construct();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$this->__loadLib("__admin.php");
				$this->__implement("__comments");

				$this->sheets_reset();
				$this->sheets_add("Все комментарии", "view_comments");
				$this->sheets_add("Неактивные комментарии", "view_noactive_comments");
			}

			$regedit = regedit::getInstance();
			$this->per_page = (int) $regedit->getVal("//modules/comments/per_page");
			$this->moderated = (int) $regedit->getVal("//modules/comments/moderated");
		}


		public function countComments($parent_element_id) {
			$parent_element_id = $this->analyzeRequiredPath($parent_element_id);

			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("comments", "comment")->getId();
			$sel = new umiSelection;

			$sel->setElementTypeFilter();
			$sel->addElementType($hierarchy_type_id);
			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($parent_element_id);
			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$total = umiSelectionsParser::runSelectionCounts($sel);
			return $total;
		}


		public function insert($parent_element_id, $template = "default") {
			if(!$template) $template = "default";

			$parent_element_id = $this->analyzeRequiredPath($parent_element_id);

			list($template_block, $template_line, $template_add_user, $template_add_guest) = self::loadTemplates("./tpls/comments/{$template}.tpl", "comments_block", "comments_block_line", "comments_block_add_user", "comments_block_add_guest");

			if(cmsController::getInstance()->getModule("users")->is_auth()) {
				$template_add = $template_add_user;
			} else {
				$template_add = (regedit::getInstance()->getVal("//modules/comments/allow_guest")) ? $template_add_guest : "";
			}

			$template_line = $template_line;


			$per_page = $this->per_page;
			$curr_page = (int) $_REQUEST['p'];

			$block_arr = Array();

			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("comments", "comment")->getId();

			$sel = new umiSelection;

			$sel->setElementTypeFilter();
			$sel->addElementType($hierarchy_type_id);
			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($parent_element_id);
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
			$lines = "";
			$i = 0;
			foreach($result as $element_id) {
				$line_arr = Array();

				$element = umiHierarchy::getInstance()->getElement($element_id);

				$line_arr['title'] = $element->getName();
				$line_arr['num'] = ($per_page * $curr_page) + (++$i);
				$line_arr['message'] = self::formatMessage($element->getValue("message"));

				templater::pushEditable("comments", "comment", $element_id);

				$lines .= self::parseTemplate($template_line, $line_arr, $element_id);
			}

			$block_arr['per_page'] = $per_page;
			$block_arr['total'] = $total;

			$block_arr['lines'] = $lines;
			$block_arr['add_form'] = $template_add;
			$block_arr['action'] = $this->pre_lang . "/comments/post/" . $parent_element_id . "/";
			return self::parseTemplate($template_block, $block_arr);
		}


		public function post() {
			$parent_element_id = (int) $_REQUEST['param0'];

			$title = strip_tags($_REQUEST['title']);
			$content = strip_tags($_REQUEST['comment']);
			$nick = strip_tags($_REQUEST['author_nick']);
			$email = strip_tags($_REQUEST['author_email']);

			$referer_url = $_SERVER['HTTP_REFERER'];
			$posttime = time();
			$ip = $_SERVER['REMOTE_ADDR'];

			// check captcha
			if (isset($_REQUEST['captcha'])) {
				$_SESSION['user_captcha'] = md5((int) $_REQUEST['captcha']);
			}

			if (!umiCaptcha::checkCaptcha() || !$parent_element_id) {
				$this->redirect($referer_url);
			}
		
			$user_id = cmsController::getInstance()->getModule('users')->user_id;

			if($users_inst = cmsController::getInstance()->getModule("users")) {
				if($users_inst->is_auth()) {
					$author_id = $users_inst->createAuthorUser($user_id);
				} else {
					if(!(regedit::getInstance()->getVal("//modules/comments/allow_guest"))) {
						return "%comments_not_allowed_post%";
					}

					$author_id = $users_inst->createAuthorGuest($nick, $email, $ip);
				}
			}

			$nick = strip_tags($_REQUEST['nick']);
			$email = strip_tags($_REQUEST['email']);

			$is_active = ($this->moderated && !$is_sv) ? 0 : 1;


			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("comments", "comment");
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("comments", "comment")->getId();

			$parentElement = umiHierarchy::getInstance()->getElement($parent_element_id);
			$tpl_id		= $parentElement->getTplId();
			$domain_id	= $parentElement->getDomainId();
			$lang_id	= $parentElement->getLangId();

			$element_id = umiHierarchy::getInstance()->addElement($parent_element_id, $hierarchy_type_id, $title, $title, $object_type_id, $domain_id, $lang_id, $tpl_id);

			cmsController::getInstance()->getModule("users")->setDefaultPermissions($element_id);

			$element = umiHierarchy::getInstance()->getElement($element_id);

			$element->setIsActive($is_active);
			$element->setIsVisible(false);

			$element->setValue("message", $content);
			$element->setValue("publish_time", $posttime);

			$element->getObject()->setName($title);
			$element->setValue("h1", $title);

			$element->setValue("author_id", $author_id);

			// moderate
			$element->commit();

			$this->redirect($referer_url);
		}


		public function getEditLink($element_id, $element_type) {
			$element = umiHierarchy::getInstance()->getElement($element_id);
			$parent_id = $element->getParentId();

			switch($element_type) {
				case "comment": {
					$link_edit = $this->pre_lang . "/admin/comments/comment_edit/{$parent_id}/{$element_id}/";

					return Array(false, $link_edit);
					break;
				}

				default: {
					return false;
				}
			}
		}


		public function comment() {
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
			$topic_hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("comments", "comment")->getId();
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

		public function config() {
			if(class_exists("__comments")) {
				return __comments::config();
			}
		}

	};
?>