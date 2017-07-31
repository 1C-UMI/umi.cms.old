<?php

	abstract class __shared_files {
		public function shared_files() {
			$this->sheets_set_active("shared_files");
			$params = array();
			$this->load_forms();

			$per_page = 10;
			$curr_page = $_REQUEST['p'];

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("filemanager", "shared_file")->getId();

			$sel->setElementTypeFilter();
			$sel->addElementType($hierarchy_type_id);

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($parent_id);

			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);
			
			$sz = sizeof($result);
			for($i = 0; $i < $sz; $i++) {
				$element_id = $result[$i];
				$params['rows'] .= $this->renderSharedFile($element_id);
			}

			$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

			return $this->parse_form("shared_files", $params);
		}
		
		public function renderSharedFile($element_id) {
			$params = array();
			$element_id = (int) $element_id;
			$element =  umiHierarchy::getInstance()->getElement($element_id);
			if ($element instanceof umiHierarchyElement) {
				$params['site_link'] = umiHierarchy::getInstance()->getPathById($element_id);
				$params['updatetime'] = date("Y-m-d H:i", $element->getUpdateTime());
				$params['name'] = $element->getName();
				$params['type_id'] = $element->getObject()->getTypeId();
				$params['element_id'] = $element_id;
				$parent_id = $element->getParentId();
				$params['parent_id'] = $parent_id;
				$params['downloads'] = $element->getValue("downloads_counter");
				$params['file_size'] = "0";
				$params['file_path'] = "<span style=\"color:red;\" >Файл не указан или не существует</span>";
				$params['file_name'] = "";
				$o_file = $element->getValue("fs_file");
				if ($o_file instanceof umiFile && !$o_file->getIsBroken()) {
					$params['file_size'] = round($o_file->getSize()/1024, 2);
					$params['file_name'] = $o_file->getFileName();
					$params['file_path'] = str_replace(ini_get("include_path"), "", $o_file->getFilePath());
				}
				if($element->getIsActive()) {
					$params['blocking'] = <<<END
						<a href="%pre_lang%/admin/filemanager/shared_file_blocking/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
				} else {
					$params['blocking'] = <<<END
						<a href="%pre_lang%/admin/filemanager/shared_file_blocking/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
				}
			}
			
			return $this->parse_form("shared_files_line", $params);
		}
		
		public function del_shared_file() {
			$element_id = $_REQUEST['param0'];
			umiHierarchy::getInstance()->delElement($element_id);
			$this->redirect($this->pre_lang . "/admin/filemanager/shared_files/");
		}
	}

?>