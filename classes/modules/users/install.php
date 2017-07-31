<?php
$INFO = Array();

$INFO['version'] = "2.0.0.0";
$INFO['version_line'] = "pro";

$INFO['name'] = "users";
$INFO['filename'] = "modules/users/class.php";
$INFO['config'] = "1";
$INFO['ico'] = "ico_users";
$INFO['default_method'] = "auth";
$INFO['default_method_admin'] = "groups_list";

$INFO['func_perms'] = "Functions, that should have their own permissions.";
$INFO['func_perms/login'] = "Авторизация";
$INFO['func_perms/login/login_do'] = "";
$INFO['func_perms/login/welcome'] = "";
$INFO['func_perms/login/auth'] = "";
$INFO['func_perms/login/is_auth'] = "";
$INFO['func_perms/login/logout'] = "";
$INFO['func_perms/login/get_user_info'] = "";
$INFO['func_perms/login/viewAuthor'] = "";
$INFO['func_perms/login/createAuthorGuest'] = "";
$INFO['func_perms/login/createAuthorUser'] = "";
$INFO['func_perms/login/profile'] = "";


$INFO['func_perms/registrate'] = "Регистрация";
$INFO['func_perms/registrate/registrate_do'] = "";
$INFO['func_perms/registrate/registrate_done'] = "";
$INFO['func_perms/registrate/activate'] = "";
$INFO['func_perms/registrate/get_delivery_list'] = "";
$INFO['func_perms/registrate/forget'] = "";
$INFO['func_perms/registrate/forget_do'] = "";
$INFO['func_perms/registrate/restore'] = "";


$INFO['func_perms/settings'] = "Редактирование настроек";
$INFO['func_perms/settings/settings_do'] = "";

$INFO['func_perms/users_list'] = "Управление пользователями";
$INFO['func_perms/users_list/add_group'] = "";
$INFO['func_perms/users_list/add_group_do'] = "";
$INFO['func_perms/users_list/group_edit'] = "";
$INFO['func_perms/users_list/group_edit_do'] = "";
$INFO['func_perms/users_list/group_delete'] = "";
$INFO['func_perms/users_list/add_user'] = "";
$INFO['func_perms/users_list/add_user_do'] = "";
$INFO['func_perms/users_list/user_delete'] = "";
$INFO['func_perms/users_list/user_edit'] = "";
$INFO['func_perms/users_list/edit_user_do'] = "";
$INFO['func_perms/users_list/groups_list'] = "";


$SQL_INSTALL = Array();

$SQL_INSTALL['drop_cms_permissions'] = "DROP TABLE cms_permissions";

$SQL_INSTALL['cms_permissions'] = <<<SQL

CREATE TABLE cms_permissions(
id		INT		NOT NULL	PRIMARY KEY	AUTO_INCREMENT,
module		VARCHAR(64)	DEFAULT NULL,
method		VARCHAR(64)	DEFAULT NULL,
owner_id	INT		DEFAULT NULL,
allow		TINYINT		DEFAULT '1',
KEY(module), KEY(method), KEY(owner_id)
)

SQL;


?>