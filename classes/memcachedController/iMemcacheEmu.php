<?php
	interface iMemcacheEmu {
		public function __construct();
		public function delete($key);
		public function get($key);
		public function set($key, $val);
		public function saveCache();
	};
?>