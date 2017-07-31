<?php

abstract class __config_users {
	public function config() {
		$this->sheets_reset();

		$params = Array();
		$this->load_forms("forms_admin.php");
		$regedit = regedit::getInstance();


/*
		$arr1 = Array();
		$arr2 = Array();

		$sql = "SELECT id, group_name FROM cms_groups";
		$result = mysql_query($sql);
		while(list($gid, $gname) = mysql_fetch_row($result)) {
			if($gid == 1)
				continue;

			$arr1[] = $gid;
			$arr2[] = $gname;
		}

		$params['def_group'] = $regedit->getVal("//modules/users/def_group");

		$params['groups_list'] = putSelectBox($arr2, $arr1, $params['def_group']);
*/
		$def_group = $regedit->getVal("//modules/users/def_group");
		$guest_id = $regedit->getVal("//modules/users/guest_id");

		$sel = new umiSelection;
		$sel->setObjectTypeFilter();
		$sel->addObjectType(6);
		$result = umiSelectionsParser::runSelection($sel);

		$sz = sizeof($result);
		$arr_groups = Array();
		for($i = 0; $i < $sz; $i++) {
			$group_id = $result[$i];
			$group = umiObjectsCollection::getInstance()->getObject($group_id);

			$arr_groups[$group_id] = $group->getName();
		}



		$sel = new umiSelection;
		$sel->setObjectTypeFilter();
		$sel->addObjectType(4);
		$result = umiSelectionsParser::runSelection($sel);

		$sz = sizeof($result);
		$arr_users = Array();
		for($i = 0; $i < $sz; $i++) {
			$user_id = $result[$i];
			$user = umiObjectsCollection::getInstance()->getObject($user_id);
			if(sizeof($user->getPropByName("groups")->getValue()) > 0) continue;

			$arr_users[$user_id] = $user->getName();
		}


		$params['groups_list'] = putSelectBox_assoc($arr_groups, $def_group);
		$params['guest_user'] = putSelectBox_assoc($arr_users, $guest_id);
		return $this->parse_form("config", $params);
	}


	public function config_do() {
		$regedit = regedit::getInstance();

		$def_group = (int) $_REQUEST['def_group'];
		$guest_id = (int) $_REQUEST['guest_id'];

		$regedit->setVar("//modules/users/def_group", $def_group);
		$regedit->setVar("//modules/users/guest_id", $guest_id);

		$this->redirect($this->pre_lang . "/admin/users/config/");
	}

}

?>