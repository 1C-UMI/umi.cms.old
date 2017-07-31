<?php
	abstract class __register_users {

		public function settings($template = "default") {
			if(!$template) $template = "default";

			list($template_block) = def_module::loadTemplates("tpls/users/register/{$template}.tpl", "settings_block");
			$block_arr = Array();
			$block_arr['user_id'] = $this->user_id;


			return def_module::parseTemplate($template_block, $block_arr, false, $this->user_id);
		}

		public function settings_do($template = "default") {
			$object_id = $this->user_id;

			$password = (string) $_POST['password'];
			$email = (string) $_REQUEST['email'];

			$object = umiObjectsCollection::getInstance()->getObject($object_id);

			$object->setValue("password", (($password) ? md5($password) : ""));
			$object->setValue("e-mail", $email);

			cmsController::getInstance()->getModule('data');
			$data_module = cmsController::getInstance()->getModule('data');
			$data_module->saveEditedObject($object_id);

			$object->commit();

			$url = ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->pre_lang . "/users/settings/";
			$this->redirect($url);
		}

		public function registrate($template = "default") {
			if(!$template) $template = "default";

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");

			list($template_block) = def_module::loadTemplates("tpls/users/register/{$template}.tpl", "registrate_block");

			$block_arr = Array();
			$block_arr['type_id'] = $object_type_id;


			return def_module::parseTemplate($template_block, $block_arr);
		}

		public function registrate_do($template = "default") {
			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			$login = (string) $_REQUEST['login'];
			$password = (string) $_REQUEST['password'];
			$email = (string) $_REQUEST['email'];


			$login_field_id = $object_type->getFieldId("login");


			// check captcha
			$referer_url = $_SERVER['HTTP_REFERER'];
			if (isset($_REQUEST['captcha'])) {
				$_SESSION['user_captcha'] = md5((int) $_REQUEST['captcha']);
			}
			if (!umiCaptcha::checkCaptcha()) {
				$this->redirect($referer_url);
				exit();
			}

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit(1);

			$sel->setObjectTypeFilter();
			$sel->addObjectType($object_type_id);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($login_field_id, $login);

			$is_exists = (bool) umiSelectionsParser::runSelectionCounts($sel);

			if($is_exists) {
				return "Пользователь с таким логином уже существует";
			}

			//Creating user...
			$object_id = umiObjectsCollection::getInstance()->addObject($login, $object_type_id);
			$activate_code = md5($login . time());
//			$object_id = 25760;

			$object = umiObjectsCollection::getInstance()->getObject($object_id);

			$object->setValue("login", $login);
			$object->setValue("password", md5($password));
			$object->setValue("e-mail", $email);

			$object->setValue("is_activated", false);
			$object->setValue("activate_code", $activate_code);

			$group_id = regedit::getInstance()->getVal("//modules/users/def_group");
			$object->setValue("groups", Array($group_id));

			cmsController::getInstance()->getModule('data');
			$data_module = cmsController::getInstance()->getModule('data');
			$data_module->saveEditedObject($object_id, true);

			$object->commit();



			//Forming mail...
			list($template_mail, $template_mail_subject) = def_module::loadTemplates("tpls/users/register/{$template}.tpl", "mail_registrated", "mail_registrated_subject");
			$mail_arr = Array();
			$mail_arr['domain'] = $domain = $_SERVER['HTTP_HOST'];
			$mail_arr['activate_link'] = "http://" . $domain . $this->pre_lang . "/users/activate/" . $activate_code . "/";
			$mail_arr['password'] = $password;

			$mail_content = def_module::parseTemplate($template_mail, $mail_arr, false, $object_id);

			$fio = $object->getValue("lname") . " " . $object->getValue("fname") . " " . $object->getValue("father_name");

			$email_from = regedit::getInstance()->getVal("//settings/email_from");
			$fio_from = regedit::getInstance()->getVal("//settings/fio_from");


			$someMail = new umiMail();
			$someMail->addRecipient($email, $fio);
			$someMail->setFrom($email_from, $fio_from);
			$someMail->setSubject($template_mail_subject);
			$someMail->setContent($mail_content);
			$someMail->commit();
			$someMail->send();

			$this->redirect($this->pre_lang . "/users/registrate_done/");
		}

		public function registrate_done($tempalte = "default") {
			if(!$template) $template = "default";

			list($template_block) = def_module::loadTemplates("./tpls/users/register/{$template}.tpl", "registrate_done_block");
			$block_arr = Array();

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function activate($template = "default") {
			if(!$template) $template = "default";

			list($template_block, $template_block_failed) = def_module::loadTemplates("./tpls/users/register/{$template}.tpl", "activate_block", "activate_block_failed");


			$activate_code = (string) $_REQUEST['param0'];


			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			$activate_code_field_id = $object_type->getFieldId("activate_code");

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit(1);

			$sel->setObjectTypeFilter();
			$sel->addObjectType($object_type_id);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($activate_code_field_id, $activate_code);

			$result = umiSelectionsParser::runSelection($sel);

			if($result) {
				list($user_id) = $result;

				$user = umiObjectsCollection::getInstance()->getObject($user_id);
				$user->setValue("is_activated", 1);
				$user->setValue("activate_code", md5(uniqid(rand(), true)));

				$template = $template_block;
			} else {
				$template = $template_block_failed;
			}

			return def_module::parseTemplate($template, $block_arr);
		}

	};
?>