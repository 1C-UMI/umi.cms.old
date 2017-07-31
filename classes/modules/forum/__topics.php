<?php
	abstract class __topics_forum {
		public function topics_list() {
			$this->sheets_set_active("confs_list");

			$parent_id = (int) $_REQUEST['param0'];
			$this->load_forms();

			$per_page = 25;
			$curr_page = $_REQUEST['p'];

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("forum", "topic")->getId();
			$sel->addElementType($hierarchy_type_id);

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($parent_id);

			$sel->setPermissionsFilter();
			$sel->addPermissions();


			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("forum", "topic");
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$publish_time_field_id = $object_type->getFieldId('publish_time');

			$sel->setOrderFilter();
			$sel->setOrderByProperty($publish_time_field_id, false);

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);

			$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

			$rows = "";
			for($i = 0; $i < sizeof($result); $i++) {
				$element_id = $result[$i];

				$element = umiHierarchy::getInstance()->getElement($element_id);
				$name = $element->getName();




				$object_type_id = $element->getObject()->getTypeId();
				$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

				$path = umiHierarchy::getInstance()->getPathById($element_id);
				$updatetime = date("Y-m-d H:i", $element->getUpdateTime());


				if($publish_time = $element->getValue("publish_time")) {
					$publish_time = $publish_time->getFormattedDate("Y-m-d H:i");
				} else {
					$publish_time = "<i><![CDATA[Неизвестно]]></i>";
				}

				if($element->getIsActive()) {
					$blocking = <<<END
						<a href="%pre_lang%/admin/forum/topic_blocking/{$parent_id}/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
				} else {
					$blocking = <<<END
						<a href="%pre_lang%/admin/forum/topic_blocking/{$parent_id}/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
				}

				$rows .= <<<END
<row>
	<col>
		<a href="%pre_lang%/admin/forum/messages_list/{$element_id}/"><b><![CDATA[$name]]></b></a> %core getTypeEditLink({$object_type_id})%
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
					<a href="{$path}"><![CDATA[{$path}]]></a><br /><br />
				</td>
			</tr>

			<tr>
				<td>
					Дата создания:
				</td>

				<td>
					{$publish_time}
				</td>
			</tr>

		</table>
	</col>

	<col style="text-align: center;">
		<a href="%pre_lang%/admin/forum/messages_list/{$element_id}/">
			<img src='/images/cms/admin/%skin_path%/ico_subitems.gif' title="Содержание" alt="Содержание" border="0" />
		</a>
	</col>

	<col style="text-align: center;">
		{$blocking}
	</col>
	<col style="text-align: center;">
		<a href="%pre_lang%/admin/forum/topic_edit/{$parent_id}/{$element_id}/">
			<img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" />
		</a>
	</col>

	<col style="text-align: center;">
		<a href="%pre_lang%/admin/forum/topic_del/{$parent_id}/{$element_id}/" commit="Вы уверены?">
			<img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" />
		</a>
	</col>
</row>

END;
			}



			$params['rows'] = $rows;
			$params['parent_id'] = $parent_id;
			return $this->parse_form("topics", $params);
		}
	};
?>