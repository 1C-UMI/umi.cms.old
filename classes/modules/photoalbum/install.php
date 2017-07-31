<?php

$INFO = Array();

$INFO['name'] = "photoalbum";
$INFO['title'] = "Фотоальбомы";
$INFO['description'] = "Модуль фотогалерей.";
$INFO['filename'] = "modules/photoalbum/class.php";
$INFO['config'] = "1";
$INFO['ico'] = "ico_photoalbum";
$INFO['default_method'] = "albums";
$INFO['default_method_admin'] = "albums_list";

$INFO['func_perms'] = "Functions, that should have their own permissions.";

$INFO['func_perms/albums'] = "Просмотр фотогалерей";
$INFO['func_perms/albums/album'] = "";
$INFO['func_perms/albums/photo'] = "";

$SQL_INSTALL = Array();
?>