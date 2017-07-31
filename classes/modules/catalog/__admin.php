<?php

abstract class __catalog {

	public function config() {
		$this->sheets_reset();

		$res = '';

		$this->load_forms();

		$params = Array();

		$params['per_page'] = (int) regedit::getInstance()->getVal("//modules/catalog/per_page");
		$res = $this->parse_form("config", $params);
		return $res;
	}
	public function config_do() {
		$res = "";
		regedit::getInstance()->setVar("//modules/catalog/per_page", (int) $_REQUEST['per_page']);
		$var = (int) regedit::getInstance()->getVal("//modules/catalog/per_page");
		$this->redirect("admin", "catalog", "config");

		return true;
	}
	public function fill_navibar($element_id) {
		$elements = umiHierarchy::getInstance()->getAllParents($element_id, true);

		if(sizeof($elements)) {
			$this->navibar_back();
		} else {
			return;
		}

		foreach($elements as $curr_element_id) {
			if($curr_element = umiHierarchy::getInstance()->getElement($curr_element_id))  {
				if(!$curr_element->getName()) continue;
				$this->navibar_push($curr_element->getName(), "/admin/catalog/tree/" . $curr_element_id);
			}
		}
	}

};

?>