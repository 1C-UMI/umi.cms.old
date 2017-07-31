<?php
	ini_set('include_path', str_replace("\\", "/", dirname(__FILE__)) . '/');
	ini_set('session.use_only_cookies', '1');
	
	error_reporting(~E_ALL);

	if(!$_REQUEST['path']) {
		$_REQUEST['path'] = substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']) - 1);
	}

	header("Cache-Control: no-store, no-cache, must-revalidate");	// HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);	// HTTP/1.1
	header("Pragma: no-cache");	// HTTP/1.0
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	$_NORMAL = 		0x000000;
	$_ADMIN  = 		0x000001;

	@set_time_limit(0);

	setlocale (LC_ALL, 'ru_RU.utf-8');
	
	if(function_exists("mb_internal_encoding")) {
		mb_internal_encoding('UTF-8');
	}

	function getmicrotime() {
		list($usec, $sec) = explode(' ', microtime()); 
		return ((float)$usec + (float)$sec); 
	}

	function set_timebreak($s = true) {
		global $time_start;

		$time_end = getmicrotime();
		$time = $time_end - $time_start;

		echo "\r\n" . '<!-- This page generated in ' . $time . ' secs -->' . "\r\n";
		flush();
		exit();
	}

	if (!defined("UMI_SESSION_LIVETIME")) define ("UMI_SESSION_LIVETIME", 60); // in min
	ini_set("session.gc_maxlifetime", (string) UMI_SESSION_LIVETIME*60);
	ini_set("session.cookie_lifetime", "0");
	ini_set("session.use_cookies", "1");
	ini_set("session.use_only_cookies", "1");

	if(is_file("./cacheControl.php")) {
		include "./cacheControl.php";
	}

	session_start();


	$request_uri = trim($_SERVER['REQUEST_URI'], "/");
	if(!$request_uri) $request_uri = "__splash";

	if(function_exists("prepareCacheFile")) {
		$path_c = prepareCacheFile($request_uri);
	}

	$is_admin = (substr($request_uri, 0, strlen("admin")) == "admin") ? true : false;
	if(substr($request_uri, 3, strlen("admin")) == "admin") $is_admin = true;
	$is_cached = false;

	$time_start = getmicrotime();
	tryGetCache($path_c, $is_admin);

	include 'config.php';

	$CMS_ENV = Array();

	cmsController::getInstance()->nav_arr = Array();

	$CMS_ENV['navibar_path'] = Array();
	$CMS_ENV['pages_listed'] = Array();

	regedit::getInstance()->getList('//modules');
	regedit::getInstance()->getList('//settings');


	if($_REQUEST['skin_sel']) {
		$CMS_ENV['skin']  = $_REQUEST['skin_sel'];
	} else {
		$CMS_ENV['skin'] = $_COOKIE['skin'];
	}

	cmsController::getInstance();
	umiHierarchy::getInstance();
	cmsController::getInstance()->analyzePath();

	$mode = cmsController::getInstance()->getCurrentMode();
	$module = $_REQUEST['module'];
	$method = $_REQUEST['method'];

	system_runSession();

	$parser = templater::getInstance();

	$parser->defaultMacroses[] = Array("%content%", "macros_content");
	$parser->defaultMacroses[] = Array("%menu%", "macros_menu");
	$parser->defaultMacroses[] = Array("%header%", "macros_header");
	$parser->defaultMacroses[] = Array("%pid%", "macros_returnPid");
	$parser->defaultMacroses[] = Array("%pre_lang%", "macros_returnPreLang");
	$parser->defaultMacroses[] = Array("%curr_time%", "macros_curr_time");
	$parser->defaultMacroses[] = Array("%domain%", "macros_returnDomain");

	$parser->defaultMacroses[] = Array("%title%", "macros_title");
	$parser->defaultMacroses[] = Array("%keywords%", "macros_keywords");
	$parser->defaultMacroses[] = Array("%describtion%", "macros_describtion");
	$parser->defaultMacroses[] = Array("%adm_menu%", "macros_adm_menu");
	$parser->defaultMacroses[] = Array("%adm_navibar%", "macros_adm_navibar");
	$parser->defaultMacroses[] = Array("%skin_path%", "macros_skin_path");
	$parser->defaultMacroses[] = Array("%ico_ext%", "macros_ico_ext");

	$parser->defaultMacroses[] = Array("%current_user_id%", "macros_current_user_id");
	$parser->defaultMacroses[] = Array("%current_version_line%", "macros_current_version_line");
	$parser->defaultMacroses[] = Array("%context_help%", "macros_help");

	if(cmsController::getInstance()->getCurrentMode() == "admin") {
		system_returnSkinPath();
	}

	$primary_module = cmsController::getInstance()->getCurrentModule();
	
	$cmsControllerInstance = cmsController::getInstance();
	$cmsControllerInstance->parsedContent = macros_content();

	if(cmsController::getInstance()->getCurrentMode() == "admin") {
		$tpl_skinned = system_returnSkinPath();
	}

	if(cmsController::getInstance()->getCurrentMode() == "admin") {
		require_once 'utf8.php';
		header('Content-Type: text/xml; charset=utf-8');
		$resourse = file_get_contents('tpls/admin/' . $tpl_skinned, 1);
	} else {
		header('Content-Type: text/html; charset=utf-8');
		if($primary_module_inst = cmsController::getInstance()->getModule($primary_module)) {
			$tpl_id = $primary_module_inst->get_tpl_id();
		} else {
			$tpl_id = def_module::get_tpl_id();
		}
		
		$CMS_ENV['tpl_id'] = $tpl_id;
		$tpl_path = system_get_tpl($tpl_id);

		if(!$tpl_path) {
			$tpl_path = 'index.tpl';
		}

		$resourse = file_get_contents('tpls/content/' . $tpl_path, 1);
	}

	$parser->init($resourse);

	if(!(regedit::getInstance()->getVal('//settings/chache_browser'))) {
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Pragma: no-cache");
	}

	$res = $parser->putLangs($parser->output);

	if(cmsController::getInstance()->getCurrentMode() == '') {
		$res = str_replace("&#037;", "%", $res);
	}

	$res = str_replace("%pid%", cmsController::getInstance()->getCurrentElementId(), $res);
	$res = $parser->parseInput($res);


	if($stat_module = cmsController::getInstance()->getModule('stat')) {
		$stat_module->pushStat();
	}

	echo $res;


	if($is_cached == false && !$is_admin) {
		if(function_exists("trySaveCache")) {
			trySaveCache($path_c, $res);
		}
	}

	set_timebreak();
?>