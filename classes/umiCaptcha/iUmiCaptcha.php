<?php
	interface iUmiCaptcha {
		public static function generateCaptcha($template="default", $input_id="sys_captcha", $captcha_hash="");
		public static function isNeedCaptha();
		public static function checkCaptcha();
	}
?>