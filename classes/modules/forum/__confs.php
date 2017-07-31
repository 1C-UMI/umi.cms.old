<?php
	abstract class __confs_forum {
		public function confs_list() {
			$this->sheets_set_active("confs_list");

			$this->load_forms();
			$params = Array();

			$rows = "";

			$per_page = 25;
			$curr_page = $_REQUEST['p'];

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "conf")->getId();
			$sel->addElementType($hierarchy_type_id);

			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);

			for($i = 0; $i < sizeof($result); $i++) {
				$element_id = $result[$i];

				$element = umiHierarchy::getInstance()->getElement($element_id);

				$name = $element->getName();

				$object_type_id = $element->getObject()->getTypeId();
				$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

				$path = umiHierarchy::getInstance()->getPathById($element_id);
				$updatetime = date("Y-m-d H:i", $element->getUpdateTime());

				if($element->getIsActive()) {
					$blocking = <<<END
						<a href="%pre_lang%/admin/forum/conf_blocking/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
				} else {
					$blocking = <<<END
						<a href="%pre_lang%/admin/forum/conf_blocking/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
				}

				$rows .= <<<ROWS

<row>
	<col>
		<a href="%pre_lang%/admin/forum/topics_list/{$element_id}/"><b><![CDATA[$name]]></b></a> %core getTypeEditLink({$object_type_id})%
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
		</table>
	</col>

	<col style="text-align: center;">
		<a href="%pre_lang%/admin/forum/topics_list/{$element_id}/">
			<img src='/images/cms/admin/%skin_path%/ico_subitems.gif' title="Содержание" alt="Содержание" border="0" />
		</a>
	</col>

	<col style="text-align: center">
		{$blocking}
	</col>

	<col style="text-align: center">
		<a href="%pre_lang%/admin/forum/conf_edit/{$element_id}/">
			<img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" />
		</a>
	</col>

	<col style="text-align: center">
		<a href="%pre_lang%/admin/forum/conf_del/{$element_id}/" commit="Вы уверены?">
			<img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" />
		</a>
	</col>
</row>

ROWS;
			}

			$params['rows'] = $rows;

//			$params['unpublished_messages'] = $this->returnUnpublishedMessages();
//			$params['last_messages'] = $this->returnLastMessages();

			$res = $this->parse_form("confs", $params);
			return $res;
		}
	};
?>