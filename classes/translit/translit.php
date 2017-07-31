<?php
	class translit implements iTranslit {
		public static	$fromUpper = Array("Ý/g", "×", "Ø", "¨", "¨", "Æ", "Þ", "Þ", "ß", "ß", "À", "Á", "Â", "Ã", "Ä", "Å", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ù", "Ú", "Û", "Ü");
		public static	$fromLower = Array("ý", "÷", "ø", "¸", "¸", "æ", "þ", "þ", "ÿ", "ÿ", "à", "á", "â", "ã", "ä", "å", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ù", "ú", "û", "ü");
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