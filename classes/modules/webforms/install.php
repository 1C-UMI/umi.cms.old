<?php

$INFO = Array();
$INFO['version_line'] = "pro";

$INFO['name'] = "webforms";
$INFO['filename'] = "webforms/class.php";
$INFO['config'] = "0";
$INFO['ico'] = "ico_webforms";
$INFO['default_method'] = "insert";
$INFO['default_method_admin'] = "addresses";

$INFO['func_perms'] = "Functions, that should have their own permissions.";
$INFO['func_perms/insert'] = "Отправка сообщений";
$INFO['func_perms/insert/post'] = "";
$INFO['func_perms/insert/posted'] = "";

$INFO['func_perms/addresses'] = "Редактирование адресатов";
$INFO['func_perms/addresses/addr_upd'] = "";

$SQL_INSTALL = Array();

$SQL_INSTALL['cms_webforms_drop'] = "DROP TABLE cms_webfors";

$SQL_INSTALL['cms_webforms'] = <<<CMS_WEBFORMS

CREATE TABLE cms_webforms (
id		INT		NOT NULL	AUTO_INCREMENT	PRIMARY KEY,
email		VARCHAR(255)	NOT NULL	DEFAULT '',
descr		VARCHAR(255)	NOT NULL	DEFAULT '',
KEY(email)
)

CMS_WEBFORMS;

?>