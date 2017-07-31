<?php
	function saveCacheElementsRelations($path) {
		$elements = umiHierarchy::getInstance()->getCollectedElements();
		foreach($elements as $element_id) {
			saveCacheElementRelation($element_id, $path);
		}
	}

	function saveCacheElementRelation($element_id, $path) {
		$dbpath = ini_get('include_path') . "static/cacheElementsRelations/" . substr($element_id, 0, 1);
		if(!is_dir($dbpath)) mkdir($dbpath);
		$dbpath .= "/" . $element_id;

		if(!is_file($dbpath)) touch($dbpath);
		if(!is_file($dbpath)) return false;

		$i = 0;

		$f = fopen($dbpath, "r");
		while(!feof($f)) {
			$tpath = trim(fgets($f, 1024));
			if($tpath == $path) return true;
		}
		fclose($f);

		$f = fopen($dbpath, "a");
		fwrite($f, $path . "\n");
		fclose($f);
		
		return true;
	}
	
	function deleteElementsRelatedPages() {
		$elements = umiHierarchy::getInstance()->getUpdatedElements();
		
		foreach($elements as $element_id) {
			deleteElementRelatedPages($element_id);
		}
	}
	
	function deleteElementRelatedPages($element_id) {
		$dbpath = ini_get('include_path') . "static/cacheElementsRelations/" . substr($element_id, 0, 1) . "/" . $element_id;
		if(!is_file($dbpath)) return false;

		$f = fopen($dbpath, "r");
		while(!feof($f)) {
			$path = ini_get('include_path') . trim(fgets($f, 1024));
			if(is_file($path)) {
				unlink($path);
			}
		}
		fclose($f);
		unlink($dbpath);
	}


	function prepareCacheFile($request_uri) {
		$user_id = $_SESSION['user_id'];
		if(!$user_id) {
			$user_id = 2373;
		}

		if(!is_dir("./static")) {
			mkdir("./static");
		}

		if(!is_dir("./static/userCache")) {
			mkdir("./static/userCache");
		}

		if(!is_dir("./static/userCache/" . $user_id)) {
			mkdir("./static/userCache/" . $user_id);
		}

		if(!is_dir("./static/userCache/" . $user_id . "/" . sha1($_SERVER['HTTP_HOST']))) {
			mkdir("./static/userCache/" . $user_id . "/" . sha1($_SERVER['HTTP_HOST']));
		}

		if(!is_dir("./static/cacheElementsRelations")) {
			mkdir("./static/cacheElementsRelations");
		}

		return $path_c = "./static/userCache/" . $user_id . "/" . sha1($_SERVER['HTTP_HOST']) . "/" . sha1($request_uri);
	}


	function tryGetCache($path_c, $is_admin) {
		if(!is_file(ini_get('include_path') . "cache.config")) {
			return false;
		}

		if(is_file($path_c) && sizeof($_POST) == 0 && !$is_admin) {
			$cnt = file_get_contents($path_c);
	        	if(trim($cnt)) {
				if(stripos(file_get_contents("cache.config"), "IGNORE_STAT") === false) {
					include "./config.php";
					cmsController::getInstance();
					cmsController::getInstance()->analyzePath();
					if($stat_inst = cmsController::getInstance()->getModule("stat")) {
						$stat_inst->pushStat();
					}
				}
				echo $cnt;
				flush();
				$path_c = md5($path_c);

				$is_cached = true;

				
				set_timebreak();
			}
		}
	}


	function trySaveCache($path_c, $res) {
		if(!is_file(ini_get('include_path') . "cache.config")) {
			return false;
		}

		$from = Array("<?", "?>");
		$to = Array("&lt?;", "?&gt;");
		$res = str_replace($from, $to, $res);

		file_put_contents($path_c, $res);
		saveCacheElementsRelations($path_c);
	}
?>