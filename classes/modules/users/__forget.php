<?php
	abstract class __forget_users {
		public function forget($template = "default") {
			if(!$template) $template = "default";

			list($template_block) = def_module::loadTemplates("tpls/users/forget/{$template}.tpl", "forget_block");
			$block_arr = Array();

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function forget_do($template = "default") {
			if(!$template) $template = "default";

			list($template_wrong_login_block, $template_forget_sended, $template_mail_verification, $template_mail_verification_subject) = def_module::loadTemplates("tpls/users/forget/{$template}.tpl", "wrong_login_block", "forget_sended", "mail_verification", "mail_verification_subject");

			$forget_login = (string) $_REQUEST['forget_login'];

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$login_field_id = $object_type->getFieldId("login");

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit(1);

			$sel->setObjectTypeFilter();
			$sel->addObjectType($object_type_id);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($login_field_id, $forget_login);

			list($user_id) = umiSelectionsParser::runSelection($sel);

			if($user_id) {
				$activate_code = md5(self::getRandomPassword());

				$object = umiObjectsCollection::getInstance()->getObject($user_id);
				$object->setValue("activate_code", $activate_code);
				$object->commit();

				$email = $object->getValue("e-mail");
				$fio = $object->getValue("lname") . " " . $object->getValue("fname") . " " . $object->getValue("father_name");

				$email_from = regedit::getInstance()->getVal("//settings/email_from");
				$fio_from = regedit::getInstance()->getVal("//settings/fio_from");




				$mail_arr = Array();
				$mail_arr['domain'] = $domain = $_SERVER['HTTP_HOST'];
				$mail_arr['restore_link'] = "http://" . $domain . $this->pre_lang . "/users/restore/" . $activate_code . "/";

				$mail_content = def_module::parseTemplate($template_mail_verification, $mail_arr, false, $user_id);


  echo "<!--\n";
  var_dump($email, $fio);
  echo "\n-->";
				$someMail = new umiMail();
				$someMail->addRecipient($email, $fio);
				$someMail->setFrom($email_from, $fio_from);
				$someMail->setSubject($template_mail_verification_subject);
				$someMail->setPriorityLevel("highest");
				$someMail->setContent($mail_content);
				$someMail->commit();
//				$someMail->send();


				$block_arr = Array();
				return def_module::parseTemplate($template_forget_sended, $block_arr);
			} else {
				$block_arr = Array();
				$block_arr['forget_login'] = $forget_login;
				return def_module::parseTemplate($template_wrong_login_block, $block_arr);
			}
		}


		public function restore($template = "default") {
			if(!$template) $template = "default";

			list($template_restore_failed_block, $template_restore_ok_block, $template_mail_password, $template_mail_password_subject) = def_module::loadTemplates("tpls/users/forget/{$template}.tpl", "restore_failed_block", "restore_ok_block", "mail_password", "mail_password_subject");

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

			list($user_id) = umiSelectionsParser::runSelection($sel);

			$block_arr = Array();

			if($user_id) {
				$password = self::getRandomPassword();

				$object = umiObjectsCollection::getInstance()->getObject($user_id);
				$login = $object->getValue("login");
				$email = $object->getValue("e-mail");
				$fio  = $object->getValue("lname") . " " . $object->getValue("fname") . " " . $object->getValue("father_name");

				$object->setValue("password", md5($password));
				$object->setValue("activate_code", "");

				$object->commit();



				$email_from = regedit::getInstance()->getVal("//settings/email_from");
				$fio_from = regedit::getInstance()->getVal("//settings/fio_from");

				$mail_arr = Array();
				$mail_arr['domain'] = $domain = $_SERVER['HTTP_HOST'];
				$mail_arr['password'] = $password;

				$mail_content = def_module::parseTemplate($template_mail_password, $mail_arr, false, $user_id);


				$someMail = new umiMail();
				$someMail->addRecipient($email, $fio);
				$someMail->setFrom($email_from, $fio_from);
				$someMail->setSubject($template_mail_password_subject);
				$someMail->setContent($mail_content);
				$someMail->commit();
//				$someMail->send();



				$block_arr['password'] = $password;
				return def_module::parseTemplate($template_restore_ok_block, $block_arr, false, $user_id);
			} else {
				return def_module::parseTemplate($template_restore_failed_block, $block_arr);
			}
		}


		public static function getRandomPassword ($length = 12) {
			$avLetters = "$#@^&!1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
			$length = $length;
			$npass = "";
			for($i = 0; $i < $length; $i++)
				$npass .= $avLetters[rand(0, strlen($avLetters))];
			return $npass;
		}
	};

?>