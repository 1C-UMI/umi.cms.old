<?php

$FORMS = Array();


$FORMS['directory_list'] = <<<END

	<script type="text/javascript">
				<![CDATA[

					function js_base64_encode(sStr) {
						var sWinChrs = 'АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдежзийклмнопрстуфхцчшщъыьэюя'
						var sBase64Chrs  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
						var arrBase64  = sBase64Chrs.split('')

						var a = new Array();
						var i = 0;
						for(i=0; i<sStr.length; i++ ) {
							var cch=sStr.charCodeAt(i)
							if (cch>127) {
								cch=sWinChrs.indexOf(sStr.charAt(i))+163; if(cch<163) continue; 
							}
							a.push(cch)
						};
						var s=Array(), lPos = a.length - a.length % 3
						for (i=0; i<lPos; i+=3) {
							var t=(a[i]<<16)+(a[i+1]<<8)+a[i+2]
							s.push(arrBase64[(t>>18)&0x3f]+arrBase64[(t>>12)&0x3f]+arrBase64[(t>>6)&0x3f]+arrBase64[t&0x3f] )
						}
						switch (a.length-lPos) {
							case 1 : var t=a[lPos]<<4; s.push(arrBase64[(t>>6)&0x3f]+arrBase64[t&0x3f]+'=='); break
							case 2 : var t=(a[lPos]<<10)+(a[lPos+1]<<2); s.push(arrBase64[(t>>12)&0x3f]+arrBase64[(t>>6)&0x3f]+arrBase64[t&0x3f]+'='); break
						}
						return s.join('')
					}

					function fs_rename_dlg(s_link, old_name) {
						var href = s_link;
						
						sNewName = "";
						var callback = function () {
							if(href && sNewName.length > 0) {
								document.location.href = href+"/"+escape(sNewName.replace(".", "[dot]"));
							}
						};

						var contDiv = document.createElement("div");
						var html = "<div style='margin: 15px;'><h1>Переименовать?</h1>";
						html += "<p>Введите новое имя:<br />";
						html += "<input onchange='sNewName = this.value;' type='text' value='"+old_name+"' style='width:200px;' />";
						html += "<p align='right'><input type='button' value='Переименовать' style='width: 100px; font-weight: bold;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().callCallbackFunction();' id='confirmButton' /> <input type='button' value='Отменить' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
						html += "</div>";

						contDiv.innerHTML = html;

						var up = new umiPopup();
							up.setSize(350, 110);
						up.setCallbackFunction(callback);
						up.open();
						up.setContent(contDiv);

						var cfmButton = document.getElementById('confirmButton');
						if(cfmButton) {
							cfmButton.focus();
						}


						return false;
					}

					function fs_md_dlg(s_link) {
						var href = s_link;
						
						sNewName = "";
						var callback = function () {
							if(href && sNewName.length > 0) {
								document.location.href = href+"/"+escape(sNewName.replace(".", "[dot]"));
							}
						};

						var contDiv = document.createElement("div");
						var html = "<div style='margin: 15px;'><h1>Создать директорию?</h1>";
						html += "<p>Введите имя директории:<br />";
						html += "<input onchange='sNewName = this.value;' type='text' style='width:200px;' />";
						html += "<p align='right'><input type='button' value='Создать' style='width: 100px; font-weight: bold;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().callCallbackFunction();' id='confirmButton' /> <input type='button' value='Отменить' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
						html += "</div>";

						contDiv.innerHTML = html;

						var up = new umiPopup();
							up.setSize(350, 110);
						up.setCallbackFunction(callback);
						up.open();
						up.setContent(contDiv);

						var cfmButton = document.getElementById('confirmButton');
						if(cfmButton) {
							cfmButton.focus();
						}


						return false;
					}
					
					function fs_change_dir() {
						oNewDir = document.getElementById('fs_path');
						if (oNewDir) {
							sNewDir = js_base64_encode('%root_path%/'+oNewDir.value);
							document.location.href = '%pre_lang%/admin/filemanager/directory_list/'+sNewDir;
						}
					}
					
					function fs_add_to_upload(oFileInput) {
						var oReadyUpload = document.getElementById('fs_ready_upload');
						var oNextUploadDiv = document.getElementById('fs_next_upload');
						if (oFileInput && oReadyUpload && oNextUploadDiv) {
							oFileInput.style.visibility = "hidden";
							oFileInput.style.display = "none";
							var oNextFile = document.createElement('DIV');
							oNextFile.innerHTML = oFileInput.value;
							oReadyUpload.appendChild(oNextFile);

							var oNextUpload = document.createElement('INPUT');
							oNextUpload.type = 'file';
							oNextUpload.name = 'fs_upl_files[]';
							oNextUpload.onchange = function() {
								fs_add_to_upload(oNextUpload);
							}

							oNextUploadDiv.appendChild(oNextUpload);
						}
						
					}
				]]>

	</script>


	<imgButton>
		<src><![CDATA[/images/cms/admin/%skin_path%/ico_add.%ico_ext%]]></src>
		<link>#</link>
		<onclick>fs_md_dlg('%pre_lang%/admin/filemanager/make_directory');</onclick>
		<title><![CDATA[Создать директорию]]></title>
	</imgButton>

	<br /><br />

	<setgroup name="Текущая директория" id="fs_current_dir" form="no">
		<input br="no" quant="no" size="58" style="width:355px">
			<id><![CDATA[fs_path]]></id>
			<name><![CDATA[fs_path]]></name>
			<value><![CDATA[%current_path%]]></value>
		</input>
		<button title="Перейти" onclick="fs_change_dir();" />
	</setgroup>
	
	

	<tablegroup>
		<hrow>
			<hcol>Содержимое папки</hcol>
			<hcol style="width: 100px">Переименовать</hcol>
			<hcol style="width: 100px">Сделать скачиваемым</hcol>
			<hcol style="width: 100px">Удалить</hcol>
		</hrow>
		
		<row>
			<col colspan="4">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<a href="%uplink%"><img src="/images/cms/admin/%skin_path%/ico_folder_up.%ico_ext%" alt="Вверх.." title="Вверх.." border="0" /></a>
					</td>
					<td style="padding-left:5px;">
						<a href="%uplink%">вверх</a>
					</td>
				</tr>
				</table>
			</col>
		</row>

		%rows%

	</tablegroup>
	
	<br />

	<setgroup name="Закачать файлы" id="fs_grp_files" form="no">
		<form method="post" name="fs_upload_frm" enctype="multipart/form-data" action="%pre_lang%/admin/filemanager/directory_list/">

			<div id="fs_ready_upload">
					
			</div>

			<div id="fs_next_upload" style="padding-top:10px;">
				<file name="fs_upl_files[]" onchange="fs_add_to_upload(this)" size="50" />
			</div>

			<p align="right">
				<submit title="Закачать" />
			</p>
		</form>
	</setgroup>
END;

$FORMS['directory_list_dir'] = <<<END
	<row>
		<col>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<a href="%link%"><img src="/images/cms/admin/%skin_path%/ico_folder.%ico_ext%" alt="Директория" title="Директория" border="0" /></a>
					</td>
					<td style="padding-left:5px;">
						<a href="%link%">%name%</a>
					</td>
				</tr>
			</table>
		</col>
		<col style="text-align: center;">
			%rename_link%
		</col>
		<col style="text-align: center;">

		</col>
		<col style="text-align: center;">
			%remove_link%
		</col>
	</row>
END;


$FORMS['directory_list_file'] = <<<END
	<row>
		<col>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<img src="/images/cms/admin/%skin_path%/ico_file.%ico_ext%" alt="Файл" title="Файл" border="0" />
					</td>
					<td style="padding-left:5px;">
						%name%
					</td>
				</tr>
			</table>
		</col>
		<col style="text-align: center;">
			%rename_link%
		</col>
		<col style="text-align: center;">
			%share_link%
		</col>
		<col style="text-align: center;">
			%remove_link%
		</col>
	</row>

END;

$FORMS['shared_files'] = <<<END
	<imgButton>
		<src><![CDATA[/images/cms/admin/%skin_path%/ico_add.%ico_ext%]]></src>
		<link><![CDATA[%pre_lang%/admin/filemanager/add_shared_file/]]></link>
		<title><![CDATA[Добавить файл]]></title>
	</imgButton>

	<br /><br />
	%pages%
	<tablegroup>
		<hrow>
			<hcol>Список файлов</hcol>
			<hcol style="width: 100px">Активность</hcol>
			<hcol style="width: 100px">Изменить</hcol>
			<hcol style="width: 100px">Удалить</hcol>
		</hrow>

	%rows%
	</tablegroup>

END;

$FORMS['shared_files_line'] = <<<END

	<row>
		<col>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<a href="%pre_lang%/admin/filemanager/edit_shared_file/%element_id%/"><img src="/images/cms/admin/%skin_path%/ico_file.%ico_ext%" alt="Файл" title="Файл" border="0" /></a>
					</td>
					<td style="padding-left:5px;">
						<a href="%pre_lang%/admin/filemanager/edit_shared_file/%element_id%/"><![CDATA[%name%]]></a> %core getTypeEditLink(%type_id%)%
					</td>
				</tr>
			</table>

			<br />
			<table border="0">
				<tr>
					<td style="width: 150px;">
						Путь к файлу:
					</td>

					<td>
						%file_path%
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						Размер файла:
					</td>

					<td>
						%file_size% Кб
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						Количество скачиваний:
					</td>

					<td>
						%downloads%
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
			<a href="%pre_lang%/admin/filemanager/edit_shared_file/%element_id%/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" border="0" alt="Редактировать" title="Редактировать" /></a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/filemanager/del_shared_file/%element_id%/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" border="0" alt="Удалить" title="Удалить" /></a>
		</col>
	</row>

END;

$FORMS['edit_shared_file'] = <<<END
<script type="text/javascript">
			<![CDATA[
				function init_me() {
					var df = document.forms['adding_new_page'];
					if (df) {
						def_value = df.name.value;
						def_alt = df.alt_name.value;
					}
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

				cifi_upload_text = '%fs_cifi_upload_text%';
			]]>
		</script>
<form method="post" name="adding_new_page" enctype="multipart/form-data" action="%pre_lang%/admin/filemanager/%method%/%element_id%/">


	<setgroup name="Редактирование раздела" id="news_add_list" form="no">
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
						<name><![CDATA[meta_descriptions]]></name>
						<title><![CDATA[Описания (meta name="DESCRIPTIONS")]]></title>
						<value><![CDATA[%meta_descriptions%]]></value>
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
						<title><![CDATA[Тип страницы]]></title>
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
				<td>
					<span class="ftext">Скачиваемый файл</span>
					%cifi_shared_file%
				</td>

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

			<tr>
				<td>
					<input class="" br="yes" size="58" quant="no" style="width:355px">
						<id><![CDATA[downloads]]></id>
						<name><![CDATA[downloads]]></name>
						<title><![CDATA[Количество скачиваний]]></title>
						<value><![CDATA[%downloads%]]></value>
					</input>
				</td>


				<td>
				
				</td>
			</tr>
		</table>

		<br />

		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td height="19" class="ntext">
					Описание файла
				</td>
			</tr>

			<tr>
				<td class="ntext">
					<wysiwyg id="descr"><![CDATA[%descr%]]></wysiwyg>
				</td>
			</tr>
		</table>

		<p align="right">%save_n_save%</p>

		<passthru name="parent">%curr_rel%</passthru>
		<passthru name="mode">admin</passthru>
		<passthru name="target_domain">%domain%</passthru>
		<passthru name="exit_after_save"></passthru>
		<passthru name="quickmode">%quickmode%</passthru>


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

?>