<?php
$INFO = Array();

$INFO['version'] = "2.0.0.0";
$INFO['version_line'] = "pro";

$INFO['name'] = "content";
$INFO['filename'] = "modules/content/class.php";
$INFO['config'] = "1";
$INFO['ico'] = "ico_content";
$INFO['default_method'] = "content";
$INFO['default_method_admin'] = "sitetree";
$INFO['is_indexed'] = "1";

$INFO['func_perms'] = "Functions, that should have their own permissions.";

$INFO['func_perms/content'] = "Просмотр страниц";
$INFO['func_perms/content/title'] = "";
$INFO['func_perms/content/menu'] = "";
$INFO['func_perms/content/sitemap'] = "";
$INFO['func_perms/content/get_page_url'] = "";
$INFO['func_perms/content/get_page_id'] = "";
$INFO['func_perms/content/redirect'] = "";
$INFO['func_perms/content/get_describtion'] = "";
$INFO['func_perms/content/get_keywords'] = "";
$INFO['func_perms/content/insert'] = "";
$INFO['func_perms/content/header'] = "";
$INFO['func_perms/content/gen404'] = "";
$INFO['func_perms/content/json_get_editable_blocks'] = "";
$INFO['func_perms/content/json_get_tickets'] = "";

$INFO['func_perms/sitetree'] = "Управление контентом";
$INFO['func_perms/sitetree/rec_tree'] = "";
$INFO['func_perms/sitetree/add_page'] = "";
$INFO['func_perms/sitetree/add_page_do'] = "";
$INFO['func_perms/sitetree/del_page'] = "";
$INFO['func_perms/sitetree/edit_page'] = "";
$INFO['func_perms/sitetree/edit_page_do'] = "";
$INFO['func_perms/sitetree/move_page'] = "";
$INFO['func_perms/sitetree/treelink_parse'] = "";
$INFO['func_perms/sitetree/treelink'] = "";
$INFO['func_perms/sitetree/edit_domain'] = "";
$INFO['func_perms/sitetree/edit_domain_do'] = "";
$INFO['func_perms/sitetree/insertimage'] = "";
$INFO['func_perms/sitetree/insertmacros'] = "";
$INFO['func_perms/sitetree/replace'] = "";
$INFO['func_perms/sitetree/json_load'] = "";
$INFO['func_perms/sitetree/json_move'] = "";
$INFO['func_perms/sitetree/json_copy'] = "";
$INFO['func_perms/sitetree/json_del'] = "";
$INFO['func_perms/sitetree/json_load_hierarchy'] = "";
$INFO['func_perms/sitetree/templates_do'] = "";


$INFO['func_perms/tickets'] = "Работа с заметками";
$INFO['func_perms/tickets/json_add_ticket'] = "";
$INFO['func_perms/tickets/json_del_ticket'] = "";
$INFO['func_perms/tickets/json_update_ticket'] = "";


$SQL_INSTALL = Array();

?>