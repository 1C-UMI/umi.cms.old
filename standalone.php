<?php
	ini_set('include_path', dirname(__FILE__) . '/');
	$include_dir = dirname(__FILE__) . '/';
	/* Script for runnig UMI.CMS 2.0 - modules standalone from system-core. */

	require_once $include_dir."./config.php";

	function run_standalone($module_name) {
		return cmsController::getInstance()->getModule($module_name);
	}
?>