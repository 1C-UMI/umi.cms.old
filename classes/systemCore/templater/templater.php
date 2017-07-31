<?php

class templater extends singleton implements iTemplater {
	public $defaultMacroses = Array();
	public $cacheMacroses = Array();
	public $cachePermitted = false;
	public $LANGS = Array();
	public $cacheEnabled = 0;

	protected function __construct() {
	}

	public static function getInstance() {
		return parent::getInstance(__CLASS__);
	}



	public function init($input) {
		$this->loadLangs();
		

		$this->cacheMacroses["%content%"] = $this->parseInput(cmsController::getInstance()->parsedContent);

		$res = $this->parseInput($input);
		$res = $this->putLangs($res);

		$this->output = system_parse_short_calls($res);
	}

	public function loadLangs() {
		$try_path = ini_get('include_path') . "classes/modules/lang." . cmsController::getInstance()->getLang()->getPrefix() . ".php";
		if(!is_file($try_path))
			$try_path = ini_get('include_path') . "classes/modules/lang.php";

		include_once $try_path;

		if($LANG_EXPORT) {
			cmsController::getInstance()->langs = array_merge(cmsController::getInstance()->langs, $LANG_EXPORT);
			unset($LANG_EXPORT);
		}
		return true;
	}

	public function putLangs($input) {
		$res = $input;
		
		if(($p = strpos($res, "%")) === false) return $res;

		$langs = cmsController::getInstance()->langs;

		foreach($langs as $cv => $cvv) {
			if(is_array($cvv)) continue;
			
			$m = "%" . $cv . "%";
			
			if(($mp = strpos($res, $m, $p)) !== false) {
				$res = str_replace($m, $cvv, $res, $mp);
			}
		}

		return $res;
	}

	public function parseInput($input) {
		$res = $input;

		$pid = cmsController::getInstance()->getCurrentElementId();
		$input = str_replace("%pid%", $pid, $input);


		if(strrpos($res, "%") === false)
			return $res;

		$input = str_replace("%%", "%\r\n%", $input);

		if(preg_match_all("/%([A-z_]*)%/m", $input, $temp)) {
			$temp = $temp[0];

			$sz = sizeof($temp);
			for($i = 0; $i < $sz; $i++)
				$r = $this->parseMacros($temp[$i]);
		}

		if(preg_match_all("/%([A-zА-п–Р-—П \/\._\-\(\)0-9%:<>,!@\|']*)%/m", $input, $temp)) {
			$temp = $temp[0];

			$sz = sizeof($temp);
			for($i = 0; $i < $sz; $i++)
				$r = $this->parseMacros($temp[$i]);
		}

		$cache = $this->cacheMacroses;
		$cache = array_reverse($cache);
		foreach($cache as $ms => $mr) {
			if(($p = strpos($res, $ms)) !== false) {
				$res = str_replace($ms, $mr, $res);
			}
		}
		return $this->putLangs($res);
	}

	public function parseMacros($macrosStr) {	//–азбиваем строковой макрос на составл€ющие: название модул€, метода
		$macrosArr = Array();			// и строку аргументов

		if(strrpos($macrosStr, "%") === false)
			return $macrosArr;

		if(preg_match("/%([A-z0-9]+) ([A-z0-9]+)\((.*)\)%/im", $macrosStr, $pregArr)) {
			$macrosArr['str']    = $pregArr[0];
			$macrosArr['module'] = $pregArr[1];
			$macrosArr['method'] = $pregArr[2];
			$macrosArr['args']   = $pregArr[3];

			if(array_key_exists($macrosArr['str'], $this->cacheMacroses))
				return $this->cacheMacroses[$macrosArr['str']];

			//разбиваем строку аргументов на массив
			$params = split(",", $macrosArr['args']);

			$sz = sizeof($params);
			for($i = 0; $i < $sz; $i++) {
				$cparam = $params[$i];

				if(strpos($cparam, "%") !== false) {
					$cparam = $this->parseInput($cparam);
				}
				$params[$i] = trim($cparam, "'\" ");
			}
			$macrosArr['args'] = $params;

			$res = $macrosArr['result'] = $this->executeMacros($macrosArr);
			$this->cacheMacroses[$macrosArr['str']] = $macrosArr['result'];	//в кэш
			return $res;

		} else {

			//проверим. может быть, это какой-то дефолтовый макрос...
			$defMs = $this->defaultMacroses;

			$sz = sizeof($defMs);
			for($i = 0; $i < $sz; $i++)
				if(stripos($macrosStr, $defMs[$i][0]) !== false) {
						if(array_key_exists($defMs[$i][0], $this->cacheMacroses))
							return $this->cacheMacroses[$defMs[$i][0]];

						$res = $this->executeMacros(
										Array(
											"module" => $defMs[$i][1],
											"method" => $defMs[$i][2],
											"args"   => Array()
											)
									);
						$res = $this->parseInput($res);
						$this->cacheMacroses[$defMs[$i][0]] = $res;	//в кэш
						return $res;
					}

			$this->cacheMacroses[$macrosStr] = $macrosStr;
			return $macrosStr;
		}
	}

	public function executeMacros($macrosArr) {
		$module = $macrosArr['module'];
		$method = $macrosArr['method'];

		//echo $module . ":" . cmsController::getInstance()->getCurrentModule() . "<br/>";
		if($module == "current_module")
			$module = cmsController::getInstance()->getCurrentModule();
		$res = "";

		if(!$method) {
			$cArgs = array_merge(Array($this->CMS_ENV), $macrosArr['args']);
			$res = call_user_func_array($macrosArr['module'], $cArgs);
		}

		if($module == "core" || $module == "system" || $module == "custom") {
			$pk = &system_buildin_load($module);

			if($pk) {
				$res = $pk->cms_callMethod($method, $macrosArr['args']);
			}
		}

		if($module != "core" && $module != "system") {
			if(system_is_allowed($module, $method)) {
				if($module_inst = cmsController::getInstance()->getModule($module)) {
					$res = $module_inst->cms_callMethod($method, $macrosArr['args']);
				}
			}
		}

		if(strpos($res, "%") !== false) {
			$res = $this->parseInput($res);
		}

		return $res;
	}


	public function __destruct() {
		$this->prepareQuickEdit();
	}

	public $blocks = Array();

	public static function pushEditable($module, $method, $id) {
		if($module === false && $method === false) {

			if($element = umiHierarchy::getInstance()->getElement($id)) {
				$elementTypeId = $element->getTypeId();

				if($elementType = umiObjectTypesCollection::getInstance()->getType($elementTypeId)) {
					$elementHierarchyTypeId = $elementType->getHierarchyTypeId();

					if($elementHierarchyType = umiHierarchyTypesCollection::getInstance()->getType($elementHierarchyTypeId)) {
						$module = $elementHierarchyType->getName();
						$method = $elementHierarchyType->getExt();
					} else {
						return false;
					}
				}
			}
		}

		templater::getInstance()->blocks[] = Array($module, $method, $id);
	}

	private function prepareQuickEdit() {
		$toFlush = $this->blocks;
		
		if(sizeof($toFlush) == 0) return;
		
		$dirpath = ini_get("include_path") . "cache";
		
		if(!is_dir($dirpath)) {
			mkdir($dirpath);
			chmod($dirpath, 0777);
		}

		$filename = md5("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . ".block";
		$filename = $dirpath . "/" . $filename;
		file_put_contents($filename, serialize($toFlush));
		chmod($filename, 0777);
	}


	final public static function getSomething($version_line = "pro") {
		$default_domain = domainsCollection::getInstance()->getDefaultDomain();

		$cs2 = md5($_SERVER['SERVER_ADDR']);
		
		switch($version_line) {
			case "pro":
				$cs3 = md5(md5(md5(md5(md5(md5(md5(md5(md5(md5($default_domain->getHost()))))))))));
				break;

			case "free":
				$cs3 = md5(md5(md5($default_domain->getHost())));
				break;

			case "lite":
				$cs3 = md5(md5(md5(md5(md5($default_domain->getHost())))));
				break;

			case "freelance":
				$cs3 = md5(md5(md5(md5(md5(md5(md5($default_domain->getHost())))))));
				break;
		}

		$licenseKeyCode = strtoupper(substr($cs2, 0, 11) . "-" . substr($cs3, 0, 11));
		return $licenseKeyCode;
	}
};
?>