<?php

$FORMS = Array();



$FORMS['tree'] =  <<<TREE

<imgButton>
	<title><![CDATA[Создать раздел каталога]]></title>
	<src>/images/cms/admin/%skin_path%/ico_add.%ico_ext%</src>
	<link>%pre_lang%/admin/catalog/tree_section_add/%section_id%/</link>
</imgButton>


<imgButton>
	<title><![CDATA[Создать объект каталога]]></title>
	<src>/images/cms/admin/%skin_path%/ico_add.%ico_ext%</src>
	<link>%pre_lang%/admin/catalog/tree_object_add/%section_id%/</link>
</imgButton>

<br /><br />

%pages%

%sections%


TREE;

$FORMS['config'] = <<<END

<form action="%pre_lang%/admin/catalog/config_do/">

	<setgroup name="Настройка каталога" id="params_cont" form="no">

		<tablegroup>

			<row>
				<col>Количество элементов на странице</col>
				<col>
					<input quant="no">
						<name><![CDATA[per_page]]></name>
						<value><![CDATA[%per_page%]]></value>
					</input>
				</col>
			</row>
		</tablegroup>
		<p align="left"><submit title="Сохранить" /></p>
	</setgroup>
</form>
END;

$FORMS['tree_section_add'] = <<<TREE_SECTION_ADD

<script type="text/javascript">
<![CDATA[

function init_me() {
	df = document.forms['adding_new_page'];
	def_value = df.name.value;
	def_alt = df.alt_name.value;
}

function save_with_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "1";
	return acf_check(1);
}
function save_without_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "0";
	return acf_check(1);
}

function save_with_redirect() {
	document.forms['adding_new_page'].exit_after_save.value = "2";
	return acf_check(1);
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


cifi_upload_text = '%catalog_cifi_upload_text%';
]]>
</script>

<form method="post" name="adding_new_page" action="%pre_lang%/admin/catalog/%method%/%parent_section_id%/%section_id%">


<setgroup name="Общие параметры" id="catalog_section" form="no">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="50%">
				<input class="" br="yes" quant="no" size="58" style="width:355px">
					<id><![CDATA[pname]]></id>
					<name><![CDATA[name]]></name>
					<title><![CDATA[Название]]></title>
					<value><![CDATA[%name%]]></value>
					<tip><![CDATA[%tip_name%]]></tip>

					<onkeydown><![CDATA[javascript: go_alt(this);]]></onkeydown>
					<onclick><![CDATA[javascript: go_alt(this);]]></onclick>
				</input>
			</td>

			<td width="50%">
				<input class="" br="yes" quant="no" style="width:355px">
					<id><![CDATA[pkeywords]]></id>
					<name><![CDATA[meta_keywords]]></name>
					<title><![CDATA[Ключевые слова (meta name="KEYWORDS")]]></title>
					<value><![CDATA[%meta_keywords%]]></value>
					<tip><![CDATA[%tip_keywords%]]></tip>
				</input>
			</td>
		</tr>

		<tr>
			<td>
				<input  autocheck="yes"   class="" br="yes" quant="no" style="width:355px">
					<id><![CDATA[ptitle]]></id>
					<name><![CDATA[title]]></name>
					<title><![CDATA[<TITLE>]]></title>
					<value><![CDATA[%title%]]></value>
					<tip><![CDATA[%tip_title%]]></tip>
				</input>

			</td>

			<td>
				<input class="" br="yes" quant="no" style="width:355px">
					<id><![CDATA[pdescription]]></id>
					<name><![CDATA[meta_descriptions]]></name>
					<title><![CDATA[Описания (meta name="DESCRIPTIONS")]]></title>
					<value><![CDATA[%meta_descriptions%]]></value>
					<tip><![CDATA[%tip_description%]]></tip>
				</input>

			</td>
		</tr>

		<tr>
			<td>
				<input    class="" br="yes" size="58" quant="no" style="width:355px">
					<id><![CDATA[palt_name]]></id>
					<name><![CDATA[alt_name]]></name>
					<title><![CDATA[Псевдостатический адрес (URL)]]></title>
					<value><![CDATA[%alt_name%]]></value>
					<tip><![CDATA[%tip_alt_name%]]></tip>
				</input>

			</td>

			<td>
				<input   class="" br="yes" size="58" quant="no" style="width:355px">
					<name><![CDATA[h1]]></name>
					<title><![CDATA[Заголовок страницы (H1)]]></title>
					<value><![CDATA[%h1%]]></value>
					<tip><![CDATA[%tip_h1%]]></tip>
				</input>

			</td>
		</tr>

		<tr>
			<td>
				<select quant="no" br="yes" style="width: 375px;" class="std_select">
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
					<value><![CDATA[%tags%]]></value>
					<tip><![CDATA[%tip_tags%]]></tip>
				</input>

			</td>
		</tr>


		<tr>
			<td></td>

			<td>
			<br />

				<checkbox selected="%is_active%">
					<name><![CDATA[is_active]]></name>
					<title><![CDATA[Активен]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_is_active%]]></tip>
				</checkbox>
			</td>
		</tr>
	</table>
	<p align="right">%save_n_save%&nbsp;&nbsp;&nbsp;&nbsp;</p>
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
				<select   quant="no" br="yes" style="width: 86%;" class="std_select">
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
					<title><![CDATA[Показывать подменю 3 уровня]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_show_submenu%]]></tip>
				</checkbox>

			</td>

			<td>
				<checkbox selected="%expanded%">
					<name><![CDATA[expanded]]></name>
					<title><![CDATA[Меню всегда развернуто]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_expanded%]]></tip>
				</checkbox>

			</td>
		</tr>

		<tr>
			<td colspan="3" height="5"></td>
		</tr>

		<tr>
			<td>
				<checkbox selected="%is_default%">
					<name><![CDATA[is_default]]></name>
					<title><![CDATA[Страница по-умолчанию]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_is_default%]]></tip>
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

			<td>
				<!--<checkbox name="index_item" title="Раздел поиска" value="1">%index_item%</checkbox>-->
			</td>
		</tr>

		<tr>
			<td colspan="3" height="5"></td>
		</tr>


		<tr>
			<td>
				<checkbox selected="%robots_deny%">
					<name><![CDATA[robots_deny]]></name>
					<title><![CDATA[Запретить индексацию поисковиками]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_robots_deny%]]></tip>
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

	<passthru name="exit_after_save"></passthru>
</form>

%backup_panel%

TREE_SECTION_ADD;


$FORMS['tree_object_add'] = <<<TREE_OBJECT_ADD


<script type="text/javascript">
<![CDATA[

function init_me() {
	df = document.forms['adding_new_page'];
	def_value = df.name.value;
	def_alt = df.alt_name.value;
}

function save_with_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "1";
	return acf_check(1);
}
function save_without_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "0";
	return acf_check(1);
}

function save_with_redirect() {
	document.forms['adding_new_page'].exit_after_save.value = "2";
	return acf_check(1);
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


cifi_upload_text = '%catalog_cifi_upload_text%';
]]>
</script>

<form method="post" name="adding_new_page" action="%pre_lang%/admin/catalog/%method%/%section_id%/%object_id%">


<setgroup name="Общие параметры" id="catalog_section" form="no">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="50%">
				<input    class="" br="yes" quant="no" size="58" style="width:355px" onkeydown="javascript:go_alt(this)" onchange="javascript:go_alt(this)">
					<id><![CDATA[pname]]></id>
					<name><![CDATA[name]]></name>
					<title><![CDATA[Название]]></title>
					<value><![CDATA[%name%]]></value>
					<tip><![CDATA[%tip_name%]]></tip>
					<onkeydown><![CDATA[javascript: go_alt(this);]]></onkeydown>
					<onclick><![CDATA[javascript: go_alt(this);]]></onclick>
				</input>

			</td>

			<td width="50%">
				<input class="" br="yes" quant="no" style="width:355px">
					<id><![CDATA[pkeywords]]></id>
					<name><![CDATA[meta_keywords]]></name>
					<title><![CDATA[Ключевые слова (meta name="KEYWORDS")]]></title>
					<value><![CDATA[%meta_keywords%]]></value>
					<tip><![CDATA[%tip_keywords%]]></tip>
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
				<input class="" br="yes" quant="no" style="width:355px">
					<id><![CDATA[pdescription]]></id>
					<name><![CDATA[meta_descriptions]]></name>
					<title><![CDATA[Описания (meta name="DESCRIPTIONS")]]></title>
					<value><![CDATA[%meta_descriptions%]]></value>
					<tip><![CDATA[%tip_description%]]></tip>
				</input>

			</td>
		</tr>

		<tr>
			<td>
				<input    class="" br="yes" size="58" quant="no" style="width:355px">
					<id><![CDATA[palt_name]]></id>
					<name><![CDATA[alt_name]]></name>
					<title><![CDATA[Псевдостатический адрес (URL)]]></title>
					<value><![CDATA[%alt_name%]]></value>
					<tip><![CDATA[%tip_alt_name%]]></tip>
				</input>

			</td>

			<td>
				<input   class="" br="yes" size="58" quant="no" style="width:355px">
					<name><![CDATA[h1]]></name>
					<title><![CDATA[Заголовок страницы (H1)]]></title>
					<value><![CDATA[%h1%]]></value>
					<tip><![CDATA[%tip_h1%]]></tip>
				</input>

			</td>
		</tr>

		<tr>
			<td>
				<select   quant="no" br="yes" style="width: 375px;" class="std_select">
					<name><![CDATA[object_type_id]]></name>
					<title><![CDATA[Тип объекта]]></title>
					<tip><![CDATA[%tip_object_type%]]></tip>
					%object_types%
				</select>

			</td>

			<td>
				<input class="" br="yes" size="58" quant="no" style="width:355px">
					<id><![CDATA[tags]]></id>
					<name><![CDATA[tags]]></name>
					<title><![CDATA[Теги]]></title>
					<value><![CDATA[%tags%]]></value>
					<tip><![CDATA[%tip_tags%]]></tip>
				</input>

			</td>
		</tr>

		<tr>
			<td></td>
			<td>
			<br />
				<checkbox selected="%is_active%">
					<name><![CDATA[is_active]]></name>
					<title><![CDATA[Активен]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_is_active%]]></tip>
				</checkbox>
			</td>
		</tr>
	</table>
				<p align="right">%save_n_save%&nbsp;&nbsp;&nbsp;&nbsp;</p>
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
				<select   quant="no" br="yes" style="width: 86%;" class="std_select">
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
				<checkbox selected="%unindexed%">
					<name><![CDATA[unindexed]]></name>
					<title><![CDATA[Исключить из поиска]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_is_unindexed%]]></tip>
				</checkbox>
			</td>

			<td>
				<checkbox selected="%robots_deny%">
					<name><![CDATA[robots_deny]]></name>
					<title><![CDATA[Запретить индексацию поисковиками]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_robots_deny%]]></tip>
				</checkbox>
			</td>
		</tr>

		<tr>
			<td colspan="3" height="5"></td>
		</tr>

		<tr>
			<td>
				<checkbox selected="%is_default%">
					<name><![CDATA[is_default]]></name>
					<title><![CDATA[Страница по-умолчанию]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_is_default%]]></tip>
				</checkbox>
			</td>

			<td>

			</td>

			<td>
			</td>
		</tr>

		<tr>
			<td colspan="3" height="5"></td>
		</tr>


		<tr>
			<td>
			</td>

			<td></td>

			<td></td>
		</tr>
	</table>

	<p align="right">%save_n_save%</p>

</setgroup>



%data_field_groups%

%perm_panel%

	<passthru name="exit_after_save"></passthru>
</form>

%backup_panel%

TREE_OBJECT_ADD;



$FORMS['matrix'] = <<<MATRIX


<imgButton>
	<link><![CDATA[%pre_lang%/admin/catalog/matrix_add/]]></link>
	<src><![CDATA[/images/cms/admin/%skin_path%/ico_add.%ico_ext%]]></src>
	<title><![CDATA[Добавить матрицу подбора]]></title>
</imgButton>

<br /><br />

<tablegroup>
	<hrow>
		<hcol>Название матрицы</hcol>
		<hcol style="width: 100px;">Изменить</hcol>
		<hcol style="width: 100px;">Удалить</hcol>
	</hrow>
%rows%
</tablegroup>

MATRIX;

$FORMS['matrix_edit'] = <<<MATRIX_EDIT

<form method="post" name="adding_new_page" id="adding_new_page" action="%pre_lang%/admin/catalog/%method%/%matrix_id%">

	<setgroup name="Общие свойства" id="catalog_matrix_common" form="no">

		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td style="width: 50%;">
					<input   class="" br="yes" quant="no" size="58" style="width:355px">
						<name><![CDATA[name]]></name>
						<title><![CDATA[Название матрицы]]></title>
						<value><![CDATA[%name%]]></value>
					</input>

				</td>

				<td>
					<input   class="" br="yes" quant="no" size="58" style="width:150px">
						<name><![CDATA[min_rang]]></name>
						<title><![CDATA[Минимальный порог]]></title>
						<value><![CDATA[%min_rang%]]></value>
					</input>

				</td>
			</tr>

			<tr>
				<td>
					<br />
					<checkbox style="margin-left: 3px;" selected="%is_active%">
							<name><![CDATA[is_active]]></name>
							<title><![CDATA[Активен]]></title>
							<value><![CDATA[1]]></value>
					</checkbox>

				</td>

				<td>
					<input   class="" br="yes" quant="no" size="58" style="width:150px">
						<name><![CDATA[per_page]]></name>
						<title><![CDATA[Количество выводимых объектов]]></title>
						<value><![CDATA[%per_page%]]></value>
					</input>

				</td>
			</tr>

		</table>

		<p align="right">%save_n_save%</p>

	</setgroup>

	<setgroup name="Список сравниваемых товаров" id="catalog_matrix_items" form="no">

<!--		<multiple name="items[]" style="width: 100%; height: 200px;">
			%rows_items%
		</multiple>
-->
%rows_items%


		<p align="right">%save_n_save%</p>
	</setgroup>

	<setgroup name="Список вопросов" id="catalog_matrix_questions" form="no">

		<tablegroup>
			<hrow>
				<hcol>Текст вопроса</hcol>
				<hcol style="width: 100px;">Ответы</hcol>
				<hcol style="width: 100px;">Удалить</hcol>
			</hrow>

			%rows_questions%

			<row>
				<col>
					<input type="text" quant="no" br="no"  style="width: 97%;">
						<name><![CDATA[questions_new]]></name>
					</input>
				</col>

				<col style="text-align: center;"></col>
				<col style="text-align: center;"></col>
			</row>
		</tablegroup>
		<p align="right">%save_n_save%</p>
	</setgroup>

	<passthru name="exit_after_save"></passthru>
</form>

<script type="text/javascript">
<![CDATA[

function save_with_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "1";
	return true;
}

function save_without_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "0";
	return true;
}

]]>
</script>


MATRIX_EDIT;

$FORMS['matrix_matrix'] = <<<MATRIX_MATRIX

<form method="post" name="adding_new_page" id="adding_new_page" action="%pre_lang%/admin/catalog/matrix_matrix_do/%matrix_id%">
<tablegroup>
	%matrix%
</tablegroup>

	<p align="right">%save_n_save%</p>
	<passthru name="exit_after_save"></passthru>
</form>

<script type="text/javascript">
<![CDATA[

function save_with_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "1";
	return true;
}

function save_without_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "0";
	return true;
}

]]>
</script>

MATRIX_MATRIX;


$FORMS['matrix_edit_answ'] = <<<MATRIX_EDIT_ANSW

<form method="post" name="adding_new_page" id="adding_new_page" action="%pre_lang%/admin/catalog/matrix_edit_answ_do/%matrix_id%/%question_id%/">

<tablegroup>
	<hrow>
		<hcol>Ответы</hcol>
		<hcol style="width: 100px;">Удалить</hcol>
	</hrow>
	%rows%

	<row>
		<col>
			<input type="text" quant="no" br="no"  style="width: 97%;">
						<name><![CDATA[answer_new]]></name>
			</input>

		</col>

		<col>
		</col>

	</row>
</tablegroup>

	<p align="right">%save_n_save%</p>
	<passthru name="exit_after_save"></passthru>
</form>

<script type="text/javascript">
<![CDATA[

function save_with_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "1";
	return true;
}

function save_without_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "0";
	return true;
}

]]>
</script>

MATRIX_EDIT_ANSW;

?>