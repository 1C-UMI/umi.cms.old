<?php

class faq extends def_module {
	public function __construct() {
		parent::__construct();

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$this->__loadLib("__projects.php");
			$this->__implement("__faq_projects");

			$this->__loadLib("__project_add.php");
			$this->__implement("__faq_project_add");

			$this->__loadLib("__project_edit.php");
			$this->__implement("__faq_project_edit");

			$this->__loadLib("__categories.php");
			$this->__implement("__faq_categories");

			$this->__loadLib("__category_add.php");
			$this->__implement("__faq_category_add");

			$this->__loadLib("__category_edit.php");
			$this->__implement("__faq_category_edit");

			$this->__loadLib("__questions.php");
			$this->__implement("__faq_questions");

			$this->__loadLib("__question_add.php");
			$this->__implement("__faq_question_add");

			$this->__loadLib("__question_edit.php");
			$this->__implement("__faq_question_edit");

			$this->sheets_reset();
			$this->sheets_add("Список проектов", "projects_list");

		}

		$regedit = regedit::getInstance();
		$this->per_page = (int) $regedit->getVal("//modules/faq/per_page");
	}

	public function question($template = "default", $element_path = false) {
		if(!$template) $template = "default";
		list($template_block) = def_module::loadTemplates("tpls/faq/{$template}.tpl", "question");

		$element_id = $this->analyzeRequiredPath($element_path);

		$element = umiHierarchy::getInstance()->getElement($element_id);

		$line_arr = Array();
		if ($element) {
			$line_arr['id'] = $element_id;
			$line_arr['text'] = $element->getName();
			$line_arr['alt_name'] = $element->getAltName();
			$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);
			$line_arr['question'] = $element->getValue("question");
			$line_arr['answer'] = $element->getValue("content");
		}
		$block_arr = Array();

		templater::pushEditable("faq", "question", $element_id);

		return self::parseTemplate($template_block, $block_arr, $element_id);
	}

	public function project($template = "default", $element_path = false, $limit=false) {
		if(!$template) $template = "default";
		list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/faq/{$template}.tpl", "categories_block", "categories_block_empty", "categories_block_line");
		
		$project_id = $this->analyzeRequiredPath($element_path);

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "category")->getId();

		$per_page = ($limit) ? $limit : $this->per_page;
		$curr_page = (int) $_REQUEST['p'];

		
		$sel = new umiSelection;
		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);
		$sel->setHierarchyFilter();
		$sel->addHierarchyFilter($project_id);
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$sel->setPermissionsFilter();
		$sel->addPermissions();
		$result = umiSelectionsParser::runSelection($sel);
		

		if(($sz = sizeof($result)) > 0) {
			$block_arr = Array();

			$lines = "";
			for($i = 0; $i < $sz; $i++) {
				if ($i < $limit || $limit === false) {
					$element_id = $result[$i];
					$element = umiHierarchy::getInstance()->getElement($element_id);

					if(!$element) continue;

					$line_arr = Array();
					$line_arr['id'] = $element_id;
					$line_arr['text'] = $element->getName();
					$line_arr['alt_name'] = $element->getAltName();
					$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);

					templater::pushEditable("faq", "category", $element_id);

					$lines .= self::parseTemplate($template_line, $line_arr, $element_id);
				}
			}

			$block_arr['lines'] = $lines;
			return self::parseTemplate($template_block, $block_arr);
		} else {
			return $template_block_empty;
		}
	}

	public function category($template = "default", $element_path = false, $limit=false) {
		if(!$template) $template = "default";
		list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/faq/{$template}.tpl", "questions_block", "questions_block_empty", "questions_block_line");
		
		$category_id = $this->analyzeRequiredPath($element_path);

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "question")->getId();

		$per_page = ($limit) ? $limit : $this->per_page;
		$curr_page = (int) $_REQUEST['p'];

		
		$sel = new umiSelection;
		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);
		$sel->setHierarchyFilter();

		$sel->addHierarchyFilter($category_id);
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$sel->setPermissionsFilter();
		$sel->addPermissions();
		$result = umiSelectionsParser::runSelection($sel);
		

		if(($sz = sizeof($result)) > 0) {
			$block_arr = Array();

			$lines = "";
			for($i = 0; $i < $sz; $i++) {
				if ($i < $limit || $limit === false) {
					$element_id = $result[$i];
					$element = umiHierarchy::getInstance()->getElement($element_id);

					if(!$element) continue;

					$line_arr = Array();
					$line_arr['id'] = $element_id;
					$line_arr['text'] = $element->getName();
					$line_arr['alt_name'] = $element->getAltName();
					$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);
					$line_arr['question'] = $element->getValue("question");
					$line_arr['answer'] = $element->getValue("content");
					
					templater::pushEditable("faq", "question", $element_id);

					$lines .= self::parseTemplate($template_line, $line_arr, $element_id);
				}
			}

			$block_arr['lines'] = $lines;
			return self::parseTemplate($template_block, $block_arr);
		} else {
			return $template_block_empty;
		}
	}

	public function projects($template = "default", $element_path = false, $limit=false) {
		if(!$template) $template = "default";
		list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("tpls/faq/{$template}.tpl", "projects_block", "projects_block_empty", "projects_block_line");
		
		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "project")->getId();

		$per_page = ($limit) ? $limit : $this->per_page;
		$curr_page = (int) $_REQUEST['p'];

		
		$sel = new umiSelection;
		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$sel->setPermissionsFilter();
		$sel->addPermissions();
		$result = umiSelectionsParser::runSelection($sel);
		

		if(($sz = sizeof($result)) > 0) {
			$block_arr = Array();

			$lines = "";
			for($i = 0; $i < $sz; $i++) {
				if ($i < $limit || $limit === false) {
					$element_id = $result[$i];
					$element = umiHierarchy::getInstance()->getElement($element_id);

					if(!$element) continue;

					$line_arr = Array();
					$line_arr['id'] = $element_id;
					$line_arr['text'] = $element->getName();
					$line_arr['alt_name'] = $element->getAltName();
					$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);

					templater::pushEditable("faq", "project", $element_id);
					$lines .= self::parseTemplate($template_line, $line_arr, $element_id);
				}
			}

			$block_arr['lines'] = $lines;
			return self::parseTemplate($template_block, $block_arr);
		} else {
			return $template_block_empty;
		}
	}

	public function addQuestionForm($template="default", $category_path=false) {
		if(!$template) $template = "default";
		list($template_add_user, $template_add_guest) = def_module::loadTemplates("tpls/faq/{$template}.tpl", "question_add_user", "question_add_guest");

		$category_id = $this->analyzeRequiredPath($category_path);

		if(cmsController::getInstance()->getModule("users")->is_auth()) {
			$template_add = $template_add_user;
		} else {
			$template_add = $template_add_guest;
		}
		$block_arr['action'] = $this->pre_lang . "/faq/post_question/" . $category_id . "/";

		return self::parseTemplate($template_add, $block_arr);
	}

	public function post_question() {
		$parent_element_id = $_REQUEST['param0'];
		// input
		$email = strip_tags($_REQUEST['email']);
		$nick = strip_tags($_REQUEST['nick']);
		$title = strip_tags($_REQUEST['title']);
		$question = strip_tags($_REQUEST['question']);

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
				$user_id = $users_inst->user_id;
				$this->is_auth = true;
				if($user_obj = umiObjectsCollection::getInstance()->getObject($user_id)) {
					$email = $user_obj->getValue("e-mail");
					$nick = $user_obj->getName();
				}
			}
		}
		

		$is_active = 0;

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("faq", "question");
		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "question")->getId();

		$parentElement = umiHierarchy::getInstance()->getElement($parent_element_id);
		$tpl_id		= $parentElement->getTplId();
		$domain_id	= $parentElement->getDomainId();
		$lang_id	= $parentElement->getLangId();

		$element_id = umiHierarchy::getInstance()->addElement($parent_element_id, $hierarchy_type_id, $title, $title, $object_type_id, $domain_id, $lang_id, $tpl_id);

		cmsController::getInstance()->getModule("users")->setDefaultPermissions($element_id);

		$element = umiHierarchy::getInstance()->getElement($element_id);

		$element->setIsActive($is_active);
		$element->setIsVisible(false);

		$element->setValue("question", $question);
		$element->setValue("publish_time", $posttime);

		$element->getObject()->setName($title);
		$element->setValue("h1", $title);

		$element->setValue("author_id", $author_id);

		// send mails
		
		$from = regedit::getInstance()->getVal("//settings/fio_from");
		$from_email = regedit::getInstance()->getVal("//settings/email_from");
		$admin_email = regedit::getInstance()->getVal("//settings/admin_email");

		list($confirm_mail_subj_user, $confirm_mail_user, $confirm_mail_subj_admin, $confirm_mail_admin) = def_module::loadTemplates("tpls/faq/default.tpl", "confirm_mail_subj_user", "confirm_mail_user", "confirm_mail_subj_admin", "confirm_mail_admin");

		// for admin
		$mail_arr = Array();
		$mail_arr['domain'] = $domain = $_SERVER['HTTP_HOST'];
		$mail_arr['question'] = $question;
		$mail_arr['question_link'] = "http://" . $domain . $this->pre_lang. "/admin/faq/question_edit/".$element->getParentId()."/".$element_id;
		$mail_adm_content = def_module::parseTemplate($confirm_mail_admin, $mail_arr);

		$confirmAdminMail = new umiMail();
		$confirmAdminMail->addRecipient($admin_email);
		$confirmAdminMail->setFrom($email, $nick);
		$confirmAdminMail->setSubject($confirm_mail_subj_admin);
		$confirmAdminMail->setContent($mail_adm_content);
		$confirmAdminMail->commit();
		$confirmAdminMail->send();

		// for user 
		$user_mail = Array();
		$user_mail['domain'] = $domain = $_SERVER['HTTP_HOST'];
		$user_mail['question'] = $question;
		$user_mail['ticket'] = $element_id;
		$mail_usr_content = def_module::parseTemplate($confirm_mail_user, $user_mail);

		$confirmMail = new umiMail();
		$confirmMail->addRecipient($email);
		$confirmMail->setFrom($from_email, $from);
		$confirmMail->setSubject($confirm_mail_subj_user);
		$confirmMail->setContent($mail_usr_content);
		$confirmMail->commit();
		$confirmMail->send();
		$element->commit();
		
		return $mail_usr_content;
	}

	public function config() {
		$params = array();
		$this->load_forms();
		$regedit = regedit::getInstance();

		$params['per_page']  = $regedit->getVal("//modules/faq/per_page");

		return $this->parse_form("config", $params);
	}

	public function config_do() {

		$regedit = regedit::getInstance();
		$regedit->setVar("//modules/faq/per_page", (int) $_REQUEST['per_page']);

		$this->redirect("admin", "faq", "config");
	}

	public function getEditLink($element_id, $element_type) {
		$element = umiHierarchy::getInstance()->getElement($element_id);
		$parent_id = $element->getParentId();

		switch($element_type) {
			case "project": {
				$link_add = $this->pre_lang . "/admin/faq/category_add/{$element_id}/";
				$link_edit = $this->pre_lang . "/admin/faq/project_edit/0/{$element_id}/";

				return Array($link_add, $link_edit);
				break;
			}

			case "category": {
				$link_add = $this->pre_lang . "/admin/faq/question_add/{$element_id}/";
				$link_edit = $this->pre_lang . "/admin/faq/category_edit/{$parent_id}/{$element_id}/";

				return Array($link_add, $link_edit);
				break;
			}

			case "question": {
				$link_edit = $this->pre_lang . "/admin/faq/question_edit/{$parent_id}/{$element_id}/";

				return Array(false, $link_edit);
				break;
			}

			default: {
				return false;
			}
		}
	}
}

?>