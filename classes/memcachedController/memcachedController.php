<?php
	class memcachedController extends singleton implements iMemcachedController {
		protected $memcache, $enabled = false, $connected = false, $compress = MEMCACHE_COMPRESSED;
		protected $mode;
		public static $cacheMode = false;
		
		
		protected function __construct() {
			$this->mode = md5($_SERVER['HTTP_HOST']) . "_";

			$this->enabled = $this->connect();
		}
		
		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}
		
		protected function connect() {
			$regedit = regedit::getInstance();

			$is_enabled = $regedit->getVal("//settings/memcached/is_enabled");
			$host = $regedit->getVal("//settings/memcached/host");
			$port = $regedit->getVal("//settings/memcached/port");

			if(!$is_enabled) return false;

			if(class_exists("Memcache")) {
				$memcache = new Memcache;

				if($memcache->pconnect($host, $port)) {
					$this->memcache = $memcache;
					$this->connected = true;
					return true;
				} else {
					return false;
				}
			} else {
				include "./classes/memcachedController/iMemcacheEmu.php";
				include "./classes/memcachedController/memcacheEmu.php";

				if(class_exists("memcacheEmu")) {
					$memcache = new memcacheEmu;
					$this->memcache = $memcache;
					$this->connected = true;
					return true;
				} else {
					return false;
				}
			}
		}
		
		public function save(umiEntinty $obj, $type = "unknown", $expire = 86400) {
			if(!$this->enabled) return false;

			if(self::$cacheMode == false) {
				$this->memcache->delete($this->mode . "_" . $type . "_" . $obj->getId());
				return true;
			}
			
		        if($this->memcache->set($this->mode . "_" . $type . "_" . $obj->getId(), $obj, $this->compress, $expire)) {
				return true;
			} else {
				return false;
			}
		}
		
		public function load($id, $type = "unknown") {
			if(!$this->enabled) return false;
			if(!self::$cacheMode) return false;


			if($obj = $this->memcache->get($this->mode . "_" . $type . "_" . $id)) {
				return $obj;
			} else {
				return false;
			}
		}
		
		
		protected function convertSqlToHash($sql) {
			return md5($sql);
		}
		
		public function saveSql($sql, $result) {
			if(!$this->enabled) return false;

			$hash = $this->convertSqlToHash($sql);

			if(self::$cacheMode == false) {
				$this->memcache->delete($this->mode . "_sql_" . $hash);
				return true;
			}
			
			if($this->memcache->set($this->mode . "_sql_" . $hash, $result, $this->compress, 3600)) {
				return true;
			} else {
				return false;
			}
		}
		
		
		public function loadSql($sql) {
			if(!$this->enabled) return false;
			if(!self::$cacheMode) return false;

			$hash = $this->convertSqlToHash($sql);
			
			if($result = $this->memcache->get($this->mode . "_sql_" . $hash)) {
				return $result;
			} else {
				return false;
			}
		}

		public function getIsConnected() {
			return (bool) $this->connected;
		}

		public function __destruct() {
			if(class_exists("memcacheEmu")) {
				if($this->memcache instanceof memcacheEmu) {
					$this->memcache->saveCache();
				}
			}
		}
	};
	memcachedController::getInstance();
?>