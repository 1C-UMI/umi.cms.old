<?php
	class umiFile implements iUmiFile {
		protected	$filepath,
				$size, $ext, $name, $dirname, $modify_time,
				$is_broken = false;
		public static $mask = 0777;

		public function __construct($filepath) {
			if(!is_file($filepath)) {
				$this->is_broken = true;
				return false;
			}

			$this->filepath = $filepath;
			$this->loadInfo();
		}

		public function delete() {
			if(is_writable($this->filepath)) {
				return unlink($this->filepath);
			} else {
				return false;
			}
		}


		public static function upload($group_name, $var_name, $target_folder, $id = false) {
			global $_FILES;
			$files_array = &$_FILES;

			$target_folder_input = $target_folder;
			if(substr($target_folder_input, strlen($target_folder_input) - 1, 1) != "/") $target_folder_input .= "/";

			$target_folder = realpath($target_folder);

			if(!is_dir($target_folder)) {
				return false;
			}

			if(!is_writable($target_folder)) {
				return false;
			}

			if(!is_array($files_array)) {
				return false;
			}

			if(array_key_exists($group_name, $files_array)) {
				$file_info = $files_array[$group_name];

				$size = ($id === false) ? $file_info['size'][$var_name] : $file_info['size'][$id][$var_name];

				if($size == 0) {
					return false;
				} else {
					$temp_path = ($id === false) ? $file_info['tmp_name'][$var_name] : $file_info['tmp_name'][$id][$var_name];
					$name = ($id === false) ? $file_info['name'][$var_name] : $file_info['name'][$id][$var_name];	//TODO: make cyrilic to translit conversion

					if(substr($name, -4, 4) == ".php") return false;
					if(substr($name, -5, 5) == ".php5") return false;
					if(substr($name, -6, 6) == ".phtml") return false;

//					if($tmp = iconv("UTF-8", "CP1251", $name)) {
//						$name = $tmp;
//					}

					list(,, $extension) = array_values(pathinfo($name));
					$name = substr($name, 0, strlen($name) - strlen($extension));
					$name = translit::convert($name);
					$name .= "." . $extension;

					$new_path = $target_folder . "/" . $name;
					
					if($name == ".htaccess") {
						return false;
					}
					
					$extension = strtolower($extension);
					
					if($extension == "php" || $extension == "phtml" || $extension == "exe" || $extension == "php5") {
						return false;
					}
					

					if(is_uploaded_file($temp_path)) {
						if(move_uploaded_file($temp_path, $new_path)) {
							chmod($new_path, self::$mask);
							return new umiFile($target_folder_input . $name);
						} else {
							return false;
						}
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
		}


		public function getFileName() {
			return $this->name;
		}

		public function getDirName() {
			return $this->dirname;
		}

		public function getModifyTime() {
			return $this->modify_time;
		}

		public function getExt() {
			return $this->ext;
		}

		public function getSize() {
			return $this->size;
		}

		public function getFilePath($web_mode = false) {
			if($web_mode) {
				return (substr($this->filepath, 0, 2) == "./") ? ("/" . substr($this->filepath, 2, strlen($this->filepath) - 2)) : $this->filepath;
			} else {
				return $this->filepath;
			}
		}

		private function loadInfo() {
			if(!is_readable($this->filepath)) return false;

			$this->modify_time = filemtime($this->filepath);
			$this->size = filesize($this->filepath);
			$this->dirname = pathinfo($this->filepath, PATHINFO_DIRNAME);
			$this->name = pathinfo($this->filepath, PATHINFO_BASENAME);
			$this->ext = pathinfo($this->filepath, PATHINFO_EXTENSION);
			$this->ext = strtolower($this->ext);

			if($this->ext == "php" || $this->ext == "php5" || $this->ext == "phtml") {
				$this->is_broken = true;
			}
			
			if($this->name == ".htaccess") {
				$this->is_broken = true;
			}
		}

		public function __toString() {
			return "umiFile::{$this->filepath}";
		}

		public function getIsBroken() {
			return (bool) $this->is_broken;
		}
	}
?>