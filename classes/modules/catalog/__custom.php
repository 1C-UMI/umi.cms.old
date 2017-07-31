<?php
	abstract class __custom_catalog {
		//TODO: Write here your own macroses
		public function regedit() {
			// catalog::=======================================================================================
			regedit::getInstance()->setVar("//modules/catalog/func_perms", "");

			// client side
			regedit::getInstance()->setVar("//modules/catalog/func_perms/view", "Просмотр каталога");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/view/category", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/view/object", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/view/getCategoryList", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/view/getObjectsList", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/view/viewObject", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/view/search", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/view/getEditLink", "");

			// edit
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree", "Редактирование каталога");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_section_add", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_section_add_do", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_section_edit", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_section_edit_do", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_del", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_blocking", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_object_add", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_object_add_do", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_object_edit", "");
			regedit::getInstance()->setVar("//modules/catalog/func_perms/tree/tree_object_edit_do", "");
			// ::catalog=======================================================================================
		}
	};
?>