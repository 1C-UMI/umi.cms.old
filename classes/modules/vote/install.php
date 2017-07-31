<?php
$INFO = Array();

$INFO['version'] = "2.0.0.0";
$INFO['version_line'] = "pro";

$INFO['name'] = "vote";
$INFO['filename'] = "modules/vote/class.php";
$INFO['config'] = "0";
$INFO['ico'] = "ico_vote";
$INFO['default_method'] = "insertvote";
$INFO['default_method_admin'] = "polls";


$INFO['func_perms'] = "Functions, that should have their own permissions.";

$INFO['func_perms/add_poll'] = "Добавление опросов";
$INFO['func_perms/add_poll/add_poll_do'] = "";
$INFO['func_perms/add_poll/polls'] = "";

$INFO['func_perms/edit_poll'] = "Редактирование опросов";
$INFO['func_perms/edit_poll/edit_poll_do'] = "";
$INFO['func_perms/edit_poll/polls'] = "";

$INFO['func_perms/del_poll'] = "Удаление опросов";

$INFO['func_perms/poll'] = "Просмотр опросов";
$INFO['func_perms/poll/insertvote'] = "";
$INFO['func_perms/poll/results'] = "";
$INFO['func_perms/poll/insertlast'] = "";

$INFO['func_perms/post'] = "Разрешить голосовать";

?>
