<?php
	abstract class __albums_photoalbum {
		public function albums_list () {
			$this->load_forms();
			$params = Array();


			$rows = "";

			$per_page = 25;
			$curr_page = $_REQUEST['p'];

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("photoalbum", "album")->getId();
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
						<a href="%pre_lang%/admin/photoalbum/album_blocking/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
				} else {
					$blocking = <<<END
						<a href="%pre_lang%/admin/photoalbum/album_blocking/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
				}


				if($user_id = $element->getValue("user_id")) {
					$owner_info = cmsController::getInstance()->getModule("users")->get_user_info($user_id, "<a href='%pre_lang%/admin/users/user_edit/{$user_id}/all/' title='Отредактировать пользователя'><b><![CDATA[%login%]]></b>, <![CDATA[%lname% %fname% %father_name%]]></a>");
				} else {
					$owner_info = "";
				}


				$rows .= <<<ROWS

<row>
	<col>
		<a href="%pre_lang%/admin/photoalbum/photos_list/{$element_id}/"><b><![CDATA[$name]]></b></a> %core getTypeEditLink({$object_type_id})%
		<br /><br />
		<table border="0">
			<tr>
				<td style="width: 150px;">
					<![CDATA[Владелец альбома:]]>
				</td>

				<td>
					{$owner_info}
				</td>
			</tr>

			<tr>
				<td style="width: 150px;">
					<![CDATA[Последнее обновление:]]>
				</td>

				<td>
					{$updatetime}
				</td>
			</tr>

			<tr>
				<td>
					<![CDATA[Ссылка на сайте:]]>
				</td>

				<td>
					<a href="{$path}"><![CDATA[{$path}]]></a>
				</td>
			</tr>
		</table>
	</col>

	<col style="text-align: center;">
		<a href="%pre_lang%/admin/photoalbum/photos_list/{$element_id}/">
			<img src='/images/cms/admin/%skin_path%/ico_subitems.gif' title="Содержание" alt="Содержание" border="0" />
		</a>
	</col>

	<col style="text-align: center">
		{$blocking}
	</col>

	<col style="text-align: center">
		<a href="%pre_lang%/admin/photoalbum/album_edit/{$element_id}/">
			<img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" />
		</a>
	</col>

	<col style="text-align: center">
		<a href="%pre_lang%/admin/photoalbum/album_del/{$element_id}/" commit="Вы уверены?">
			<img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" />
		</a>
	</col>
</row>

ROWS;
			}

			$params['rows'] = $rows;


			return $this->parse_form("albums_list", $params);
		}
	};
?>