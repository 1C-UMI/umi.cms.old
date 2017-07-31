<?php
$FORMS = Array();


$FORMS['types'] = <<<END

<imgButton>
	<src><![CDATA[/images/cms/admin/%skin_path%/ico_add.gif]]></src>
	<link><![CDATA[%pre_lang%/admin/data/type_add/%parent_type_id%/]]></link>
	<title><![CDATA[Добавить тип данных]]></title>
</imgButton>


<br /><br />

%pages%

<tablegroup>
	<hrow>
		<hcol><![CDATA[Название типа данных]]></hcol>
		<hcol style="width: 100px;"><![CDATA[Содержание]]></hcol>
		<hcol style="width: 100px;"><![CDATA[Изменить]]></hcol>
		<hcol style="width: 100px;"><![CDATA[Удалить]]></hcol>
	</hrow>
	%types_rows%
</tablegroup>

END;



$FORMS['type_edit'] = <<<END

<form method="post" action="%pre_lang%/admin/data/type_edit_do/%type_id%/" name="adding_new_page">

<setgroup name="Свойства типа" id="data_type_props" form="no">

	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="50%">
					<input br="yes" quant="no" size="58" style="width:355px">
						<name><![CDATA[name]]></name>
						<title><![CDATA[Название типа]]></title>
						<value><![CDATA[%name%]]></value>
					</input>

			</td>

			<td>
					<br />
					<checkbox  selected="%is_public%">
						<name><![CDATA[is_public]]></name>
						<title><![CDATA[Общедоступный]]></title>
						<value><![CDATA[1]]></value>
					</checkbox>
			</td>
		</tr>

		<tr>
			<td>
					<select  quant="no" br="yes" style="width: 370px;">
						<name><![CDATA[hierarchy_type_id]]></name>
						<title><![CDATA[Назначение типа]]></title>
						%hierarchy_types%
					</select>

			</td>

			<td>
					<checkbox  selected="%is_guidable%">
						<name><![CDATA[is_guidable]]></name>
						<title><![CDATA[Можно использовать как справочник]]></title>
						<value><![CDATA[1]]></value>
					</checkbox>

			</td>

		</tr>

	</table>
	<p align="right">%save_n_save%</p>

	<passthru name="exit_after_save"></passthru>
</setgroup>

<setgroup name="Поля и группы полей" id="data_type_fields" form="no">
	<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td>
				<a href="%pre_lang%/admin/data/type_group_add/%type_id%/"><img src="/images/cms/admin/%skin_path%/ico_data_add_group.gif" border="0" /></a>
			</td>

			<td>
				&nbsp;&nbsp;<a href="%pre_lang%/admin/data/type_group_add/%type_id%/">Добавить группу полей</a>
			</td>
		</tr>
	</table>

	<div id="data_fields_placer"></div>

	<p align="right">%save_n_save%</p>
</setgroup>

</form>

<script type="text/javascript">
<![CDATA[

var are_you_sure = '%are_you_sured%';

var field_types = new Array();
%field_types%

var field_groups = new Array();
%field_groups%

var fields = new Array();
%fields%

function h() {
	var tmp = new data_blocks(field_types, field_groups, fields, '%type_id%');
}

addOnLoadEvent(h);



function save_with_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "1";
	return true;
}

function save_without_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "0";
	return true;
}

function edit_cancel() {
	if(confirm("Вы уверены, что хотите выйти? Все изменения будут потеряны")) {
		var redirect_str = "%edit_cancel_redirect%";
		if(redirect_str) {
			window.location = redirect_str;
		}
	}
	return false;
}


]]>
</script>

END;


$FORMS['type_field_edit'] = <<<END

<form method="post" action="%pre_lang%/admin/data/%method%/%field_id%/%type_id%/" name="adding_new_page">

<setgroup name="Свойства поля" id="data_type_props" form="no">

	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="50%">
				<input br="yes" quant="no" size="58" style="width:355px">
					<name><![CDATA[title]]></name>
					<title><![CDATA[Название поля]]></title>
					<value><![CDATA[%title%]]></value>

					<onchange><![CDATA[javascript: go_alt_a(this);]]></onchange>
					<onkeydown><![CDATA[javascript: go_alt_a(this);]]></onkeydown>
				</input>

			</td>

			<td>
				<input br="yes" quant="no" style="width:355px">
					<name><![CDATA[name]]></name>
					<title><![CDATA[Идентификатор]]></title>
					<value><![CDATA[%name%]]></value>
				</input>

			</td>
		</tr>

		<tr>
			<td>
				<select  quant="no" br="yes" style="width: 370px;" class="std_select">
						<name><![CDATA[field_type]]></name>
						<title><![CDATA[Тип поля]]></title>
						%field_types%
					</select>
			</td>

			<td>
				<select  quant="no" br="yes" style="width: 370px;" class="std_select">
						<name><![CDATA[guide_id]]></name>
						<title><![CDATA[Использовать справочник]]></title>
						%guides_allowed%
					</select>

			</td>
		</tr>

		<tr>
			<td>
				<input br="yes" quant="no" style="width:355px">
					<name><![CDATA[tip]]></name>
					<title><![CDATA[Подсказка]]></title>
					<value><![CDATA[%tip%]]></value>
				</input>
			</td>

			<td></td>
		</tr>


		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>


		<tr>
			<td>
				<checkbox  selected="%is_visible%">
					<name><![CDATA[is_visible]]></name>
					<title><![CDATA[Видимое]]></title>
					<value><![CDATA[1]]></value>
				</checkbox>

			</td>

			<td>
				<checkbox  selected="%is_inheritable%">
					<name><![CDATA[is_inheritable]]></name>
					<title><![CDATA[Наследуемое]]></title>
					<value><![CDATA[1]]></value>
				</checkbox>
			</td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td>
				<checkbox  selected="%in_search%">
					<name><![CDATA[in_search]]></name>
					<title><![CDATA[Индексировать]]></title>
					<value><![CDATA[1]]></value>
				</checkbox>
			</td>

			<td>
				<checkbox  selected="%in_filter%">
					<name><![CDATA[in_filter]]></name>
					<title><![CDATA[Использовать в фильтрах]]></title>
					<value><![CDATA[1]]></value>
				</checkbox>
			</td>
		</tr>

		<tr>
			<td colspan="2"></td>
		</tr>
	</table>
	<br />
	<p align="right">%save_n_save%</p>

	<passthru name="exit_after_save"></passthru>
</setgroup>

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

function edit_cancel() {
	if(confirm("Вы уверены, что хотите выйти? Все изменения будут потеряны")) {
		var redirect_str = "%edit_cancel_redirect%";
		if(redirect_str) {
			window.location = redirect_str;
		}
	}
	return false;
}


]]>
</script>

END;


$FORMS['type_group_edit'] = <<<END

<form method="post" action="%pre_lang%/admin/data/%method%/%group_id%/%type_id%/" name="adding_new_page">

<setgroup name="Свойства группы полей" id="adding_new_page" form="no">

	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="50%">
				<input br="yes" quant="no" size="58" style="width:355px">
					<id><![CDATA[pname]]></id>
					<name><![CDATA[title]]></name>
					<title><![CDATA[Название группы]]></title>
					<value><![CDATA[%title%]]></value>

					<onchange><![CDATA[javascript: go_alt_a(this);]]></onchange>
					<onkeydown><![CDATA[javascript: go_alt_a(this);]]></onkeydown>
				</input>

			</td>

			<td>
				<input br="yes" quant="no" style="width:355px">
					<id><![CDATA[palt_name]]></id>
					<name><![CDATA[name]]></name>
					<title><![CDATA[Идентификатор]]></title>
					<value><![CDATA[%name%]]></value>
				</input>

			</td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>


		<tr>
			<td>
				<checkbox  selected="%is_visible%">
					<name><![CDATA[is_visible]]></name>
					<title><![CDATA[Видимое]]></title>
					<value><![CDATA[1]]></value>
				</checkbox>

			</td>

			<td>
				<checkbox  selected="%is_active%">
					<name><![CDATA[is_active]]></name>
					<title><![CDATA[Активное]]></title>
					<value><![CDATA[1]]></value>
				</checkbox>

			</td>
		</tr>


		<tr>
			<td colspan="2"></td>
		</tr>
	</table>
	<br />
	<p align="right">%save_n_save%</p>

	<passthru name="exit_after_save"></passthru>
</setgroup>

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

function edit_cancel() {
	if(confirm("Вы уверены, что хотите выйти? Все изменения будут потеряны")) {
		var redirect_str = "%edit_cancel_redirect%";
		if(redirect_str) {
			window.location = redirect_str;
		}
	}
	return false;
}


]]>
</script>

END;


$FORMS['guides'] = <<<END

<imgButton>
	<title><![CDATA[Добавить справочник]]></title>
	<src><![CDATA[/images/cms/admin/%skin_path%/ico_add.%ico_ext%]]></src>
	<link><![CDATA[%pre_lang%/admin/data/type_add/7/]]></link>
</imgButton>

<br /><br />

%pages%

<tablegroup>
	<hrow>
		<hcol><![CDATA[Название справочника]]></hcol>
		<hcol style="width: 100px;"><![CDATA[Содержание]]></hcol>
		<hcol style="width: 100px;"><![CDATA[Редактировать тип]]></hcol>
		<hcol style="width: 100px;"><![CDATA[Удалить]]></hcol>
	</hrow>
	%rows%
</tablegroup>

END;


$FORMS['guide_items'] = <<<END

<form method="post" action="%pre_lang%/admin/data/guide_items_do/%guide_id%/">

%pages%

<tablegroup>
	<hrow>
		<hcol>Наименование</hcol>
		<hcol style="width: 100px;">Редактировать</hcol>
		<hcol style="width: 100px;">Удалить</hcol>
	</hrow>
	%rows%
</tablegroup>

<p align="right">%save_n_save%</p>

</form>

END;


$FORMS['config'] = <<<END

<form method="post" action="%pre_lang%/admin/data/config_do/">

<tablegroup>
	<hrow>
		<hcol>
			Название
		</hcol>

		<hcol>
			Модуль
		</hcol>

		<hcol>
			Метод
		</hcol>

		<hcol style="width: 100px;">
			Удалить
		</hcol>
	</hrow>

	%rows%

<row>
	<col>
		<input  quant='no' style='width: 95%'>
			<name><![CDATA[title_new]]></name>
		</input>

	</col>

	<col>
		<input  quant='no' style='width: 95%'>
			<name><![CDATA[name_new]]></name>
		</input>

	</col>

	<col>
		<input  quant='no' style='width: 95%'>
			<name><![CDATA[ext_new]]></name>
		</input>

	</col>

	<col></col>
</row>

</tablegroup>

<p align="right"><submit title="Сохранить" /></p>

</form>

END;


$FORMS['trash'] = <<<END

	<p>
		<img src="/images/cms/admin/%skin_path%/ico_trash_empty.%ico_ext%" />
		<a href="%pre_lang%/admin/data/trash_empty/" commit_unrestorable="После очистки корзины, удаленные страницы невозможно будет восстановить. Вы уверены, что хотите полностью очистить корзину?"><b>Очистить корзину</b></a>
	</p>
	<br />


<tablegroup>
	<hrow>
		<hcol>
			Удаленные элементы
		</hcol>

		<hcol style="width: 150px;">
			Дата удаления
		</hcol>


		<hcol style="width: 100px;">
			Восстановить
		</hcol>


		<hcol style="width: 100px;">
			Удалить
		</hcol>
	</hrow>
	%rows%
</tablegroup>

END;



$FORMS['guide_item_edit'] = <<<TREE_SECTION_ADD

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


frm = document.forms['adding_new_page'];
frm.onsubmit = acf_check;



cifi_upload_text = '%catalog_cifi_upload_text%';
]]>
</script>

<form method="post" name="adding_new_page" action="%pre_lang%/admin/data/guide_item_edit_do/%item_id%/%guide_id%/">


<setgroup name="Общие параметры" id="data_guide_item" form="no">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="50%">
				<input br="yes" quant="no" size="58" style="width:355px">
					<id><![CDATA[pname]]></id>
					<name><![CDATA[name]]></name>
					<title><![CDATA[Название]]></title>
					<value><![CDATA[%name%]]></value>
				</input>

			</td>

			<td></td>
		</tr>
	</table>

	<p align="right">%save_n_save%</p>
</setgroup>





%data_field_groups%

	<passthru name="exit_after_save"></passthru>
</form>


TREE_SECTION_ADD;


?>