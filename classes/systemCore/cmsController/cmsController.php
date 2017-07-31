<?php
	class cmsController extends singleton implements iSingleton, iCmsController {
		private	$modules = Array(),
				$current_module = false,
				$current_method = false,
				$current_mode = false,
				$current_element_id = false,
				$current_lang = false,
				$current_domain = false;

		public		$parsedContent = false,
				$currentTitle = false,
				$currentHeader = false,
				$currentMetaKeywords = false,
				$currentMetaDescription = false,

				$langs = Array(),
				$langs_export = Array(),

				$nav_arr = Array(),
				$pre_lang = "";


		protected function __construct() {
			$this->init();
		}

		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}


		private function loadModule($module_name) {
			$xpath = "//modules/" . $module_name;
			
			if(CURRENT_VERSION_LINE == "free" || CURRENT_VERSION_LINE == "lite") {
				if($module_name == "forum" || $module_name == "vote" || $module_name == "webforms") {
					return false;
				}
			}

			if(regedit::getInstance()->getVal($xpath) == $module_name) {
				$module_path = ini_get('include_path') . "classes/modules/" . $module_name . "/class.php";
				$interface_path = ini_get('include_path') . "classes/modules/" . $module_name . "/interface.php";

				if(is_file($module_path)) {
					if(is_file($interface_path)) {
						include_once $interface_path;
					}

					include_once $module_path;

					if(class_exists($module_name)) {
						$new_module = new $module_name();
						$new_module->cms_init();
						$new_module->pre_lang = $this->pre_lang;
						$this->modules[$module_name] = $new_module;

						return $new_module;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		}


		public function loadBuildInModule($moduleName) {
			//TODO
		}

		public function getModule($module_name) {
			if(!$module_name) return false;

			if(array_key_exists($module_name, $this->modules)) {
				return $this->modules[$module_name];
			} else {
				return $this->loadModule($module_name);
			}
		}

		public function installModule($moduleName) {
			//TODO
		}

		public function getSkinPath() {
			//TODO
		}


		public function getCurrentModule() {
			return $this->current_module;
		}

		public function getCurrentMethod() {
			return $this->current_method;
		}

		public function getCurrentElementId() {
			return $this->current_element_id;
		}

		public function getLang() {
			return $this->current_lang;
		}

		public function getCurrentLang() {
			return $this->getLang();
		}

		public function getCurrentMode() {
			return $this->current_mode;
		}

		public function getCurrentDomain() {
			return $this->current_domain;
		}



		private function init() {
			$this->detectDomain();
			$this->detectLang();
			$this->detectMode();
			$this->loadLangs();

			include ini_get('include_path') . "classes/modules/lang.php";
			$this->langs = array_merge($this->langs, $LANG_EXPORT);


			$ext_lang = ini_get('include_path') . "classes/modules/lang." . $this->getCurrentLang()->getPrefix() . ".php";
			if(is_file($ext_lang)) {
				include $ext_lang;
				$this->langs = array_merge($this->langs, $LANG_EXPORT);
			}


			$this->doSomething();
		}

		private function detectDomain() {
			$host = $_SERVER['HTTP_HOST'];
			if($domain_id = domainsCollection::getInstance()->getDomainId($host)) {
				$domain = domainsCollection::getInstance()->getDomain($domain_id);
			} else {
				$domain = domainsCollection::getInstance()->getDefaultDomain()->getId();
			}

			if(is_object($domain)) {
				$this->current_domain = $domain;
				return true;
			} else {
//				trigger_error("Can't detect domain \"{$host}\" in domains list", E_USER_WARNING);
				$this->current_domain = domainsCollection::getInstance()->getDefaultDomain();
				return false;
			}
		}

		private function detectLang() {
			list($sub_path) = $this->getPathArray();
			if($lang_id = langsCollection::getInstance()->getLangId($sub_path)) {
				$this->current_lang = langsCollection::getInstance()->getLang($lang_id);
			} else {
				if($this->current_domain) {
					if($lang_id = $this->current_domain->getDefaultLangId()) {
						$this->current_lang = langsCollection::getInstance()->getLang($lang_id);
					} else {
						$this->current_lang = langsCollection::getInstance()->getDefaultLang();
					}
				} else {
					$this->current_lang = langsCollection::getInstance()->getDefaultLang();
				}
			}
			if(!$this->current_lang->getIsDefault()) {
				$this->pre_lang = "/" . $this->current_lang->getPrefix();
				$_REQUEST['pre_lang'] = $this->pre_lang;
			}
		}

		private function getPathArray() {
			$path = $_REQUEST['path'];
			$path = trim($path, "/");

			return split("\/", $path);
		}

		private function detectMode() {
			list($sub_path1, $sub_path2) = $this->getPathArray();

			if($sub_path1 == "admin" || $sub_path2 == "admin") {
				$this->current_mode = "admin";
			} else {
				$this->current_mode = "";
				memcachedController::$cacheMode = true;
			}
		}


		private function getSubPathType($sub_path) {
			$regedit = regedit::getInstance();

			if(!$this->current_module) {
				if($regedit->getVal("//modules/" . $sub_path)) {
					$this->setCurrentModule($sub_path);
					return "MODULE";
				}
			}

			if($this->current_module && !$this->current_method) {
				$this->setCurrentMethod($sub_path);
				return "METHOD";
			}

			if($this->current_module && $this->current_method) {
				return "PARAM";
			}

			return "UNKNOWN";
		}


		public function analyzePath() {		//TODO: Add in interface
			$regedit= regedit::getInstance();
			$path_arr = $this->getPathArray();

			$path = $_REQUEST['path'];
			$path = trim($path, "/");


			$sz = sizeof($path_arr);
			$url_arr = Array();
			$p = 0;
			for($i = 0; $i < $sz; $i++) {
				$sub_path = $path_arr[$i];

				if($i <= 1) {
					if(($sub_path == $this->current_mode) || ($sub_path == $this->current_lang->getPrefix())) {
						continue;
					}
				}
				$url_arr[] = $sub_path;

				$sub_path_type = $this->getSubPathType($sub_path);

				if($sub_path_type == "PARAM") {
					$_REQUEST['param' . $p++] = $sub_path;
				}
			}

			if(!$this->current_module) {
				if($this->current_mode == "admin") {
					$module_name = $regedit->getVal("//settings/default_module_admin");
				} else {
					$module_name = $regedit->getVal("//settings/default_module");
				}
				$this->setCurrentModule($module_name);
			}

			if(!$this->current_method) {
				if($this->current_mode == "admin") {
					$method_name = $regedit->getVal("//modules/" . $this->current_module . "/default_method_admin");
				} else {
					$method_name = $regedit->getVal("//modules/" . $this->current_module . "/default_method");
				}
				$this->setCurrentMethod($method_name);
			}


			if($this->getCurrentMode() == "admin") {
				return;
			}

			$element_id = false;
			$sz = sizeof($url_arr);
			$sub_path = "";
			for($i = 0; $i < $sz; $i++) {
				$sub_path .= "/" . $url_arr[$i];

				if(!($tmp = umiHierarchy::getInstance()->getIdByPath($sub_path))) {
					$element_id = false;
					break;
				} else {
					$element_id = $tmp;
				}
			}

			if(($path == "" || $path == $this->current_lang->getPrefix()) && $this->current_mode != "admin") {
				if($element_id = umiHierarchy::getInstance()->getDefaultElementId($this->getCurrentLang()->getId(), $this->getCurrentDomain()->getId())) {
					$this->current_element_id = $element_id;
				}
			}


			if($element = umiHierarchy::getInstance()->getElement($element_id, true)) {
				$type = umiHierarchyTypesCollection::getInstance()->getType($element->getTypeId());
				
				if(!$type) return false;

				$this->current_module = $type->getName();
				
				if($ext = $type->getExt()) {
					$this->setCurrentMethod($ext);
				} else {
					$this->setCurrentMethod("content");	//Fixme: content "constructor". Maybe, fix in future?
				}

				$this->current_element_id = $element_id;
			}
		}


		public function setCurrentModule($module_name) {
		
			$this->current_module = $module_name;
		}


		public function setCurrentMethod($method_name) {
			if(defined("CURRENT_VERSION_LINE")) {
				if(CURRENT_VERSION_LINE == "free" || CURRENT_VERSION_LINE == "lite" || CURRENT_VERSION_LINE == "freelance") {
					if($this->current_module == "data" && substr($method_name, 0, strlen("trash")) != "trash") {
						$this->current_module = "content";
						$this->current_method = "sitetree";
						return false;
					}
				}
			}

			$this->current_method = $method_name;
		}


		public function loadLangs() {
			$modules = regedit::getInstance()->getList("//modules");
			foreach($modules as $module) {
				$module_name = $module[0];

				$lang_path = ini_get('include_path') . 'classes/modules/' . $module_name . '/';
				$lang_path .= "lang.php";

				include_once $lang_path;

				if(is_array($C_LANG)) {
					$this->langs[$module_name] = $C_LANG;
					unset($C_LANG);
				}

				if(is_array($LANG_EXPORT)) {
					$this->langs = array_merge($this->langs, $LANG_EXPORT);
					unset($LANG_EXPORT);
				}

				$lang_path = ini_get('include_path') . 'classes/modules/' . $module_name . '/';
				$lang_path .= "lang." . $this->getCurrentLang()->getPrefix() .".php";

				if(is_file($lang_path)) {
					include_once $lang_path;

					if(is_array($C_LANG)) {
						$this->langs[$module_name] = $C_LANG;
						unset($C_LANG);
					}

					if(is_array($LANG_EXPORT)) {
						$this->langs = array_merge($this->langs, $LANG_EXPORT);
						unset($LANG_EXPORT);
					}
				}
			}
		}


		final private function doSomething () {
			if(defined("CURRENT_VERSION_LINE")) {
				if(CURRENT_VERSION_LINE != "demo") {
					include "./errors/invalid_license.html";
					exit();
				}
			}

			$keycode = regedit::getInstance()->getVal("//settings/keycode");
			$comp_keycode = Array();
			$comp_keycode['pro'] = templater::getSomething("pro");
			$comp_keycode['free'] = templater::getSomething("free");
			$comp_keycode['lite'] = templater::getSomething("lite");
			$comp_keycode['freelance'] = templater::getSomething("freelance");

			if(regedit::checkSomething($keycode, $comp_keycode)) {
				return true;
			} else {
				include "errors/invalid_license.html";
				exit();
			}
		}
	};
?>