<?php
$INFO = Array();

$INFO['version'] = "2.0.0.0";
$INFO['version_line'] = "pro";

$INFO['name'] = "comments";
$INFO['title'] = "Комментарии";
$INFO['filename'] = "modules/comments/class.php";
$INFO['config'] = "1";
$INFO['ico'] = "ico_comments";
$INFO['default_method'] = "void_func";
$INFO['default_method_admin'] = "view_comments";
$INFO['is_indexed'] = "0";

$INFO['per_page'] = "10";
$INFO['moderated'] = "0";
$INFO['guest_posting'] = "0";
$INFO['allow_guest'] = "1";

$INFO['func_perms'] = "";
$INFO['func_perms/insert'] = "Просмотр комментариев";
$INFO['func_perms/insert/post'] = "";
$INFO['func_perms/insert/countComments'] = "";
$INFO['func_perms/insert/comment'] = "";

$INFO['func_perms/view_comments'] = "Редактирование комментариев";
$INFO['func_perms/view_comments/comment_del'] = "";
$INFO['func_perms/view_comments/comment_edit'] = "";
$INFO['func_perms/view_comments/comment_edit_do'] = "";


$SQL_INSTALL = Array();


?>