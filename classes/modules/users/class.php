<?php

class users extends def_module implements iUsers {
	public $user_login = "%users_anonymous_login%";
	public $user_id = 2;
	public $user_fullname = "%users_anonymous_fullname%";
	public $groups = "";

	public function __construct() {
		parent::__construct();

		define('SV_GROUP_ID', 15);

		$this->__admin();

		$ia = $this->is_auth();
	}

	public function __call($a, $b) {
		return parent::__call($a, $b);
	}

	public function __admin() {
		parent::__admin();

		$this->sheets_add("%core_users_sheets_users%", "users_list_all");
		$this->sheets_add("%core_users_sheets_groups%", "groups_list");


		if(cmsController::getInstance()->getCurrentMode() == "admin" && !class_exists("__imp__" . get_class($this))) {
			$this->__loadLib("__import.php");
			$this->__implement("__imp__" . get_class($this));

			$this->__loadLib("__config.php");
			$this->__implement("__config_" . get_class($this));
		} else {
			$this->__loadLib("__register.php");
			$this->__implement("__register_users");

			$this->__loadLib("__author.php");
			$this->__implement("__author_users");

			$this->__loadLib("__forget.php");
			$this->__implement("__forget_users");

			$this->__loadLib("__profile.php");
			$this->__implement("__profile_users");

			$this->__loadLib("__custom.php");
			$this->__implement("__custom_users");
		}
	}

	public function config() {
		$this->__admin();
		if(class_exists("__config_users"))
			return __config_users::config();
		else
			return "";
	}


	public function login($template = "default") {
		if(!$template)
			$template = "default";
		$this->sheets_reset();

		$res = "";

		$params = Array();

		$from_page = $_REQUEST['from_page'];

		if(!$from_page)
			$from_page = $_SERVER['REQUEST_URI'];

		$params['from_page'] = $from_page;


		$skins = "";
		$regedit = regedit::getInstance();

		$def_skin = $regedit->getVal("//skins");
		$skins_arr = $regedit->getList("//skins");

		$curr_skin = $_COOKIE['skin'];
		if($_REQUEST['skin_sel']) $curr_skin = $_REQUEST['skin_sel'];
		if(!$curr_skin) {
			$curr_skin = $def_skin;
		}

		foreach($skins_arr as $cs) {
			$cs = $cs[0];

			if($curr_skin == $cs)
				$skins .= "<item value='$cs' selected='1'>%core_skin_" . $cs . "%</item>\r\n";
			else
				$skins .= "<item value='$cs'>%core_skin_" . $cs . "%</item>\r\n";
		}

		$params['skins'] = $skins;

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$this->load_forms("forms_admin.php");
			$res = $this->parse_form('login', $params);
		} else {
			list($template_login) = self::loadTemplates("tpls/users/{$template}.tpl", "login");

			$block_arr = Array();
			$block_arr['from_page'] = self::protectStringVariable($from_page);
			$block_arr['skins'] = "$skins";

			return self::parseTemplate($template_login, $block_arr);
		}

		return $res;
	}

	public function login_do() {
		$this->sheets_reset();

		$login = $_REQUEST['login'];
		$password = $_REQUEST['password'];
		$skin_sel = $_REQUEST['skin_sel'];

		$from_page = $_REQUEST['from_page'];

		if($_COOKIE['skin'] != $skin_sel && $skin_sel)
			setcookie("skin", $skin_sel, (time() + 31536000), "/");

		if(!$login) return $this->auth();

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");
		$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
		$login_field_id = $object_type->getFieldId("login");
		$password_field_id = $object_type->getFieldId("password");
		$is_active_id = $object_type->getFieldId("is_activated");


		$sel = new umiSelection;
		$sel->setLimitFilter();
		$sel->addLimit(1);

		$sel->setObjectTypeFilter();
		$sel->addObjectType($object_type_id);

		$sel->setPropertyFilter();

		$login = umiObjectProperty::filterInputString($login);
		$sel->addPropertyFilterEqual($login_field_id, $login);

		$password = umiObjectProperty::filterInputString($password);
		$sel->addPropertyFilterEqual($password_field_id, md5($password));
		$sel->addPropertyFilterEqual($is_active_id, 1);


		$result = umiSelectionsParser::runSelection($sel);


		if(sizeof($result) == 1) {
			$user_id = $result[0];

			$_SESSION['cms_login'] = $login;
			$_SESSION['cms_pass'] = md5($password);
			$_SESSION['user_id'] = $user_id;
			
			session_commit();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				if($from_page) {
					$this->redirect($from_page);
				} else {
					$this->redirect($this->pre_lang . "/admin/users/auth/");
				}
			} else {
				if($from_page) {
					$this->redirect($from_page);
				} else {
					$this->redirect($this->pre_lang . "/users/auth/");
				}
			}
		} else {
			$res = '<p><warning>%users_error_loginfailed%</warning></p>';
			$res .= "%users auth()%";
			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				return $res;
			} else {
				return $this->auth();
			}
		}
		return $res;
	}


	public function welcome($template = "default") {
		if(!$template) $template = "default";

		if($this->is_auth()) {
			$res = $this->auth($template);
		}

		return $res;
	}

	public function auth($template = "default") {
		$this->sheets_reset();
		if(!$template) $template = "default";

		if($this->is_auth()) {
			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$this->redirect($this->pre_lang . "/admin/");
			} else {
				list($template_logged) = self::loadTemplates("tpls/users/{$template}.tpl", "logged");

				$block_arr = Array();
				$block_arr['user_name'] = $this->user_fullname;
				$block_arr['user_login'] = $this->user_login;

				return self::parseTemplate($template_logged, $block_arr, false, $this->user_id);
			}
		} else {
			$res = $this->login($template);
		}

		return $res;
	}

	public function is_auth() {
		$login = $_SESSION['cms_login'];
		$pass = $_SESSION['cms_pass'];

		$this->user_login = "%users_anonymous_login%";
		$this->user_fullname = "%users_anonymous_fullname%";
		
		$this->CMS_ENV['user_login'] = $this->user_login;
		$this->CMS_ENV['user_name'] = $this->user_fullname;

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");
		$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
		$login_field_id = $object_type->getFieldId("login");
		$password_field_id = $object_type->getFieldId("password");

		$sel = new umiSelection;
		$sel->setLimitFilter();
		$sel->addLimit(1);

		$sel->setObjectTypeFilter();
		$sel->addObjectType($object_type_id);

		$sel->setPropertyFilter();

		$sel->addPropertyFilterEqual($login_field_id, $login);
		$sel->addPropertyFilterEqual($password_field_id, $pass);

		$result = umiSelectionsParser::runSelection($sel);

		if(sizeof($result) == 1) {
			$user_id = $result[0];
			$user_object = umiObjectsCollection::getInstance()->getObject($user_id);

			$login = $user_object->getPropByName("login")->getValue();
			$fname = $user_object->getPropByName("fname")->getValue();
			$lname = $user_object->getPropByName("lname")->getValue();

			$groups = $user_object->getPropByName("groups")->getValue();

			$this->groups = $groups;
			$this->user_id = $user_id;

			$this->user_login = $login;
			$this->user_fullname = "{$fname} {$lname}";
			
			$_SESSION['user_id'] = $user_id;


			return true;
		} else {
			$regedit = regedit::getInstance();
			$guest_id = $regedit->getVal("//modules/users/guest_id");
			$this->user_id = $guest_id;
			
			$_SESSION['user_id'] = $guest_id;

			return false;
		}
	}


	public function logout() {
		$_SESSION['cms_login'] = "";
		$_SESSION['cms_pass'] = "";
		$_SESSION['user_id'] = false;
		
		session_commit();
		
		$this->redirect();
	}




	public function get_user_info($user_id, $format) {
		$this->sheets_reset();

		if($object = umiObjectsCollection::getInstance()->getObject($user_id)) {
			$arr = Array();
			$arr['login'] = $object->getValue("login");
			$arr['email'] = $object->getValue("e-mail");
			$arr['last_name'] = $arr['lname'] = $object->getValue("lname");
			$arr['first_name'] = $arr['fname'] = $object->getValue("fname");
			$arr['father_name'] = $object->getValue("father_name");
			$arr['phone'] = $object->getValue("phone");
			$arr['age'] = $object->getValue("age");
			$arr['avatar'] = $object->getValue("avatar");

			$format = self::parseTemplate($format, $arr, false, $user_id);
		}
		return $format;
	}



	public function getOwnerType($owner_id) {
		if($owner_object = umiObjectsCollection::getInstance()->getObject($owner_id)) {
			if($groups = $owner_object->getPropByName("groups")) {
				return $groups->getValue();
			} else {
				return $owner_id;
			}
		}
	}

	public function makeSqlWhere($owner_id) {
		$owner = $this->getOwnerType($owner_id);

		if(is_numeric($owner)) {
			$sql = "(cp.owner_id = '{$owner_id}')";
		} else {
			$owner[] = $owner_id;

			$sql = "";
			$sz = sizeof($owner);
			for($i = 0; $i < $sz; $i++) {
				$sql .= "cp.owner_id = '{$owner[$i]}'";
				if($i < ($sz - 1)) {
					$sql .= " OR ";
				}
			}
			$sql = "({$sql})";
		}

		return $sql;
	}


	public function isAllowedModule($owner_id, $module) {
		$sql_where = $this->makeSqlWhere($owner_id);

		$sql = "SELECT MAX(cp.allow) FROM cms_permissions cp WHERE module = '{$module}' AND method IS NULL AND {$sql_where}";
		$result = mysql_query($sql);
		list($allow) = mysql_fetch_row($result);

		return (bool) $allow;
	}

	public function isAllowedMethod($owner_id, $module, $method) {
		$sql_where = $this->makeSqlWhere($owner_id);


		//Requied default permissions
		if($module == "config" && ($method == "lang_list" || $method == "lang_phrases")) return true;
		if($module == "users" && ($method == "auth" || $method == "login_do" || $method == "login")) return true;


		$sql = "SELECT MAX(cp.allow) FROM cms_permissions cp WHERE module = '{$module}' AND method = '{$method}' AND {$sql_where}";

		$result = mysql_query($sql);
		list($allow) = mysql_fetch_row($result);

		return (bool) $allow;
	}

	public function isAllowedObject($owner_id, $object_id) {
		if($this->isSv($owner_id)) {
			return Array(true, true);
		}

		$sql_where = $this->makeSqlWhere($owner_id);

		$sql = "SELECT MAX(cp.level) FROM cms3_permissions cp WHERE rel_id = '{$object_id}' AND {$sql_where}";
		$result = mysql_query($sql);
		list($level) = mysql_fetch_row($result);

		$r = false; $e = false;
		if($level >= 1) {
			$r = true;
		}

		if($level >= 2) {
			$e = true;
		}

		return Array($r, $e);
	}


	public function isSv($user_id) {
		if($user = umiObjectsCollection::getInstance()->getObject($user_id)) {
			if($groups = $user->getPropByName("groups")) {
				if(in_array(15, $groups->getValue())) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	public function isOwnerOfObject($object_id, $user_id = false) {
		if($user_id === false) {
			$user_id = $this->user_id;
		}

		if($user_id == $object_id) {	//Objects == User, that's ok
			return true;
		} else {
			$object = umiObjectsCollection::getInstance()->getObject($user_id);
			$owner_id = $object->getOwnerId();

			if($owner_id == 0 || $owner_id == $user_id) {
				return true;
			} else {
				return false;
			}
		}
	}


	public function setDefaultPermissions($element_id) {
		if(!umiHierarchy::getInstance()->isExists($element_id)) {
			return false;
		}

		mysql_query("SET AUTOCOMMIT=0");

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}


		$sql = "DELETE FROM cms3_permissions WHERE rel_id = '{$element_id}'";
		mysql_query($sql);

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
			return false;
		}


		$element = umiHierarchy::getInstance()->getElement($element_id, true);
		$hierarchy_type_id = $element->getTypeId();
		$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getType($hierarchy_type_id);

		$module = $hierarchy_type->getName();
		$method = $hierarchy_type->getExt();


		//Getting outgroup users
		$type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");

		$sel = new umiSelection;
		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);

		$group_field_id = umiObjectTypesCollection::getInstance()->getType($type_id)->getFieldId("groups");
		$sel->setPropertyFilter();
		$sel->addPropertyFilterIsNull($group_field_id);

		$users = umiSelectionsParser::runSelection($sel);


		//Getting groups list
		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "users");

		$sel = new umiSelection;

		$sel->setObjectTypeFilter();
		$sel->addObjectType($object_type_id);
		$groups = umiSelectionsParser::runSelection($sel);

		$objects = array_merge($users, $groups);


		//Let's get element's ownerId and his groups (if user)
		$owner_id = $element->getObject()->getOwnerId();
		if($owner = umiObjectsCollection::getInstance()->getObject($owner_id)) {
			if($owner_groups = $owner->getValue("groups")) {
				$owner_arr = $owner_groups;
			} else {
				$owner_arr = Array($owner_id);
			}
		} else {
			$owner_arr = Array();
		}


		foreach($objects as $ugid) {
			if($this->isAllowedMethod($ugid, $module, $method)) {
				if(in_array($ugid, $owner_arr) || $ugid == SV_GROUP_ID) {
					$level = 2;
				} else {
					$level = 1;
				}

				$sql = "INSERT INTO cms3_permissions (rel_id, owner_id, level) VALUES('{$element_id}', '{$ugid}', '{$level}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}
			}
		}
		


		mysql_query("COMMIT");

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}


		mysql_query("SET AUTOCOMMIT=1");

		if($err = mysql_error()) {
			trigger_error($err, E_USER_WARNING);
		}

	}


		public static function protectStringVariable($stringVariable = "") {
			$stringVariable = htmlspecialchars($stringVariable);
			return $stringVariable;
		}


};
?>