<?php

class cifi implements iCifi {
	private $name, $dir;
	private $exts = Array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf', 'GIF', 'JPG', 'JPEG', 'BMP', 'PNG', 'SWF');
	private $image_only;

	public function __construct($name, $dir, $image_only = true) {
		$this->name = $name;
		$this->dir = $dir;
		$this->image_only = $image_only;
	}

	public function read_files() {
		$res = Array();

		$dir = $this->dir;

		$o_dir = new umiDirectory($dir);
		$arr_files = $o_dir->getFiles("(?<!\.htaccess|\.svn)\s?$");

		foreach ($arr_files as $s_file_name => $s_file_path) {
			$name_arr = split("\.", $s_file_name);
			$ext = $name_arr[sizeof($name_arr)-1];
			if(!in_array($ext, $this->exts) && $this->image_only)
				continue;
			$res[] = $s_file_name;
		}
		
		//natsort($res);
		sort($res);

		return $res;
	}

	public function make_element($def = "", $arr_extfiles = array()) {
		$res = "";

		$files_arr = $this->read_files();
		
		$files_arr = array_merge($arr_extfiles, $files_arr);

		$res .= "<script type=\"text/javascript\">\r\n";
		$res .= "\tcifi_images_arr_" . $this->name . " = Array();\r\n";

		if(!is_array($files_arr))
			return false;

		$sz = sizeof($files_arr);
		for($i = 0; $i < $sz; $i++) {
			$res .= "\tcifi_images_arr_" . $this->name . "[" . $i . "] = \"" . $files_arr[$i] . "\";\r\n";
		}

		if($def)
			$def = " ,'$def'";
		else
			$def = "";

		$res .= "\tcifi_generate('" . $this->name . "', cifi_images_arr_" . $this->name . $def . ");\r\n";

		$res .= "</script>\r\n";

		return $res;
	}

	public function make_div() {
		$res = "<div id=\"cifi_mdiv_" . $this->name . "\" style=\"text-align: left; border: #FFF 1px solid;\"></div>\r\n";
		return $res;
	}

	public function make_upload() {
		$selected = $_REQUEST['select_' . $this->name];
		$uploaded = $_FILES['f_' . $this->name];

		if($uploaded['name'] == $selected) {
			system_upload_file($uploaded['tmp_name'], $this->dir, $uploaded['name']);
			return $uploaded['name'];
		} else
			return false; 
	}



	public function getUpdatedValue($mode = false) {
		$name = $this->name;
		$folder = $this->dir;

		$select_value = $_REQUEST['select_' . $name];

		$files_arr = ($mode) ? $HTTP_POST_FILES : $_FILES;

		if($files_arr['pics']['size'][$name] != 0) {
			if($select_value == $files_arr['pics']['name'][$name]) {
				system_upload_file($files_arr['pics']['tmp_name'][$name], $folder, $files_arr['pics']['name'][$name]);
				$res = $files_arr['pics']['name'][$name];
			} else
				$res = $select_value;
		} else
			$res = $select_value;

		return $res;
	}

};

?>