<?php
class ranges {
	public function __construct() {
		$this->days = Array(
			'�����������'	=> 0,
			'�������'	=> 1,
			'�����'		=> 2,
			'�������'	=> 3,
			'�������'	=> 4,
			'�������'	=> 5,
			'������������'	=> 6,
			'��'		=> 0,
			'���'		=> 0,
			'��'		=> 1,
			'��'		=> 2,
			'���'		=> 2,
			'��'		=> 3,
			'���'		=> 3,
			'��'		=> 4,
			'���'		=> 4,
			'��'		=> 5,
			'��'		=> 6);

		$this->months = Array(
			'������'	=> 0,
			'�������'	=> 1,
			'����'		=> 2,
			'������'	=> 3,
			'���'		=> 4,
			'����'		=> 5,
			'����'		=> 6,
			'������'	=> 7,
			'��������'	=> 8,
			'�������'	=> 9,
			'������'	=> 10,
			'�������'	=> 11,

			'���'	=> 0,
			'��'	=> 0,
			'���'	=> 1,
			'��'	=> 1,
			'���'	=> 2,
			'���'	=> 3,
			'��'	=> 3,
			'���'	=> 4,
			'����'		=> 5,
			'��'		=> 5,
			'����'		=> 6,
			'��'		=> 6,
			'������'	=> 7,
			'���'	=> 7,
			'����'	=> 8,
			'���'	=> 8,
			'���'	=> 9,
			'��'	=> 9,
			'���'	=> 10,
			'���'	=> 11);
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