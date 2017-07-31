<?php

$FORMS = array();

$FORMS['projects_list'] = <<<PROJS_LIST

	<imgButton>
		<title><![CDATA[Добавить проект]]></title>
		<src>/images/cms/admin/%skin_path%/ico_add.%ico_ext%</src>
		<link>%pre_lang%/admin/faq/project_add/%parent_id%/</link>
	</imgButton>

	<br /><br />

	%pages%

	<tablegroup>
		<hrow>
			<hcol><![CDATA[Проект]]></hcol>
			<hcol style="width: 100px;"><![CDATA[Содержание]]></hcol>
			<hcol style="width: 100px;"><![CDATA[Активность]]></hcol>
			<hcol style="width: 100px;"><![CDATA[Изменить]]></hcol>
			<hcol style="width: 100px;"><![CDATA[Удалить]]></hcol>
		</hrow>
		%lines%
	</tablegroup>


PROJS_LIST;

$FORMS['project_line'] = <<<PROJ_LINE

	<row>
		<col>
			<a href="%pre_lang%/admin/faq/categories_list/%element_id%/"><![CDATA[%name%]]></a> %core getTypeEditLink(%object_type_id%)%

			<br /><br />
			<table border="0">
				<tr>
					<td style="width: 150px;">
						<![CDATA[Последнее обновление:]]>
					</td>

					<td>
						%updatetime%
					</td>
				</tr>

				<tr>
					<td>
						<![CDATA[Ссылка на сайте:]]>
					</td>

					<td>
						<a href="%site_link%"><![CDATA[%site_link%]]></a>
					</td>
				</tr>
			</table>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/faq/categories_list/%element_id%/">
				<img src='/images/cms/admin/%skin_path%/ico_subitems.gif' title="Содержание" alt="Содержание" border="0" />
			</a>
		</col>


		<col style="text-align: center;">
			%blocking%
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/faq/project_edit/%parent_id%/%element_id%/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" border="0" alt="Редактировать" title="Редактировать" /></a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/faq/project_del/%element_id%/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" border="0" alt="Удалить" title="Удалить" /></a>
		</col>
	</row>

PROJ_LINE;

$FORMS['project_edit'] = <<<PROJ_EDT

<form method="post" name="adding_new_page" enctype="multipart/form-data" action="%pre_lang%/admin/faq/%method%/%parent_id%/%element_id%/">


	<setgroup name="Основные свойства" id="faq_add_list" form="no">
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%">
					<input br="yes" quant="no" size="58" style="width:355px">
						<id><![CDATA[pname]]></id>
						<name><![CDATA[name]]></name>
						<title><![CDATA[Название]]></title>
						<value><![CDATA[%name%]]></value>
						<tip><![CDATA[%tip_name%]]></tip>

						<onchange><![CDATA[javascript: go_alt(this);]]></onchange>
						<onkeydown><![CDATA[javascript: go_alt(this);]]></onkeydown>
					</input>
				</td>

				<td width="50%">
					<input br="yes" quant="no" style="width:355px">
						<id><![CDATA[pkeywords]]></id>
						<name><![CDATA[meta_keywords]]></name>
						<tip><![CDATA[%tip_keywords%]]></tip>

						<title><![CDATA[Ключевые слова (meta name="KEYWORDS")]]></title>
						<value><![CDATA[%meta_keywords%]]></value>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					<input autocheck="yes" class="" br="yes" quant="no" style="width:355px">
						<id><![CDATA[ptitle]]></id>
						<name><![CDATA[title]]></name>
						<title><![CDATA[<TITLE>]]></title>
						<value><![CDATA[%title%]]></value>
						<tip><![CDATA[%tip_title%]]></tip>
					</input>
				</td>

				<td>
					<input br="yes" quant="no" style="width:355px">
						<id><![CDATA[pdescription]]></id>
						<name><![CDATA[meta_description]]></name>
						<title><![CDATA[Описания (meta name="DESCRIPTIONS")]]></title>
						<value><![CDATA[%meta_description%]]></value>
						<tip><![CDATA[%tip_description%]]></tip>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					<input br="yes" size="58" quant="no" style="width:355px">
						<id><![CDATA[palt_name]]></id>
						<name><![CDATA[alt_name]]></name>
						<title><![CDATA[Псевдостатический адрес (URL)]]></title>
						<value><![CDATA[%alt_name%]]></value>
						<tip><![CDATA[%tip_alt_name%]]></tip>
					</input>
				</td>

				<td>
					<input br="yes" size="58" quant="no" style="width:355px">
						<name><![CDATA[h1]]></name>
						<title><![CDATA[Заголовок страницы (H1)]]></title>
						<value><![CDATA[%h1%]]></value>
						<tip><![CDATA[%tip_h1%]]></tip>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					<select quant="no" br="yes" style="width: 375px;">
						<name><![CDATA[object_type_id]]></name>
						<title><![CDATA[Тип раздела]]></title>
						<tip><![CDATA[%tip_object_type%]]></tip>
						%object_types%
					</select>
				</td>

				<td>
					<input   class="" br="yes" size="58" quant="no" style="width:355px">
						<id><![CDATA[tags]]></id>
						<name><![CDATA[tags]]></name>
						<title><![CDATA[Теги]]></title>
						<tip><![CDATA[%tip_tags%]]></tip>
						<value><![CDATA[%tags%]]></value>
					</input>

				</td>
			</tr>

			<tr>
				<td></td>
				<td>
					<br />
					<checkbox selected="%is_active%">
						<name><![CDATA[is_active]]></name>
						<title><![CDATA[Активная]]></title>
						<value><![CDATA[1]]></value>
						<tip><![CDATA[%tip_is_active%]]></tip>
					</checkbox>

				</td>
			</tr>
		</table>


		<br />

		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td height="19" class="ntext">
					Описание
				</td>
			</tr>

			<tr>
				<td class="ntext">
					<wysiwyg id="content"><![CDATA[%content%]]></wysiwyg>
				</td>
			</tr>
		</table>


		<p align="right">%save_n_save%</p>

		<passthru name="parent">%curr_rel%</passthru>
		<passthru name="mode">admin</passthru>
		<passthru name="target_domain">%domain%</passthru>
		<passthru name="exit_after_save"></passthru>
		<passthru name="quickmode">%quickmode%</passthru>

		<script type="text/javascript">
			<![CDATA[
				function init_me() {
					df = document.forms['adding_new_page'];
					def_value = df.name.value;
					def_alt = df.alt_name.value;
				}

				function save_with_redirect() {
					document.forms['adding_new_page'].exit_after_save.value = "2";
					return acf_check(1);
				}

				function save_with_exit() {
					document.forms['adding_new_page'].exit_after_save.value = "1";
					return acf_check(1);
				}

				function save_without_exit() {
					document.forms['adding_new_page'].exit_after_save.value = "0";
					return acf_check(1);
				}

				function edit_cancel() {
					if(confirm("Вы уверены, что хотите выйти? Все изменения будут потеряны")) {
						redirect_str = "%edit_cancel_redirect%";
						if(redirect_str) {
							window.location = redirect_str;
						}
					}
					return false;
				}

				frm = document.forms['adding_new_page'];
				frm.onsubmit = acf_check;

				acf_inputs_test[acf_inputs_test.length] = Array('pname', 'Enter page name');
				acf_inputs_test[acf_inputs_test.length] = Array('palt_name', 'Enter static urlname');

				acf_inputs_catch[acf_inputs_catch.length] = 'pname';
				acf_inputs_catch[acf_inputs_catch.length] = 'ptitle';
				acf_inputs_catch[acf_inputs_catch.length] = 'palt_name';
				acf_inputs_catch[acf_inputs_catch.length] = 'pkeywords';
				acf_inputs_catch[acf_inputs_catch.length] = 'pdescription';

				cifi_upload_text = '%faq_cifi_upload_text%';
			]]>
		</script>
	</setgroup>


	<setgroup name="Параметры" id="params" form="no">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>
					<span class="ftext">
						<![CDATA[Изображение неактивного раздела]]>
						<tip>
							<title><![CDATA[Изображение неактивного раздела]]></title>
							<content><![CDATA[%tip_menu_ua%]]></content>
						</tip>
					</span>
					%cifi_menu_ua%
				</td>

				<td style="width: 10px;">&nbsp;&nbsp;&nbsp;</td>

				<td>
					<span class="ftext">
						<![CDATA[Изображение активного раздела]]>
						<tip>
							<title><![CDATA[Изображение активного раздела]]></title>
							<content><![CDATA[%tip_menu_a%]]></content>
						</tip>
					</span>
					%cifi_menu_a%
				</td>
			</tr>

			<tr>
				<td>
					<span class="ftext">
						<![CDATA[Изображение для заголовка]]>
						<tip>
							<title><![CDATA[Изображение для заголовка]]></title>
							<content><![CDATA[%tip_headers%]]></content>
						</tip>
					</span>
					%cifi_headers%
				</td>

				<td style="width: 10px;"></td>

				<td>
					<select quant="no" br="yes" style="width: 86%;">
						<name><![CDATA[tpl]]></name>
						<title><![CDATA[Шаблон дизайна]]></title>
						<tip><![CDATA[%tip_template_id%]]></tip>
						%templates%
					</select>
				</td>
			</tr>
		</table>


		<br/>

		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>
					<checkbox selected="%is_visible%">
						<name><![CDATA[is_visible]]></name>
						<title><![CDATA[Отображать в меню]]></title>
						<value><![CDATA[1]]></value>
						<tip><![CDATA[%tip_is_visible%]]></tip>
					</checkbox>
				</td>

				<td>
					<checkbox selected="%show_submenu%">
						<name><![CDATA[show_submenu]]></name>
						<title><![CDATA[Показывать подменю]]></title>
						<tip><![CDATA[%tip_show_submenu%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td>
					<checkbox selected="%expanded%">
						<name><![CDATA[expanded]]></name>
						<title><![CDATA[Меню всегда развернуто]]></title>
						<tip><![CDATA[%tip_expanded%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>
			</tr>

			<tr>
				<td colspan="3" height="5"></td>
			</tr>

			<tr>
				<td>
					<checkbox selected="%is_default%">
						<name><![CDATA[def]]></name>
						<title><![CDATA[Страница по-умолчанию]]></title>
						<tip><![CDATA[%tip_is_default%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td>
					<checkbox selected="%unindexed%">
						<name><![CDATA[unindexed]]></name>
						<title><![CDATA[Исключить из поиска]]></title>
						<tip><![CDATA[%tip_is_unindexed%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td></td>
			</tr>

			<tr>
				<td colspan="3" height="5"></td>
			</tr>


			<tr>
				<td>
					<checkbox selected="%robots_deny%">
						<name><![CDATA[robots_deny]]></name>
						<title><![CDATA[Запретить индексацию поисковиками]]></title>
						<tip><![CDATA[%tip_robots_deny%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td></td>
				<td></td>
			</tr>
		</table>

		<p align="right">%save_n_save%</p>
	</setgroup>

	%data_field_groups%

	%perm_panel%
</form>

%backup_panel%


PROJ_EDT;

// 

$FORMS['categories_list'] = <<<CATS_LIST

	<imgButton>
		<title><![CDATA[Добавить категорию вопросов]]></title>
		<src>/images/cms/admin/%skin_path%/ico_add.%ico_ext%</src>
		<link>%pre_lang%/admin/faq/category_add/%parent_id%/</link>
	</imgButton>

	<br /><br />

	%pages%

	<tablegroup>
		<hrow>
			<hcol><![CDATA[Категория вопросов]]></hcol>
			<hcol style="width: 100px;"><![CDATA[Содержание]]></hcol>
			<hcol style="width: 100px;"><![CDATA[Активность]]></hcol>
			<hcol style="width: 100px;"><![CDATA[Изменить]]></hcol>
			<hcol style="width: 100px;"><![CDATA[Удалить]]></hcol>
		</hrow>
		%lines%
	</tablegroup>


CATS_LIST;

$FORMS['category_line'] = <<<CAT_LINE

	<row>
		<col>
			<a href="%pre_lang%/admin/faq/questions_list/%element_id%/"><![CDATA[%name%]]></a> %core getTypeEditLink(%object_type_id%)%

			<br /><br />
			<table border="0">
				<tr>
					<td style="width: 150px;">
						<![CDATA[Последнее обновление:]]>
					</td>

					<td>
						%updatetime%
					</td>
				</tr>

				<tr>
					<td>
						<![CDATA[Ссылка на сайте:]]>
					</td>

					<td>
						<a href="%site_link%"><![CDATA[%site_link%]]></a>
					</td>
				</tr>
			</table>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/faq/questions_list/%element_id%/">
				<img src='/images/cms/admin/%skin_path%/ico_subitems.gif' title="Содержание" alt="Содержание" border="0" />
			</a>
		</col>

		<col style="text-align: center;">
			%blocking%
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/faq/category_edit/%parent_id%/%element_id%/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" border="0" alt="Редактировать" title="Редактировать" /></a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/faq/category_del/%element_id%/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" border="0" alt="Удалить" title="Удалить" /></a>
		</col>
	</row>

CAT_LINE;

$FORMS['category_edit'] = <<<CAT_EDT

<form method="post" name="adding_new_page" enctype="multipart/form-data" action="%pre_lang%/admin/faq/%method%/%parent_id%/%element_id%/">


	<setgroup name="Основные свойства" id="faq_add_list" form="no">
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%">
					<input br="yes" quant="no" size="58" style="width:355px">
						<id><![CDATA[pname]]></id>
						<name><![CDATA[name]]></name>
						<title><![CDATA[Название]]></title>
						<value><![CDATA[%name%]]></value>
						<tip><![CDATA[%tip_name%]]></tip>

						<onchange><![CDATA[javascript: go_alt(this);]]></onchange>
						<onkeydown><![CDATA[javascript: go_alt(this);]]></onkeydown>
					</input>
				</td>

				<td width="50%">
					<input br="yes" quant="no" style="width:355px">
						<id><![CDATA[pkeywords]]></id>
						<name><![CDATA[meta_keywords]]></name>
						<tip><![CDATA[%tip_keywords%]]></tip>

						<title><![CDATA[Ключевые слова (meta name="KEYWORDS")]]></title>
						<value><![CDATA[%meta_keywords%]]></value>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					<input autocheck="yes" class="" br="yes" quant="no" style="width:355px">
						<id><![CDATA[ptitle]]></id>
						<name><![CDATA[title]]></name>
						<title><![CDATA[<TITLE>]]></title>
						<value><![CDATA[%title%]]></value>
						<tip><![CDATA[%tip_title%]]></tip>
					</input>
				</td>

				<td>
					<input br="yes" quant="no" style="width:355px">
						<id><![CDATA[pdescription]]></id>
						<name><![CDATA[meta_description]]></name>
						<title><![CDATA[Описания (meta name="DESCRIPTIONS")]]></title>
						<value><![CDATA[%meta_description%]]></value>
						<tip><![CDATA[%tip_description%]]></tip>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					<input br="yes" size="58" quant="no" style="width:355px">
						<id><![CDATA[palt_name]]></id>
						<name><![CDATA[alt_name]]></name>
						<title><![CDATA[Псевдостатический адрес (URL)]]></title>
						<value><![CDATA[%alt_name%]]></value>
						<tip><![CDATA[%tip_alt_name%]]></tip>
					</input>
				</td>

				<td>
					<input br="yes" size="58" quant="no" style="width:355px">
						<name><![CDATA[h1]]></name>
						<title><![CDATA[Заголовок страницы (H1)]]></title>
						<value><![CDATA[%h1%]]></value>
						<tip><![CDATA[%tip_h1%]]></tip>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					<select quant="no" br="yes" style="width: 375px;">
						<name><![CDATA[object_type_id]]></name>
						<title><![CDATA[Тип раздела]]></title>
						<tip><![CDATA[%tip_object_type%]]></tip>
						%object_types%
					</select>
				</td>

				<td>
					<input   class="" br="yes" size="58" quant="no" style="width:355px">
						<id><![CDATA[tags]]></id>
						<name><![CDATA[tags]]></name>
						<title><![CDATA[Теги]]></title>
						<tip><![CDATA[%tip_tags%]]></tip>
						<value><![CDATA[%tags%]]></value>
					</input>

				</td>
			</tr>

			<tr>
				<td></td>
				<td>
					<br />
					<checkbox selected="%is_active%">
						<name><![CDATA[is_active]]></name>
						<title><![CDATA[Активная]]></title>
						<value><![CDATA[1]]></value>
						<tip><![CDATA[%tip_is_active%]]></tip>
					</checkbox>

				</td>
			</tr>
		</table>


		<br />

		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td height="19" class="ntext">
					Описание
				</td>
			</tr>

			<tr>
				<td class="ntext">
					<wysiwyg id="content"><![CDATA[%content%]]></wysiwyg>
				</td>
			</tr>
		</table>


		<p align="right">%save_n_save%</p>

		<passthru name="parent">%curr_rel%</passthru>
		<passthru name="mode">admin</passthru>
		<passthru name="target_domain">%domain%</passthru>
		<passthru name="exit_after_save"></passthru>
		<passthru name="quickmode">%quickmode%</passthru>

		<script type="text/javascript">
			<![CDATA[
				function init_me() {
					df = document.forms['adding_new_page'];
					def_value = df.name.value;
					def_alt = df.alt_name.value;
				}

				function save_with_redirect() {
					document.forms['adding_new_page'].exit_after_save.value = "2";
					return acf_check(1);
				}

				function save_with_exit() {
					document.forms['adding_new_page'].exit_after_save.value = "1";
					return acf_check(1);
				}

				function save_without_exit() {
					document.forms['adding_new_page'].exit_after_save.value = "0";
					return acf_check(1);
				}

				function edit_cancel() {
					if(confirm("Вы уверены, что хотите выйти? Все изменения будут потеряны")) {
						redirect_str = "%edit_cancel_redirect%";
						if(redirect_str) {
							window.location = redirect_str;
						}
					}
					return false;
				}

				frm = document.forms['adding_new_page'];
				frm.onsubmit = acf_check;

				acf_inputs_test[acf_inputs_test.length] = Array('pname', 'Enter page name');
				acf_inputs_test[acf_inputs_test.length] = Array('palt_name', 'Enter static urlname');

				acf_inputs_catch[acf_inputs_catch.length] = 'pname';
				acf_inputs_catch[acf_inputs_catch.length] = 'ptitle';
				acf_inputs_catch[acf_inputs_catch.length] = 'palt_name';
				acf_inputs_catch[acf_inputs_catch.length] = 'pkeywords';
				acf_inputs_catch[acf_inputs_catch.length] = 'pdescription';

				cifi_upload_text = '%faq_cifi_upload_text%';
			]]>
		</script>
	</setgroup>


	<setgroup name="Параметры" id="params" form="no">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>
					<span class="ftext">
						<![CDATA[Изображение неактивного раздела]]>
						<tip>
							<title><![CDATA[Изображение неактивного раздела]]></title>
							<content><![CDATA[%tip_menu_ua%]]></content>
						</tip>
					</span>
					%cifi_menu_ua%
				</td>

				<td style="width: 10px;">&nbsp;&nbsp;&nbsp;</td>

				<td>
					<span class="ftext">
						<![CDATA[Изображение активного раздела]]>
						<tip>
							<title><![CDATA[Изображение активного раздела]]></title>
							<content><![CDATA[%tip_menu_a%]]></content>
						</tip>
					</span>
					%cifi_menu_a%
				</td>
			</tr>

			<tr>
				<td>
					<span class="ftext">
						<![CDATA[Изображение для заголовка]]>
						<tip>
							<title><![CDATA[Изображение для заголовка]]></title>
							<content><![CDATA[%tip_headers%]]></content>
						</tip>
					</span>
					%cifi_headers%
				</td>

				<td style="width: 10px;"></td>

				<td>
					<select quant="no" br="yes" style="width: 86%;">
						<name><![CDATA[tpl]]></name>
						<title><![CDATA[Шаблон дизайна]]></title>
						<tip><![CDATA[%tip_template_id%]]></tip>
						%templates%
					</select>
				</td>
			</tr>
		</table>


		<br/>

		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>
					<checkbox selected="%is_visible%">
						<name><![CDATA[is_visible]]></name>
						<title><![CDATA[Отображать в меню]]></title>
						<value><![CDATA[1]]></value>
						<tip><![CDATA[%tip_is_visible%]]></tip>
					</checkbox>
				</td>

				<td>
					<checkbox selected="%show_submenu%">
						<name><![CDATA[show_submenu]]></name>
						<title><![CDATA[Показывать подменю]]></title>
						<tip><![CDATA[%tip_show_submenu%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td>
					<checkbox selected="%expanded%">
						<name><![CDATA[expanded]]></name>
						<title><![CDATA[Меню всегда развернуто]]></title>
						<tip><![CDATA[%tip_expanded%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>
			</tr>

			<tr>
				<td colspan="3" height="5"></td>
			</tr>

			<tr>
				<td>
					<checkbox selected="%is_default%">
						<name><![CDATA[def]]></name>
						<title><![CDATA[Страница по-умолчанию]]></title>
						<tip><![CDATA[%tip_is_default%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td>
					<checkbox selected="%unindexed%">
						<name><![CDATA[unindexed]]></name>
						<title><![CDATA[Исключить из поиска]]></title>
						<tip><![CDATA[%tip_is_unindexed%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td></td>
			</tr>

			<tr>
				<td colspan="3" height="5"></td>
			</tr>


			<tr>
				<td>
					<checkbox selected="%robots_deny%">
						<name><![CDATA[robots_deny]]></name>
						<title><![CDATA[Запретить индексацию поисковиками]]></title>
						<tip><![CDATA[%tip_robots_deny%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td></td>
				<td></td>
			</tr>
		</table>

		<p align="right">%save_n_save%</p>
	</setgroup>

	%data_field_groups%

	%perm_panel%
</form>

%backup_panel%


CAT_EDT;




// questions

$FORMS['questions_list'] = <<<QS_LIST

	<imgButton>
		<title><![CDATA[Добавить вопрос]]></title>
		<src>/images/cms/admin/%skin_path%/ico_add.%ico_ext%</src>
		<link>%pre_lang%/admin/faq/question_add/%parent_id%/</link>
	</imgButton>

	<br /><br />

	%pages%

	<tablegroup>
		<hrow>
			<hcol>Список вопросов</hcol>
			<hcol style="width: 100px;">Активность</hcol>
			<hcol style="width: 100px;">Изменить</hcol>
			<hcol style="width: 100px;">Удалить</hcol>
		</hrow>
		%lines%
	</tablegroup>


QS_LIST;

$FORMS['question_line'] = <<<QS_LINE

	<row>
		<col>
			<a href="%pre_lang%/admin/faq/question_edit/%parent_id%/%element_id%/"><![CDATA[%name%]]></a> %core getTypeEditLink(%object_type_id%)%

			<br /><br />
			<table border="0">
				<tr>
					<td style="width: 150px;">
						Последнее обновление:
					</td>

					<td>
						%updatetime%
					</td>
				</tr>

				<tr>
					<td>
						Ссылка на сайте:
					</td>

					<td>
						<a href="%site_link%"><![CDATA[%site_link%]]></a>
					</td>
				</tr>
			</table>
		</col>

		<col style="text-align: center;">
			%blocking%
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/faq/question_edit/%parent_id%/%element_id%/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" border="0" alt="Редактировать" title="Редактировать" /></a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/faq/question_del/%element_id%/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" border="0" alt="Удалить" title="Удалить" /></a>
		</col>
	</row>

QS_LINE;

$FORMS['question_edit'] = <<<QS_EDT

<form method="post" name="adding_new_page" enctype="multipart/form-data" action="%pre_lang%/admin/faq/%method%/%parent_id%/%element_id%/">


	<setgroup name="Основные свойства" id="faq_add_list" form="no">
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%">
					<input br="yes" quant="no" size="58" style="width:355px">
						<id><![CDATA[pname]]></id>
						<name><![CDATA[name]]></name>
						<title><![CDATA[Название]]></title>
						<value><![CDATA[%name%]]></value>
						<tip><![CDATA[%tip_name%]]></tip>

						<onchange><![CDATA[javascript: go_alt(this);]]></onchange>
						<onkeydown><![CDATA[javascript: go_alt(this);]]></onkeydown>
					</input>
				</td>

				<td width="50%">
					<input br="yes" quant="no" style="width:355px">
						<id><![CDATA[pkeywords]]></id>
						<name><![CDATA[meta_keywords]]></name>
						<tip><![CDATA[%tip_keywords%]]></tip>

						<title><![CDATA[Ключевые слова (meta name="KEYWORDS")]]></title>
						<value><![CDATA[%meta_keywords%]]></value>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					<input autocheck="yes" class="" br="yes" quant="no" style="width:355px">
						<id><![CDATA[ptitle]]></id>
						<name><![CDATA[title]]></name>
						<title><![CDATA[<TITLE>]]></title>
						<value><![CDATA[%title%]]></value>
						<tip><![CDATA[%tip_title%]]></tip>
					</input>
				</td>

				<td>
					<input br="yes" quant="no" style="width:355px">
						<id><![CDATA[pdescription]]></id>
						<name><![CDATA[meta_description]]></name>
						<title><![CDATA[Описания (meta name="DESCRIPTIONS")]]></title>
						<value><![CDATA[%meta_description%]]></value>
						<tip><![CDATA[%tip_description%]]></tip>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					<input br="yes" size="58" quant="no" style="width:355px">
						<id><![CDATA[palt_name]]></id>
						<name><![CDATA[alt_name]]></name>
						<title><![CDATA[Псевдостатический адрес (URL)]]></title>
						<value><![CDATA[%alt_name%]]></value>
						<tip><![CDATA[%tip_alt_name%]]></tip>
					</input>
				</td>

				<td>
					<input br="yes" size="58" quant="no" style="width:355px">
						<name><![CDATA[h1]]></name>
						<title><![CDATA[Заголовок страницы (H1)]]></title>
						<value><![CDATA[%h1%]]></value>
						<tip><![CDATA[%tip_h1%]]></tip>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					<select quant="no" br="yes" style="width: 375px;">
						<name><![CDATA[object_type_id]]></name>
						<title><![CDATA[Тип раздела]]></title>
						<tip><![CDATA[%tip_object_type%]]></tip>
						%object_types%
					</select>
				</td>

				<td>
					<input   class="" br="yes" size="58" quant="no" style="width:355px">
						<id><![CDATA[tags]]></id>
						<name><![CDATA[tags]]></name>
						<title><![CDATA[Теги]]></title>
						<tip><![CDATA[%tip_tags%]]></tip>
						<value><![CDATA[%tags%]]></value>
					</input>

				</td>
			</tr>

			<tr>
				<td></td>
				<td>
					<br />
					<checkbox selected="%is_active%">
						<name><![CDATA[is_active]]></name>
						<title><![CDATA[Активная]]></title>
						<value><![CDATA[1]]></value>
						<tip><![CDATA[%tip_is_active%]]></tip>
					</checkbox>

				</td>
			</tr>
		</table>

		<br />

		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td height="19" class="ntext">
					Вопрос
				</td>
			</tr>

			<tr>
				<td class="ntext">
					<textarea name="question" style="width: 100%; height: 56px;">%question%</textarea>
				</td>
			</tr>
		</table>

		<br />

		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td height="19" class="ntext">
					Ответ на вопрос
				</td>
			</tr>

			<tr>
				<td class="ntext">
					<wysiwyg id="content"><![CDATA[%content%]]></wysiwyg>
				</td>
			</tr>
		</table>


		<p align="right">%save_n_save%</p>

		<passthru name="parent">%curr_rel%</passthru>
		<passthru name="mode">admin</passthru>
		<passthru name="target_domain">%domain%</passthru>
		<passthru name="exit_after_save"></passthru>
		<passthru name="quickmode">%quickmode%</passthru>

		<script type="text/javascript">
			<![CDATA[
				function init_me() {
					df = document.forms['adding_new_page'];
					def_value = df.name.value;
					def_alt = df.alt_name.value;
				}

				function save_with_redirect() {
					document.forms['adding_new_page'].exit_after_save.value = "2";
					return acf_check(1);
				}

				function save_with_exit() {
					document.forms['adding_new_page'].exit_after_save.value = "1";
					return acf_check(1);
				}

				function save_without_exit() {
					document.forms['adding_new_page'].exit_after_save.value = "0";
					return acf_check(1);
				}

				function edit_cancel() {
					if(confirm("Вы уверены, что хотите выйти? Все изменения будут потеряны")) {
						redirect_str = "%edit_cancel_redirect%";
						if(redirect_str) {
							window.location = redirect_str;
						}
					}
					return false;
				}

				frm = document.forms['adding_new_page'];
				frm.onsubmit = acf_check;

				acf_inputs_test[acf_inputs_test.length] = Array('pname', 'Enter page name');
				acf_inputs_test[acf_inputs_test.length] = Array('palt_name', 'Enter static urlname');

				acf_inputs_catch[acf_inputs_catch.length] = 'pname';
				acf_inputs_catch[acf_inputs_catch.length] = 'ptitle';
				acf_inputs_catch[acf_inputs_catch.length] = 'palt_name';
				acf_inputs_catch[acf_inputs_catch.length] = 'pkeywords';
				acf_inputs_catch[acf_inputs_catch.length] = 'pdescription';

				cifi_upload_text = '%faq_cifi_upload_text%';
			]]>
		</script>
	</setgroup>


	<setgroup name="Параметры" id="params" form="no">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>
					<span class="ftext">
						<![CDATA[Изображение неактивного раздела]]>
						<tip>
							<title><![CDATA[Изображение неактивного раздела]]></title>
							<content><![CDATA[%tip_menu_ua%]]></content>
						</tip>
					</span>
					%cifi_menu_ua%
				</td>

				<td style="width: 10px;">&nbsp;&nbsp;&nbsp;</td>

				<td>
					<span class="ftext">
						<![CDATA[Изображение активного раздела]]>
						<tip>
							<title><![CDATA[Изображение активного раздела]]></title>
							<content><![CDATA[%tip_menu_a%]]></content>
						</tip>
					</span>
					%cifi_menu_a%
				</td>
			</tr>

			<tr>
				<td>
					<span class="ftext">
						<![CDATA[Изображение для заголовка]]>
						<tip>
							<title><![CDATA[Изображение для заголовка]]></title>
							<content><![CDATA[%tip_headers%]]></content>
						</tip>
					</span>
					%cifi_headers%
				</td>

				<td style="width: 10px;"></td>

				<td>
					<select quant="no" br="yes" style="width: 86%;">
						<name><![CDATA[tpl]]></name>
						<title><![CDATA[Шаблон дизайна]]></title>
						<tip><![CDATA[%tip_template_id%]]></tip>
						%templates%
					</select>
				</td>
			</tr>
		</table>


		<br/>

		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>
					<checkbox selected="%is_visible%">
						<name><![CDATA[is_visible]]></name>
						<title><![CDATA[Отображать в меню]]></title>
						<value><![CDATA[1]]></value>
						<tip><![CDATA[%tip_is_visible%]]></tip>
					</checkbox>
				</td>

				<td>
					<checkbox selected="%show_submenu%">
						<name><![CDATA[show_submenu]]></name>
						<title><![CDATA[Показывать подменю]]></title>
						<tip><![CDATA[%tip_show_submenu%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td>
					<checkbox selected="%expanded%">
						<name><![CDATA[expanded]]></name>
						<title><![CDATA[Меню всегда развернуто]]></title>
						<tip><![CDATA[%tip_expanded%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>
			</tr>

			<tr>
				<td colspan="3" height="5"></td>
			</tr>

			<tr>
				<td>
					<checkbox selected="%is_default%">
						<name><![CDATA[def]]></name>
						<title><![CDATA[Страница по-умолчанию]]></title>
						<tip><![CDATA[%tip_is_default%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td>
					<checkbox selected="%unindexed%">
						<name><![CDATA[unindexed]]></name>
						<title><![CDATA[Исключить из поиска]]></title>
						<tip><![CDATA[%tip_is_unindexed%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td></td>
			</tr>

			<tr>
				<td colspan="3" height="5"></td>
			</tr>


			<tr>
				<td>
					<checkbox selected="%robots_deny%">
						<name><![CDATA[robots_deny]]></name>
						<title><![CDATA[Запретить индексацию поисковиками]]></title>
						<tip><![CDATA[%tip_robots_deny%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>

				<td></td>
				<td></td>
			</tr>
		</table>

		<p align="right">%save_n_save%</p>
	</setgroup>

	%data_field_groups%

	%perm_panel%
</form>

%backup_panel%


QS_EDT;

?>