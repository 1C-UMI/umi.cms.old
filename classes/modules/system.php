<?php

class system {
	public function __toString() {
		return "umi.__system";
	}

	public function cms_callMethod($method_name, $args) {
		$res = call_user_method_array($method_name, $this, $args);
		return $res;
	}

	public function is_int($arg) {
		return is_numeric($arg);
	}

	public function bool2str($arg) {
		if($arg)
			return "true";
		else
			return "false";
	}

	public function fileExists($arg) {
		if(is_file(ini_get('include_path') . $arg))
			return true;
		return false;
	}

	private function getImageProps($arg, $exptect = "width") {
		$fpath = ini_get('include_path') . $arg;

		if(!is_file($fpath))
			return false;

		list($width, $height) = getimagesize($fpath);

		if($exptect == "width")  return $width;
		if($exptect == "height") return $height;
	}

	public function getImageWidth($arg) {
		return $this->getImageProps($arg, "width");
	}

	public function getImageHeight($arg) {
		return $this->getImageProps($arg, "height");
	}

	public function getOuterContent($arg) {
		if(str_replace("http://", "", $arg) != $arg) {
			return file_get_contents($arg);
		} else {			if(substr($arg, -4, 4) == ".tpl") {
				return file_get_contents($arg);
			} else {
				return false;
			}
		}
	}

	public function getSize($arg1, $arg2 = "B", $arg3 = 0) {
		$fpath = ini_get('include_path') . $arg1;
		
		if(is_numeric($arg1)) {
			$sz = $arg1;
		} else {
			if(!is_file($fpath)) {
					return false;
			}
			$sz = filesize($fpath);
		}
		
		if(!$arg3) {
			$arg3 = 0;
		}

		

		if($arg2 == "B")
			return round($sz, $arg3);

		if($arg2 == "K")
			return round($sz / 1024, $arg3);

		if($arg2 == "M")
			return round($sz / (1024*1024), $arg3);

		if($arg2 == "G")
			return round($sz / (1024*1024*1024), $arg3);
	}

	public function convertDate($timestamp, $format) {
		return date($format, $timestamp);
	}


	public function ifClause($cond, $r1 = "", $r2 = "") {
		if($cond)
			return $r1;
		else
			return $r2;
	}

	public function parse_price($num) {
		return number_format($num, 0, ',', ' ');
	}
	
	
	public function getCurrentURI() {
		return $_SERVER['REQUEST_URI'];
	}



	public function makeThumbnail($path, $width, $height, $template = "default", $returnArrayOnly = false) {
		if(!$template) $template = "default";

		$image = new umiImageFile($path);
		$file_name = $image->getFileName();
		$file_ext = $image->getExt();
		
		$file_ext = strtolower($file_ext);
		$allowedExts = Array('gif', 'jpeg', 'jpg', 'png', 'bmp');
		if(!in_array($file_ext, $allowedExts)) return "";

		$file_name = substr($file_name, 0, (strlen($file_name) - (strlen($file_ext) + 1)) );
		$file_name_new = $file_name . "_" . $width . "_" . $height . "." . $file_ext;
		$path_new = "./images/cms/thumbs/" . $file_name_new;

		if(!is_file($path_new)) {
			$width_src = $image->getWidth();
			$height_src = $image->getHeight();

			if($width_src <= $width && $height_src <= $height) {
				copy($path, $path_new);
			} else {

				if($height == "auto") {
					$real_height = (int) round($height_src * ($width / $width_src));
					$real_width = (int) $width;
				} else {
					if($width == "auto") {
						if($height < $height_src) {
							$real_width = (int) round($width_src * ($height / $height_src));
						} else {
							$real_width = $width_src;
						}
					} else {
						$real_width = (int) $width;
					}
					
					$real_height = (int) $height;
				}

				$thumb = imagecreatetruecolor($real_width, $real_height);
				if($image->getExt() == "gif") {
					$source = imagecreatefromgif($path);
					
					$thumb_white_color = imagecolorallocate($thumb, 255, 255, 255);
					imagefill($thumb, 0, 0, $thumb_white_color);
					imagecolortransparent($thumb, $thumb_white_color);
					
					imagealphablending($source, TRUE);
					imagealphablending($thumb, TRUE);
				} else if($image->getExt() == "png") {
					$source = imagecreatefrompng($path);
					
					$thumb_white_color = imagecolorallocate($thumb, 255, 255, 255);
					imagefill($thumb, 0, 0, $thumb_white_color);
					imagecolortransparent($thumb, $thumb_white_color);
					
					imagealphablending($source, TRUE);
					imagealphablending($thumb, TRUE);
				} else {
					$source = imagecreatefromjpeg($path);
				}

				imagecopyresampled($thumb, $source, 0, 0, 0, 0, $real_width, $real_height, $width_src, $height_src);
				
				if($image->getExt() == "png") {
					imagepng($thumb, $path_new);
				} else if($image->getExt() == "gif") {
					imagegif($thumb, $path_new);
				} else {
					imagejpeg($thumb, $path_new, 100);
				}
			}
		}

		//Parsing
		$value = new umiImageFile($path_new);

		list($tpl) = def_module::loadTemplates("tpls/thumbs/{$template}.tpl", "image");

		$arr = Array();
		$arr['name'] = $name;
		$arr['title'] = $title;
		$arr['size'] = $value->getSize();
		$arr['filename'] = $value->getFileName();
		$arr['filepath'] = $value->getFilePath();
		$arr['src'] = $value->getFilePath(true);
		$arr['ext'] = $value->getExt();

		$arr['width'] = $value->getWidth();
		$arr['height'] = $value->getHeight();

		$arr['template'] = $template;

		if($returnArrayOnly) return $arr;
		return def_module::parseTemplate($tpl, $arr);
	}

	public function numpages($total, $per_page, $template = "default", $varName = "p") {
		if(!$varName) $varName = "p";
		return umiPagenum::generateNumPage($total, $per_page, $template, $varName);
	}

	public function order_by($fieldName, $typeId, $template = "default") {
		return umiPagenum::generateOrderBy($fieldName, $typeId, $template);
	}

	public function uri_path_pic() {
		list($res) = split("/", $_REQUEST['path']);

		$allowed_res = Array('about', 'portfolio', 'sites', 'promotion', 'multimedia', 'own_projects', 'contacts');
		if(!in_array($res, $allowed_res)) {
			$res = array_pop($allowed_res);
		}
		return $res;
	}

	public function captcha($template="default") {
		return umiCaptcha::generateCaptcha($template);
	}
	
	
	public function redirectIfOpera() {
		//TODO: Redirect from admin-side to error-page, if opera in user-agent is detected, until there will be normal xml+xslt support.
	}
	
	
	public function smartSubstring($string, $max_length = 30) {
		if(!$max_length) $max_length = 30;
		
		if(strlen($string) > ($max_length - 3)) {
			return substr($string, 0, ($max_length - 3)) . "...";
		} else {
			return $string;
		}
	}
	
	
	public function referer_uri() {
		return $_SERVER['HTTP_REFERER'];
	}
};

?>