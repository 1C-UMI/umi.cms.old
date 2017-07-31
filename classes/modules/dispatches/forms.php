<?php

$FORMS = Array();


$FORMS['dispatches_list'] = <<<DISPLIST
	<imgButton>
		<title><![CDATA[Добавить рассылку]]></title>
		<src>/images/cms/admin/%skin_path%/ico_add.%ico_ext%</src>
		<link>%pre_lang%/admin/dispatches/dispatch_add/</link>
	</imgButton>

	<br /><br />

	<tablegroup>
		<header>
			<hcol style="text-align: left"><![CDATA[Название рассылки]]></hcol>
			<hcol style="width: 100px"><![CDATA[Подписчики]]></hcol>
			<hcol style="width: 100px"><![CDATA[Последний выпуск]]></hcol>
			<hcol style="width: 100px"><![CDATA[Изменить]]></hcol>
			<hcol style="width: 100px"><![CDATA[Удалить]]></hcol>
		</header>
	%rows%
	</tablegroup>	
DISPLIST;

$FORMS['dispatches_list_row'] = <<<DISPLISTROW
	<row>
		<col>
			<a href="%pre_lang%/admin/dispatches/dispatch_edit/%disp_id%/"><b><![CDATA[%disp_name%]]></b></a>
			<br /><br />
			Описание	: <![CDATA[%disp_description%]]>
		</col>
		<col style="text-align: center;">
			<a href="%pre_lang%/admin/dispatches/subscribers_list/%disp_id%/"><img src="/images/cms/admin/%skin_path%/ico_group.%ico_ext%" title="Подписчики" alt="Подписчики" border="0" /></a>
		</col>
		<col style="text-align: center;">
			<![CDATA[%disp_last_release%]]>
		</col>
		<col style="text-align: center;">
			<a href="%pre_lang%/admin/dispatches/dispatch_edit/%disp_id%/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" /></a>
		</col>
		<col style="text-align: center;">
			<a href="%pre_lang%/admin/dispatches/dispatch_del/%disp_id%/" commit_unrestorable="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" /></a>
		</col>
	</row>
DISPLISTROW;

$FORMS['release_messages'] = <<<DISPNEWREL

<form method="post" name="disp_release_frm" enctype="multipart/form-data" action="%pre_lang%/admin/dispatches/release_send/%disp_id%/">
		<setgroup name="Сообщения нового выпуска" id="disp_msgs_list" form="no">
			%messages_list%
			<p align="right">
				<submit title="Отправить" />
			</p>
		</setgroup>
</form>

DISPNEWREL;

$FORMS['dispatch_toolbar'] = <<<DISPTOOLBAR
	<imgButton>
		<title><![CDATA[Добавить сообщение]]></title>
		<src>/images/cms/admin/%skin_path%/ico_add.%ico_ext%</src>
		<link>%pre_lang%/admin/dispatches/message_add/%disp_id%/</link>
	</imgButton>
	<imgButton>
		<title><![CDATA[Архив выпусков]]></title>
		<src>/images/cms/admin/%skin_path%/ico_subitems.%ico_ext%</src>
		<link>%pre_lang%/admin/dispatches/releasees_list/%disp_id%/</link>
	</imgButton>
	<imgButton>
		<title><![CDATA[Подписчики на рассылку]]></title>
		<src>/images/cms/admin/%skin_path%/ico_group.%ico_ext%</src>
		<link>%pre_lang%/admin/dispatches/subscribers_list/%disp_id%/</link>
	</imgButton>
<br /><br />
DISPTOOLBAR;

$FORMS['dispatch_edit'] = <<<DISPEDIT
	<script type="text/javascript">
	<![CDATA[
		cifi_upload_text = '%dispatches_cifi_upload_text%';
	]]>
	</script>
	%dispatch_toolbar%
	<form method="post" name="disp_edt_frm" enctype="multipart/form-data" action="%pre_lang%/admin/dispatches/%method%/">
		<setgroup name="Основные свойства рассылки" id="disp_edit_common" form="no">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<input br="yes" quant="no" size="58" style="width:355px">
							<id><![CDATA[disp_name]]></id>
							<name><![CDATA[disp_name]]></name>
							<title><![CDATA[Название]]></title>
							<value>%disp_name%</value>

							<onchange><![CDATA[javascript: go_alt(this);]]></onchange>
							<onkeydown><![CDATA[javascript: go_alt(this);]]></onkeydown>
						</input>
					</td>
				</tr>
				<tr>
					<td>
						Описание<br />
						<textarea name="disp_description" style="width: 99%; height: 56px;">%disp_description%</textarea>
					</td>
				</tr>
			</table>
			<p align="right">%control_bar%</p>
		</setgroup>

		%data_field_groups%

		<passthru name="after_save_act"></passthru>

	</form>
	
	%release_messages%

	<script type="text/javascript">
		<![CDATA[
			function edtWithExit() {
				document.forms['disp_edt_frm'].after_save_act.value = "exit";
				return acf_check(1);
			}

			function edtWithEdit() {
				document.forms['disp_edt_frm'].after_save_act.value = "edit";
				return acf_check(1);
			}
			function edtCancel() {
				if(confirm("Вы уверены, что хотите выйти? Все изменения будут потеряны")) {
					redirect_str = "%pre_lang%/admin/dispatches/dispatches_list/";
					if(redirect_str) {
						window.location = redirect_str;
					}
				}
				return false;
			}

			oEdtFrm = document.forms['disp_edt_frm'];
			oEdtFrm.onsubmit = acf_check;
			acf_inputs_test[acf_inputs_test.length] = Array('disp_name', 'Enter dispatch name');
		]]>
	</script>
DISPEDIT;

$FORMS['messages_list'] = <<<DISPLIST
		<tablegroup>
			<header>
				<hcol style="text-align: left">Имя сообщения</hcol>
				<hcol style="width: 100px">Изменить</hcol>
				<hcol style="width: 100px">Удалить</hcol>
			</header>
			%rows%
		</tablegroup>
DISPLIST;

$FORMS['messages_list_row'] = <<<DISPLISTROW
	<row>
		<col>
			<a href="%pre_lang%/admin/dispatches/message_edit/%mess_id%/"><b><![CDATA[%mess_name%]]></b></a>
		</col>
		<col style="text-align: center;">
			<a href="%pre_lang%/admin/dispatches/message_edit/%mess_id%/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" /></a>
		</col>
		<col style="text-align: center;">
			<a href="%pre_lang%/admin/dispatches/message_del/%mess_id%/" commit_unrestorable="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" /></a>
		</col>
	</row>
DISPLISTROW;

$FORMS['messages_list_not_editable'] = <<<DISPLIST
		<tablegroup>
			<header>
				<hcol style="text-align: left">Имя сообщения</hcol>
			</header>
			%rows%
		</tablegroup>
DISPLIST;

$FORMS['messages_list_row_not_editable'] = <<<DISPLISTROW
	<row>
		<col>
			<a href="%pre_lang%/admin/dispatches/message_edit/%mess_id%/"><b><![CDATA[%mess_name%]]></b></a>
		</col>
	</row>
DISPLISTROW;

$FORMS['message_edit'] = <<< SBSLIST
	<script type="text/javascript">
	<![CDATA[
		cifi_upload_text = '%dispatches_cifi_upload_text%';
	]]>
	</script>

	<form method="post" name="msg_edt_frm" enctype="multipart/form-data" action="%pre_lang%/admin/dispatches/%method%/">
		<setgroup name="Основные свойства сообщения" id="msg_edit_common" form="no">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<input br="yes" quant="no" size="58" style="width:355px">
							<id><![CDATA[msg_name]]></id>
							<name><![CDATA[msg_name]]></name>
							<title><![CDATA[Название:]]></title>
							<value>%msg_name%</value>

							<onchange><![CDATA[javascript: go_alt(this);]]></onchange>
							<onkeydown><![CDATA[javascript: go_alt(this);]]></onkeydown>
						</input>
					</td>
					<td>
						<input br="yes" quant="no" size="58" style="width:355px">
							<id><![CDATA[msg_header]]></id>
							<name><![CDATA[msg_header]]></name>
							<title><![CDATA[Заголовок:]]></title>
							<value>%msg_header%</value>
						</input>
					</td>
				</tr>
				<tr>
					<td colspan="2">
							%msg_body%
					</td>
				</tr>
			</table>
			<p align="right">%control_bar%</p>
		</setgroup>

		%data_field_groups%

		<passthru name="after_save_act"></passthru>

	</form>

	<script type="text/javascript">
		<![CDATA[
			function edtWithExit() {
				document.forms['msg_edt_frm'].after_save_act.value = "exit";
				return acf_check(1);
			}

			function edtWithEdit() {
				document.forms['msg_edt_frm'].after_save_act.value = "edit";
				return acf_check(1);
			}
			function edtCancel() {
				if(confirm("Вы уверены, что хотите выйти? Все изменения будут потеряны")) {
					redirect_str = "%cancel_redirect%";
					if(redirect_str) {
						window.location = redirect_str;
					}
				}
				return false;
			}

			oEdtFrm = document.forms['msg_edt_frm'];
			oEdtFrm.onsubmit = acf_check;
			acf_inputs_test[acf_inputs_test.length] = Array('msg_name', 'Enter message name');
		]]>
	</script>
SBSLIST;

$FORMS['subscribers_list'] = <<< SBSLIST
	<imgButton>
		<title><![CDATA[Добавить подписчика]]></title>
		<src>/images/cms/admin/%skin_path%/ico_add.%ico_ext%</src>
		<link>%pre_lang%/admin/dispatches/subscriber_add/</link>
	</imgButton>

	<br /><br />
	%pages%
	<tablegroup>
		<header>
			<hcol style="text-align: center;width: 15px;">№</hcol>
			<hcol style="text-align: left;">Информация о подписчике</hcol>
			<hcol style="width: 100px">Изменить</hcol>
			<hcol style="width: 100px">Удалить</hcol>
		</header>
	%rows%
	</tablegroup>	
SBSLIST;

$FORMS['subscribers_list_row'] = <<< SBSLISTROW
	<row>
		<col>
			%sbs_num%
		</col>
		<col>
			<a href="%pre_lang%/admin/dispatches/subscriber_edit/%sbs_id%/"><b><![CDATA[%sbs_name%]]></b></a>
			<br />
			<table border="0">
			<tr>
				<td style="width:150px;">
					Статус:
				</td>
				<td>
					%sbs_status%
				</td>
			</tr>
			<tr>
				<td style="width:130px;">
					Дата первой подписки:
				</td>
				<td>
					%sbs_date%
				</td>
			</tr>
			<tr>
				<td>
					Подписан на рассылки:
				</td>
				<td>
					%sbs_dispatches%
				</td>
			</tr>
			</table>
		</col>
		<col style="text-align: center;">
			<a href="%pre_lang%/admin/dispatches/subscriber_edit/%sbs_id%/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" /></a>
		</col>
		<col style="text-align: center;">
			<a href="%pre_lang%/admin/dispatches/subscriber_del/%sbs_id%/" commit_unrestorable="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" /></a>
		</col>
	</row>
SBSLISTROW;

$FORMS['subscriber_edit'] = <<< SBSEDIT
	<script type="text/javascript">
	<![CDATA[
		cifi_upload_text = '%dispatches_cifi_upload_text%';
	]]>
	</script>

	<form method="post" name="sbs_edt_frm" enctype="multipart/form-data" action="%pre_lang%/admin/dispatches/%method%/">
		<setgroup name="Информация о подписчике" id="sbs_edit_common" form="no">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<input br="yes" quant="no" size="58" style="width:355px">
							<id><![CDATA[sbs_mail]]></id>
							<name><![CDATA[sbs_mail]]></name>
							<title><![CDATA[E-mail:]]></title>
							<value>%sbs_mail%</value>

							<onchange><![CDATA[javascript: go_alt(this);]]></onchange>
							<onkeydown><![CDATA[javascript: go_alt(this);]]></onkeydown>
						</input>
					</td>
				</tr>
				<tr>
					<td>
						<input br="yes" quant="no" size="58" style="width:355px">
							<id><![CDATA[sbs_fname]]></id>
							<name><![CDATA[sbs_fname]]></name>
							<title><![CDATA[Фамилия:]]></title>
							<value>%sbs_fname%</value>
						</input>
					</td>
				</tr>
				<tr>
					<td>
						<input br="yes" quant="no" size="58" style="width:355px">
							<id><![CDATA[sbs_lname]]></id>
							<name><![CDATA[sbs_lname]]></name>
							<title><![CDATA[Имя:]]></title>
							<value>%sbs_lname%</value>
						</input>
					</td>
				</tr>
				<tr>
					<td>
						<input br="yes" quant="no" size="58" style="width:355px">
							<id><![CDATA[sbs_father_name]]></id>
							<name><![CDATA[sbs_father_name]]></name>
							<title><![CDATA[Отчество:]]></title>
							<value>%sbs_father_name%</value>
						</input>
					</td>
				</tr>
				<tr>
					<td>
						%sbs_gender%
					</td>
				</tr>
			</table>
			<p align="right">%control_bar%</p>
		</setgroup>

		%data_field_groups%

		<passthru name="after_save_act"></passthru>

	</form>

	<script type="text/javascript">
		<![CDATA[
			function edtWithExit() {
				document.forms['sbs_edt_frm'].after_save_act.value = "exit";
				return acf_check(1);
			}

			function edtWithEdit() {
				document.forms['sbs_edt_frm'].after_save_act.value = "edit";
				return acf_check(1);
			}
			function edtCancel() {
				if(confirm("Вы уверены, что хотите выйти? Все изменения будут потеряны")) {
					history.go(-1); 
				}
				return false;
			}

			oEdtFrm = document.forms['sbs_edt_frm'];
			oEdtFrm.onsubmit = acf_check;
			acf_inputs_test[acf_inputs_test.length] = Array('sbs_mail', 'Enter E-mail');
		]]>
	</script>
SBSEDIT;


$FORMS['releasees_list'] = <<<RELSLIST
	<setgroup name="Архив выпусков" id="disp_releasees_list" form="no">
		<tablegroup>
			<header>
				<hcol style="text-align: left">Выпуск</hcol>
				<hcol style="width: 100px">Статус</hcol>
			</header>
		%rows%
		</tablegroup>
	</setgroup>
RELSLIST;

$FORMS['releasees_list_row'] = <<<RELSLISTROW
	<row>
		<col>
			<a href="%pre_lang%/admin/dispatches/messages_list/%release_id%/"><b>%disp_name% (%release_date%)</b></a>
		</col>
		<col>
			%release_status%
		</col>
	</row>
RELSLISTROW;
?>