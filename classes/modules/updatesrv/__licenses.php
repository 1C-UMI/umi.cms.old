<?php
	abstract class __licenses_updatesrv {
		public function license_add() {
			$params = Array();
			$this->load_forms();
			$this->sheets_set_active("licenses");

			if(system_is_allowed("updatesrv", "license_add_do"))
				$submit = "<p align=\"right\"><submit title=\"Добавить и выйти\" onclick=\"return save_with_exit();\"/>&#160;&#160;<submit title=\"Добавить\" onclick=\"return save_without_exit();\" /></p>";
			else
				$submit = "<p align=\"right\"><submit title=\"Добавить и выйти\" onclick=\"return save_with_exit();\" disabled=\"yes\" />&#160;&#160;<submit title=\"Добавить\" onclick=\"return save_without_exit();\" disabled=\"yes\" /></p>";


			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("updatesrv", "license")->getId();
			list($type_id) = array_keys(umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type_id));

			if(cmsController::getInstance()->getModule('data')) {
				$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($type_id);
			}

			$params['method'] = "license_add_do";
			$params['save_n_save'] = $submit;
			return $this->parse_form("license_add", $params);
		}

		public function license_add_do() {
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("updatesrv", "license")->getId();
			list($type_id) = array_keys(umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type_id));

			$license_id = umiObjectsCollection::getInstance()->addObject("umilicense", $type_id);
			if(cmsController::getInstance()->getModule('data')) {
				cmsController::getInstance()->getModule('data')->saveEditedGroups($license_id, true);
			}

			$license = umiObjectsCollection::getInstance()->getObject($license_id);
			$license->setName($license->getPropByName("domain_name")->getValue());
			$license->setValue("keycode", $this->generatePrimaryKeycode());

			if($_REQUEST['exit_after_save'])
				$this->redirect($this->pre_lang . "/admin/updatesrv/licenses/");
			else
				$this->redirect($this->pre_lang . "/admin/updatesrv/license_edit/{$license_id}/");
		}


		public function license_edit() {
			$params = Array();
			$this->load_forms();
			$this->sheets_set_active("licenses");

			$license_id = $_REQUEST['param0'];

			$license_object = umiObjectsCollection::getInstance()->getObject($license_id);

			if(system_is_allowed("updatesrv", "license_add_do"))
				$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\"/>&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" /></p>";
			else
				$submit = "<p align=\"right\"><submit title=\"Сохранить и выйти\" onclick=\"return save_with_exit();\" disabled=\"yes\" />&#160;&#160;<submit title=\"Сохранить\" onclick=\"return save_without_exit();\" disabled=\"yes\" /></p>";


			if(cmsController::getInstance()->getModule('data')) {
				$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($license_object->getTypeId(), $license_id, true);
			}


			$params['method'] = "license_edit_do";
			$params['license_id'] = $license_id;
			$params['save_n_save'] = $submit;
			return $this->parse_form("license_add", $params);
		}

		public function license_edit_do() {
			$license_id = (int) $_REQUEST['param0'];

			$license_object = umiObjectsCollection::getInstance()->getObject($license_id);

			if(cmsController::getInstance()->getModule('data')) {
				cmsController::getInstance()->getModule('data')->saveEditedGroups($license_id, true);
			}		
			$license_object->commit();

			$license_object->update();

			$license_type_id = $license_object->getPropByName("license_type")->getValue();
			$license_type = umiObjectsCollection::getInstance()->getObject($license_type_id);
			$license_codename = $license_type->getPropByName("codename")->getValue();

			$domain_name = $license_object->getPropByName("domain_name")->getValue();
			$ip = $license_object->getPropByName("ip")->getValue();
			$ip = ($ip) ? $ip : false;

			$license_info = updatesrv::generateLicense($license_codename, $domain_name, $ip);
			
			if($domain_name && $ip && !$license_object->getValue("domain_keycode")) {
				if($license_codename == "old_free" || $license_codename == "old_lite" || $license_codename == "old_lite_plus") {
					$domain_keycode = $license_info['keycode'];
					$license_object->setValue("domain_keycode", $domain_keycode);
				}
			}


//			$license_object->getPropByName("ip")->setValue($license_info['ip']);
//			$license_object->getPropByName("keycode")->setValue($license_info['keycode']);

			$license_object->commit();

			if($_REQUEST['exit_after_save'])
				$this->redirect($this->pre_lang . "/admin/updatesrv/licenses/");
			else
				$this->redirect($this->pre_lang . "/admin/updatesrv/license_edit/{$license_id}/");
		}

		public function license_del() {
			$license_id = (int) $_REQUEST['param0'];

			umiObjectsCollection::getInstance()->delObject($license_id);

			$this->redirect($this->pre_lang . "/admin/updatesrv/licenses/");
		}


		public function licenses() {
			$params = Array();
			$this->load_forms();

			$per_page = 10;
			$curr_page = $_REQUEST['p'];

			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("updatesrv", "license_type")->getId();
			list($type_id) = array_keys(umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type_id));

			$filter_domain = $_REQUEST['filter_domain'];
			$filter_ip = $_REQUEST['filter_ip'];
			$filter_license_type = (int) $_REQUEST['filter_license_type'];
			$filter_keycode = $_REQUEST['filter_keycode'];


			$license_types = umiObjectsCollection::getInstance()->getGuidedItems($type_id);
			$licenses = putSelectBox_assoc($license_types, $filter_license_type, true);

			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName("updatesrv", "license")->getId();
			list($type_id) = array_keys(umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type_id));

			$sel = new umiSelection;
			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setObjectTypeFilter();
			$sel->addObjectType($type_id);

			$sel->setOrderFilter();
			$type = umiObjectTypesCollection::getInstance()->getType($type_id);
			$field_id = $type->getFieldId("gen_time");
			$sel->setOrderByProperty($field_id, false);

			if($filter_license_type) {
				$sel->setPropertyFilter();
				$field_id = $type->getFieldId("license_type");
				$sel->addPropertyFilterEqual($field_id, $filter_license_type);
			}

			if($filter_ip) {
				$sel->setPropertyFilter();
				$field_id = $type->getFieldId("ip");
				$sel->addPropertyFilterLike($field_id, "%" . $filter_ip . "%");
			}

			if($filter_domain) {
				$sel->setPropertyFilter();
				$field_id = $type->getFieldId("domain_name");
				$sel->addPropertyFilterLike($field_id, "%" . $filter_domain . "%", true);
			}

			if($filter_keycode) {
				$sel->setPropertyFilter();
				$field_id = $type->getFieldId("keycode");
				$sel->addPropertyFilterLike($field_id, "%" . $filter_keycode . "%", true);
			}


			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);

			$filter_params = Array();
			if($filter_domain) $filter_params['filter_domain'] = $_REQUEST['filter_domain'];
			if($filter_ip) $filter_params['filter_ip'] = $_REQUEST['filter_ip'];
			if($filter_license_type) $filter_params['filter_license_type'] = $_REQUEST['filter_license_type'];
			if($filter_keycode) $filter_params['filter_keycode'] = $_REQUEST['filter_keycode'];

			$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page, "p", $filter_params);


			


			$rows = "";
			$sz = sizeof($result);
			for($i = 0; $i < $sz; $i++) {
				$license_id = $result[$i];

				$license = umiObjectsCollection::getInstance()->getObject($license_id);
				$domain = $license->getPropByName("domain_name")->getValue();
				$ip = $license->getPropByName("ip")->getValue();

				$keycode = $license->getPropByName("keycode")->getValue();

				$owner_lname = $license->getPropByName("owner_lname")->getValue();
				$owner_fname = $license->getPropByName("owner_fname")->getValue();
				$owner_mname = $license->getPropByName("owner_mname")->getValue();

				$owner_email = $license->getPropByName("owner_email")->getValue();

				$autoupdate_is_disabled = $license->getValue("autoupdate_is_disabled");
				$autoupdate_state = ($autoupdate_is_disabled) ? "Выключены" : "Включены";

				$license_type_id = $license->getPropByName("license_type")->getValue();

				if($license_type = umiObjectsCollection::getInstance()->getObject($license_type_id)) {
					$license_name = $license_type->getName();
				} else {
					$license_name = "";
				}

				if($screenshot = $license->getValue("screenshot")) {
					$screenshot_filepath = $screenshot->getFilePath();
					$screenshot_size = $screenshot->getWidth() . "x" . $screenshot->getHeight() . " px";
					$systemModule = &system_buildin_load("system");
					$screenshot_thumb = $systemModule->makeThumbnail($screenshot_filepath, 100, 'auto', false, true);
				} else {
					$screenshot_filepath = "";
					$screenshot_size = "";
					$screenshot_thumb = "";
				}

				$rows .= <<<ROW
	<row>
		<col style="vertical-align: top;">
			{$license_id}.
		</col>

		<col>
			<table border="0" style="width: 100%;">
				<tr>
					<td style="width: 80px;">
						<![CDATA[Лицензия:]]>
					</td>

					<td style="width: 250px;">
						<b><![CDATA[{$keycode}]]></b>
					</td>
				</tr>


				<tr>
					<td>
						<![CDATA[Тип лицензии:]]>
					</td>

					<td>
						<![CDATA[{$license_name}]]>
					</td>
				</tr>



				<tr>
					<td>
						<![CDATA[Владелец:]]>
					</td>

					<td>
						<a href="mailto:{$owner_email}"><![CDATA[{$owner_lname} {$owner_fname} {$owner_mname}]]></a>
					</td>
				</tr>

				<tr>
					<td>
						<![CDATA[Автообновления:]]>
					</td>

					<td>
						<![CDATA[{$autoupdate_state}]]>
					</td>
				</tr>


				<tr>
					<td style="width: 100px;">
						<![CDATA[Домен:]]>
					</td>

					<td>
						<a href="http://{$domain}/" target="_blank"><b><![CDATA[{$domain}]]></b></a>
					</td>
				</tr>

				<tr>
					<td>
						<![CDATA[IP сервера:]]>
					</td>

					<td>
						<![CDATA[{$ip}]]>
					</td>
				</tr>

			</table>
		</col>


		<col style="vertical-align: top;">
			<a href="%pre_lang%/admin/updatesrv/license_edit/{$license_id}/">
				<img src="{$screenshot_thumb['src']}" width="{$screenshot_thumb['width']}" height="{$screenshot_thumb['height']}" style="border: #000 1px solid;" />
			</a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/updatesrv/license_edit/{$license_id}/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" /></a>
		</col>


		<col style="text-align: center;">
			<a href="%pre_lang%/admin/updatesrv/license_del/{$license_id}/" commit_unrestorable="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" /></a>
		</col>
	</row>
ROW;
			}


			$params['filter_domain'] = $filter_domain;
			$params['filter_ip'] = $filter_ip;
			$params['filter_keycode'] = $filter_keycode;

			$params['licenses'] = $licenses;
			$params['rows'] = $rows;
			return $this->parse_form("licenses", $params);
		}
	}
?>