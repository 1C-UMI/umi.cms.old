<?php

abstract class __search {
	public function config() {
		$res = '';

		$this->load_forms();

		$params = Array();

		$params['per_page'] = (int) regedit::getInstance()->getVal("//modules/search/per_page");

		$params['hightlight_color'] = regedit::getInstance()->getVal("//modules/search/hightlight_color");
		$params['weight_name'] = (int) regedit::getInstance()->getVal("//modules/search/weight_name");
		$params['weight_title'] = (int) regedit::getInstance()->getVal("//modules/search/weight_title");
		$params['weight_h1'] = (int) regedit::getInstance()->getVal("//modules/search/weight_h1");
		$params['weight_content'] = (int) regedit::getInstance()->getVal("//modules/search/weight_content");
		$params['autoindex'] = (int) regedit::getInstance()->getVal("//modules/search/autoindex");
		$params['search_deep'] = (int) regedit::getInstance()->getVal("//modules/search/search_deep");


		$res = $this->parse_form("config", $params);

		return $res;
	}

	public function config_do() {
		$res = "";

		regedit::getInstance()->setVar("//modules/search/hightlight_color", $_REQUEST['hightlight_color']);
		regedit::getInstance()->setVar("//modules/search/weight_name", (int) $_REQUEST['weight_name']);
		regedit::getInstance()->setVar("//modules/search/weight_title", (int) $_REQUEST['weight_title']);
		regedit::getInstance()->setVar("//modules/search/weight_h1", (int) $_REQUEST['weight_h1']);
		regedit::getInstance()->setVar("//modules/search/weight_content", (int) $_REQUEST['weight_content']);
		regedit::getInstance()->setVar("//modules/search/autoindex", (int) $_REQUEST['autoindex']);
		regedit::getInstance()->setVar("//modules/search/per_page", (int) $_REQUEST['per_page']);
		regedit::getInstance()->setVar("//modules/search/search_deep", (int) $_REQUEST['search_deep']);

		$this->redirect("admin", "search", "config");

		return true;
	}


	public function index_control() {
		$params = Array();
		$this->load_forms();

		$params['index_pages'] = $this->getIndexPages();
		$params['index_words'] = $this->getIndexWords();
		$params['index_words_uniq'] = $this->getIndexWordsUniq();

		$index_last = $this->getIndexLast();
		$params['index_last'] = ($index_last) ? date("Y-m-d H:i:s", $index_last) : "-";

		return $this->parse_form("control", $params);
	}

	public function truncate() {
		$this->truncate_index();

		$this->redirect($this->pre_lang . "/admin/search/");
	}

	public function reindex() {
		$this->index_all();

		$this->redirect($this->pre_lang . "/admin/search/");
	}
};

?>