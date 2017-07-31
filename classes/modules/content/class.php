<?php

class content extends def_module implements iContent {

	public function __construct() {
		parent::__construct();

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			cmsController::getInstance()->getModule('users');
			$this->__loadLib("__admin.php");
			$this->__implement("__content");

		} else {
			$this->__loadLib("__custom.php");
			$this->__implement("__custom_content");

			$this->__loadLib("__tickets.php");
			$this->__implement("__tickets_content");

		}
		$this->__loadLib("__json.php");
		$this->__implement("__json_content");
	}

	public function content($element_id = false) {
		if(!$element_id) $element_id = cmsController::getInstance()->getCurrentElementId();

		$element = umiHierarchy::getInstance()->getElement($element_id);

		if(!$element) return $this->gen404();

		$content = $element->getValue("content");
		$h1 = $element->getValue("h1");

		if(!$h1) $h1 = $element->getName();
		$this->setHeader($h1);


		templater::pushEditable("content", "", $element_id);
		return $content;
	}

	public function title() {
		return cmsController::getInstance()->currentTitle;
	}


	public function rec_tree($domain = "", $md = "site_tree", $max_levels = 0, $show_all = 1) {
		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("content")->getId();

//		return umiHierarchy::getInstance()->getChilds(0, true, true, 99, $hierarchy_type_id);
		return umiHierarchy::getInstance()->getChilds(0, true, true, 99);
	}


	//перегрузим вручную функцию config
	public function config() {
		if(class_exists("__content"))
			return __content::config();	//вызов псевдостатического метода.
	}

	private function getMenuTemplates($templates, $curr_depth) {
		$suffix = "_level" . $curr_depth;

		$block = $templates["menu_block" . $suffix];
		$line = $templates["menu_line" . $suffix];
		$line_a = (array_key_exists("menu_line" . $suffix . "_a", $templates)) ? $templates["menu_line" . $suffix . "_a"] : $line;

		if(!$block) {
			switch($curr_depth) {
				case 1:
					$suffix = "_fl";
					break;
				case 2:
					$suffix = "_sl";
					break;
			}
			$block = $templates["menu_block" . $suffix];
			$line = $templates["menu_line" . $suffix];
			$line_a = (array_key_exists("menu_line" . $suffix . "_a", $templates)) ? $templates["menu_line" . $suffix . "_a"] : $line;
		}

		$separator = $templates['separator'];

		return Array($block, $line, $line_a, $separator);
	}

	private function build_menu($page_id, $templates, $curr_depth = 0, $parent_alt_name = "/") {
		list($template_block, $template_line, $template_line_a) = $this->getMenuTemplates($templates, ($curr_depth + 1));


		$childs = umiHierarchy::getInstance()->getChilds($page_id);
		$result = array_keys($childs);

		$lines = "";
		
		
		$arr_lines = array();
		$c = 0;

		foreach($result as $element_id) {
			$element = umiHierarchy::getInstance()->getElement($element_id);


			if(!$element) {
				continue;
			}

			if(!$element->getIsActive() || !$element->getIsVisible()) {
				continue;
			}

			$text = $element->getName();

			$link = $parent_alt_name . $element->getAltName() . "/";

			$is_active = (in_array($element_id, umiHierarchy::getInstance()->getAllParents(cmsController::getInstance()->getCurrentElementId(), true)) !== false);
			$line = ($is_active) ? $template_line_a : $template_line;


			if(strstr($line, "%sub_menu%")) {
				if($element->getValue("show_submenu") && ($is_active || $element->getValue("is_expanded"))) {
					$sub_menu = $this->build_menu($element_id, $templates, ($curr_depth + 1), $link);
				} else {
					$sub_menu = "";
				}
			} else {
				$sub_menu = "";
			}


			$item_arr = Array();
			$item_arr['id'] = $element_id;
			$item_arr['text'] = $text;
			$item_arr['link'] = $link;
			$item_arr['num'] = ($c+1);
			$item_arr['sub_menu'] = $sub_menu;
			$item_arr['separator'] = $separator;
			$arr_lines[] = self::parseTemplate($line, $item_arr, $element_id);
			
			$c++;
			
			umiHierarchy::getInstance()->unloadElement($element_id);
		}
		
		if($c == 0) {
			return "";
		}

		$lines = implode($templates['separator'], $arr_lines);
		$lines .= $templates['separator_last'];

		$block_arr = Array();
		$block_arr['lines'] = $lines;
		$block_arr['id'] = $page_id;
		return self::parseTemplate($template_block, $block_arr, $page_id);
	}



	public function menu($menu_tpl = "default", $max_depth = 2, $pid = false) {

		if($pid) {
			if(!is_numeric($pid)) {
				$pid = umiHierarchy::getInstance()->getIdByPath($pid);
			}
			$parent_alt = umiHierarchy::getInstance()->getPathById($pid);
		} else {
			$pid = 0;
			$parent_alt = cmsController::getInstance()->pre_lang . "/";
		}

		$this->parents = $this->get_parents(cmsController::getInstance()->getCurrentElementId());

		if(!is_file(ini_get("include_path") . "/tpls/content/menu/" . $menu_tpl . ".tpl"))
			return "%core_error_notemplate%";
		$this->__loadLib($menu_tpl . ".tpl", "tpls/content/menu/", "FORMS");

		$templates = $this->FORMS;

		return $this->build_menu($pid, $templates, 0, $parent_alt);
	}





	public function sitemap($template = "template", $max_depth = false) {
		if(!$max_depth) $max_depth = $_REQUEST['param0'];
		if(!$max_depth) $max_depth = 3;

		if(cmsController::getInstance()->getCurrentMethod() == "sitemap") {
			$this->setHeader("%content_sitemap%");
		}

		$site_tree = umiHierarchy::getInstance()->getChilds(0, false, false, $max_depth);
		return $this->gen_sitemap("default", $site_tree, ($max_depth + 1));
	}

	private function gen_sitemap($template = "default", $site_tree, $max_depth) {
		$res = "";

		list($template_block, $template_item) = def_module::loadTemplates("tpls/content/sitemap/{$template}.tpl", "block", "item");
		list($template_block, $template_item) = def_module::loadTemplates("tpls/content/sitemap/{$template}.tpl", "block", "item");

		$block_arr = Array();
		$items = "";
		if(is_array($site_tree)) {
			foreach($site_tree as $element_id => $childs) {
				if($element = umiHierarchy::getInstance()->getElement($element_id)) {
					$link = umiHierarchy::getInstance()->getPathById($element_id);

					$item_arr = Array();
					$item_arr['id'] = $element_id;
					$item_arr['name'] = $element->getObject()->getName();
					$item_arr['link'] = $link;

					if(($max_depth > 0) && $element->getValue("show_submenu")) {

						$item_arr['sub_items'] = (sizeof($childs) && is_array($childs)) ? $this->gen_sitemap("default", $childs, ($max_depth - 1)) : "";
					} else {
						$item_arr['sub_items'] = "";
					}

					$items .= self::parseTemplate($template_item, $item_arr);
					
					umiHierarchy::getInstance()->unloadElement($element_id);
				} else {
					continue;
				}
			}
		}

		$block_arr['items'] = $items;
		return self::parseTemplate($template_block, $block_arr);
	}


	public function get_navibar_template($tpl_name = "default") {
		if(!$tpl_name)
			$tpl_name = "default";

		if(!is_file(ini_get("include_path") . "/tpls/content/navibar/" . $tpl_name . ".tpl"))
			return "<fatal>%core_error_notemplate%</fatal>";

		$this->__loadLib($tpl_name . ".tpl", "tpls/content/navibar/", "FORMS");
		$templates = Array(
			"navibar" => $this->FORMS['navibar'],
			"navibar_empty" => $this->FORMS['navibar_empty'],
			"element" => $this->FORMS['element'],
			"element_active" => $this->FORMS['element_active'],
			"quantificator" => $this->FORMS['quantificator']

		);
		return $templates;
	}

	public function get_page_url($element_id, $ignore_lang = false) {
		$ignore_lang = (bool) $ignore_lang;
		return umiHierarchy::getInstance()->getPathById($element_id, $ignore_lang);
	}

	public function get_page_id($url) {
		return umiHierarchy::getInstance()->getIdByPath($url);
	}

	public function redirect($mode = "", $module = "", $method = "", $ext = "") {
		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			return parent::redirect($mode, $module, $method, $ext);
		} else {
			if(is_numeric($mode) ){
				$id=$mode;
				$redirect_string= 'Location: ' . $this->get_page_url($id);
			}elseif(substr($mode, 0, strlen("http://")) == "http://")
				$redirect_string = 'Location: ' . $mode;
			else
				$redirect_string = "Location: http://" . $_SERVER['HTTP_HOST'] . $mode;


			umiHierarchy::getInstance()->__destruct();

			header("Status: 302 Found");
			header($redirect_string);
			exit();
			return $redirect_string;
		}
	}

	public function get4index() {
		$res = Array();

		$this->__loadLib("__4index.php");
		if(class_exists("__4index_content")) {
			$this->__implement("__4index_content");
			$res = __4index_content::get4index();
		}

		return $res;
	}

	public function insert($input) {
		$input = trim($input);

		if(!$input)
			return "%content_error_insert_null%";

		if(!is_numeric($input))
			$input = umiHierarchy::getInstance()->getIdByPath($input);
		else
			$input = (int) $input;

		if($input == cmsController::getInstance()->getCurrentElementId())
			return "%content_error_insert_recursy%";

		if(!$input) {
			return "%content_error_insert_nopage%";
		}

		if($element = umiHierarchy::getInstance()->getElement($input)) {
			templater::pushEditable("content", "", $input);
			return $element->getValue("content");
		} else {
			return "";
		}
	}

	public function get_parents($page_id) {
		return umiHierarchy::getInstance()->getAllParents($page_id, true);
	}


	public function get_cut_template($tpl_name = "default") {

		if(!is_file(ini_get("include_path") . "/tpls/content/cut/" . $tpl_name . ".tpl"))
			return "<fatal>%core_error_notemplate%</fatal>";

		$this->__loadLib($tpl_name . ".tpl", "tpls/content/cut/", "FORMS");
		$templates = Array(
			"block" => $this->FORMS['cut_block'],
			"item" => $this->FORMS['cut_item'],
			"item_a" => $this->FORMS['cut_item_a'],
			"quant" => $this->FORMS['cut_quant'],
			"toprev" => $this->FORMS['cut_toprev'],
			"tonext" => $this->FORMS['cut_tonext'],
			"tobegin" => $this->FORMS['cut_tobegin'],
			"toend" => $this->FORMS['cut_toend'],
			"toprev_ua" => $this->FORMS['cut_toprev_ua'],
			"tonext_ua" => $this->FORMS['cut_tonext_ua'],
			"tobegin_ua" => $this->FORMS['cut_tobegin_ua'],
			"toend_ua" => $this->FORMS['cut_toend_ua']
		);

		return $templates;
	}


	public function genMacroses() {
		$res = Array();

		$res[] = Array("%pid%", "Id текущей страницы");
		$res[] = Array("%domain%", "Текущий домен");
		$res[] = Array("%h1%", "Заголовок");
		$res[] = Array("%content insert(param1)%", "Вставка страницы");
		$res[] = Array("%content redirect(param1)%", "Перенаправление на URL");
		$res[] = Array("%content menu(param1, param2, param3)%", "Вставка меню");
		$res[] = Array("%content sitemap()%", "Карта сайта");
		$res[] = Array("%core insertPopup(текст ссылки, /images/cms/content/photo_big.jpg)%", "Ссылка-попап");
		$res[] = Array("%core insertThumb(/images/cms/content/photo_small.jpg, /images/cms/content/photo_big.jpg)%", "Картинка-попап");


		return $res;
	}

	public function gen404($template = "default") {
		if(!$template) $template = "default";

		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");

		$this->setHeader('%content_error_404_header%');

		list($template_block) = def_module::loadTemplates("tpls/content/not_found/{$template}.tpl", "block");

		if($template_block) {
			return $template_block;
		} else {
			return '%content_usesitemap%';
		}
	}


	public function getNameByParam($method, $param) {
		$param = (int) $param;
		list($name) = mysql_fetch_row(mysql_query("SELECT name FROM cms_content WHERE id='$param'"));
		return $name;
	}



	public function getEditLink($element_id, $element_type) {
		$element = umiHierarchy::getInstance()->getElement($element_id);
		$parent_id = $element->getParentId();

		$link_add = $this->pre_lang . "/admin/content/add_page/?parent={$element_id}";
		$link_edit = $this->pre_lang . "/admin/content/edit_page/{$element_id}/";

		return Array($link_add, $link_edit);
	}


};


?>