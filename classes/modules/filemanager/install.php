<?php

$INFO = Array();

$INFO['name'] = "filemanager";
$INFO['title'] = "Файловый менеджер";
$INFO['description'] = "Управление файловой системой.";
$INFO['filename'] = "modules/filemanager/class.php";
$INFO['config'] = "0";
$INFO['ico'] = "ico_filemanager";
$INFO['default_method'] = "list_files";
$INFO['default_method_admin'] = "directory_list";

$INFO['func_perms'] = "Functions, that should have their own permissions.";
$INFO['func_perms/list_files'] = "Просмотр файлов для скачивания";
$INFO['func_perms/list_files/shared_file'] = "";

$INFO['func_perms/download'] = "Скачивание файлов";

$SQL_INSTALL = Array();
?>