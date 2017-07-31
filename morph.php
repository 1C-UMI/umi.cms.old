<?php
$suf = Array();

$suf[] = 'ование';
$suf[] = 'ельный';
$suf[] = 'енный';
$suf[] = 'ировать';
$suf[] = 'овая';
$suf[] = 'ого';
$suf[] = 'ему';
$suf[] = 'ный';
$suf[] = 'ями';
$suf[] = 'чно';
$suf[] = 'ах';
$suf[] = 'ях';
$suf[] = 'ей';
$suf[] = 'ях';
$suf[] = 'ом';
$suf[] = 'ий';
$suf[] = 'ые';
$suf[] = 'ие';
$suf[] = 'ый';
$suf[] = 'ий';
$suf[] = 'ам';
$suf[] = 'ах';
$suf[] = 'ми';
$suf[] = 'ям';
$suf[] = 'та';
$suf[] = 'ов';

$suf[] = 'ок';

$suf[] = 'сичк'; //женский вариант

$suf[] = 'ик'; //ум-ласкать
$suf[] = 'ки'; //ум-ласкать
$suf[] = 'ка';

$suf[] = 'ся';	//глагол - возвр.
$suf[] = 'ть';	//глагол - инфинитив
$suf[] = 'но';

$suf[] = 'ец';  //суффикс слова пиздец

$suf[] = 'у';
$suf[] = 'и';
$suf[] = 'ы';
$suf[] = 'е';
$suf[] = 'а';
$suf[] = 'я';
$suf[] = 'ь';
$suf[] = 'о';
$suf[] = 'ю';
$suf[] = 'ч';

$rsuf = Array();
$rsuf[] = 'ир';
$rsuf[] = 'ов';
$rsuf[] = 'ев';
$rsuf[] = 'и';


function morph_get_root($word) {
	global $suf, $rsuf, $morph_cache;
	$min_word_l = 2;

	$tsuf = $suf;
	$sz = sizeof($tsuf);
	for($i = 0; $i < $sz; $i++) {
		$csuf = $tsuf[$i];
		$suf_l = strlen($csuf);
		$word_l = strlen($word);

		if( ($word_l - $suf_l) <= $min_word_l)
			continue;

		if(substr($word, $word_l-$suf_l, $suf_l) == $csuf) {
			$word = substr($word, 0, $word_l-$suf_l);
		}
	}

	$tsuf = $rsuf;
	$sz = sizeof($tsuf);
	for($i = 0; $i < $sz; $i++) {
		$csuf = $tsuf[$i];
		$suf_l = strlen($csuf);
		$word_l = strlen($word);

		if( ($word_l - $suf_l) <= $min_word_l)
			continue;

		if(substr($word, $word_l-$suf_l, $suf_l) == $csuf) {
			$word = substr($word, 0, $word_l-$suf_l);
		}
	}

	return $word;
}

function num2ending($num, $lang = "ru", $type = "noun") {
	$res = "";
	$num = (string) (int) $num;

	if($lang == "en") {
		if($type == "verb")
			return "";
		if($num == 1)
			return "";
		else
			return "s";
	}

	$r1 = "а";
	$r2 = "ы";
	$r3 = "";
	if($type == "verb") {
		$r1 = "а";
		$r2 = "о";
		$r3 = "о";
	}

	$ln = substr($num, strlen($num) - 1, 1);
	$ln1 = substr($num, strlen($num) - 2, 2);
	if($ln1 >= 5 && $ln1 <= 20)
		return $r3;

	if($ln == 1)
		return $r1;
	if($ln >= 2 && $ln <= 4)
		return $r2;
	if(($ln >= 5 && ln <= 9) || $ln == 0)
		return $r3;
}
?>