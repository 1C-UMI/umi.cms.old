<?php

$INFO = Array();

$INFO['version'] = "2.0.0.0";
$INFO['version_line'] = "pro";

$INFO['name'] = "dispatches";
$INFO['filename'] = "dispatches/class.php";
$INFO['config'] = "0";
$INFO['ico'] = "ico_dispatches";
$INFO['default_method'] = "subscribe";
$INFO['default_method_admin'] = "dispatches_list";
$INFO['is_indexed'] = "0";

$INFO['func_perms'] = "Functions, that should have their own permissions.";

$INFO['func_perms/dispatches'] = "Рассылки";
$INFO['func_perms/dispatches/subscribe_do'] = "";
$INFO['func_perms/dispatches/unsubscribe'] = "";

$INFO['func_perms/dispatches_list'] = "Редактирование рассылок";
$INFO['func_perms/dispatches_list/dispatch_del'] = "";
$INFO['func_perms/dispatches_list/dispatch_edit'] = "";
$INFO['func_perms/dispatches_list/dispatch_edit_do'] = "";
$INFO['func_perms/dispatches_list/dispatch_add'] = "";
$INFO['func_perms/dispatches_list/dispatch_add_do'] = "";

$INFO['func_perms/releasees_list'] = "Отправка выпусков";
$INFO['func_perms/releasees_list/release_send'] = "";

$INFO['func_perms/messages_list'] = "Редактирование сообщений выпуска";
$INFO['func_perms/messages_list/message_edit'] = "";
$INFO['func_perms/messages_list/message_edit_do'] = "";
$INFO['func_perms/messages_list/message_add'] = "";
$INFO['func_perms/messages_list/message_add_do'] = "";
$INFO['func_perms/messages_list/message_del'] = "";

$INFO['func_perms/subscribers_list'] = "Управление подписчиками";
$INFO['func_perms/subscribers_list/subscriber_edit'] = "";
$INFO['func_perms/subscribers_list/subscriber_edit_do'] = "";
$INFO['func_perms/subscribers_list/subscriber_add'] = "";
$INFO['func_perms/subscribers_list/subscriber_add_do'] = "";
$INFO['func_perms/subscribers_list/subscriber_del'] = "";

$INFO['func_perms/subscribe'] = "Разрешить подписку и отписку";
$INFO['func_perms/subscribe/subscribe_do'] = "";
$INFO['func_perms/subscribe/unsubscribe'] = "";

?>