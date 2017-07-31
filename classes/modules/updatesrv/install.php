<?php
$INFO = Array();

$INFO['name'] = "updatesrv";
$INFO['title'] = "Сервер обновления";
$INFO['description'] = "Модуль сервера обновлений.";
$INFO['filename'] = "modules/updatesrv/class.php";
$INFO['config'] = "0";
$INFO['ico'] = "ico_updatesrv";
$INFO['default_method'] = "status";
$INFO['default_method_admin'] = "licenses";

$INFO['func_perms'] = "Functions, that should have their own permissions.";
$INFO['func_perms/service'] = "Автоматический бот";

/*
$SQL_INSTALL['cms_updatesrv_lines'] = <<<END

CREATE TABLE cms_updatesrv_lines (
id		INT		NOT NULL			PRIMARY KEY	AUTO_INCREMENT,
title		VARCHAR(255) 			DEFAULT '',
keyname		VARCHAR(16)			DEFAULT ''
)

END;

$SQL_INSTALL['cms_updatesrv_modules'] = <<<END

CREATE TABLE cms_updatesrv_modules (
id		INT		NOT NULL			PRIMARY KEY	AUTO_INCREMENT,
rel_line	INT		NOT NULL,
module_name	VARCHAR(48)			DEFAULT ''
)

END;

$SQL_INSTALL['cms_updatesrv_versions'] = <<<END

CREATE TABLE cms_updatesrv_versions (
id		INT		NOT NULL			PRIMARY KEY	AUTO_INCREMENT,
rel_module	INT		NOT NULL,
version		VARCHAR(24)	NOT NULL	DEFAULT '',
cr_time		INT		NOT NULL	DEFAULT 0,
obj_path	VARCHAR(255)	NOT NULL	DEFAULT ''
)


END;

$SQL_INSTALL['cms_updatesrv_licenses'] = <<<END

CREATE TABLE cms_updatesrv_licenses (
id		INT		NOT NULL			PRIMARY KEY	AUTO_INCREMENT,
domain		VARCHAR(255)	NOT NULL	DEFAULT '',
ip		VARCHAR(48)	NOT NULL	DEFAULT '',
keycode		VARCHAR(255)	NOT NULL	DEFAULT '',
fio		VARCHAR(255) 	NOT NULL	DEFAULT '',
email		VARCHAR(64)	NOT NULL	DEFAULT '',
phone		VARCHAR(48)	NOT NULL	DEFAULT '',
posttime	INT		NOT NULL	DEFAULT '',
is_free		INT				DEFAULT 0
)

END;

$SQL_INSTALL['cms_updatesrv_licenses_modules'] = <<<END

CREATE TABLE cms_updatesrv_licenses_modules (
mid		INT		NOT NULL,
lid		INT		NOT NULL
)

END;

//$SQL_INSTALL['cms_news_drop'] = "DROP ";
//$SQL_INSTALL['cms_news'] = "";
//$SQL_INSTALL['perms1'] = "INSERT INTO cms_permissions (module, method, user_id) VALUES('news','lastlist','')";
*/
?>