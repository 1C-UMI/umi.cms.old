<?php
	class translit implements iTranslit {
		public static	$fromUpper = Array("�/g", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�");
		public static	$fromLower = Array("�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�");
		public static	$toLower   = Array("e\'", "ch", "sh", "yo", "jo", "zh", "yu", "ju", "ya", "ja", "a", "b", "v", "g", "d", "e", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s",  "t", "u", "f", "h", "c", "w", "~", "y", "\'");
				
		public static function convert($str) {
			$str = umiHierarchyElement::filterInputString($str);

			$str = str_replace(self::$fromLower, self::$toLower, $str);
			$str = str_replace(self::$fromUpper, self::$toLower, $str);
			$str = strtolower($str);

			$str = preg_replace("/([^A-z^0-9^_]+)/", "_", $str);

			$str = preg_replace("/[\/\\',\t]*/", "", $str);
			$str = str_replace("\"", "", $str);
			$str = str_replace("\\", "", $str);
			$str = preg_replace("/[ \.]+/", "_", $str);

			$str = preg_replace("/([_]+)/", "_", $str);
			$str = trim(trim($str), "_");

			return $str;
		}
	}
?>