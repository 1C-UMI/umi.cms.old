<?php

function system_module_install($install_path, &$CMS_ENV) {
	if(!is_file($install_path)) {
		return "No such file!";
	}

	require_once $install_path;

	def_module::install($CMS_ENV, $INFO, $SQL_INSTALL);

	return true;
}


function system_is_allowed($module, $method = false) {
	if(!isset($cache)) {
		$cache = Array();
	}
	$ck = md5($module, $method);
	if(array_key_exists($ck, $cache)) return $cache[$ck];
	
	if($users_ext = cmsController::getInstance()->getModule("users")) {
		if($users_ext->isSv($users_ext->user_id)) return $cache[$ck] = true;

		if($method !== false && $method) {
			return $cache[$ck] = $users_ext->isAllowedMethod($users_ext->user_id, $module, $method);
		}

		if($module !== false) {
			return $cache[$ck] = $users_ext->isAllowedModule($users_ext->user_id, $module);
		}
	} else {
		return $cache[$ck] = true;
	}
}



function system_upload_file($tmp_name, $dest_path, $filename) {
	if(is_uploaded_file($tmp_name)) {
		move_uploaded_file($tmp_name, $dest_path . $filename);
		chmod($dest_path . $filename, 0777);
		return true;
	} else
		return false;
}


function system_get_tpl($tpl_id = 0) {
	$tpl_id = (int) $tpl_id;

	if($element_id = cmsController::getInstance()->getCurrentElementId()) {
		if($element = umiHierarchy::getInstance()->getElement($element_id)) {
			$tpl_id = $element->getTplId();
		}
	}

	if($tpl_id) {
		if($tpl = templatesCollection::getInstance()->getTemplate($tpl_id)) {
			$path = $tpl->getFilename();
		}
	} else {
		$path = "index.tpl";
	}

	list($name) = split(".tpl", $path);

	$lng = $_REQUEST['lang'];
	$tname = $name . "." . $lng . ".tpl";
	if(is_file("tpls/content/" . $tname))
		return $tname;
	else
		return $path;
}


function file_permissions($path, $mode = 0) {
	if(!$path)
		return false;

	$ta = stat($path);
	$perm = decoct($ta['mode']);
	$perm = substr($perm, -3);

	if($mode) {
		list($po, $pg, $pa) = str_split($perm);

		$pos = "";

		$po *= 1;

		if(($po & 0x4) == 0x4)
			$pos .= "r";
		else
			$pos .= "-";

		if(($po & 0x2) == 0x2)
			$pos .= "w";
		else
			$pos .= "-";

		if(($po & 0x1) == 0x1)
			$pos .= "x";
		else
			$pos .= "-";

		if(($pg & 0x4) == 0x4)
			$pgs .= "r";
		else
			$pgs .= "-";

		if(($pg & 0x2) == 0x2)
			$pgs .= "w";
		else
			$pgs .= "-";

		if(($pg & 0x1) == 0x1)
			$pgs .= "x";
		else
			$pgs .= "-";

		if(($pa & 0x4) == 0x4)
			$pas .= "r";
		else
			$pas .= "-";

		if(($pa & 0x2) == 0x2)
			$pas .= "w";
		else
			$pas .= "-";

		if(($pa & 0x1) == 0x1)
			$pas .= "x";
		else
			$pas .= "-";



		return $pos . "&nbsp;" . $pgs . "&nbsp;" . $pas;
	} else
		return $perm;
}

function system_buildin_load($package_name) {
	static $mc = Array();
	if($mc[$package_name])
		return $mc[$package_name];

	$package_path = ini_get('include_path') . "classes/modules/" . $package_name . ".php";

	if(is_file($package_path)) {
		include_once $package_path;
		if(class_exists($package_name)) {
			$pk = $mc['packages'][$package_name] = new $package_name();
			return $pk;
		}
	}
	return false;
}

function system_remove_cache($alt) {
	$cacheFolder = ini_get('include_path') . "cache";
	$cacheFileName = md5($alt);
	$cacheFilePath = $this->cacheFolder . "/" . $this->cacheFileName;

	if(is_file($cacheFilePath))
		return unlink(md5($cacheFilePath));
	else
		return false;
}

function system_compare_str($str1, $str2) {
	$res = 100 * (
		similar_text($str1, $str2) / (
			(strlen($str1) + strlen($str2))
		/ 2)
		);

	return $res;
}

function system_try_alt($wtt, $result) {
	$max_compare = Array(0, 0, "");	//comp, id, alt

	while(list($id, $alt_name) = mysql_fetch_row($result)) {
		$cr = system_compare_str($wtt, $alt_name);
		if($max_compare[0] < $cr)
			$max_compare = Array($cr, $id, $alt_name);
	}

	if($max_compare[0] > 85)
		return Array($max_compare[1], $max_compare[2]);
	else
		return false;
}

function system_checkSession() {
	if(is_array($_COOKIE))
		return array_key_exists("umicms_session", $_COOKIE);
	return false;
}

function system_setSession() {
	$sess_id = md5(time());
	setcookie("umicms_session", $sess_id, 0, "/");
	return $sess_id;
}

function system_removeSession() {
	setcookie("umicms_session", "", 1, "/");
}

function system_getSession() {
	if(is_array($_COOKIE))
		return $_COOKIE['umicms_session'];
	else
		return false;
}

function system_runSession() {
	if(!system_checkSession())
		return system_setSession();
	else
		return system_getSession();
}



function system_gen_password($length = 12, $avLetters = "\$#@^&!1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM") {
		$npass = "";
		for($i = 0; $i < $length; $i++) {
			$npass .= $avLetters[rand(0, strlen($avLetters)-1)];
		}
		return $npass;
}


function toTimeStamp($ds) {
	$s = "[ \.\-\/\\\\]{1,10}";
	//for common formats...

	$ds = str_replace("-", " ", $ds);
	$ds = str_replace(",", " ", $ds);

	$ds = str_replace("\\'", " ", $ds);

	if(preg_match("/\d{2}\:\d{2}/", $ds, $temp)) {
		$ms = $temp[0];
		preg_replace("/\d{2}\:\d{2}/", "", $ds);

		list($hours, $mins) = split(":", $ms);
	}

	$ds = preg_replace("/(\d{4})$s(\d{2})$s(\d{2})/im", "^\\3^ !\\2! ?\\1?", $ds);
	$ds = preg_replace("/(\d{1,2})$s(\d{1,2})$s(\d{2,4})/im", "^\\1^ !\\2! ?\\3?", $ds);


	//for uncommon formats

	$days = Array(
			'понедельник',
			'вторник',
			'среда',
			'четверг',
			'пятница',
			'суббота',
			'воскресенье'
			);

	$months = Array(
			'январь',
			'февраль',
			'март',
			'апрель',
			'май',
			'июнь',
			'июль',
			'август',
			'сентябрь',
			'октябрь',
			'ноябрь',
			'декабрь'
			);

	$months_vin = Array(
			'января',
			'февраля',
			'марта',
			'апреля',
			'мая',
			'июня',
			'июля',
			'августа',
			'сентября',
			'октября',
			'ноября',
			'декабря'
			);

	$months_short = Array(
			'янв',
			'фев',
			'мар',
			'апр',
			'май',
			'июн',
			'июл',
			'авг',
			'сен',
			'окт',
			'ноя',
			'дек'
			);

	$months_to = Array(
			'01',
			'02',
			'03',
			'04',
			'05',
			'06',
			'07',
			'08',
			'09',
			'10',
			'11',
			'12'
			);

	foreach($months as $k => $v)
		$months[$k] = "/" . $v . "/i";

	foreach($months_vin as $k => $v)
		$months_vin[$k] = "/" . $v . "/i";

	foreach($months_short as $k => $v)
		$months_short[$k] = "/" . $v . "/i";

	foreach($months_to as $k => $v) {
		$months_to[$k] = " !" . $v . "! ";
	}

	$ds = preg_replace($months, $months_to, $ds);
	$ds = preg_replace($months_vin, $months_to, $ds);
	$ds = preg_replace($months_short, $months_to, $ds);

	//let's convert year
	$years = Array(
			'/(\d{2,4})[ ]*года/i',
			'/(\d{2,4})[ ]*год/i',
			'/(\d{2,4})[ ]*г/i',
			'/(\d{4})/i',
			);

	$ds = preg_replace($years, "?\\1?", $ds);

	$ds = preg_replace("/[^!^\?^\d](\d{1,2})[^!^\?^\d]/i", "^\\1^", " ".$ds." ");


	if(preg_match("/\^(\d{1,2})\^/", $ds, $mt)) {
		$day = $mt[1];
		if(strlen($day) == 1)
			$day = "0" . $day;
	}

	if(preg_match("/!(\d{1,2})!/", $ds, $mt)) {
		$month = $mt[1];
		if(strlen($month) == 1)
			$month = "0" . $month;
	}

	if(preg_match("/\?(\d{2,4})\?/", $ds, $mt)) {
		$year = $mt[1];
		if(strlen($year) == 2) {
			$ss = (int) substr($year, 0, 1);
			if( ($ss >= 0 && $ss <= 4))
				$year = "20" . $year;
			else
				$year = "19" . $year;
		}
	}

	if($day > 31) {
		$t = $year;
		$year = $day;
		$day = $t;
	}

	if($month > 12) {
		$t = $month;
		$month = $day;
		$day = $t;
		unset($t);
	}


	$tds = trim(strtolower($ds));
	switch($tds) {

		case "сегодня":
				$ts = time();

				$year = date("Y", $ts);
				$month = date("m", $ts);
				$day = date("d", $ts);

				break;


		case "завтра":
				$ts = time() + (3600*24);

				$year = date("Y", $ts);
				$month = date("m", $ts);
				$day = date("d", $ts);

				break;
		case "вчера":
				$ts = time() - (3600*24);

				$year = date("Y", $ts);
				$month = date("m", $ts);
				$day = date("d", $ts);


				break;

		case "послезавтра":
				$ts = time() + (3600*48);

				$year = date("Y", $ts);
				$month = date("m", $ts);
				$day = date("d", $ts);

				break;
		case "позавчера":
				$ts = time() - (3600*48);

				$year = date("Y", $ts);
				$month = date("m", $ts);
				$day = date("d", $ts);


				break;
	}


	if(!$day) {
		$tds = str_replace(Array($year, $month), "", $ds);
		preg_match("/(\d{1,2})/", $tds, $tmp);
		$day = $tmp[1];
	}



	return $timestamp = mktime($hours, $mins, 0, $month, $day, $year);
}


function translit($input, $mode = "R_TO_E") {
	$rusBig = Array( "Э", "Ч", "Ш", "Ё", "Ё", "Ж", "Ю", "Ю", "\Я", "\Я", "А", "Б", "В", "Г", "Д", "Е", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Щ", "Ъ", "Ы", "Ь");
	$rusSmall = Array("э", "ч", "ш", "ё", "ё","ж", "ю", "ю", "я", "я", "а", "б", "в", "г", "д", "е", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "щ", "ъ", "ы", "ь" );
	$engBig = Array("E\'", "CH", "SH", "YO", "JO", "ZH", "YU", "JU", "YA", "JA", "A","B","V","G","D","E", "Z","I","J","K","L","M","N","O","P","R","S","T","U","F","H","C", "W","~","Y", "\'");
	$engSmall = Array("e\'", "ch", "sh", "yo", "jo", "zh", "yu", "ju", "ya", "ja", "a", "b", "v", "g", "d", "e", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s",  "t", "u", "f", "h", "c", "w", "~", "y", "\'");
	$rusRegBig = Array("Э", "Ч", "Ш", "Ё", "Ё", "Ж", "Ю", "Ю", "Я", "Я", "А", "Б", "В", "Г", "Д", "Е", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Щ", "Ъ", "Ы", "Ь");
	$rusRegSmall = Array("э", "ч", "ш", "ё", "ё", "ж", "ю", "ю", "я", "я", "а", "б", "в", "г", "д", "е", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "щ", "ъ", "ы", "ь");
	$engRegBig = Array("E'", "CH", "SH", "YO", "JO", "ZH", "YU", "JU", "YA", "JA", "A", "B", "V", "", "D", "E", "Z", "I", "J", "K", "L", "M", "N", "O", "P", "R", "S", "T", "U", "F", "H", "C", "W", "~", "Y", "'");
	$engRegSmall = Array("e'", "ch", "sh", "yo", "jo", "zh", "yu", "ju", "ya", "ja", "a", "b", "v", "", "d", "e", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "w", "~", "y", "'");


	$textar = $input;
	$res = $input;

	if($mode == "E_TO_R") {
		if ($textar) {
			for ($i=0; $i<sizeof($engRegSmall); $i++) {
				$textar = str_replace($engRegSmall[$i], $rusSmall[$i], $textar);
			}
			for ($i=0; $i<sizeof($engRegBig); $i++) {
				$textar = str_replace($engRegBig[$i], $rusBig[$i], $textar);
				$textar = str_replace($engRegBig[$i], $rusBig[$i], $textar);
			}
			$res = $textar;
		}
	}

	if($mode == "R_TO_E") {
		if ($textar) {
			$textar = str_replace($rusRegSmall, $engSmall, $textar);
			$textar = str_replace($rusRegBig, $engSmall, $textar);
			$res = strtolower($textar);
		}
	}

	$from = Array("/", "\\", "'", "\t", "\r\n", "\n", "\"", " ", "?", ".");
	$to = Array("", "", "", "", "", "", "", "_", "", "");

	$res = str_replace($from, $to, $res);

	$res = preg_replace("/[ ]+/", "_", $res);
	return $res;
}

function system_returnSkinPath() {
	if($_REQUEST['skin_sel']) {
		$skin  = $_REQUEST['skin_sel'];
	} else {
		$skin = $_COOKIE['skin'];
	}

	$regedit = &regedit::getInstance();
	
	if(!$skin) {
		$skin = $regedit->getVal("//skins");;
	}

	$icon_ext = $regedit->getVal("//skins/" . $skin . "/icon_ext");
	define("ICO_EXT", $icon_ext);


	if(!is_file("./tpls/admin/" . $skin . ".xml")) {
		$skin = "full";
	}

	if(cmsController::getInstance()->getCurrentModule() == "users" && (cmsController::getInstance()->getCurrentMethod() == "login" || cmsController::getInstance()->getCurrentMethod() == "auth" || cmsController::getInstance()->getCurrentMethod() == "login_do")) {
		if(is_file("./tpls/admin/" . $skin . "_login.xml")) {
			define("SKIN_PATH", $skin);
			return $skin . "_login.xml";
		} else {
			define("SKIN_PATH", $skin);
			return $skin . ".xml";
		}
	} else {
		define("SKIN_PATH", $skin);
		return $skin . ".xml";
	}
}


function system_pstr(&$str) {
	$from = Array(	"&",
			"%",
			"<",
			">",
			"&ntilde;");

	$to = Array(	"&amp;",
			"&#037;",
			"&#60;",
			"&#62;",
			"~");

	$str = str_replace($from, $to, $str);
}

function system_filter_str($str = "", $proc = false) {
	$str = $str;
	$str = preg_replace('/[^A-zА-я !-%\x27-;=?-~]/e', '"&#" . ord("$0") . chr(59)', $str);
	if($proc)
		$str = str_replace("%", "&#37;", $str);
	return $str;
}

function system_assoc_replace($str, $arr) {
	foreach($arr as $k => $v)
		$str = str_ireplace($k, ($v+1), $str);
	return $str;
}

function system_parse_short_calls($res, $element_id = false, $object_id = false) {
	if($element_id === false && $object_id === false) {
		$element_id = cmsController::getInstance()->getCurrentElementId();
	}
	
	if(strpos($res, "id%") !== false) {
		$res = str_replace("%id%", $element_id, $res);
		$res = str_replace("%pid%", cmsController::getInstance()->getCurrentElementId(), $res);
	}

	if($element_id !== false) {
		if(!($element = umiHierarchy::getInstance()->getElement($element_id))) {
			return $res;
		} else {
			$object = $element->getObject();
		}
	}

	if($object_id !== false) {
		if(!($object = umiObjectsCollection::getInstance()->getObject($object_id))) {
			return $res;
		}

	}


	if(!$object) return $res;

	$object_type_id = $object->getTypeId();
	$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);


	if(preg_match_all("/%([A-z0-9\-_]*)%/", $res, $out)) {
		foreach($out[1] as $obj_prop_name) {
			if($object_type->getFieldId($obj_prop_name) != false) {
				$val = $object->getValue($obj_prop_name);

				if(is_object($val)) {
					if($val instanceof umiDate) {
						$val = $val->getFormattedDate("U");
					}

					if($val instanceof umiImageFile) {
						$val = $val->getFilePath(true);
					}
					
					if($val instanceof umiHierarchy) {
						$val = $val->getName();
					}
				}

				if(is_array($val)) {
					$value = "";

					$sz = sizeof($val);
					for($i = 0; $i < $sz; $i++) {
						$cval = $val[$i];

						if(is_numeric($cval)) {
							if($obj = umiObjectsCollection::getInstance()->getObject($cval)) {
								$cval = $obj->getName();
								unset($obj);
							} else {
								continue;
							}
						}

						if($cval instanceof umiHierarchyElement) {
							$cval = $cval->getName();
						}


						$value .= $cval;
						if($i < ($sz - 1)) $value .= ", ";
					}

					$val = $value;
				}
				$res = str_replace("%" . $obj_prop_name . "%", $val, $res);
			}
		}
	}
	
	
	if(strpos($res, "id%") !== false) {
		$res = str_replace("%id%", $element_id, $res);
		$res = str_replace("%pid%", cmsController::getInstance()->getCurrentElementId(), $res);
	}

	return $res;
}

?>