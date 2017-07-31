<?php
class ranges {
	public function __construct() {
		$this->days = Array(
			'понедельник'	=> 0,
			'вторник'	=> 1,
			'среда'		=> 2,
			'четверг'	=> 3,
			'п€тница'	=> 4,
			'суббота'	=> 5,
			'восскресенье'	=> 6,
			'пн'		=> 0,
			'пон'		=> 0,
			'вт'		=> 1,
			'ср'		=> 2,
			'срд'		=> 2,
			'чт'		=> 3,
			'чет'		=> 3,
			'пт'		=> 4,
			'птн'		=> 4,
			'сб'		=> 5,
			'вс'		=> 6);

		$this->months = Array(
			'€нварь'	=> 0,
			'февраль'	=> 1,
			'март'		=> 2,
			'апрель'	=> 3,
			'май'		=> 4,
			'июнь'		=> 5,
			'июль'		=> 6,
			'август'	=> 7,
			'сент€брь'	=> 8,
			'окт€брь'	=> 9,
			'но€брь'	=> 10,
			'декабрь'	=> 11,

			'€нв'	=> 0,
			'€н'	=> 0,
			'фев'	=> 1,
			'фв'	=> 1,
			'мар'	=> 2,
			'апр'	=> 3,
			'ап'	=> 3,
			'май'	=> 4,
			'июнь'		=> 5,
			'ин'		=> 5,
			'июль'		=> 6,
			'ил'		=> 6,
			'август'	=> 7,
			'авг'	=> 7,
			'сент'	=> 8,
			'сен'	=> 8,
			'окт'	=> 9,
			'ок'	=> 9,
			'нбр'	=> 10,
			'дек'	=> 11);
	}

	public function get($str = "", $mode = 0) {
		$str = $this->prepareStr($str, $mode);
		return $this->str2range($str);
	}

	private function prepareStr($str = "", $mode = 0) {
		switch($mode) {
			case 0: {
				return system_assoc_replace($str, $this->days);
				break;
			}

			case 1: {
				return system_assoc_replace($str, $this->months);
				break;
			}
		}
	}

	private function str2range($s) {
		$s = preg_replace("/ +/", " ", $s);
		$s = preg_replace("/ - /", "-", $s);
		$s = preg_replace("/! /", "!", $s);

		if(preg_match_all("/(?!!)(\d+)(?!\-)/", $s, $nums)) {
			$nums = $nums[1];
		}

		if(preg_match_all("/!(\d+)(?!\-)/", $s, $unnums)) {
			$unnums = $unnums[1];
		}

		if(preg_match_all("/(?!!)(\d+\-\d+)/", $s, $range)) {
			$range = $range[0];
		}

		if(preg_match_all("/!(\d+\-\d+)/", $s, $urange)) {
			$urange = $urange[1];
		}

		$res = Array();

		$sz = sizeof($urange);
		for($i = 0; $i  < $sz; $i++) {
			list($from, $to) = split("-", $urange[$i]);
			for($n = $from; $n <= $to; $n++) {
				$unnums[] = $n;
			}
		}

		$sz = sizeof($range);
		for($i = 0; $i < $sz; $i++) {
			list($from, $to) = split("-", $range[$i]);
			for($n = $from; $n <= $to; $n++) {
				if(!in_array((int) $n, $unnums))
					$res[] = (int) $n;
			}
		}

		$sz = sizeof($nums);
		for($i = 0; $i < $sz; $i++) {
			if(!in_array((int) $nums[$i], $unnums) && !in_array((int) $nums[$i], $res) && !empty($nums[$i]))
				$res[] = (int) $nums[$i];
		}
		return $res;
	}
}

?>