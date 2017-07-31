<?php

function putSelectBox($arr1, $arr2, $val = false) {
	if(!is_array($arr1) || !is_array($arr2))
		return "";
	if(($sz = sizeof($arr1)) != sizeof($arr2))
		return "";

	$res = "";

	for($i = 0; $i < $sz; $i++) {
		if($val !== false) {
			if((!is_array($val) && $val == $arr2[$i]) || (is_array($val) && in_array($arr2[$i], $val)))
				$c = " selected=\"yes\"";
			else
				$c = "";
		}

		$res .= "<item value=\"" . $arr2[$i] . "\" id=\"" . $arr2[$i] . "\"" . $c . ">" . $arr1[$i] . "</item>\r\n";
	}
	return $res;
}

function putSelectBox_assoc($arr1, $val = false, $firstEmpty = false, $isEscaped = true) {
	if(!is_array($arr1))
		return "";

	$res = "";

	foreach($arr1 as $fval => $fname) {
		if(is_array($fname)) {
			list($fname) = $fname;

			$res .= <<<END
<ortgroup><![CDATA[{$fname}]]></ortgroup>
END;
			continue;
		}

		if($val !== false) {
			if(is_array($val)) {
				if(in_array($fval, $val))
					$c = " selected=\"yes\"";
				else
					$c = "";
			} else {
				if($val == $fval)
					$c = " selected=\"yes\"";
				else
					$c = "";
			}
		}

		$res .= <<<END
<item {$c}>
	<value>{$fval}</value>
	<title><![CDATA[{$fname}]]></title>
</item>

END;
	}

	if($firstEmpty) {
		$res = "<item></item>\r\n" . $res;
	}

	return $res;
}


function wa_strtolower($str) {
	if(function_exists("mb_strtolower")) {
		return mb_strtolower($str);
	} else {
		return strtolower($str);
	}
}
?>