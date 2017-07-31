<?php


$FORMS = Array();

$FORMS['sitetree'] = <<<END

%all_domains%

<script type="text/javascript">
//alert(to_open);
for(i = 0; i &#60; to_open.length; i++) {
	sitetree_switch(to_open[i], "ONLOAD");
}

</script>

END;


$FORMS['add_page'] = <<<END
<form method="post" name="adding_new_page" enctype="multipart/form-data" action="%pre_lang%/admin/content/%method%/%pid%">

<setgroup name="Редактор содержимого" id="add_page_wysiwyg" form="no">

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
				<input   class="" br="yes" quant="no" style="width:355px">
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
				<input  autocheck="yes"   class="" br="yes" quant="no" style="width:355px">
					<id><![CDATA[ptitle]]></id>
					<name><![CDATA[title]]></name>
					<title><![CDATA[<TITLE>]]></title>
					<value><![CDATA[%title%]]></value>
					<tip><![CDATA[%tip_title%]]></tip>
				</input>

			</td>

			<td>
				<input   class="" br="yes" quant="no" style="width:355px">
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
					<id><![CDATA[h1]]></id>
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
			<td>

</td>
			<td>
			<br />

				<checkbox selected="%is_active%">
					<name><![CDATA[is_active]]></name>
					<title><![CDATA[Активен]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_is_active%]]></tip>
				</checkbox></td>
		</tr>
	</table>

<br/>
<wysiwyg id="content"><![CDATA[%content%]]></wysiwyg>

<p align="right">%save_n_save%</p>

<passthru name="pid">%pid%</passthru>
<passthru name="parent_id">%parent%</passthru>
<passthru name="mode">admin</passthru>
<passthru name="module">content</passthru>
<passthru name="method">%method%</passthru>
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


cifi_upload_text = '%content_cifi_upload_text%';
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
					<title><![CDATA[Показывать подменю]]></title>
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
					<name><![CDATA[def]]></name>
					<title><![CDATA[Страница по-умолчанию]]></title>
					<value><![CDATA[1]]></value>
					<tip><![CDATA[%tip_is_default%]]></tip>
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

</form>

%files_panel%

%backup_panel%

<br />

END;

$FORMS['config'] = <<<END
<form method='post' action='%pre_lang%/admin/content/templates_do/'>

 %templates_list%

<p align='left'><submit title='Сохранить' /></p>
<passthru name='mode'>admin</passthru>
<passthru name='module'>content</passthru>
<passthru name='method'>templates_do</passthru>
</form>
END;


$FORMS['edit_domain'] = <<<END

<tablegroup>

	<hrow>
		<hcol style="text-align: center;" colspan="2">Домен %domain_name%</hcol>
	</hrow>

	<row>
		<col style="width: 30%;">Префикс для TITLE</col>
		<col>
			<input  quant="no" style="width: 510px">
					<name><![CDATA[title_prefix[%domain_id%]]]></name>
					<value><![CDATA[%title_prefix%]]></value>
			</input>

		</col>
	</row>

	<row>
		<col>Keywords (по-умолчанию)</col>
		<col>
			<input  quant="no" style="width: 510px">
					<name><![CDATA[keywords[%domain_id%]]]]></name>
					<value><![CDATA[%keywords%]]></value>
			</input>

		</col>
	</row>

	<row>
		<col>Description (по-умолчанию)</col>
		<col>
			<input  quant="no" style="width: 510px">
					<name><![CDATA[description[%domain_id%]]]]></name>
					<value><![CDATA[%description%]]></value>
			</input>

		</col>
	</row>


</tablegroup>
<br/>
END;


$FORMS['tree_link'] = <<<END
<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<head>
		<title>Inserting tree-links...</title>
		<link href="/styles/xsl/umicms.css" type="text/css" rel="stylesheet">

<style type="text/css">
html, body {
  background: ButtonFace;
  color: ButtonText;
  font: 11px Tahoma,Verdana,sans-serif;
  margin: 0px;
  padding: 0px;
}
body { padding: 5px; }
table {
  font: 11px Tahoma,Verdana,sans-serif;
}
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
table .label { text-align: right; width: 8em; }

.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}

#buttons {
      margin-top: 1em; border-top: 1px solid #999;
      padding: 2px; text-align: right;
}
</style>


		<script type="text/javascript" src="/js/cms/jsonRequestsController.js"></script>


		<script type="text/javascript">
			function retURL(selObj) {
				if(!selObj) {
					var selObj = document.getElementById('treePagesSelection');
				}

				var umiPopup = window.parent.umiPopup.getSelf();
				umiPopup.setReturnValue("%content get_page_url(" + selObj.value + ")%");
				umiPopup.callCallbackFunction();
				umiPopup.close();
			}

			function onClose() {
				window.parent.umiPopup.getSelf().close();
			}

			document.onkeydown = function(e) {
		        	var is_ie = !(navigator.appName.indexOf("Netscape") != -1);

				if(!is_ie) {
					event = e;
				}

				if(event.keyCode == 27) {
					onClose();
				}
			}

			function onInit() {
				loadHierarchyLevel(0);
			}

			var navTree = new Array();
			var currentRel = 0;
			var currentNodes = null;


			function loadHierarchyLevel(parentId) {
				parentId = parseInt(parentId);

				if(!navTree[parentId]) {
					navTree[parentId] = 	{
									"visited":	true,
									"id":		currentRel
								};
				}
				currentRel = parentId;


				function handler(args) {
					rerenderSelectList(args);
				}

				var url = "%pre_lang%/admin/content/json_load_hierarchy/?requestParam=" + parentId;

				jsonRequestsController.getInstance().sendRequest(url, handler);
			}


			function rerenderSelectList(nodes) {
				currentNodes = nodes;
				var selObj = document.getElementById('treePagesSelection');
				selObj.innerHTML = "";

				if(currentRel) {
					var optionObj = document.createElement("option");
					optionObj.innerHTML = "&lt; На уровень вверх &gt;";
					optionObj.value = (navTree[currentRel]) ? navTree[currentRel].id : 0;
					selObj.appendChild(optionObj);
				}


				var i;
				for(i in nodes) {
					var node = nodes[i];
					var optionObj = document.createElement("option");
					optionObj.innerHTML = node.title + ( (parseInt(node.childs_count) > 0) ? " (" + node.childs_count + ")" : "" );
					optionObj.value = node.id;
					selObj.appendChild(optionObj);
				}
			}

			function tryChangeHierarchyLevel(pageId) {
				pageId = parseInt(pageId);
				var isReloadAllowed = true;

				var i = 0;
				for(i in currentNodes) {
					var node = currentNodes[i];
					if(node.id == pageId) {
						if(parseInt(node.childs_count) == 0) {
							isReloadAllowed = false;
						}
						break;
					}
				}

				if(isReloadAllowed) {
					loadHierarchyLevel(pageId);
					return true;
				} else {
					return false;
				}
			}

		</script>
	</head>

	<body style="margin: 0px;" onload="javascript: onInit();">

<div class="title">Выберите страницу сайта</div>

	<select style="width: 515px; height: 167px; border: 0px; margin: 0px;" onDblClick="javascript: tryChangeHierarchyLevel(this.value);" id="treePagesSelection" size="10">
	</select>


<div id="buttons">
  <button type="button" name="ok" onclick="return retURL(document.getElementById('lnk_sel')); return false;">OK</button>
  <button type="button" name="cancel" onclick="return onClose();">Cancel</button>
</div>
<div id="placer"></div>
	</body>
</html>


END;


$FORMS['insertimage'] = <<<END

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Вставка</title>
		<link href="/styles/xsl/umicms.css" type="text/css" rel="stylesheet">

<style type="text/css">
html, body {
  background: ButtonFace;
  color: ButtonText;
  font: 11px Tahoma,Verdana,sans-serif;
  margin: 0px;
  padding: 0px;
}
body { padding: 5px; }
table {
  font: 11px Tahoma,Verdana,sans-serif;
}
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
table .label { text-align: right; width: 8em; }

.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}

#buttons {
      margin-top: 1em; border-top: 1px solid #999;
      padding: 2px; text-align: right;
}
</style>


		<script>
			function retURL(selObj) {
				var umiPopup = window.parent.umiPopup.getSelf();
				umiPopup.setReturnValue(selObj.value);
				umiPopup.callCallbackFunction();
				umiPopup.close();
			}

			function onClose() {
				window.parent.umiPopup.getSelf().close();
			}


	document.onkeydown = function(e) {
        	var is_ie = !(navigator.appName.indexOf("Netscape") != -1);

		if(!is_ie) {
			event = e;
		}

		if(event.keyCode == 27) {
			onClose();
		}
	}
		</script>

	</head>

	<body style="margin: 0px;">

<div class="title">Выберите изображение</div>

         <select style="width: 495px; height: 167px; border: 0px; margin: 0px;" onDblClick="javascript: retURL(this);" id="lnk_sel" size="10">
          %lines%
         </select>

<form id="download" method="post" enctype="multipart/form-data" action="%pre_lang%/admin/content/insertimage_do/" style="margin: 0px;">
	<br />
	Закачать изображение:<br />
	<input type="file" name="pics[new]" size="60" /> <input type="submit" value="Закачать" />
</form>

<div id="buttons">
  <button type="button" name="ok" onclick="return retURL(document.getElementById('lnk_sel')); return false;">OK</button>
  <button type="button" name="cancel" onclick="return onClose();">Cancel</button>
</div>

	</body>
</html>

END;


?>