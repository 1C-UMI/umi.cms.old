<?php

abstract class __catalog_tree {

	public function __catalog_tree_onInit() {
		$this->sheets_add("Иерархия объектов", "tree");
	}

	public function tree() {
		$params = Array();
		$this->load_forms();
		$section_id = (int) $_REQUEST['param0'];

		$this->fill_navibar($section_id);

		$per_page = 25;
		$curr_page = $_REQUEST['p'];



		$sel = new umiSelection;
		$sel->setLimitFilter();
		$sel->addLimit($per_page, $curr_page);

		$sel->setElementTypeFilter();

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("catalog", "category")->getId();
		$sel->addElementType($hierarchy_type_id);

		$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("catalog", "object")->getId();
		$sel->addElementType($hierarchy_type_id);

		$sel->setObjectTypeFilter();

		$sel->setPermissionsFilter();
		$sel->addPermissions();

		$sel->setHierarchyFilter();
		for($i = 0; ($i <= 5) && ($total <= 0); $i++) {
			$sel->addHierarchyFilter($section_id, (($i) ? $i : false));

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);
		}


		$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

		$sections_rows = "";

		$sz = sizeof($result);
		for($i = 0; $i < $sz; $i++) {
			$element_id = $result[$i];

			$element = umiHierarchy::getInstance()->getElement($element_id);
			$name = $element->getObject()->getName();

			$element_type_id = $element->getTypeId();
			$element_type = umiHierarchyTypesCollection::getInstance()->getType($element_type_id);
			$object_type_id = $element->getObject()->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			$edit_link = "";

			if($element_type->getName() == "catalog" && $element_type->getExt() == "category") {
				$link = $this->pre_lang . "/admin/catalog/tree/" . $element_id . "/";
				$edit_link = "%pre_lang%/admin/catalog/tree_section_edit/{$section_id}/{$element_id}/";

				$subitems_link = <<<LINK
			<a href="{$link}/">
				<img src='/images/cms/admin/%skin_path%/ico_subitems.gif' title="Содержание" alt="Содержание" border="0" />
			</a>
LINK;
			}

			if($element_type->getName() == "catalog" && $element_type->getExt() == "object") {
				$edit_link = "%pre_lang%/admin/catalog/tree_object_edit/{$section_id}/{$element_id}/";
				$link = $edit_link;
				$subitems_link = "";
			}


			if($element->getIsActive()) {
				$blocking = <<<END
<a href="%pre_lang%/admin/catalog/tree_blocking/{$section_id}/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
			} else {
				$blocking = <<<END
<a href="%pre_lang%/admin/catalog/tree_blocking/{$section_id}/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
			}

			$site_link = umiHierarchy::getInstance()->getPathById($element_id);


			$updatetime = date("Y-m-d H:i", $element->getUpdateTime());

			$sections_rows .= <<<END

	<row>
		<col>
			<a href="{$link}"><![CDATA[{$name}]]></a> %core getTypeEditLink({$object_type_id})%

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
						<a href="{$site_link}"><![CDATA[{$site_link}]]></a>
					</td>
				</tr>
			</table>
		</col>

		<col style="text-align: center;">
			{$subitems_link}
		</col>
		<col style="text-align: center;">
			{$blocking}
		</col>

		<col style="text-align: center;">
			<a href="{$edit_link}"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" border="0" alt="Редактировать" title="Редактировать" /></a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/catalog/tree_del/{$section_id}/{$element_id}/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" border="0" alt="Удалить" title="Удалить" /></a>
		</col>
	</row>


END;

		}

		if($sections_rows) {

			$sections_rows = <<<END

<tablegroup>
	<hrow>
		<hcol>Раздел каталога</hcol>
		<hcol style="width: 100px;">Содержание</hcol>
		<hcol style="width: 100px;">Активность</hcol>
		<hcol style="width: 100px;">Изменить</hcol>
		<hcol style="width: 100px;">Удалить</hcol>
	</hrow>
	{$sections_rows}
</tablegroup>

<br /><br />

END;

		} else {
			$sections_rows = "";
		}



		$params['sections'] = $sections_rows;
		$params['section_id'] = $section_id;
		return $this->parse_form("tree", $params);
	}


	public function tree_del() {
		$parent_id = (int) $_REQUEST['param0'];
		$element_id = (int) $_REQUEST['param1'];

		umiHierarchy::getInstance()->delElement($element_id);

		$this->redirect($this->pre_lang . "/admin/catalog/tree/" . $parent_id . "/");
	}


	public function tree_blocking() {
		$section_id = (int) $_REQUEST['param0'];
		$element_id = (int) $_REQUEST['param1'];
		$is_active = (bool) $_REQUEST['param2'];

		$element = umiHierarchy::getInstance()->getElement($element_id);
		$element->setIsActive($is_active);
		$element->commit();

		$this->redirect($this->pre_lang . "/admin/catalog/tree/" . $section_id . "/");
	}

}
?>
