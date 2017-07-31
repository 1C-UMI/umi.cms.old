<?php
	function utf8_1251($s) {
		return iconv("UTF-8", "CP1251", $s);
	}

?>