<?php
//// UMI.CMS 2.0
//Безопасность.


//экранируем метасимволы в массиве
function protect_array(&$input_array)  {
	if(is_array($input_array)) {

		foreach($input_array as $var => $val) {
			if(is_array($val)) {
				$val = protect_array($val);
			} else {
//				$val = str_replace(Array("\\\"", "\\'", "\\\\"), Array("\"", "'", "\\"), $val);
				$val = stripslashes($val);
			}
			$input_array[$var] = $val;
		}

	}

	return $input_array;
}

if(get_magic_quotes_gpc()) {
	$_REQUEST = protect_array($_REQUEST);
	$_POST = protect_array($_POST);
	$_GET = protect_array($_GET);
}


?>
