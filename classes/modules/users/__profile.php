<?php
	abstract class __profile_users {
		public function profile($template = "default") {
			if(!$template) $template = "default";

			list($template_block, $template_bad_user_block) = def_module::loadTemplates("tpls/users/profile/{$template}.tpl", "profile_block", "bad_user_block");
			$block_arr = Array();

			$user_id = (int) $_REQUEST['param0'];

			if($user = umiObjectsCollection::getInstance()->getObject($user_id)) {
				$userTypeId = $user->getTypeId();

				if($userType = umiObjectTypesCollection::getInstance()->getType($userTypeId)) {
					$userHierarchyTypeId = $userType->getHierarchyTypeId();

					if($userHierarchyType = umiHierarchyTypesCollection::getInstance()->getType($userHierarchyTypeId)) {

						if($userHierarchyType->getName() == "users" && $userHierarchyType->getExt() == "user") {
							$block_arr['id'] = $user_id;

							return def_module::parseTemplate($template_block, $block_arr, false, $user_id);
						}
					}
				}
			}
			return def_module::parseTemplate($template_bad_user_block, $block_arr);
		}
	};
?>