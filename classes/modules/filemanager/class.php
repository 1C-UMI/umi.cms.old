<?php
	class filemanager extends def_module {
		public function __construct() {
			parent::__construct();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$this->__loadLib("__admin.php");
				$this->__implement("__filemanager");

				$this->__loadLib("__shared_files.php");
				$this->__implement("__shared_files");

				$this->__loadLib("__add_shared_file.php");
				$this->__implement("__add_shared_file");

				$this->__loadLib("__edit_shared_file.php");
				$this->__implement("__edit_shared_file");

			} else {
				$this->__loadLib("__custom.php");
				$this->__implement("__custom_filemanager");
			}
			$this->per_page = 25;

			$this->sheets_reset();
			$this->sheets_add("Файловая система", "directory_list");
			$this->sheets_add("Доступные для скачивания файлы", "shared_files");

		}

		public function list_files($element_id = false, $template = "default") {
			if(!$template) $template = "default";
			list($template_block, $template_line) = def_module::loadTemplates("tpls/filemanager/{$template}.tpl", "list_files", "list_files_row");

			$block_arr = Array();

			$element_id = $this->analyzeRequiredPath($element_id);

			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("filemanager", "shared_file")->getId();

			$per_page = $this->per_page;
			$curr_page = (int) $_REQUEST['p'];

			$sel = new umiSelection;

			$sel->setElementTypeFilter();
			$sel->addElementType($hierarchy_type_id);

			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($element_id);

			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);

			$lines = "";
			foreach($result as $next_element_id) {
				$line_arr = Array();

				$element = umiHierarchy::getInstance()->getElement($next_element_id);

				$line_arr['id'] = $element->getId();
				$line_arr['name'] = $element->getName();
				$line_arr['desc'] = $element->getValue("content");
				$line_arr['link'] = umiHierarchy::getInstance()->getPathById($next_element_id);
				templater::pushEditable("filemanager", "shared_file", $next_element_id);

				$lines .= self::parseTemplate($template_line, $line_arr, $next_element_id);
			}
			$block_arr['lines'] = $lines;
			$block_arr['per_page'] = $per_page;
			$block_arr['total'] = $total;

			return self::parseTemplate($template_block, $block_arr);
		}

		public function shared_file($template = "default", $element_path) {
			if(!$template) $template = "default";
			list($s_download_file, $s_broken_file) = def_module::loadTemplates("tpls/filemanager/{$template}.tpl", "shared_file", "broken_file");

			$element_id = $this->analyzeRequiredPath($element_path);

			$element = umiHierarchy::getInstance()->getElement($element_id);

			$block_arr = Array();
			$template_block = $s_broken_file;
			if ($element) {
				$block_arr['id'] = $element_id;
				$block_arr['descr'] = $element->getValue("descr");
				$block_arr['alt_name'] = $element->getAltName();
				$block_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);
				// file
				$block_arr['download_link'] = "";
				$block_arr['file_name'] = "";
				$block_arr['file_size'] = 0;

				$o_file = $element->getValue("fs_file");
				if ($o_file instanceof umiFile) {
					if (!$o_file->getIsBroken()) {
						$template_block = $s_download_file;
						$block_arr['download_link'] = $this->pre_lang."/filemanager/download/".$element_id;
						$block_arr['file_name'] = $o_file->getFileName();
						$block_arr['file_size'] = round($o_file->getSize()/1024, 2);
					}
				}

			} else {
				return cmsController::getInstance()->getModule("users")->auth();
			}

			templater::pushEditable("filemanager", "shared_file", $element_id);

			return self::parseTemplate($template_block, $block_arr);
		}

		public function download() {
			$element_id = $_REQUEST['param0'];
			$element = umiHierarchy::getInstance()->getElement($element_id);
			if ($element instanceof umiHierarchyElement) {
				$o_file = $element->getValue("fs_file");
				if ($o_file instanceof umiFile) {
					if (!$o_file->getIsBroken()) {

						$s_file_path = realpath($o_file->getFilePath());
						header('HTTP/1.1 200 OK');
						header("Cache-Control: public, must-revalidate");
						header("Pragma: no-cache");
						header('Date: ' . date("D M j G:i:s T Y"));
						header('Last-Modified: ' . date("D M j G:i:s T Y"));
						header("Content-type: application/force-download");
						header("Content-Length: " . $o_file->getSize());
						header('Accept-Ranges: bytes');
						header('Content-Transfer-Encoding: Binary');
						header("Content-Disposition: attachment; filename=".$o_file->getFileName()."");
						readfile($s_file_path);

						// counter
						$i_downloads_counter = (int) $element->getValue("downloads_counter");
						$element->setValue("downloads_counter", ++$i_downloads_counter);
						$element->commit();

						exit();
					} else {
						// broken file
					}
				}
			}
			
			return cmsController::getInstance()->getModule("users")->auth();
		}

		public function getEditLink($element_id, $element_type) {
			$element = umiHierarchy::getInstance()->getElement($element_id);
			$parent_id = $element->getParentId();

			switch($element_type) {
				case "shared_file": {
					$link_edit = $this->pre_lang . "/admin/filemanager/edit_shared_file/{$element_id}/";

					return Array(false, $link_edit);
					break;
				}

				default: {
					return false;
				}
			}
	}

	};
?>