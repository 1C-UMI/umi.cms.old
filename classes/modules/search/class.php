<?php
	class search extends def_module implements iSearch {

		public function __construct() {
			parent::__construct();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$this->__loadLib("__admin.php");
				$this->__implement("__search");
			} else {
				$this->__loadLib("__custom.php");
				$this->__implement("__custom_search");

				$this->per_page = regedit::getInstance()->getVal("//modules/search/per_page");
			}

			$this->__loadLib("__index.php");
			$this->__implement("__index_search");

			$this->__loadLib("__search.php");
			$this->__implement("__search_search");
		}

		public function search_do($template = "default") {
			if(!$template) $template = "default";
			list($template_block, $template_line, $template_empty_result, $template_line_quant) = self::loadTemplates("tpls/search/{$template}.tpl", "search_block", "search_block_line", "search_empty_result", "search_block_line_quant");

			$search_string = (string) $_REQUEST['search_string'];

			if(!$search_string) return $this->insert_form($template);

			$block_arr = Array();

			$lines = "";
			$result = $this->runSearch($search_string);
			$p = (int) $_REQUEST['p'];
			$total = sizeof($result);
			$per_page = $this->per_page;

			$result = array_slice($result, $per_page * $p, $per_page);

			$i = $per_page * $p;

			foreach($result as $num => $element_id) {
				$line_arr = Array();

				$element = umiHierarchy::getInstance()->getElement($element_id);

				if(!$element) continue;

				$line_arr['num'] = ++$i;
				$line_arr['id'] = $element_id;
				$line_arr['name'] = $element->getName();
				$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);
				$line_arr['context'] = $this->getContext($element_id, $search_string);
				$line_arr['quant'] = ($num < count($result)-1? self::parseTemplate($template_line_quant, array()) : "");
				$lines .= self::parseTemplate($template_line, $line_arr, $element_id);

				templater::pushEditable(false, false, $element_id);
				
				umiHierarchy::getInstance()->unloadElement($element_id);
			}

			$block_arr['lines'] = $lines;
			$block_arr['total'] = $total;
			$block_arr['per_page'] = $per_page;
			$block_arr['last_search_string'] = "";

			return self::parseTemplate(($total>0 ? $template_block : $template_empty_result), $block_arr);
		}


		public function insert_form($template = "default") {
			if(!$template) $template = "default";
			list($template_block) = self::loadTemplates("tpls/search/{$template}.tpl", "search_form");

			$search_string = (string) $_REQUEST['search_string'];
			$search_string = strip_tags($search_string);

			$block_arr = Array();
			$block_arr['last_search_string'] = ($search_string) ? $search_string : "Поиск";
			return self::parseTemplate($template_block, $block_arr);
		}
		public function config() {
			return __search::config();
		}
	};
?>
