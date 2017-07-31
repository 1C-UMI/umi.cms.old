<?php
	class custom {
		public function cms_callMethod($method_name, $args) {
			return call_user_method_array($method_name, $this, $args);
		}
		//TODO: Write your own macroses here
	};
?>