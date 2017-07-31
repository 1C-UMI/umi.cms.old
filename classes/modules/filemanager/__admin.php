<?php
	abstract class __filemanager {
		public function config() {
			$params = Array();
			$this->sheets_reset();
			$this->load_forms();

			$regedit = regedit::getInstance();

			//return $this->parse_form("config", $params);
		}


		public function config_do() {
			$regedit = regedit::getInstance();

//			$per_page = (int) $_REQUEST['per_page'];
//			$regedit->setVar("//modules/photoalbum/per_page", $per_page);

//			$this->redirect($this->pre_lang . "/admin/photoalbum/config/");
		}

		public function directory_list() {
			// path
			$s_root_path = ini_get("include_path");
			
			$s_path = $s_root_path."/files/"; // def path
			if (isset($_REQUEST['param0']) && strlen($_REQUEST['param0'])) {
				$s_path = base64_decode($_REQUEST['param0']);
				$_SESSION['umi_fs_path'] = $s_path;
			} elseif (isset($_SESSION['umi_fs_path'])) {
				$s_path = $_SESSION['umi_fs_path'];
			}

			$s_path = str_replace("\\", "/", $s_path);
			$s_path = str_replace("//", "/", $s_path);

			if(strpos($s_path, $s_root_path) === false || strpos($s_path, "..") !== false || strpos($s_path, "./") !== false) {
				$s_path = $s_root_path;
			}

			while (substr($s_path, -1)=="/") $s_path=substr($s_path, 0, (strlen($s_path)-1));
			while (substr($s_root_path, -1)=="/") $s_root_path=substr($s_root_path, 0, (strlen($s_root_path)-1));

			if (!defined("CURRENT_VERSION_LINE") || CURRENT_VERSION_LINE != "demo") {
				if (isset($_FILES['fs_upl_files']) && count($_FILES['fs_upl_files'])) {
					$arr_files = $_FILES['fs_upl_files'];
					foreach ($arr_files['name'] as $i_id => $s_name) {
						umiFile::upload("fs_upl_files", $i_id, $s_path);
					}
				}
			}

			$params = array();
			$this->load_forms();

			$o_dir = new umiDirectory($s_path);

			$arr_dirs = $o_dir->getDirectories(".+(?<!\.svn)\s?$");
			$arr_files =$o_dir->getFiles(".+(?<!\.htaccess|\.svn)\s?$");

			ksort($arr_dirs);
			ksort($arr_files);
			
			foreach ($arr_dirs as $s_name => $s_dir_path) {
				$params['rows'] .= $this->__renderDirectory($s_name, $s_dir_path);
			}

			foreach ($arr_files as $s_name => $s_file_path) {
				$params['rows'] .= $this->__renderFile($s_name, $s_file_path);
			}
			
			$params['uplink'] = "";

			$arr_path  = explode("/", $s_path);
			array_pop($arr_path);
			$s_parent_dir = implode("/", $arr_path);
			
			$params['uplink'] = "#";
			if (is_readable($s_parent_dir)) {
				$s_enc_parent_dir = base64_encode($s_parent_dir);
				$params['uplink'] = $this->pre_lang."/admin/filemanager/directory_list/{$s_enc_parent_dir}";
			}

			$current_path = str_replace($s_root_path, "", $s_path);
			$params['current_path'] = "/".trim($current_path, "/\\");

			$params['root_path'] = $s_root_path;

			return $this->parse_form("directory_list", $params);
		}

		public function __renderDirectory($s_name, $s_path) {
			$s_result = "";
			$params = array();
			$this->load_forms();

			if (is_dir($s_path)) {
				$params['name'] = $s_name;
				$params['link'] = "{$this->pre_lang}/admin/filemanager/directory_list/".base64_encode($s_path);
				$params['remove_link'] = "%pre_lang%/admin/filemanager/remove/".base64_encode($s_path);

				if (is_readable($s_path)) {
					$encoded_path = base64_encode($s_path);
					$params['remove_link'] = <<<END
						<a href="{$this->pre_lang}/admin/filemanager/remove/{$encoded_path}/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" border="0" alt="Удалить" title="Удалить" /></a>
END;
					$encoded_name = str_replace(".", "[dot]", $s_name);
					$params['rename_link'] = <<<END
						<a href="#" onclick="return fs_rename_dlg('{$this->pre_lang}/admin/filemanager/rename/{$encoded_name}', '{$s_name}');"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" border="0" alt="Переименовать" title="Переименовать" /></a>
END;
				}
				$s_result = $this->parse_form("directory_list_dir", $params);
			}

			return $s_result;
		}

		public function __renderFile($s_name, $s_path) {
			$s_result = "";
			$params = array();
			$this->load_forms();
			
			
			if (is_file($s_path) && is_readable($s_path)) {
				$o_file = new umiFile($s_path);
				$params['name'] = $s_name;
				$params['remove_link'] = '';
				if (!$o_file->getIsBroken()) {
					$encoded_path = base64_encode($s_path);
					$params['share_link'] = <<<END
						<a href="{$this->pre_lang}/admin/filemanager/add_shared_file/{$encoded_path}"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Сделать скачиваемым" title="Сделать скачиваемым" border="0" /></a>
END;
					$params['remove_link'] = <<<END
						<a href="{$this->pre_lang}/admin/filemanager/remove/{$encoded_path}/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" border="0" alt="Удалить" title="Удалить" /></a>
END;
					$encoded_name = str_replace(".", "[dot]", $s_name);
					$params['rename_link'] = <<<END
						<a href="#" onclick="return fs_rename_dlg('{$this->pre_lang}/admin/filemanager/rename/{$encoded_name}', '{$s_name}');"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" border="0" alt="Переименовать" title="Переименовать" /></a>
END;
				}
				$s_result = $this->parse_form("directory_list_file", $params);
			}

			return $s_result;
		}

		public function rename() {

			if (defined("CURRENT_VERSION_LINE") && CURRENT_VERSION_LINE == "demo") {
				$this->redirect($this->pre_lang . "/admin/filemanager/directory_list/");
				return false;
			}


			$s_old_name = $_REQUEST['param0'];
			$s_new_name = $_REQUEST['param1'];

			$s_old_name = str_replace("[dot]", ".", $s_old_name);
			$s_new_name = str_replace("[dot]", ".", $s_new_name);
			
			$s_root_path = ini_get("include_path");
			$s_path = $s_root_path."/files/"; // def path

			if (isset($_SESSION['umi_fs_path'])) {
				$s_path = $_SESSION['umi_fs_path'];
			}

			if (strlen($s_path) && strlen($s_old_name) && strlen($s_new_name)) {
				if (file_exists($s_path."/".$s_old_name) && !file_exists($s_path."/".$s_new_name)) {
					// try rename
					if (@rename($s_path."/".$s_old_name, $s_path."/".$s_new_name) === false) {
						// exception
					}
				}
			}
			
			$this->redirect($this->pre_lang . "/admin/filemanager/directory_list/");
		}

		public function make_directory() {

			if (defined("CURRENT_VERSION_LINE") && CURRENT_VERSION_LINE == "demo") {
				$this->redirect($this->pre_lang . "/admin/filemanager/directory_list/");
				return false;
			}

			$s_dir_name = $_REQUEST['param0'];

			$s_path = "";
			if (isset($_SESSION['umi_fs_path'])) {
				$s_path = $_SESSION['umi_fs_path'];
			}

			$s_new_dir_path = $s_path."/".$s_dir_name;
			if (strlen($s_path) && !is_dir($s_new_dir_path)) {
				// try md
				if (false === mkdir($s_new_dir_path)) {
					// exception
				}
			}

			$this->redirect($this->pre_lang . "/admin/filemanager/directory_list/");
		}

		public function remove() {

			if (defined("CURRENT_VERSION_LINE") && CURRENT_VERSION_LINE == "demo") {
				$this->redirect($this->pre_lang . "/admin/filemanager/directory_list/");
				return false;
			}

			$s_obj_path = base64_decode($_REQUEST['param0']);
			if (is_dir($s_obj_path)) {
				@rmdir($s_obj_path);
			} elseif (is_file($s_obj_path)) {
				@unlink($s_obj_path);
			}
			$this->redirect($this->pre_lang . "/admin/filemanager/directory_list/");
		}
	};

?>