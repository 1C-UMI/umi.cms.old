<?php
	abstract class __photos_photoalbum {

		public function photos_list() {
			$params = Array();
			$this->load_forms();
			$album_id = (int) $_REQUEST['param0'];


			$rows = "";

			$per_page = 25;
			$curr_page = $_REQUEST['p'];

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("photoalbum", "photo")->getId();
			$sel->addElementType($hierarchy_type_id);

			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($album_id);

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);

			$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

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

				if($photo = $element->getValue("photo")) {
					$photo_filepath = $photo->getFilePath();
					$photo_size = $photo->getWidth() . "x" . $photo->getHeight() . " px";
					$systemModule = &system_buildin_load("system");
					$photo_thumb = $systemModule->makeThumbnail($photo_filepath, 90, 'auto', false, true);
				} else {
					$photo_thumb = "";
				}

				$tags = implode(", ", $element->getValue("tags"));


				$rows .= <<<ROWS

<row>
	<col>
		<a href="%pre_lang%/admin/photoalbum/photo_edit/{$element_id}/"><b><![CDATA[$name]]></b></a> %core getTypeEditLink({$object_type_id})%
		<br /><br />
		<table border="0">
			<tr>
				<td style="width: 150px;">
					<![CDATA[Владелец фотографии:]]>
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

			<tr>
				<td>
					<![CDATA[Полный размер:]]>
				</td>

				<td>
					<![CDATA[{$photo_size}]]>
				</td>
			</tr>

			<tr>
				<td>
					<![CDATA[Теги:]]>
				</td>

				<td>
					<![CDATA[{$tags}]]>
				</td>
			</tr>

		</table>
	</col>

	<col>
					<a href="%pre_lang%/admin/photoalbum/photo_edit/{$element_id}/">
						<img src="{$photo_thumb['src']}" width="{$photo_thumb['width']}" height="{$photo_thumb['height']}" style="border: #000 1px solid;" />
					</a>

	</col>


	<col style="text-align: center">
		{$blocking}
	</col>

	<col style="text-align: center">
		<a href="%pre_lang%/admin/photoalbum/photo_edit/{$element_id}/">
			<img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" />
		</a>
	</col>

	<col style="text-align: center">
		<a href="%pre_lang%/admin/photoalbum/photo_del/{$element_id}/" commit="Вы уверены?">
			<img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" />
		</a>
	</col>
</row>

ROWS;
			}

			$params['rows'] = $rows;


			$params['album_id'] = $album_id;
			return $this->parse_form("photos_list", $params);
		}

	};
?>