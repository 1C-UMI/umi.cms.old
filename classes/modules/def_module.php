<?php
abstract class def_module {	private $M_SHEETS = Array();
	private $sheet_active = -1;
	public $libsCalled = Array();
	private $FORMS_CACHE = Array();
	protected $pid;
	public $max_pages = 10;

	public static $templates_cache = Array();

	//реализация мультимплементации [

	public $__classes = Array();

	protected function __implement($class_name) {    //подключение класса
	        $this->__classes[] = $class_name;    //имя класса запоминаем

		//дописываем переменные
		$vars = get_class_vars($class_name);
		if(is_array($vars)) {
			foreach($vars as $var => $val) {
				eval('$this->' . $var . ' = \'' . $val . '\';');
			}
		}
		$cm = get_class_methods($class_name);

		$fn = "onInit";
		if(in_array($fn, $cm)) {
			$this->$fn();
		}
	}

	public function __admin() {
		if(cmsController::getInstance()->getCurrentMode() == "admin" && !class_exists("__" . get_class($this))) {
			$this->__loadLib("__admin.php");
			$this->__implement("__" . get_class($this));
		}
	}

	public function __call($a, $b) {    //отсутствующие методы ищем в подгруженных классах
		foreach($this->__classes as $class_name) {    //листинг классов
			$cm = get_class_methods($class_name);

			if(in_array($a, $cm)) {    //ищем, нету ли в классе нужного метода

				$params = "";
				if(is_array($b)) {    //генерим строчку с парамами

					$sz = sizeof($b);
					for($i = 0; $i < $sz; $i++) {
						$param = $b[$i];
						$params .= '$b[' . $i . ']';
						if($i != $sz-1)
							$params .= ", ";
					}

				}
				eval('$res = ' . $class_name . '::' . $a . '(' . $params . ');');    //от eval'а хотелось бы избавиться

				return $res;
			}
		}

		cmsController::getInstance()->langs[get_class($this)][$a] = "Ошибка";

		if(cmsController::getInstance()->getModule("content")) {
			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				return "<warning>Вызов несуществующего метода.</warning>";
			} else {
				if(cmsController::getInstance()->getCurrentModule() == get_class($this) && cmsController::getInstance()->getCurrentMethod() == $a) {
					return cmsController::getInstance()->getModule("content")->gen404();
				} else {
					return "";
				}
			}
		}
	}

	//]

	protected $file_path = "", $name = "";

	//конструктор модуля. запоминает ссылку на окружение
	public function __construct() {
		$this->lang = cmsController::getInstance()->getCurrentLang()->getPrefix();
	}


	//интерфейс для вызова метода.
	//используется при обработки макросов
	public function cms_callMethod($method_name, $args) {
		$res = call_user_method_array($method_name, $this, $args);
		
		if($method_name == cmsController::getInstance()->getCurrentMethod()) {
			if($sh = $this->sheets()) {
				return $sh . $res . '</all></imenu>';
			}
		}

		return $res;	//no need anymore to check if method exists. (look "__call" function)
	}

	//инициализация модуля
	public function cms_init() {
		$this->pid = cmsController::getInstance()->getCurrentElementId();

		regedit::getInstance()->getList('//modules/' . get_class($this) );

		$xpath = '//modules/' . get_class($this) . '/title';
		$this->name = regedit::getInstance()->getVal($xpath);


		$this->file_path = regedit::getInstance()->getVal('//modules/' . $this->name);
	}


	//незачем писать Object #такой-то
	//будем называть модули своими именами
	public function __toString() {
		return 'umi.' . get_class($this);
	}

	//установка. нужна ссылка на массивы INFO, SQL_INSTALL
	//INFO - все значения будут щанесены в реестр (//modules/module_name/...)
	//SQL_INSTALL - список sql-запросов. теоретически, достаточно прописать создание необходимых таблиц.
	public function install(&$CMS_ENV, &$INFO, &$SQL_INSTALL) {

		$xpath = '//modules/' . $INFO['name'];
//		regedit::getInstance()->setVar($xpath, $INFO['name']);

		if(is_array($INFO)) {
			foreach($INFO as $var => $module_param) {
				$val = $module_param;
				//echo "$xpath/$var <br/>";
				regedit::getInstance()->setVar($xpath . "/" . $var, $val);
			}
		}

		if(is_array($SQL_INSTALL)) {
			foreach($SQL_INSTALL as $sql_n => $sql_q) {
				$mysql_result = mysql_query($sql_q);
				//echo mysql_error() . "<br/>";
			}
		}
	}

	public function uninstall() {
		$regedit = regedit::getInstance();

		$k = $regedit->getKey('//modules/' . get_class($this));
		$regedit->delVar('//modules/' . get_class($this));

		if($k > 0) {
			mysql_unbuffered_query('DELETE FROM cms_reg WHERE id=\'' . $k . '\' OR rel=\'' . $k . '\'');
			return true;
		} else
			return false;
	}

	public function adm_navibar() {
		return "";
	}

	public function config() {
		$this->sheets_reset();
		$this->setHeader("Not overloaded");
		return '<notice>%core_error_notoverloaded%</notice>';
	}

	//загрузка файла с формами. в файле-массив форм, который запоминается.
	public function load_forms($form_name = "forms.php") {	//was protected, but...

		if(array_key_exists($form_name, $this->FORMS_CACHE)) {
			$this->FORMS = $this->FORMS_CACHE[$form_name];
		} else {
			include_once ini_get('include_path') . '/classes/modules/' . get_class($this) . '/' . $form_name;
			$this->FORMS_CACHE[$form_name] = $FORMS;
			$this->FORMS = $FORMS;
		}
	}

	//обработка загруженной формы.
	//поддерживаются простые макросы:
	//%var_name% заменяется на $values_arr['var_name']
	//или заменяются на ""
	public function parse_form($form_name, &$values_arr = Array(), $to_cache = false) {	//was protected, but...
		$to_parse = $this->FORMS[$form_name];

		$to_parse = str_replace('%pre_lang%', $_REQUEST['pre_lang'], $to_parse);


		$values_arr = str_replace("%save_n_save%", $values_arr['save_n_save'], $values_arr);

		$values_arr = (Array) $values_arr + (Array) cmsController::getInstance()->langs_export;
		if(is_array($values_arr)) {
			foreach($values_arr as $var => $val) {
				if($var == "save_n_save" && !$val) continue;
				$to_parse = str_replace('%' . $var . '%', $val, $to_parse);
			}
		}
		$templater = templater::getInstance();

		//TODO: carefully test
		$from = Array("%content%", "%title%", "%header%");
		$to_parse = str_replace($from, "", $to_parse);
		$to_parse = $templater->parseInput($to_parse);
		$to_parse = preg_replace("/%([A-z0-9_]*)%/", "", $to_parse);
		if($to_cache)
			$this->FORMS[$form_name] = $to_parse;


		return $to_parse;
	}

	//редирект
	public function redirect($mode = "", $module = "", $method = "", $ext = "") {
		if($_REQUEST['redirect_disallow'])
			return "";

		umiHierarchy::getInstance()->__destruct();

		if($mode != "" && $mode != -1)
			$mode = "/" . $mode . "/";
		else
			$mode = "/";

		$mode = $_REQUEST['pre_lang'] . $mode;


		$url = $_REQUEST['pre_lang'] . $mode . $module . '/' . $method . '/' . $ext;
		if(!$method)
			$url = $_REQUEST['pre_lang'] . $mode . $module . '/' . $ext;

		if(!$module && !$method) {
			if($mode)
				$url = $mode;
			else
				$url = "";
			if($ext)
				$url .= '?ext';
		}

		if($mode == "/" && $module && $method) {
			if(!$ext)
				$url = $_REQUEST['pre_lang'] . $mode . $module . '/' . $method . '/';
			else
				$url = $_REQUEST['pre_lang'] . $mode . $module . '/' . $method . '/?' . $ext;
		}

		$url = str_replace($_REQUEST['pre_lang'] . $_REQUEST['pre_lang'], $_REQUEST['pre_lang'], $url);


		if(substr($url, 0, strlen("/http://")) != "/http://") {
			$url = str_replace("//", "/", $url);
			$redirect_string = 'Location: http://' . $_SERVER['HTTP_HOST'] . $url;
		} else {
			$url = substr($url, 1, strlen($url) - 2);

  

			$redirect_string = 'Location: ' . $url;

		}

		if(substr($redirect_string, strlen($redirect_string) - 1, 1) == "/") {
			$redirect_string = substr($redirect_string, 0, strlen($redirect_string) - 1);
		}


		$redirect_string = str_replace("Location: ", "", $redirect_string);
		$redirect_string = str_replace($_REQUEST['pre_lang'].$_REQUEST['pre_lang'], $_REQUEST['pre_lang'], $redirect_string);
		header("Content-type: text/html");

		echo "<script>window.location = '$redirect_string';</script>";
		exit();

	}


	//возвращает id шаблона, который должен использоваться модулем.
	public function get_tpl_id() {
		$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();
		$lang_id = cmsController::getInstance()->getCurrentLang()->getId();

		return templatesCollection::getInstance()->getDefaultTemplate($domain_id, $lang_id)->getId();
	}

	/////
	//ф-я __loadLib
	//подключает дополнительные файлы.
	//введена, чтобы подключать дополнительные методы в админке.
	public function __loadLib($lib, $path = "", $remember = "") {
		$lib_path = "";
		if($path)
			$lib_path = $path . $lib;
		else	//default path
			$lib_path = "classes/modules/" . get_class($this) . "/" . $lib;

		$lib_path = ini_get("include_path") . "/" . $lib_path;

		if(array_key_exists($lib_path, $this->libsCalled)) {
			$this->FORMS = $this->FORMS_CACHE[$lib_path];
			return true;
		} else {
			$this->libsCalled[$lib_path] = true;
		}

		include_once $lib_path;

		if($remember) {
			$this->FORMS = $FORMS;
			$this->FORMS_CACHE[$lib_path] = $FORMS;
		}

		return true;
	}


	public function sheets_add($title, $method = "") { //protected
		$this->M_SHEETS[] = Array($title, $method);
	}

	public function sheets_set_active($sheet_active = "") {
		$this->sheet_active = $sheet_active;
	}

	public function sheets_reset() {
		$this->M_SHEETS = Array();
	}

	protected function sheets() {
		if(cmsController::getInstance()->getCurrentMode() != "admin") {
			return "";
		}

		$M_SHEETS = $this->M_SHEETS;
		$sz = sizeof($M_SHEETS);

		$res .= "";

		if(-1 == $this->sheet_active)
			$this->sheets_set_active($_REQUEST['method']);

		if($sz > 0) {

			$res .= "<imenu>\r\n";

			for($it = 0; $it < $sz; $it++) {
				$curr_item = $M_SHEETS[$it];

				$is_active = "";

				if($curr_item[1] == $this->sheet_active)
					$is_active = " status=\"sub_active\"";

				if($curr_item[1] == cmsController::getInstance()->getCurrentMethod())
					$is_active = " status=\"active\"";

				$res .= "\t<item link=\"{$_REQUEST['pre_lang']}/admin/" . get_class($this) . "/{$curr_item[1]}/\" {$is_active}>{$curr_item[0]}</item>\r\n";
			}
			$res .= "\n\t<all>\r\n";
			$res .= "";
			return $res;
		}
		return false;

	}

	public function UC() {
		$res = '<notice><b>%core_warning%</b>%core_warning_uc%</notice>';
		return $res;
	}


	protected function setHeader($header) {
		cmsController::getInstance()->currentHeader = $header;
	}

	protected function setTitle($title = "", $mode = 0) {
		if($title) {
			if($mode)
				cmsController::getInstance()->currentTitle = regedit::getInstance()->getVal('//domains/' . $_REQUEST['domain'] . '/title_pref_' . $_REQUEST['lang']) . $title;
			else
				cmsController::getInstance()->currentTitle = $title;
		}
		else
			cmsController::getInstance()->currentTitle = cmsController::getInstance()->currentHeader;

	}

	protected function setH1($h1) {
		$this->setHeader($h1);
	}



	public function navibar_push($title, $path = "/") {
		cmsController::getInstance()->nav_arr[] = Array($title, $path);
		return true;
	}

	public function navibar_back() {
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
	}

	public function navibar_reset() {
		$this->CMS_ENV['navibar_path'] = Array();
	}



	public function cms_backup($method_name, $param, $params = false) {
	    $res = "";

	    if(cmsController::getInstance()->getModule('backup')) {
		return cmsController::getInstance()->getModule('backup')->make_backup(get_class($this), $method_name, $param, $params);
	    }

	    return $res;
	}

	public function cms_backup_panel($method_name, $param) {
	    if(cmsController::getInstance()->getModule('backup')) {
		return cmsController::getInstance()->getModule('backup')->backup_panel(get_class($this), $method_name, $param);
	    }
	}



	public function flush($output, $ctype = false) {
		if($ctype !== false) {
			header("Content-type: " . $ctype);
		}

		echo $output;
		exit();
	}

	public function get_description() {
		return false;
	}

	public function get_keywords() {
		return false;
	}


	public static function loadTemplates($filepath = "") {
		$c = func_num_args();
		$args = func_get_args();

		$filepath = realpath($filepath);
		if(!is_file($filepath)) return false;
		$lang_filepath = substr($filepath, 0, strlen($filepath) - 3) . cmsController::getInstance()->getCurrentLang()->getPrefix() . ".tpl";
		if(is_file($lang_filepath)) {
			$filepath = $lang_filepath;
		}

		if(!array_key_exists($filepath, def_module::$templates_cache)) {
			include_once $filepath;
			def_module::$templates_cache[$filepath] = $FORMS;
		}

		$templates = def_module::$templates_cache[$filepath];

		$tpls = Array();
		for($i = 1; $i < $c; $i++) {
			$tpl = "";
			if(array_key_exists($args[$i], $templates)) {
				$tpl = $templates[$args[$i]];
			}
			$tpls[] = $tpl;
		}
		return $tpls;
	}

	public static function parseTemplate($template, $arr, $parseElementPropsId = false, $parseObjectPropsId = false) {

		foreach($arr as $m => $v) {
//			$template = str_replace("%" . trim($m, "%") . "%", $v, $template);
			$template = str_replace("%" . $m . "%", $v, $template);
		}
		if($parseElementPropsId !== false || $parseObjectPropsId != false) {
			$template = system_parse_short_calls($template, $parseElementPropsId, $parseObjectPropsId);
			$template = templater::getInstance()->parseInput($template);
			$template = system_parse_short_calls($template, $parseElementPropsId, $parseObjectPropsId);
		}
		return $template;
	}

	public function formatMessage($message) {	//Made non-static: realloc, think about correct overloading in comments module

		$bb_from = Array("[b]", "[i]", "[/b]", "[/i]",
				"[quote]", "[/quote]", "\r\n"
				);

		$bb_to   = Array("<h4>", "<em>", "</h4>", "</em>",
				"<div class='quote'>", "</div>", "<br />"

				);

		$message = preg_replace("`((http)+(s)?:(//)|(www\.))((\w|\.|\-|_| )+)(/)?(\S+)?`i", "[url]http\\3://\\5\\6\\8\\9[/url]", $message);

		$message = str_ireplace($bb_from, $bb_to, $message);
		$message = str_ireplace("</h4>", "</h4><p>", $message);
		$message = str_ireplace("</div>", "</p></div>", $message);

		$message = str_replace(".[/url]", "[/url].", $message);
		$message = str_replace(",[/url]", "[/url],", $message);

		$message = str_replace(Array("[url][url]", "[/url][/url]"), Array("[url]", "[/url]"), $message);

		// split long words
		$message = preg_replace_callback("/[^\s^<^>]{70,}/", create_function('$matches', 'if (strpos($matches[0], "[url]") === false) return wordwrap($matches[0], 30, \'<br />\',1); return $matches[0]; '), $message);


		if (preg_match_all("/\[url\]([^А-я^\r^\n^\t]*)\[\/url\]/U", $message, $matches, PREG_SET_ORDER)) {
			for ($i=0; $i<count($matches); $i++) {
				$s_url = $matches[$i][1];
				$i_length = strlen($s_url);
				if ($i_length>40) {
					$i_cutpart = ceil(($i_length-40)/2);
					$i_center = ceil($i_length/2);
					
					$s_url = substr_replace($s_url, "...", $i_center-$i_cutpart, $i_cutpart*2);
				}
				$message = str_replace($matches[$i][0], "<a href='".$matches[$i][1]."' rel='nofollow' target='_blank' title='Ссылка откроется в новом окне'>".$s_url."</a>", $message);
			}
		}

		$message = str_replace("&", "&amp;", $message);


		$message = str_ireplace("[QUOTE][QUOTE]", "", $message);

		
	
		if(preg_match_all("/\[smile:([^\]]+)\]/im", $message, $out)) {
			foreach($out[1] as $smile_path) {
				$s = $smile_path;
				$smile_path = "images/forum/smiles/" . $smile_path . ".gif";
				if(is_file($smile_path)) {
					$message = str_replace("[smile:" . $s . "]", "<img src='/{$smile_path}' />", $message);
				}
			}
		}

		
		$message = preg_replace("/<p>(<br \/>)+/", "<p>", $message);
		$message = nl2br($message);
		$message = str_replace("<<br />br /><br />", "", $message);
		$message = str_replace("<p<br />>", "<p>", $message);
//		$message = preg_replace("/(<br \/>)+<\/p>/", "</>", $message);

		return $message;
	}

	public function autoDetectAttributes() {
		if($element_id = cmsController::getInstance()->getCurrentElementId()) {
			$element = umiHierarchy::getInstance()->getElement($element_id);

			if(!$element) return false;

			if($h1 = $element->getValue("h1")) {
				$this->setHeader($h1);
			} else {
				$this->setHeader($element->getName());
			}

			if($title = $element->getValue("title")) {
				$this->setTitle($title);
			}

		}
	}

	public function generateNumPage($total, $per_page, $page_current = 0, $key = "p", $params = Array()) {
		if($total < $per_page) return "";

		$pages = "";
		$pages_count = ceil($total / $per_page);

		if(!$pages_count) $pages_count = 1;
		$max_pages = $this->max_pages;

		$q = (sizeof($params)) ? "&amp;" . str_replace("&", "&amp;", http_build_query($params)) : "";

		for($i = 0; $i < $pages_count; $i++) {
			$n = $i + 1;

			if(($page_current - $this->max_pages) > $i) continue;

			if(($page_current + $max_pages) < $i) break;

			$active = ($i == $page_current) ? " active=\"yes\"" : "";
			$link = "?{$key}={$i}" . $q;

			$pages .= <<<PAGE
	<goto link='{$link}' {$active}>{$n}</goto>
PAGE;

		}

			$tobegin_link = "?{$key}=0" . $q;
			$prev_link = "?{$key}=" . ($page_current - 1) . $q;
			$toend_link = "?{$key}=" . ($pages_count - 1) . $q;
			$next_link = "?{$key}=" . ($page_current + 1) . $q;

			$begin_active = ($page_current == 0) ? "" : "yes";
			$end_active = ($page_current == ($pages_count - 1)) ? "" : "yes";




		$res = <<<NUM_PAGE
<numgroup>
	<tobegin active="{$begin_active}" link="{$tobegin_link}">&#171;</tobegin>
	<prev active="{$begin_active}" link="{$prev_link}"><![CDATA[<]]></prev>
{$pages}
	<next active="{$end_active}" link="{$next_link}"><![CDATA[>]]></next>
	<toend active="{$end_active}" link="{$toend_link}">&#187;</toend>
</numgroup>
NUM_PAGE;
		return $res;
	}

	public function generateFilters($hierarchy_type_id, $object_type_id = false, $bIsObjectFilter = false) {
		$oFilter = new umiFilter($hierarchy_type_id, $object_type_id, $bIsObjectFilter);
		
		return umiFilterProcessor::renderFilter($oFilter);
	}

	public function apply_filters() {
		$hierarchy_type_id = isset($_REQUEST['hierarchy_type_id'])? (int) $_REQUEST['hierarchy_type_id']: false;
		$object_type_id = isset($_REQUEST['object_type_id'])? (int) $_REQUEST['object_type_id']: false;
		$bIsObjectFilter = isset($_REQUEST['is_object_filter'])? (bool) $_REQUEST['is_object_filter']: false;
		$sSortedProperty = isset($_REQUEST['sortby'])? substr($_REQUEST['sortby'], 5) : "";
		$bSortOrder = isset($_REQUEST['desc_order'])? (bool) $_REQUEST['desc_order'] : false;

		$oFilter = new umiFilter($hierarchy_type_id, false, $bIsObjectFilter);
		$oHierarchyType = umiHierarchyTypesCollection::getInstance()->getType($hierarchy_type_id);
		// filter input
		foreach ($_REQUEST as $sVarName => $vValue) {
			if (substr($sVarName, 0, 5) === "fltr_") {
				$sFieldName = substr($sVarName, 5);
				switch ($sFieldName) {
					case "active":
									if (!$bIsObjectFilter) {
										$bValue = $vValue == 1? 1: 0;
										$oFilter->addFilterByActive((bool) $bValue);
									}
									break;
					case "name":
									break;
					case "sstring": 
									$oFilter->setFilterString((string) $vValue); break;
					case "types" : 
									if (is_array($vValue) && count($vValue)) {
										for ($iI = 0; $iI < count($vValue); $iI++) {
											$oFilter->addObjectTypeFilter((int) $vValue[$iI]);
										}
									}
									break;
					default: $oFilter->addPropertyFilter($sFieldName, $vValue);
				}
			}
		}

		if (strlen($oFilter->getFilterString())) {
			$oFilter->addFilterByName(true);
		}

		$oFilter->setSortedProperty($sSortedProperty, $bSortOrder);

		// results
		$this->load_forms("../forms.php");

		$arrFilterResults = umiFilterProcessor::applyFilter($oFilter);

		$params = array();
		$params['rows'] = "";
		$sFilterResults = "";

		if (count($arrFilterResults)) {
			for ($iI = 0; $iI < count($arrFilterResults); $iI++) {
				$iEntityId = $arrFilterResults[$iI];
				
				if ($bIsObjectFilter) {
					$oEntity = umiObjectsCollection::getInstance()->getObject($iEntityId);
				} else {
					$oEntity = umiHierarchy::getInstance()->getElement($iEntityId);
				}
				if ($oEntity) {
					$iObjectTypeId = $bIsObjectFilter? $oEntity->getTypeId() : $oEntity->getObject()->getTypeId();
					$iTypeId = $oEntity->getTypeId();

					$row_params = array();
					$row_params['name'] = $oEntity->getName();
					$row_params['type_id'] = $iObjectTypeId;

					$sEditLink = "";

					list(, $sEditLink) = $this->getEditLink($oEntity->getId(), $oHierarchyType->getExt());

					$row_params['edit_link'] = $sEditLink;

					$params['rows'] .= $this->parse_form("filter_results_row", $row_params);
				}
			}
			$sFilterResults = $this->parse_form("filter_results", $params);
		}

		$sResult = umiFilterProcessor::renderFilter($oFilter) . "<br />" . $sFilterResults;

		return $sResult;
	}

	public function autoDetectFilters(umiSelection $sel, $object_type_id) {
		if(array_key_exists("fields_filter", $_REQUEST)) {
			$sel->setPropertyFilter();

			$type = umiObjectTypesCollection::getInstance()->getType($object_type_id);


			$order_filter = $_REQUEST['fields_filter'];
			foreach($order_filter as $field_name => $value) {
				if($field_id = $type->getFieldId($field_name)) {
					$field = umiFieldsCollection::getInstance()->getField($field_id);

					$field_type_id = $field->getFieldTypeId();
					$field_type = umiFieldTypesCollection::getInstance()->getFieldType($field_type_id);

					$data_type = $field_type->getDataType();

					switch($data_type) {
						case "text": {
							$this->applyFilterText($sel, $field, $value);
							break;
						}

						case "wysiwyg": {
							$this->applyFilterText($sel, $field, $value);
							break;
						}

						case "string": {
							$this->applyFilterText($sel, $field, $value);
							break;
						}

						case "int": {
							$this->applyFilterInt($sel, $field, $value);
							break;
						}

						case "relation": {
							$this->applyFilterRelation($sel, $field, $value);
							break;
						}

						case "price": {
							$this->applyFilterPrice($sel, $field, $value);
							break;
						}

						case "boolean": {
							$this->applyFilterInt($sel, $field, $value);
							break;
						}





						default: {
							break;
						}
					}
				} else {
					continue;
				}
			}
		} else {
			return false;
		}
	}


	public function analyzeRequiredPath($pathOrId, $returnCurrentIfVoid = true) {

		if(is_numeric($pathOrId)) {
			return (umiHierarchy::getInstance()->isExists((int) $pathOrId)) ? (int) $pathOrId : false;
		} else {
			$pathOrId = trim($pathOrId);

			if($pathOrId) {
				if(strpos($pathOrId, " ") === false) {
					return umiHierarchy::getInstance()->getIdByPath($pathOrId);
				} else {
					$paths_arr = split(" ", $pathOrId);

					$ids = Array();

					foreach($paths_arr as $subpath) {
						$id = $this->analyzeRequiredPath($subpath, false);

						if($id === false) {
							continue;
						} else {
							$ids[] = $id;
						}
					}

					if(sizeof($ids) > 0) {
						return $ids;
					} else {
						return false;
					}
				}
			} else {
				if($returnCurrentIfVoid) {
					return cmsController::getInstance()->getCurrentElementId();
				} else {
					return false;
				}
			}
		}
	}

};

?>