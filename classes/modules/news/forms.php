<?php

$FORMS = Array();


$FORMS['lists'] = <<<END

	<imgButton>
		<title><![CDATA[Добавить новость]]></title>
		<src>/images/cms/admin/%skin_path%/ico_news_item_add.%ico_ext%</src>
		<link>%pre_lang%/admin/news/add_item/%curr_rel%/</link>
	</imgButton>

	<imgButton>
		<title><![CDATA[Добавить ленту новостей]]></title>
		<src>/images/cms/admin/%skin_path%/ico_news_lent_add.%ico_ext%</src>
		<link>%pre_lang%/admin/news/add_list/%curr_rel%/</link>
	</imgButton>

<p />

%pages%

<tablegroup>
	<header>
		<hcol style="text-align: left"><![CDATA[Ленты и новости]]></hcol>
		<hcol style="width: 100px"><![CDATA[Активность]]></hcol>
		<hcol style="width: 100px"><![CDATA[Изменить]]></hcol>
		<hcol style="width: 100px"><![CDATA[Удалить]]></hcol>
	</header>
%rows%
</tablegroup>


END;


$FORMS['last_lists'] = <<<END

%pages%

<tablegroup>
	<header>
		<hcol style="text-align: left"><![CDATA[Последние новости]]></hcol>
		<hcol style="width: 100px"><![CDATA[Активность]]></hcol>
		<hcol style="width: 100px"><![CDATA[Изменить]]></hcol>
		<hcol style="width: 100px"><![CDATA[Удалить]]></hcol>
	</header>
%rows%
</tablegroup>


END;



$FORMS['add_list'] = <<<END
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


cifi_upload_text = '%news_cifi_upload_text%';
]]>
</script>

<form method="post" name="adding_new_page" action="%pre_lang%/admin/news/%method%/%parent_id%/%rubric_id%">

	<setgroup name="Редактирование ленты новостей" id="news_add_list" form="no">
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
						<title><![CDATA[Активная]]></title>
						<value><![CDATA[1]]></value>
						<tip><![CDATA[%tip_is_active%]]></tip>
					</checkbox>

					<br/>
				</td>


            </tr>
			<tr>
				<td colspan="2" class="ftext" style="text-align: left">
					<![CDATA[Комментарий]]><br/>
					<textarea style="width: 100%; height: 173px" name="dev_desc"><![CDATA[%dev_desc%]]></textarea>

				</td>
			</tr>
		</table>

		<p align="right">%save_n_save%</p>

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
						<name><![CDATA[is_default]]></name>
						<title><![CDATA[Страница по-умолчанию]]></title>
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

	<passthru name="exit_after_save"></passthru>

</form>


%backup_panel%

END;


$FORMS['subjects'] = <<<END


<table border="0">
	<tr>
		<td width="20">
			<img src="/images/cms/admin/%skin_path%/ico_news_subject_add.%ico_ext%" />
		</td>

		<td>
			<a href="%pre_lang%/admin/news/subjects_add/" class="glink">Добавить сюжет</a>
		</td>
	</tr>
</table>

<br />

<tablegroup>

<hrow>
	<hcol style="text-align: left">
		Название сюжета
	</hcol>
	<hcol style="width:100px;">Изменить</hcol>
	<hcol style="width:100px;">Удалить</hcol>
</hrow>

%srows%

</tablegroup>

END;




$FORMS['add_item'] = <<<END

<form method="post" name="adding_new_page" enctype="multipart/form-data" action="%pre_lang%/admin/news/%method%/%parent_id%/%element_id%/">


	<setgroup name="Редактирование новости" id="news_add_list" form="no">
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
				<td>
					<input   class="" br="yes" size="58" quant="no" style="width:355px">
						<id><![CDATA[posttime]]></id>
						<name><![CDATA[posttime]]></name>
						<title><![CDATA[Дата публикации]]></title>
						<tip><![CDATA[%tip_publish_time%]]></tip>
						<value><![CDATA[%posttime%]]></value>
					</input>
				</td>

				<td>
					<br />
					<checkbox selected="%is_active%">
						<name><![CDATA[is_active]]></name>
						<title><![CDATA[Активная]]></title>
						<tip><![CDATA[%tip_is_active%]]></tip>
						<value><![CDATA[1]]></value>
					</checkbox>
				</td>
			</tr>

		</table>

		<br />

		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td height="19" class="ntext">
					Анонс новости
				</td>
			</tr>

			<tr>
				<td class="ntext">
					<wysiwyg id="anons" height="140"><![CDATA[%anons%]]></wysiwyg>
				</td>
			</tr>
		</table>

		<br />

		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td height="19" class="ntext">
					Текст новости
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

				cifi_upload_text = '%news_cifi_upload_text%';
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

END;



$FORMS['subjects'] = <<<END

<form method="post" action="%pre_lang%/admin/news/subjects_do/">

	<tablegroup>
		<hrow>
			<hcol>
				Сюжеты
			</hcol>

			<hcol style="width: 100px; text-align: center;">
				Удалить
			</hcol>
		</hrow>
		%rows%

		<row>
			<col>
				<input type="text" quant="no" style="width: 97%;">
					<name><![CDATA[subject_new]]></name>
				</input>
			</col>

			<col></col>
		</row>
	</tablegroup>

	<p align="right"><submit title="Сохранить изменения" /></p>
</form>

END;

$FORMS['config'] = <<<END

<form action="%pre_lang%/admin/news/config_do/">
	<setgroup name="Настройка новостей" id="params_cont" form="no">
		<tablegroup>
			<row>
				<col>Количество новостей на странице</col>
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





?>