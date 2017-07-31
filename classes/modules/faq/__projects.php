<?php

abstract class __faq_projects {


	public function projects_list() {
		// set tab
		$this->sheets_set_active("projects_list");
		//input:
		$this->load_forms();
		$params = array();
		$params['parent_id'] = 0;

		$per_page = 25;
		$curr_page = $_REQUEST['p'];

		$sel = new umiSelection;
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "project")->getId();

		$sel->setElementTypeFilter();
		$sel->addElementType($hierarchy_type_id);

		$sel->setPermissionsFilter();
		$sel->addPermissions();

		$result = umiSelectionsParser::runSelection($sel);
		$total = umiSelectionsParser::runSelectionCounts($sel);
		
		$sz = sizeof($result);
		for($i = 0; $i < $sz; $i++) {
			$element_id = $result[$i];
			$params['lines'] .= $this->renderProject($element_id);
		}

		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

		return $this->parse_form("projects_list", $params);
	}
	
	public function renderProject($element_id) {
		$params = array();
		$element_id = (int) $element_id;
		$element =  umiHierarchy::getInstance()->getElement($element_id);
		if ($element instanceof umiHierarchyElement) {
			$params['site_link'] = umiHierarchy::getInstance()->getPathById($element_id);
			$params['updatetime'] = date("Y-m-d H:i", $element->getUpdateTime());
			$params['name'] = $element->getName();
			$params['element_id'] = $element_id;
			$params['parent_id'] = $element->getParentId();
			if($element->getIsActive()) {
				$params['blocking'] = <<<END
					<a href="%pre_lang%/admin/faq/project_blocking/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
			} else {
				$params['blocking'] = <<<END
					<a href="%pre_lang%/admin/faq/project_blocking/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
			}
		}
		$params['object_type_id'] = $element->getObject()->getTypeId();
		return $this->parse_form("project_line", $params);
	}


	public function project_del() {
		$element_id = $_REQUEST['param0'];
		umiHierarchy::getInstance()->delElement($element_id);
		$this->redirect($this->pre_lang . "/admin/faq/projects_list/");
	}
}

?>