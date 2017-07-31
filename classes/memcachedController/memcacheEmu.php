<?php
	class memcacheEmu implements iMemcacheEmu {
		private $cache, $cacheFilePath;

		public function __construct() {
			$this->cacheFilePath = ini_get("include_path") . "cache/cache.emu";

			if(is_file($this->cacheFilePath)) {
				$this->cache = unserialize(file_get_contents("./cache/cache.emu"));
			}
		}

		public function delete($key) {
			if(array_key_exists($key, $this->cache)) {
				unset($this->cache[$key]);
				return true;
			} else {
				return false;
			}
		}

		public function get($key) {
			if(array_key_exists($key, $this->cache)) {
				return $this->cache[$key];
			} else {
				return false;
			}
		}

		public function set($key, $val) {
			return $this->cache[$key] = $val;
		}


		public function saveCache() {
			file_put_contents($this->cacheFilePath, serialize($this->cache));
			chmod($this->cacheFilePath, 0777);
		}
	};
?>