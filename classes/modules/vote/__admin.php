<?php

	abstract class __vote {

		public function item_blocking() {
			$element_id = (int) $_REQUEST['param0'];
			$is_active = (bool) $_REQUEST['param1'];

			$element = umiHierarchy::getInstance()->getElement($element_id);
			if ($element) {
				$element->setIsActive($is_active);
				$element->commit();
			}

			$this->redirect($this->pre_lang . "/admin/vote/");
		}

		public function polls() {
			$params = Array();
			$this->load_forms();

			cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");
			cmsController::getInstance()->nav_arr[] = Array("Список опросов", "/admin/vote/");


			$per_page = 25;
			$curr_page = $_REQUEST['p'];


			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setElementTypeFilter();
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("vote", "poll")->getId();
			$sel->addElementType($hierarchy_type_id);


			$sel->setPermissionsFilter();
			$sel->addPermissions();

			$sel->setHierarchyFilter();
			for($i = 0; ($i <= 5) && ($total <= 0); $i++) {
				$sel->addHierarchyFilter($parent_id, (($i) ? $i : false));

				$result = umiSelectionsParser::runSelection($sel);
				$total = umiSelectionsParser::runSelectionCounts($sel);
			}

			$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

			$sz = sizeof($result);
			$rows = "";
			for($i = 0; $i < $sz; $i++) {
				$element_id = $result[$i];
				$element = umiHierarchy::getInstance()->getElement($element_id);

				$element_name = $element->getName();

				$question = $element->getValue("question");
				$path = umiHierarchy::getInstance()->getPathById($element_id);


				$object_type_id = $element->getObject()->getTypeId();
				$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

				$updatetime = date("Y-m-d H:i", $element->getUpdateTime());

				if($element->getIsActive()) {
				$blocking = <<<END
					<a href="%pre_lang%/admin/vote/item_blocking/{$element_id}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
				} else {
				$blocking = <<<END
					<a href="%pre_lang%/admin/vote/item_blocking/{$element_id}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
				}

				$rows .= <<<END

	<row>
		<col>
		<a href="%pre_lang%/admin/vote/edit_poll/{$element_id}/"><b><![CDATA[$element_name]]></b></a> %core getTypeEditLink({$object_type_id})%
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
					Вопрос:
				</td>

				<td>
					<![CDATA[{$question}]]>
				</td>
			</tr>

		</table>
		</col>

		<col style="text-align: center;">
			{$blocking}
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/vote/edit_poll/{$element_id}/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" /></a>
		</col>

		<col align="center">
			<a href="/admin/vote/del_poll/{$element_id}/" commit="Вы уверены?">
				<img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" border="0" alt="%delete%" title="%delete%" />
			</a>
		</col>
	</row>

END;
			}


			$params['rows'] = $rows;
			return $this->parse_form("polls", $params);
		}


		public function del_poll() {
			$element_id = (int) $_REQUEST['param0'];

			umiHierarchy::getInstance()->delElement($element_id);

			$this->redirect($this->pre_lang . "/admin/vote/");
		}


		public function getEditLink($element_id, $element_type) {
			$element = umiHierarchy::getInstance()->getElement($element_id);
			$parent_id = $element->getParentId();

			switch($element_type) {
				case "poll": {
					$link_edit = $this->pre_lang . "/admin/vote/edit_poll/{$element_id}/";

					return Array(false, $link_edit);
					break;
				}

				default: {
					return false;
				}
			}
		}
	};
?>