<?php

abstract class __news {
	
	public function item_blocking() {
		$parent_id = (int) $_REQUEST['param0'];
		$element_id = (int) $_REQUEST['param1'];
		$is_active = (bool) $_REQUEST['param2'];

		$element = umiHierarchy::getInstance()->getElement($element_id);
		if ($element) {
			$element->setIsActive($is_active);
			$element->commit();
		}

		$this->redirect($this->pre_lang . "/admin/news/lists/" . $parent_id . "/");
	}

	public function lists() {
		$params = Array();
		$this->load_forms();

		$parent_id = (int) $_REQUEST['param0'];

		//setting navibar...
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array("Новостные разделы", "/admin/news/lists/", "");

		$this->fill_navibar($parent_id);

		$per_page = 25;
		$curr_page = $_REQUEST['p'];

		$sel = new umiSelection;
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$sel->setElementTypeFilter();
		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("news", "rubric")->getId();
		$sel->addElementType($hierarchy_type_id);

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("news", "item")->getId();
		$sel->addElementType($hierarchy_type_id);


		$sel->setHierarchyFilter();
		$sel->addHierarchyFilter($parent_id);

		$sel->setPermissionsFilter();
		$sel->addPermissions();

		$sel->setHierarchyFilter();
		for($i = 0; ($i <= 5) && ($total <= 0); $i++) {
			$sel->addHierarchyFilter($parent_id, (($i) ? $i : false));

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);
		}


		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

		$rows = "";

		$sz = sizeof($result);
		for($i = 0; $i < $sz; $i++) {
			$element_id = $result[$i];
			$element = umiHierarchy::getInstance()->getElement($element_id);

			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getType($element->getTypeId());

			if($hierarchy_type->getExt() == "item") {
				$rows .= self::renderItemItem($element);

			} else {
				$rows .= self::renderListItem($element);
			}
		}

		$params['rows'] = $rows;
		$params['curr_rel'] = $parent_id;
		return $this->parse_form("lists", $params);
	}

	protected function renderListItem($element) {
			$element_id = $element->getId();
			$parent_id = $element->getParentId();


			$name = $element->getName();
			$readme = $element->getValue('readme');
			$readme = nl2br($readme);

			$object_type_id = $element->getObject()->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			$path = umiHierarchy::getInstance()->getPathById($element_id);
			$updatetime = date("Y-m-d H:i", $element->getUpdateTime());
			
			$blocking = "";
			if($element->getIsActive()) {
				$blocking = <<<END
					<a href="%pre_lang%/admin/news/item_blocking/{$parent_id}/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
			} else {
				$blocking = <<<END
					<a href="%pre_lang%/admin/news/item_blocking/{$parent_id}/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
			}


			$row = <<<ROW
<row>
	<col>
		<a href="%pre_lang%/admin/news/lists/{$element_id}/"><b><![CDATA[$name]]></b></a> %core getTypeEditLink({$object_type_id})%
		<br /><br />
		<table border="0">
			<tr>
				<td style="width: 150px;">
					Последнее обновление:
				</td>

				<td>
					{$updatetime}
				</td>
			</tr>

			<tr>
				<td>
					Ссылка на сайте:
				</td>

				<td>
					<a href="{$path}"><![CDATA[{$path}]]></a>
				</td>
			</tr>

			<tr>
				<td>
					Описание:
				</td>

				<td>
					{$readme}
				</td>
			</tr>

		</table>
	</col>


	<col style="text-align: center;">
		{$blocking}
	</col>

	<col style="text-align: center;">
		<a href="%pre_lang%/admin/news/edit_list/{$parent_id}/{$element_id}/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" /></a>
	</col>


	<col style="text-align: center;">
		<a href="%pre_lang%/admin/news/del_list/{$parent_id}/{$element_id}/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" /></a>
	</col>
</row>
ROW;
		return $row;
	}


	protected function renderItemItem($element) {
			$element_id = $element->getId();
			$parent_id = $element->getParentId();

			$name = $element->getName();

			$path = umiHierarchy::getInstance()->getPathById($element_id);
			$updatetime = date("Y-m-d H:i", $element->getUpdateTime());


			$object_type_id = $element->getObject()->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			$publish_time = $element->getValue("publish_time");
			$publish_time = (is_object($publish_time)) ? $publish_time->getFormattedDate("Y-m-d H:i") : "-";

			$blocking = "";
			if($element->getIsActive()) {
				$blocking = <<<END
					<a href="{$this->pre_lang}/admin/news/item_blocking/{$parent_id}/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
			} else {
				$blocking = <<<END
					<a href="{$this->pre_lang}/admin/news/item_blocking/{$parent_id}/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
			}

			$row = <<<ROW
<row>
	<col>
		<a href="%pre_lang%/admin/news/edit_item/{$parent_id}/{$element_id}/"><b><![CDATA[$name]]></b></a> %core getTypeEditLink({$object_type_id})%
		<br /><br />
		<table border="0">
			<tr>
				<td style="width: 150px;">
					Последнее обновление:
				</td>

				<td>
					{$updatetime}
				</td>
			</tr>

			<tr>
				<td>
					Ссылка на сайте:
				</td>

				<td>
					<a href="{$path}"><![CDATA[{$path}]]></a>
				</td>
			</tr>

			<tr>
				<td style="width: 150px;">
					Дата публикации:
				</td>

				<td>
					{$publish_time}
				</td>
			</tr>
		</table>
	</col>

	<col style="text-align: center;">
		{$blocking}
	</col>

	<col style="text-align: center;">
		<a href="%pre_lang%/admin/news/edit_item/{$parent_id}/{$element_id}/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" /></a>
	</col>


	<col style="text-align: center;">
		<a href="%pre_lang%/admin/news/del_item/{$parent_id}/{$element_id}/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" /></a>
	</col>
</row>
ROW;
		return $row;
	}


	public function getEditLink($element_id, $element_type) {
		$element = umiHierarchy::getInstance()->getElement($element_id);
		$parent_id = $element->getParentId();

		switch($element_type) {
			case "rubric": {
				$link_add = $this->pre_lang . "/admin/news/add_item/{$element_id}/";
				$link_edit = $this->pre_lang . "/admin/news/edit_list/{$parent_id}/{$element_id}/";

				return Array($link_add, $link_edit);
				break;
			}

			case "item": {
				$link_edit = $this->pre_lang . "/admin/news/edit_item/{$parent_id}/{$element_id}/";

				return Array(false, $link_edit);
				break;
			}

			default: {
				return false;
			}
		}
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
				$this->navibar_push($curr_element->getName(), "/admin/news/lists/" . $curr_element_id);
			}
		}
	}
	public function config() {
		$this->sheets_reset();
	
		$params = Array();
		$this->load_forms();
		$regedit = regedit::getInstance();

		$params['per_page']  = $regedit->getVal("//modules/news/per_page");

		return $this->parse_form("config", $params);
	}


	public function config_do() {
		$this->sheets_reset();

		$regedit = regedit::getInstance();
		$regedit->setVar("//modules/news/per_page", (int) $_REQUEST['per_page']);

		$this->redirect("admin", "news", "config");
	}


	public function last_lists() {

		$params = Array();
		$this->load_forms();

		$per_page = 25;
		$curr_page = $_REQUEST['p'];

		$sel = new umiSelection;
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$sel->setElementTypeFilter();
		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("news", "item")->getId();
		$sel->addElementType($hierarchy_type_id);

		$sel->setPermissionsFilter();
		$sel->addPermissions();

		$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("news", "item");
		$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
		$publish_time_field_id = $object_type->getFieldId('publish_time');

		$sel->setOrderFilter();
		$sel->setOrderByProperty($publish_time_field_id, false);


		$result = umiSelectionsParser::runSelection($sel);
		$total = umiSelectionsParser::runSelectionCounts($sel);


		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

		$rows = "";

		$sz = sizeof($result);
		for($i = 0; $i < $sz; $i++) {
			$element_id = $result[$i];
			$element = umiHierarchy::getInstance()->getElement($element_id);

			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getType($element->getTypeId());

			$rows .= self::renderItemItem($element);
		}

		$params['rows'] = $rows;
		$params['curr_rel'] = $parent_id;
		return $this->parse_form("last_lists", $params);
	}

};

?>