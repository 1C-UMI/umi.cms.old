<?php

$INFO = Array();

$INFO['name'] = "forum";
$INFO['title'] = "Конференции";
$INFO['description'] = "Модуль конференций";
$INFO['filename'] = "modules/forum/class.php";
$INFO['config'] = "1";
$INFO['ico'] = "ico_forum";
$INFO['default_method'] = "show";
$INFO['default_method_admin'] = "confs_list";

$INFO['def_group'] = "0";
$INFO['need_moder'] = "0";
$INFO['antimat'] = "0";
$INFO['antidouble'] = "0";
$INFO['autounion'] = "0";
$INFO['allow_guest'] = "0";
$INFO['per_page'] = "20";

$INFO['func_perms'] = "Functions, that should have their own permissions.";
$INFO['func_perms/view'] = "Доступ к форуму";
$INFO['func_perms/view/conf'] = "";
$INFO['func_perms/view/topic'] = "";
$INFO['func_perms/view/formatmessage'] = "";
$INFO['func_perms/view/confs_list'] = "";
$INFO['func_perms/view/message'] = "";
$INFO['func_perms/view/topic_last_message'] = "";
$INFO['func_perms/view/conf_last_message'] = "";
$INFO['func_perms/view/topic_post'] = "";
$INFO['func_perms/view/message_post'] = "";
$INFO['func_perms/view/topic_post_do'] = "";
$INFO['func_perms/view/message_post_do'] = "";
$INFO['func_perms/view/getmessagelink'] = "";


$SQL_INSTALL = Array();
?>