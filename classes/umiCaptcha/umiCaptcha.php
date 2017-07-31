<?php

class umiCaptcha implements iUmiCaptcha {
	public static function generateCaptcha($template="default", $input_id="sys_captcha", $captcha_hash="") {
		// check captcha
		if (!self::isNeedCaptha()) return "";
		if(!$template) $template = "default";
		if(!$input_id) $input_id = "sys_captcha";
		if(!$captcha_hash) $captcha_hash = "";

		$block_arr = array();
		$block_arr['input_id'] = $input_id;
		$block_arr['captcha_hash'] = $captcha_hash;

		list($template_captcha) = def_module::loadTemplates("tpls/captcha/{$template}.tpl", "captcha");

		return def_module::parseTemplate($template_captcha, $block_arr);
	}
	public static function isNeedCaptha() {
		if (cmsController::getInstance()->getModule('users')->is_auth()) return false;
		return ($_SESSION['is_human'] != 1);
	}
	public static function checkCaptcha() {
		if (cmsController::getInstance()->getModule('users')->is_auth()) return true;
		if (isset($_SESSION['umi_captcha']) && strlen($_SESSION['umi_captcha'])) {
			if ($_SESSION['user_captcha'] == $_SESSION['umi_captcha']) {
				$_SESSION['is_human'] = 1;
				return true;
			}
		}
		return false;
	}
}

?>