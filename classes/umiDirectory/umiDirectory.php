<?php

class umiDirectory implements iUmiDirectory {
	protected $s_dir_path = "";
	protected $is_broken = false;
	protected $arr_files = array();
	protected $arr_dirs = array();

	public function __construct($s_dir_path) {

		while (substr($s_dir_path, -1)=="/") $s_dir_path=substr($s_dir_path, 0, (strlen($s_dir_path)-1));

		if(is_dir($s_dir_path)) {
			$this->s_dir_path = $s_dir_path;
			$this->read();
		} else {
			$this->is_broken = true;
			return false;
		}
	}

	private function read() {

		$this->arr_files = array();
		$this->arr_dirs = array();

		if (is_dir($this->s_dir_path) && is_readable($this->s_dir_path)) {
			if ($rs_dir = opendir($this->s_dir_path)) {
				$s_next_file = "";
				while (($s_next_obj = readdir($rs_dir)) !== false) {
					if(defined("CURRENT_VERSION_LINE")) {
						if(CURRENT_VERSION_LINE == "demo") {
							if($s_next_obj == "demo") continue;
						}
					}
					$s_obj_path = $this->s_dir_path."/".$s_next_obj;
					if (is_file($s_obj_path)) {
						$this->arr_files[$s_next_obj] = $s_obj_path;
					} elseif (is_dir($s_obj_path) && $s_next_obj != ".." && $s_next_obj != ".") {
						$this->arr_dirs[$s_next_obj] = $s_obj_path;
					}
				}
				closedir($s_dir);
			}
		}
	}


	public function getIsBroken() {
		return (bool) $this->is_broken;
	}

	public function getFSObjects($i_obj_type=0, $s_mask="", $b_only_readable=false) {
		$arr_result =array();
		$arr_objs = array();

		switch ($i_obj_type) {
			case 1:									//1: real files
					$arr_objs = $this->arr_files;
				break;
			case 2:									//2: directories
					$arr_objs = $this->arr_dirs;
				break;
			default:
					$arr_objs = array_merge($this->arr_dirs, $this->arr_files);
		}

		foreach ($arr_objs as $s_obj_name => $s_obj_path) {
			if ((!$b_only_readable || is_readable($s_obj_path)) && (!strlen($s_mask)) || preg_match("/".$s_mask."/i", $s_obj_name)) {
				$arr_result[$s_obj_name] = $s_obj_path;
			}
		}

		return $arr_result;
	}

	public function getFiles($s_mask="", $b_only_readable=false) {
		return $this->getFSObjects(1, $s_mask, $b_only_readable);
	}

	public function getDirectories($s_mask="", $b_only_readable=false) {
		return $this->getFSObjects(2, $s_mask, $b_only_readable);
	}
	
	public function __toString() {
		return "umiDirectory::{$this->s_dir_path}";
	}
}



?>