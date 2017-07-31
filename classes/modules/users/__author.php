<?php
	abstract class __author_users {
		public function createAuthorUser($user_id) {
			if(umiObjectsCollection::getInstance()->isExists($user_id) === false) {
				return false;
			}

			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("users", "author")->getId();
			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "author");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$user_field_id = $object_type->getFieldId('user_id');

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit(1);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($user_field_id, $user_id);

			$result = umiSelectionsParser::runSelection($sel);

			if(sizeof($result)) {
				list($author_id) = $result;
				return $author_id;
			} else {
				$user_object = umiObjectsCollection::getInstance()->getObject($user_id);
				$user_name = $user_object->getName();

				$author_id = umiObjectsCollection::getInstance()->addObject($user_name, $object_type_id);
				$author = umiObjectsCollection::getInstance()->getObject($author_id);
				$author->setValue("is_registrated", true);
				$author->setValue("user_id", $user_id);
				$author->commit();

				return $author_id;
			}
		}

		public function createAuthorGuest($nick, $email, $ip) {
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("users", "author")->getId();
			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "author");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$nickname_field_id = $object_type->getFieldId('nickname');
			$email_field_id = $object_type->getFieldId('email');
			$ip_field_id = $object_type->getFieldId('ip');

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit(1);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($email_field_id, $email);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($nickname_field_id, $nick);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($ip_field_id, $ip);

			$result = umiSelectionsParser::runSelection($sel);

			if(sizeof($result)) {
				list($author_id) = $result;
				return $author_id;
			} else {
				$user_object = umiObjectsCollection::getInstance()->getObject($user_id);
				$user_name = $nickname . " ({$email})";

				$author_id = umiObjectsCollection::getInstance()->addObject($user_name, $object_type_id);
				$author = umiObjectsCollection::getInstance()->getObject($author_id);
				$author->setName($user_name);
				$author->setValue("is_registrated", false);
				$author->setValue("nickname", $nick);
				$author->setValue("email", $email);
				$author->setValue("ip", $ip);
				$author->commit();

				return $author_id;
			}
		}

		public function viewAuthor($author_id, $template = "default") {
			if(!($author = umiObjectsCollection::getInstance()->getObject($author_id))) {
				return false;
			}

			if(!$template) $template = "default";
			list($template_user, $template_guest) = def_module::loadTemplates("./tpls/users/author/{$template}.tpl", "user_block", "guest_block");

			$block_arr = Array();
			if($author->getValue("is_registrated")) {
				$template = $template_user;
				$block_arr['user_id'] = $user_id = $author->getValue("user_id");
				if($users_inst = cmsController::getInstance()->getModule("users")) {
					$template = $users_inst->get_user_info($user_id, $template);
				}
			} else {
				$template = $template_guest;
				$block_arr['nickname'] = $author->getValue("nickname");
				$block_arr['email'] = $author->getValue("email");
			}
			return def_module::parseTemplate($template, $block_arr, false, $author_id);
		}
	};
?>