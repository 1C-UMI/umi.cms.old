<?php

class __trash_data {
	public function trash() {
		$params = Array();
		$this->load_forms();
		$this->sheets_reset();

		$rows = "";

		$deleted = umiHierarchy::getInstance()->getDeletedList();
		$sz = sizeof($deleted);
		for($i = 0; $i < $sz; $i++) {
			$element_id = $deleted[$i];
			$element = umiHierarchy::getInstance()->getElement($element_id, false, true);

			if(!$element) continue;

			$name = $element->getName();
			$path = umiHierarchy::getInstance()->getPathById($element_id);
			$update_time = $element->getUpdateTime();
			$update_date = date("Y-m-d | H:i:s", $update_time);

			$hierarchy_type_id = $element->getTypeId();
			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getType($hierarchy_type_id);
			$hierarchy_type_title = $hierarchy_type->getTitle();

			$rows .= <<<ROW
	<row>
		<col>
			<b><![CDATA[{$name}]]></b> - <i>(<![CDATA[{$hierarchy_type_title}]]>)</i><br />
			<![CDATA[{$path}]]>
		</col>

		<col>
			<![CDATA[{$update_date}]]>
		</col>


		<col style="text-align: center;">
			<a href="%pre_lang%/admin/data/trash_restore/{$element_id}/">
				<img src="/images/cms/admin/%skin_path%/trash_restore.gif" alt="Восстановить из корзины" title="Восстановить из корзины" border="0" />
			</a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/data/trash_del/{$element_id}/" commit_unrestorable="Вы уверены?">
				<img src="/images/cms/admin/%skin_path%/ico_del.gif" alt="Удалить" title="Удалить" border="0" />
			</a>
		</col>
	</row>
ROW;
		}


		if(!$i) {
			$rows .= <<<ROW
	<row>
		<col style="text-align: center;" colspan="4">
			Корзина пуста.
		</col>
	</row>
ROW;
		}

		$params['rows'] = $rows;
		return $this->parse_form("trash", $params);
	}


	public function trash_restore() {
		$element_id = (int) $_REQUEST['param0'];

		umiHierarchy::getInstance()->restoreElement($element_id);

		$this->redirect($this->pre_lang . "/admin/data/trash/");
	}


	public function trash_del() {
		$element_id = (int) $_REQUEST['param0'];

		umiHierarchy::getInstance()->removeDeletedElement($element_id);

		$this->redirect($this->pre_lang . "/admin/data/trash/");
	}


	public function trash_empty() {
		umiHierarchy::getInstance()->removeDeletedAll();

		$this->redirect($this->pre_lang . "/admin/data/trash/");
	}
}

?>