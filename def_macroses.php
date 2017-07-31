<?php

function macros_content() {
	if(cmsController::getInstance()->getCurrentMode() == "admin") {
		if(stripos($_SERVER['HTTP_USER_AGENT'], "Opera") !== false) {
			include "./errors/browser_not_supported.html";
			exit();
		}
	}

	$current_module = cmsController::getInstance()->getCurrentModule();
	$current_method = cmsController::getInstance()->getCurrentMethod();

	if($module = cmsController::getInstance()->getModule($current_module)) {
		$pid = cmsController::getInstance()->getCurrentElementId();
		$users_inst = cmsController::getInstance()->getModule("users");
		
		if($pid) {
			list($r, $w) = $users_inst->isAllowedObject($users_inst->user_id, $pid);
			if($r) {
				$is_element_allowed = true;
			} else {
				$is_element_allowed = false;
			}
		} else {
			$is_element_allowed = true;
		}

		if(system_is_allowed($current_module, $current_method) && ($is_element_allowed)) {
			if($parsedContent = cmsController::getInstance()->parsedContent) {
				return $parsedContent;
			}

			$res = $module->cms_callMethod($current_method);
			$res = system_parse_short_calls($res);
			$res = templater::getInstance()->parseInput($res);
			$res = system_parse_short_calls($res);
			
			if(array_key_exists("p", $_REQUEST)) {
				unset($_REQUEST['p']);
			}
			
			
			if($res !== false) {
				if(cmsController::getInstance()->getCurrentMode() != "admin") {
					if(stripos($res, "virtual-property")) {
						if(preg_match_all("/\<virtual\-property(?P<template> template=['\"]?([^\"^'^ ]*)['\"]?)?>(?P<content>.*)<\/virtual\-property\>/imsU", $res, $out)) {
							foreach($out['content'] as $i => $subContent) {
								$src = $out[0][$i];
								$template = $out[2][$i];
								
								if(!$template) $template = "default";
								
								list($block) = def_module::loadTemplates("./tpls/data/{$template}.tpl", "virtual");
								$block_arr = Array();
								$block_arr['value'] = $subContent;
								$block_arr['title'] = "";
								$block_arr['name'] = "";
								$block = def_module::parseTemplate($block, $block_arr);
								
								$res = str_replace($src, $block, $res);
							}
						}
					}
				}
			
				if(cmsController::getInstance()->getCurrentMode() != "admin" && stripos($res, "%cut%") !== false) {
					if(array_key_exists("cut", $_REQUEST)) {
						if($_REQUEST['cut'] == "all") {
							$CMS_ENV['cut_pages'] = 0;
							return str_ireplace("%cut%", "", $res);
						}
						$cut = (int) $_REQUEST['cut'];
					} else {
						$cut = 0;
					}

					$res_arr = spliti("%cut%", $res);

					if($cut > (sizeof($res_arr) - 1))
						$cut = sizeof($res_arr) - 1;
					if($cut < 0)
						$cut = 0;

					$_REQUEST['cut_pages'] = sizeof($res_arr);
					$_REQUEST['cut_curr_page'] = $cut;

					$res = $res_arr[$cut];
				}

				cmsController::getInstance()->parsedContent = $res;
				return $res;
			}
			else {
				$CMS_ENV['content_called'] = '<notice>%core_templater% %core_error_nullvalue%</notice>';
				return '<notice>%core_templater% %core_error_nullvalue%</notice>';
			}
		} else {
			if($module = cmsController::getInstance()->getModule("users")) {
				cmsController::getInstance()->setCurrentModule("users");
				cmsController::getInstance()->setCurrentMethod("login");
				return $module->login();
			}
			return '<warning>%core_templater% %core_error_nullvalue% %core_error_nopermission%</warning>';
		}

	}

	return '%core_templater% %core_error_unknown%';
}


function macros_title() {
	$res = '';

	$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();
	$lang_id = cmsController::getInstance()->getCurrentLang()->getId();

	$title_prefix = regedit::getInstance()->getVal("//settings/title_prefix/" . $lang_id . "/" . $domain_id);

	if(cmsController::getInstance()->currentTitle) {
		$page_title = cmsController::getInstance()->currentTitle;
		$res = $page_title;
	} else {
		$page_name = macros_header();
		$res = $title_prefix . $page_name;
	}

	if(cmsController::getInstance()->getCurrentMode() == "") {
		if($element_id = cmsController::getInstance()->getCurrentElementId()) {
			if($element = umiHierarchy::getInstance()->getElement($element_id)) {
				if($title = $element->getValue("title")) {
					return $title;
				}
			}
		}
	}

	return $res;
}

function macros_header() {
	$res = '';
		$current_module = cmsController::getInstance()->getCurrentModule();
		$current_method = cmsController::getInstance()->getCurrentMethod();

	if(!cmsController::getInstance()->currentHeader) {
		$current_module = cmsController::getInstance()->getCurrentModule();
		$current_method = cmsController::getInstance()->getCurrentMethod();

		$res = cmsController::getInstance()->langs[$current_module][$current_method];
	} else
		$res = cmsController::getInstance()->currentHeader;

	if(!$res && $element_id = cmsController::getInstance()->getCurrentElementId()) {
		if($element = umiHierarchy::getInstance()->getElement($element_id)) {
			if($res = $element->getValue("h1")) {
				return $res;
			}
		} else {
			return $res;
		}
	}
	return $res;
}

function macros_adm_menu() {
	$regedit = regedit::getInstance();
	$res = '';

	$modules_array = $regedit->getList('modules');

	$is_prepared_users = cmsController::getInstance()->getModule('users');
	$sz = sizeof($modules_array);
	for($it = 0; $it < $sz; $it++) {
		$current_module = $modules_array[$it];

		if($is_prepared_users) {
			if(!system_is_allowed($current_module[0], "")) {
				continue;
			}
		}
		
		if(defined("CURRENT_VERSION_LINE")) {
			if(CURRENT_VERSION_LINE == "free" || CURRENT_VERSION_LINE == "lite" || CURRENT_VERSION_LINE == "freelance") {
				$strt = $current_module[0];
				if($strt == "data") {
					continue;
				}
				
				if(CURRENT_VERSION_LINE != "freelance") {
					if($strt == "vote" || $strt == "forum" || $strt == "webforms") {
						continue;
					}
				}
			}
		}

		$xpath = '//modules/' . $current_module[0] . '/';

		$module_name = $regedit->getVal($xpath . 'name');
		$module_config = $regedit->getVal($xpath . 'config');
		$module_ico = $regedit->getVal($xpath . 'ico');

		$module_ico .= "." . ICO_EXT;

		$curr_module = cmsController::getInstance()->getCurrentModule();

		if($curr_module == $module_name && !(cmsController::getInstance()->getCurrentMethod() == 'mainpage'))
			$active_attr = "yes";
		else
			$active_attr = "no";

		if($module_config && system_is_allowed($current_module[0], "config"))
			$conf_attr = " settings=\"yes\"";
		else
			$conf_attr = " settings=\"no\"";


		$module_name = cmsController::getInstance()->langs[$current_module[0]]['module_name'];

		$res .= "\t\t<item ico=\"{$module_ico}\" active=\"{$active_attr}\" {$conf_attr} link=\"{$_REQUEST['pre_lang']}/admin/{$current_module[0]}/\" settings_link=\"{$_REQUEST['pre_lang']}/admin/{$current_module[0]}/config/\">{$module_name}</item>\r\n";
	}

	return $res;
}

function macros_adm_navibar() {
	$current_module = cmsController::getInstance()->getCurrentModule();
	$current_method = cmsController::getInstance()->getCurrentMethod();

	$default_module = regedit::getInstance()->getVal("//settings/default_module_admin");
	$default_method = regedit::getInstance()->getVal("//modules/{$default_module}/default_method_admin");

	$nav_arr = Array();

	$nav_arr[0] = Array('Главная страница', '/admin/');
	$url = cmsController::getInstance()->pre_lang . '/admin/';


	if($current_module == $default_module && 'mainpage' == $current_method || $current_module == '') {

	} else {
		if($current_module == $default_module && $default_method != $current_method) {
			$nav_arr[] = Array(cmsController::getInstance()->langs[$current_module][$current_method], $default_method);
		} else {
			if($default_method != $current_method) {
				$md_name = cmsController::getInstance()->langs[$current_module]['module_name'];
				$nav_arr[] = Array($md_name, "/admin/" . $current_module . "/");
				$nav_arr[] = Array(cmsController::getInstance()->langs[$current_module][$current_method], "/admin/" . $current_module . "/" . $current_method . "/");
			} else {
				$md_name = cmsController::getInstance()->langs[$current_module]['module_name'];
				$nav_arr[] = Array($md_name, cmsController::getInstance()->pre_lang . "/admin/" . $current_module . "/");
			}
		}
	}


	foreach(cmsController::getInstance()->nav_arr as $na) {
		if($na[0] != "BACK")
			$nav_arr[] = $na;
		else
			$nav_arr = array_slice($nav_arr, 0, sizeof($nav_arr)-1);
	}


	$res = '';

	$is_last = 'no';
	$sz1 = sizeof($nav_arr);
	$sz2 = $sz1-1;
	for($i = 0; $i < $sz1; $i++) {
		$nav_curr = $nav_arr[$i];
		
//		if(!$nav_curr[0]) continue;

		if($i == $sz2)
			$is_last = 'yes';

		$res .= '		<item last="' . $is_last . '" link="' . $_REQUEST['pre_lang'] . $nav_curr[1] . '"><![CDATA[' . $nav_curr[0] . ']]></item>' . "\r\n";
	}

	return $res;
}

function macros_menu() {
	$res = '';

	if($content_inst = cmsController::getInstance()->getModule('content')) {
		$res = $content_inst->cms_callMethod('menu', Array());
	}

	return $res;
}

function macros_describtion() {
	$module = cmsController::getInstance()->getCurrentModule();

	$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();
	$lang_id = cmsController::getInstance()->getCurrentLang()->getId();

	$def_descr = regedit::getInstance()->getVal("//settings/meta_description/" . $lang_id . "/" . $domain_id);

	$s_desc = (is_object(cmsController::getInstance()->getModule($module))) ? cmsController::getInstance()->getModule($module)->get_describtion() : "";

	if($element_id = cmsController::getInstance()->getCurrentElementId()) {
		if($element = umiHierarchy::getInstance()->getElement($element_id)) {
			if($res = $element->getValue("meta_descriptions")) {
				$s_desc = $res;
			}
		}
	}

	return ($s_desc) ? $s_desc : $def_descr;
}

function macros_keywords() {
	$module = cmsController::getInstance()->getCurrentModule();

	$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();
	$lang_id = cmsController::getInstance()->getCurrentLang()->getId();


	$def_keywords = regedit::getInstance()->getVal("//settings/meta_keywords/" . $lang_id . "/" . $domain_id);
	$s_keywords = (is_object(cmsController::getInstance()->getModule($module))) ? cmsController::getInstance()->getModule($module)->get_keywords() : "";

	if($element_id = cmsController::getInstance()->getCurrentElementId()) {
		if($element = umiHierarchy::getInstance()->getElement($element_id)) {
			if($res = $element->getValue("meta_keywords")) {
				$s_keywords = $res;
			}
		}
	}


	return ($s_keywords) ? $s_keywords : $def_keywords;
}

function macros_returnPid() {
	return cmsController::getInstance()->getCurrentElementId();
}

function macros_returnPreLang() {
	return cmsController::getInstance()->pre_lang;
}

function macros_returnDomain() {
	return $_SERVER['HTTP_HOST'];
}

function macros_curr_time() {
	return time();
}

function macros_skin_path() {
	if($_REQUEST['skin_sel']) return $_REQUEST['skin_sel'];
	return ($_COOKIE['skin']) ? $_COOKIE['skin'] : regedit::getInstance()->getVal("//skins");
}

function macros_ico_ext() {
	return "gif";
//	return ICO_EXT;
}

function macros_current_user_id() {
	if($users_ext = cmsController::getInstance()->getModule("users")) {
		return $users_ext->user_id;
	} else {
		return "";
	}
}


function macros_current_version_line() {
	if(defined("CURRENT_VERSION_LINE")) {
		return CURRENT_VERSION_LINE;
	} else {
		return "pro";
	}
}

function macros_help() {
	$module = cmsController::getInstance()->getCurrentModule();
	$method = cmsController::getInstance()->getCurrentMethod();
	//$lang = cmsController::getInstance()->getCurrentLang()->getPrefix();
	$lang = "ru";
	
	$man_path = "/man/{$lang}/{$module}/{$method}.html";
	$man_skin_path = "/man/{$lang}/{$module}/" . macros_skin_path() . "_{$method}.html";
	
	if(is_file("." . $man_skin_path)) {
		$man_path = $man_skin_path;
	}

	if(is_file("." . $man_path)) {
		return <<<END
<ticket><![CDATA[$man_path]]></ticket>
END;
	} else {
		return "";
	}
}


?>