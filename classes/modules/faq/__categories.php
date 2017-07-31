<?php

abstract class __faq_categories {


	public function categories_list() {
		// set tab
		$this->sheets_set_active("projects_list");
		//input:
		$this->load_forms();
		$params = array();
		$parent_id = $_REQUEST['param0'];
		$params['parent_id'] = $parent_id;

		$parent = umiHierarchy::getInstance()->getElement($parent_id);
		$this->navibar_back();
		$this->navibar_push($parent->getName(), "/admin/faq/project_edit/" . $parent->getParentId(). "/" . $parent_id);
		$this->navibar_push("%nav_cats_list%");


		$per_page = 25;
		$curr_page = $_REQUEST['p'];

		$sel = new umiSelection;
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("faq", "category")->getId();

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
			$params['lines'] .= $this->renderCategory($element_id);
		}

		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

		return $this->parse_form("categories_list", $params);
	}
	
	public function renderCategory($element_id) {
		$params = array();
		$element_id = (int) $element_id;
		$element =  umiHierarchy::getInstance()->getElement($element_id);
		if ($element instanceof umiHierarchyElement) {
			$params['site_link'] = umiHierarchy::getInstance()->getPathById($element_id);
			$params['updatetime'] = date("Y-m-d H:i", $element->getUpdateTime());
			$params['name'] = $element->getName();
			$params['element_id'] = $element_id;
			$parent_id = $element->getParentId();
			$params['parent_id'] = $parent_id;
			if($element->getIsActive()) {
				$params['blocking'] = <<<END
					<a href="%pre_lang%/admin/faq/category_blocking/{$parent_id}/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
			} else {
				$params['blocking'] = <<<END
					<a href="%pre_lang%/admin/faq/category_blocking/{$parent_id}/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
			}
		}

		$params['object_type_id'] = $element->getObject()->getTypeId();
		return $this->parse_form("category_line", $params);
	}


	public function category_del() {
		$element_id = $_REQUEST['param0'];
		$parent_id = umiHierarchy::getInstance()->getElement($element_id)->getParentId();
		umiHierarchy::getInstance()->delElement($element_id);
		$this->redirect($this->pre_lang . "/admin/faq/categories_list/".$parent_id);
	}
}

?>