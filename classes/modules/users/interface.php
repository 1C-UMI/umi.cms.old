<?php
	interface  iUsers {
		public function isAllowedObject($ownerId, $objectId);
		public function isAllowedMethod($ownerId, $moduleName, $methodName);
		public function isAllowedModule($ownerId, $moduleName);

		public function login($template = "default");
		public function login_do();
		public function welcome($template = "default");
		public function auth($template = "default");
		public function is_auth();
		public function logout();
		public function get_user_info($userId, $formatString);
	}
?>