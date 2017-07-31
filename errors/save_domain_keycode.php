<?php
	chdir(dirname(dirname(__FILE__)));

	include "config.php";

	$ip = $_SERVER['SERVER_ADDR'];
	$domain = $_SERVER['HTTP_HOST'];
	$domain_keycode = $_REQUEST['domain_keycode'];

	$sql = "UPDATE cms3_domains SET host = '" . mysql_escape_string($domain) . "' WHERE id = '1'";
	mysql_query($sql);

	regedit::getInstance()->setVar("//settings/keycode", $domain_keycode);
?>