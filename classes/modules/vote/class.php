<?php

class vote extends def_module {
	public function __construct() {
                parent::__construct();

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$this->__loadLib("__admin.php");
			$this->__implement("__vote");

			$this->__loadLib("__poll_add.php");
			$this->__implement("__poll_add_vote");

			$this->__loadLib("__poll_edit.php");
			$this->__implement("__poll_edit_vote");
		} else {
			$this->__loadLib("__rate.php");
			$this->__implement("__rate_vote");

			$this->__loadLib("__custom.php");
			$this->__implement("__custom_vote");
		}
	}


	public function poll($path = "", $template = "default") {
		$element_id = $this->analyzeRequiredPath($path);

		$element = umiHierarchy::getInstance()->getElement($element_id);

		if(!$element) return "";

		if($this->checkIsVoted($element->getObject()->getId())) {
			return $this->results($element_id, $template);
		} else {
			return $this->insertvote($element_id, $template);
		}
	}


	public function insertvote($path = "", $template = "default") {
		if(!$template) $template = "default";
		list($template_block, $template_line, $template_submit) = def_module::loadTemplates("tpls/vote/{$template}.tpl", "vote_block", "vote_block_line", "vote_block_submit");

		$block_arr = Array();

		$element_id = $this->analyzeRequiredPath($path);

		$element = umiHierarchy::getInstance()->getElement($element_id);

		if(!$element) return false;


		$block_arr['text'] = $element->getValue("question");

		$item_type_id = umiObjectTypesCollection::getInstance()->getBaseType("vote", "poll_item");
		$item_type = umiObjectTypesCollection::getInstance()->getType($item_type_id);
		$rel_field_id = $item_type->getFieldId("poll_rel");

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("vote", "poll_item")->getId();
		$sel = new umiSelection();

		$sel->setObjectTypeFilter();
		$sel->addObjectType($item_type_id);

		$sel->setPropertyFilter();
		$sel->addPropertyFilterEqual($rel_field_id, $element->getObject()->getId());

		$result = umiSelectionsParser::runSelection($sel);

		$lines = "";
		foreach($result as $item_id) {
			$item = umiObjectsCollection::getInstance()->getObject($item_id);

			$line_arr = Array();
			$line_arr['item_id'] = $item->getId();
			$line_arr['item_name'] = $item->getName();

			$lines .= self::parseTemplate($template_line, $line_arr);
		}


		$is_closed = (bool) $element->getValue("is_closed");

		$block_arr['submit'] = ($is_closed) ? "" : $template_submit;
		$block_arr['lines'] = $lines;
		$block_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);
		return self::parseTemplate($template_block, $block_arr, $element_id);
	}


	public function results($path, $template) {
		if(!$template) $template = "default";
		list($template_block, $template_line) = def_module::loadTemplates("tpls/vote/{$template}.tpl", "result_block", "result_block_line");

		$element_id = $this->analyzeRequiredPath($path);

		$element = umiHierarchy::getInstance()->getElement($element_id);
		if(!$element) return false;

		$block_arr = Array();

		$block_arr['text'] = $element->getValue("question");
		$block_arr['vote_header'] = $element->getValue("h1");
		$block_arr['alt_name'] = $element->getAltName();

		$item_type_id = umiObjectTypesCollection::getInstance()->getBaseType("vote", "poll_item");
		$item_type = umiObjectTypesCollection::getInstance()->getType($item_type_id);
		$rel_field_id = $item_type->getFieldId("poll_rel");

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("vote", "poll_item")->getId();
		$sel = new umiSelection();

		$sel->setObjectTypeFilter();
		$sel->addObjectType($item_type_id);

		$sel->setPropertyFilter();
		$sel->addPropertyFilterEqual($rel_field_id, $element->getObject()->getId());

		$count_field_id = $item_type->getFieldId("count");

		$sel->setOrderByProperty($count_field_id);

		$result = umiSelectionsParser::runSelection($sel);

		$items = Array();
		$total = 0;
		foreach($result as $item_id) {
			$item = umiObjectsCollection::getInstance()->getObject($item_id);
			$total += (int) $item->getPropByName("count")->getValue();
			$items[] = $item;
		}

		$lines = "";
		foreach($items as $item) {
			$line_arr = Array();

			$line_arr['item_name'] = $item->getName();
			$line_arr['item_result'] = $c = $item->getPropByName("count")->getValue();

			$curr_procs = round( (100*$c) / $total );
			$line_arr['item_result_proc'] = $curr_procs;
			$line_arr['item_result_proc_reverce'] = 100 - $curr_procs;

			$lines .= self::parseTemplate($template_line, $line_arr);
		}
		

		$block_arr['lines'] = $lines;
		$block_arr['total_posts'] = $total;
		$block_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);
		return self::parseTemplate($template_block, $block_arr, $element_id);
	}


	public function post() {
		$item_id = (int) $_REQUEST['param0'];
		$item = umiObjectsCollection::getInstance()->getObject($item_id);

		$poll_rel = $item->getPropByName("poll_rel")->getValue();

		$object_id = $poll_rel;
		$object = umiObjectsCollection::getInstance()->getObject($object_id);
		if($this->checkIsVoted($object_id)) {
			$res = "Вы уже проголосовали";
		} else {

			if($object->getValue("is_closed")) {
				$res = "Ошибка. Голосование не активно, либо закрыто.";
			} else {
				$count = $item->getPropByName("count")->getValue();
				$item->getPropByName("count")->setValue(++$count);
				$item->getPropByName("poll_rel")->setValue($poll_rel);
				$item->commit();

				$res = "Ваше мнение учтено";
			}

			if(!is_array($_SESSION['vote_polled'])) {
				$_SESSION['vote_polled'] = Array();
			}
		}
		$_SESSION['vote_polled'][] = $object_id;

		$res = templater::getInstance()->putLangs($res);
		$this->flush("alert('{$res}');", "text/javascript");
	}


	private function checkIsVoted($object_id) {
		return in_array($object_id, $_SESSION['vote_polled']);
	}



	public function insertlast($template = "default") {
		if(!$template) $template = "default";

		$type_id = umiObjectTypesCollection::getInstance()->getBaseType("vote", "poll");
		$type = umiObjectTypesCollection::getInstance()->getType($type_id);
		$time_field_id = $type->getFieldId("publish_time");

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("vote", "poll")->getId();

		$sel = new umiSelection();
		$sel->setHierarchyFilter();
		$sel->addElementType($hierarchy_type_id);

		$sel->setLimitFilter();
		$sel->addLimit(1);

		$sel->setOrderFilter();
		$sel->setOrderByProperty($time_field_id, false);

		$sel->forceHierarchyTable();

		$result = umiSelectionsParser::runSelection($sel);

		list($element_id) = $result;

		if($element_id) {
			return $this->poll($element_id, $template);
		}
	}
/*
	public function genQuick($arg1, $arg2 = "", $arg3 = "") {
		$res = NULL;

		if(!$arg2['args'][0]) {
			$sql = "SELECT id, name FROM cms_votes_polls WHERE is_active='1' AND is_closed='0' ORDER BY id DESC LIMIT 1";
			list($id, $name) = mysql_fetch_row(mysql_query($sql));

			$url = "/admin/vote/edit_poll/" . $id . "/";
			$res = Array(
					"url"   => $url,
					"title" => "Редактировать опрос ($name)");
			return $res;
		}
	}
*/
};
?>
