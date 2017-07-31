<?php

abstract class __guides_data {
	public $per_page = 50;

	public function guides() {
		$this->sheets_set_active("guides");

		$curr_page = (int) $_REQUEST['p'];
		$per_page = $this->per_page;

		$params = Array();
		$this->load_forms();

		$guides_list = umiObjectTypesCollection::getGuidesList(true);
		$rows = "";

		$total = sizeof($guides_list);

		natsort($guides_list);
		$guides_list = array_slice($guides_list, $per_page * $curr_page, $per_page, true);

		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

		foreach($guides_list as $id => $name) {
			$rows .= <<<END
	<row>
		<col>
			<a href="%pre_lang%/admin/data/guide_items/{$id}/">{$name}</a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/data/guide_items/{$id}/"><img src="/images/cms/admin/%skin_path%/ico_subitems.%ico_ext%" title="Содержание" alt="Содержание" border="0" /></a>
		</col>
		
		<col style="text-align: center;">
			<a href="%pre_lang%/admin/data/type_edit/{$id}/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" title="Редактировать" alt="Редактировать" border="0" /></a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/data/guide_del/{$id}/" commit_unrestorable="?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" title="Удалить" alt="Удалить" border="0" /></a>
		</col>
	</row>
END;
		}

		$params['rows'] = $rows;
		return $this->parse_form("guides", $params);
	}

	public function guide_items() {
		$this->sheets_set_active("guides");

		$params = Array();
		$this->load_forms();

		$curr_page = (int) $_REQUEST['p'];
		$per_page = $this->per_page;


		$guide_id = (int) $_REQUEST['param0'];
		$guide_type = umiObjectTypesCollection::getInstance()->getType($guide_id);

		$this->navibar_back();
		$this->navibar_push("Справочники", "/admin/data/guides/");
		$this->navibar_push($guide_type->getName(), "/admin/data/guide_items/" . $guide_id);


		$guide_items = umiObjectsCollection::getInstance()->getGuidedItems($guide_id);
		$rows = "";

		$total = sizeof($guide_items);

		natsort($guide_items);
		$guide_items = array_slice($guide_items, $per_page * $curr_page, $per_page, true);

		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);


		foreach($guide_items as $id => $name) {
			$rows .= <<<END
<row>
	<col>
		<input quant="no" br="no" style="width: 97%;">
			<name><![CDATA[items[{$id}]]]></name>
			<value><![CDATA[{$name}]]></value>
		</input>
	</col>

	<col style="text-align: center;">
		<a href="%pre_lang%/admin/data/guide_item_edit/{$id}/{$guide_id}/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" border="0" /></a>
	</col>

	<col style="text-align: center;">
		<checkbox>
			<name><![CDATA[dels[]]]></name>
			<value><![CDATA[{$id}]]></value>
		</checkbox>
	</col>
</row>

END;
		}

		$rows .= <<<END
<row>
	<col>
		<input quant="no" br="no" style="width: 97%;">
			<name><![CDATA[items_new]]></name>
		</input>
	</col>

	<col></col>
	<col></col>
</row>
END;

		$params['rows'] = $rows;
		$params['save_n_save'] = '<submit title="Сохранить" />';
		$params['guide_id'] = $guide_id;
		return $this->parse_form("guide_items", $params);
	}


	public function guide_items_do() {
		$this->sheets_set_active("guides");
		$guide_id = $_REQUEST['param0'];

		$items = $_REQUEST['items'];
		$dels = $_REQUEST['dels'];
		$items_new = $_REQUEST['items_new'];

		foreach($items as $id => $name) {
			$guide_type = umiObjectsCollection::getInstance()->getObject($id);
			if($guide_type) {
				$guide_type->setName($name);
				$guide_type->commit();
			}
		}

		foreach($dels as $id) {
			umiObjectsCollection::getInstance()->delObject($id);
		}

		if($items_new) {
			umiObjectsCollection::getInstance()->addObject($items_new, $guide_id);
		}
		$this->redirect($this->pre_lang . "/admin/data/guide_items/" . $guide_id . "/");
	}


	public function guide_item_edit() {
		$this->sheets_set_active("guides");

		$params = Array();
		$this->load_forms();

		$item_id = (int) $_REQUEST['param0'];
		$guide_id = (int) $_REQUEST['param1'];

		$item_object = umiObjectsCollection::getInstance()->getObject($item_id);

		if(system_is_allowed("users", "edit_user_do"))
			$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\"/>&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" /></p>";
		else
			$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\" disabled=\"yes\" />&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" disabled=\"yes\" /></p>";



		$params['name'] = $item_object->getName();

		$params['item_id'] = $item_id;
		$params['guide_id'] = $guide_id;

		$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($item_object->getTypeId(), $item_id, true);
		$params['save_n_save'] = $submit;

		return $this->parse_form("guide_item_edit", $params);
	}


	public function guide_item_edit_do() {
		$this->sheets_set_active("guides");

		$item_id = (int) $_REQUEST['param0'];
		$guide_id = (int) $_REQUEST['param1'];

		$name = $_REQUEST['name'];

		$object = umiObjectsCollection::getInstance()->getObject($item_id);
		$object->setName($name);

		$this->saveEditedGroups($item_id, true);

		$object->commit();


		if($_REQUEST['exit_after_save'])
			$this->redirect($this->pre_lang . "/admin/data/guide_items/" . $guide_id . "/");
		else
			$this->redirect($this->pre_lang . "/admin/data/guide_item_edit/" . $item_id . "/" . $guide_id . "/");

	}

	public function guide_del() {
		$type_id = (int) $_REQUEST['param0'];

		umiObjectTypesCollection::getInstance()->delType($type_id);

		$this->redirect($this->pre_lang . "/admin/data/guides/");
	}
}

?>