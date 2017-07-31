<?php

class regedit extends singleton implements iRegedit {
	public $cacheFolder = "cache/";

	private $useFileCache;
	private $needUpdate = false;

	//кеш ключей и значений
	private $cache = Array();

	private function serPath(&$kPath) {
		$kPath = strtolower($kPath);
		if(substr($kPath, 0, 2) != "//")
			$kPath = "//" . $kPath;
	}

	protected function __construct($useFileCache = true) {
		$this->useFileCache = $useFileCache;

		$this->cacheFolder = ini_get("include_path") . $this->cacheFolder;

		$this->readCache();
	}

	public static function getInstance() {
		return parent::getInstance(__CLASS__);
	}

	public function __destruct() {
		$this->saveCache();
	}

	//... свои именя
	public function __toString() {
		return "umi.__regedit";
	}

	//внутренняя ф-я getKey
	//возвращает id ключа в таблице cms_reg по
	//символьному пути (modules/content/func_perms/...)
	//введу поддержку путей типа //modules/... !!!
	public function getKey($kPath, $roffset = 0) {
		$this->serPath(&$kPath);

		if(substr($kPath, 0, 2) == "//")	// for "//.../......"
			$kPath = substr($kPath, 2, strlen($kPath) - 2);

		if(array_key_exists('key://' . $kPath, $this->cache))
			return $this->cache['key://' . $kPath];

		$sp = split("/", $kPath);

		$key = 0;
		$i = 0;
		foreach($sp as $cp) {
			if(sizeof($sp) < ++$i + $roffset)
				return $key;
			$sql = "SELECT id FROM cms_reg WHERE rel='" . $key . "' AND var='" . mysql_escape_string($cp). "' LIMIT 1";
			$result = mysql_query($sql);


			if(!(list($key) = mysql_fetch_row($result)))
				return false;
		}

		$this->need_update = true;
		$this->cache['key://' . $kPath] = $key;

		return $key;
	}

	//возвращает значение ключа реестра и заносит в кеш
	public function getVal($kPath) {
		$this->serPath(&$kPath);

		if(array_key_exists($kPath, $this->cache)) {
			$cached = $this->cache[$kPath];
		} else {
			$cached = NULL;
		}

		if($cached != false) {
			//echo "Returned from cache \"$cached\" by reg key \"$kPath\"<br />\r\n";
			return $cached;
		}

		$key_id = (int) $this->getKey($kPath);
		if($key_id == 0)
			return false;

		$this->needUpdate = true;

//		echo "Попытка получить ключ в обход кэша: \"$kPath\"<br />\r\n";

		$sql = "SELECT val FROM cms_reg WHERE id='" . $key_id . "' LIMIT 1";
		$result = mysql_unbuffered_query($sql);
		if($row = mysql_fetch_array($result)) {
			$val = $row['val'];
			if(!$val)
				$val = "";
			$this->cache[$kPath] = $val;
			return $val;
		} else {
			$this->cache[$kPath] = false;
			return false;
		}

	}

	//возвращает массив значений раздела реестра и заносит их в кеш
	public function getList($kPath) {
		$this->serPath(&$kPath);


		if($res = $this->cache['list:' . $kPath]) {	//закешировано - возвращаем.
			return $res;
		}

//		echo "Попытка получить список ключей в обход кэша: \"$kPath\"<br />\r\n";

		if($key_id = (int) $this->getKey($kPath)) {
			$res = Array();

			$sql = "SELECT var, val FROM cms_reg WHERE rel='" . $key_id . "' ORDER BY id";
			$result = mysql_unbuffered_query($sql);
			$cache = $this->cache;

			while($row = mysql_fetch_array($result)) {
				$var = $row['var'];
				$val = $row['val'];

				$cache[$kPath . "/" . $var] = $val;
				$res[] = Array($var, $val);
			}
			mysql_free_result($result);

			$this->cache['list:' . $kPath] = $res;

			$this->needUpdate = true;
			return $res;
		} else
			return false;
	}

	//добавляет ключ с занесением значения в кэш
	public function setVar($kPath, $val) {
		$this->serPath(&$kPath);


		$this->needUpdate = true;
//		echo $kPath . "\r\n";
/*
		$tPath = substr($kPath, 2, strlen($kPath) - 2);
		$tPath_arr = split("/", $tPath);
		$tPath = "//";
		foreach($tPath_arr as $tmp)
			$tPath .= $tmp . "/";
		$tPath = substr($tPath, 0, strlen($tPath) - 1);
		unset($this->cache['list://' . $tPath]);
*/
		unset($this->cache[$kPath]);
		unset($this->cache['list:' . $kPath]);
		$nPath = preg_replace("/(.*)\/[A-z0-9]*/i", "\\1", $kPath);
		unset($this->cache['list:' . $nPath]);
//		$this->delVar($kPath);

//echo $kPath . " | " . $nPath . "\r\n";
//echo 'list:' . $nPath;
//exit();
		if($key_id = (int) $this->getKey($kPath)) {
			$sql = "UPDATE cms_reg SET val='" . str_replace("'", "\\'", ($val)) . "' WHERE id='" . $key_id . "' LIMIT 1";
//			$sql = "UPDATE cms_reg SET val='" . mysql_escape_string($val) . "' WHERE id='" . $key_id . "' LIMIT 1";

			mysql_unbuffered_query($sql);

			if(mysql_error() == "")
				return true;
			else
				return false;
		} else {
			$key_id = (int) $this->getKey($kPath, 1);
			$sp = split("/", $kPath);
//			$var = $sp[sizeof($sp)-1];
			$var = array_pop($sp);

			$sql = "INSERT INTO cms_reg (var, val, rel) VALUES ('" . mysql_escape_string($var) . "', '" . str_replace("'", "\\'", ($val)) . "', '" . $key_id . "')";
			mysql_unbuffered_query($sql);

			return true;
		}
	}

	//удаляет ключ из реестра
	//!!N1!! подразделы автоматически не удаляются. по крайнер мере пока.
	//!!N2!! в кеш ключу присваивается false. поэтому не стоит пользоваться ф-ей in_array()
	public function delVar($kPath) {
		$this->serPath(&$kPath);

//		unset($this->cache[$kPath]);
		unset($this->cache[$kPath]);
		unset($this->cache['key:' . $kPath]);
		unset($this->cache['list:' . $kPath]);
		$nPath = preg_replace("/(.*)\/[A-z0-9]*/i", "\\1", $kPath);
//		unset($this->cache['key:' . $nPath]);
		unset($this->cache['list:' . $nPath]);

		$this->needUpdate = true;

		if($key_id = (int) $this->getKey($kPath)) {
			$sql = "DELETE FROM cms_reg WHERE id='" . $key_id . "' LIMIT 1";
			mysql_unbuffered_query($sql);

			return true;
		} else
			return false;
	}

	public function saveCache() {
		if((!$this->useFileCache || !$this->needUpdate) && file_exists($this->cacheFolder . "reg"))
			return false;

		file_put_contents($this->cacheFolder . "reg", serialize($this->cache));
		chmod($this->cacheFolder . "reg", 0777);
	}

	public function readCache() {
		if(!$this->useFileCache)
			return false;

		if(!file_exists($this->cacheFolder . "reg"))
			return false;

		$this->cache = unserialize(file_get_contents($this->cacheFolder . "reg"));
	}


	final public static function checkSomething($a, $b) {
		if($_SERVER['HTTP_HOST'] == 'localhost' && $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
			return true;
		}
		
		foreach($b as $version_line => $c3) {
			$is_valid = (bool) (substr($a, 12, strlen($a) - 12) == $c3);
			if($is_valid === true) {
				define("CURRENT_VERSION_LINE", $version_line);
				return true;
			}
		}
	}
};

?>