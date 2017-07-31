<?php
	abstract class __delivery_eshop {
		public function delivery($template = "default") {
			if(!$template) $template = "default";

			list($template_block, $template_line) = def_module::loadTemplates("./tpls/eshop/delivery/{$template}.tpl", "delivery_block", "delivery_block_line");
			$block_arr = Array();

			$user_id = $this->user_id;
			$user = umiObjectsCollection::getInstance()->getObject($user_id);

			$delivery_list = $user->getValue("delivery_addresses");
			$lines = "";
			foreach($delivery_list as $object_id) {
				$line_arr = Array();
				$line_arr['link'] = $this->pre_lang . "/eshop/delivery_edit/" . $object_id . "/";
				$line_arr['link_del'] = $this->pre_lang . "/eshop/delivery_del/" . $object_id . "/";

				$lines .= def_module::parseTemplate($template_line, $line_arr, false, $object_id);
			}

			$block_arr['lines'] = $lines;
			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function delivery_add($template = "default") {
			if(!$template) $template = "default";

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("eshop", "address");

			list($template_block) = def_module::loadTemplates("tpls/eshop/delivery/{$template}.tpl", "delivery_add_block");

			$block_arr = Array();
			$block_arr['type_id'] = $object_type_id;

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function delivery_add_do($template = "default") {
			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("eshop", "address");

			$object_id = umiObjectsCollection::getInstance()->addObject("Delivery address", $object_type_id);
			$object = umiObjectsCollection::getInstance()->getObject($object_id);

			$object->setName("Delivery address #{$object_id}");
			cmsController::getInstance()->getModule('data');
			$data_module = cmsController::getInstance()->getModule('data');
			$data_module->saveEditedObject($object_id, true);

			$object->commit();

			//Let's apply delivery place to current user
			$user_id = $this->user_id;
			$user = umiObjectsCollection::getInstance()->getObject($user_id);
			$addresses = $user->getValue("delivery_addresses");
			if(!is_array($addresses)) $addresses = Array();

			$addresses[] = $object_id;
			$user->setValue("delivery_addresses", $addresses);
			$user->commit();

			$this->redirect($this->pre_lang . "/eshop/personal/");
		}


		public function delivery_edit($template = "default") {
			if(!$template) $template = "default";

			$object_id = (int) $_REQUEST['param0'];	//TODO: Check, if this address belongs to current user

			list($template_block) = def_module::loadTemplates("tpls/eshop/delivery/{$template}.tpl", "delivery_edit_block");

			$block_arr = Array();
			$block_arr['id'] = $object_id;

			return def_module::parseTemplate($template_block, $block_arr);
		}


		public function delivery_edit_do($template = "default") {
			$object_id = (int) $_REQUEST['param0'];	//TODO: Check, if this address belongs to current user

			if(!cmsController::getInstance()->getModule("users")->isOwnerOfObject($object_id)) {
				return "%data_edit_foregin_object%";
			}


			$object = umiObjectsCollection::getInstance()->getObject($object_id);

			cmsController::getInstance()->getModule('data');
			$data_module = cmsController::getInstance()->getModule('data');
			$data_module->saveEditedObject($object_id);

			$object->commit();

			$this->redirect($this->pre_lang . "/eshop/personal/");
		}


		public function delivery_del($template = "default") {
			$object_id = (int) $_REQUEST['param0'];	//TODO: Check, if this address belongs to current user


			umiObjectsCollection::getInstance()->delObject($object_id);

			$this->redirect($this->pre_lang . "/eshop/personal/");
		}


		public function address_choice($template = "default") {
			if(!$template) $template = "default";

			list($template_block, $template_block_empty, $template_line) = def_module::loadTemplates("./tpls/eshop/delivery/{$template}.tpl", "choise_block", "choise_block_empty", "choise_block_line");
			$block_arr = Array();

			$user_id = $this->user_id;
			$user = umiObjectsCollection::getInstance()->getObject($user_id);

			$delivery_list = $user->getValue("delivery_addresses");

			if(sizeof($delivery_list)) {
				$lines = "";
				foreach($delivery_list as $object_id) {
					$line_arr = Array();
					$line_arr['id'] = $object_id;
					$line_arr['link'] = $this->pre_lang . "/eshop/delivery_edit/" . $object_id . "/";
					$line_arr['link_del'] = $this->pre_lang . "/eshop/delivery_del/" . $object_id . "/";

					$lines .= def_module::parseTemplate($template_line, $line_arr, false, $object_id);
				}
				$block_arr['lines'] = $lines;

				$template = $template_block;
			} else {
				$template = $template_block_empty;
			}

			return def_module::parseTemplate($template, $block_arr);
		}
	};
?>