<?php

$FORMS = Array();

$FORMS['add_user'] = <<<END

<script language="javascript" type="text/javascript">
<![CDATA[
cifi_upload_text = '%users_cifi_upload_text%';
]]>
</script>

<form method="post" action="%pre_lang%/admin/users/%method%/%user_id%/%group_id%" name="user_form">
	<setgroup name="Пользователь" id="anketa" form="no">

		<table width="100%" border="0">
			<tr>
				<td width="50%" class="ntext">

					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td width="50%" class="ntext">
								<input size="25" br="yes">
									<id><![CDATA[ulogin]]></id>
									<name><![CDATA[login]]></name>
									<title><![CDATA[Логин]]></title>
									<value><![CDATA[%login%]]></value>
								</input>
							</td>

							<td width="50%" class="ntext">
								<input size="25" br="yes">
									<id><![CDATA[upassword]]></id>
									<name><![CDATA[password]]></name>
									<title><![CDATA[Пароль]]></title>
									<value><![CDATA[%password%]]></value>
								</input>
							</td>
						</tr>

						<tr>
							<td width="50%" class="ntext">
								<p>
									<checkbox size="25" br="yes" selected="%is_active%">
										<name><![CDATA[is_active]]></name>
										<title><![CDATA[Активен]]></title>
										<value><![CDATA[1]]></value>
									</checkbox>
								</p>
							</td>
							<td></td>
						</tr>
					</table>
				</td>

				<td width="50%" class="ntext">
					<b>Входит в группы</b>

					%list_groups%
				</td>
			</tr>
		</table>

		<passthru name="exit_after_save"></passthru>

		%save_n_save%
	</setgroup>

	%data_field_groups%

	<setgroup name="Настройки прав доступа" id="perms" form="no">
		%perms_form%
		%save_n_save%
	</setgroup>
</form>


%backup_panel%

<script language="javascript" type="text/javascript">
<![CDATA[

%acf_add%
acf_inputs_test[acf_inputs_test.length] = Array('ulogin', 'Enter user name');

acf_inputs_catch[acf_inputs_catch.length] = 'ulogin';
acf_inputs_catch[acf_inputs_catch.length] = 'upassword';
acf_inputs_catch[acf_inputs_catch.length] = 'ufname';
acf_inputs_catch[acf_inputs_catch.length] = 'ulname';
acf_inputs_catch[acf_inputs_catch.length] = 'ufather_name';
acf_inputs_catch[acf_inputs_catch.length] = 'uemail';
acf_inputs_catch[acf_inputs_catch.length] = 'uphone';


def_perms = Array();

%s_groups%
%s_gr_perms%
%def%

function sel_group(obj) {
	group_id = obj.value;
	checked_arr = new Array();
	

	for(i = 0; i < s_groups.length; i++) {	//listing groups...
		curr_gr = s_groups[i];
		perms_arr = genetic[curr_gr];

		for(n = 0; n < perms_arr.length; n++) {	//now - listing functions for this group...
			perms = perms_arr[n];

			module = perms[0];
			method = perms[1];
			allow = perms[2];

			co = document.getElementById(module + '__' + method);
			go = document.getElementById('gr_' + curr_gr);


			if(def_perms[module + '__' + method] == 1 || def_perms[module + '__' + method] == -1)
				continue;

			if(go.checked) {
				if(co)
					co.checked = true;
				checked_arr[module + '__' + method] = true;
			}

			if(!go.checked) {
				if(checked_arr[module + '__' + method] != true)  {
					if(co)
						co.checked = false;
				}
			}
		}

	
	}
}

if(%is_in_groups% && !%new_unit%) {
	sel_group(document);
}


f4u = function  () {
	sel_group(document);
};


function save_with_exit() {
	document.forms['user_form'].exit_after_save.value = "1";
	return acf_check(1);
}
function save_without_exit() {
	document.forms['user_form'].exit_after_save.value = "0";
	return acf_check(1);
}


]]>
</script>


END;

$FORMS['add_group'] = <<<END

<form method='post' action='%pre_lang%/admin/users/%method%/%group_id%' name="group_form">

	<setgroup name="Группа" form="no" id="a_group">
		<table border="0" width="100%">
			<tr>
				<td class="ntext" width="100">
					<input size="100%" comment="%field_add_group_name%" br="yes">
						<id><![CDATA[ggname]]></id>
						<name><![CDATA[group_name]]></name>
						<title><![CDATA[Название группы]]></title>
						<value><![CDATA[%group_name%]]></value>
					</input>
				</td>
			</tr>

			<tr>
				<td>
					%submit%
				</td>
			</tr>
		</table>
	</setgroup>

<setgroup name="Дополнительные настройки" form="no" id="perms">
	%perms_form%
	%submit%
</setgroup>


%data_field_groups%

<script type="text/javascript">
<![CDATA[
	function save_with_exit() {
		document.forms['group_form'].exit_after_save.value = "1";
		return acf_check(1);
	}

	function save_without_exit() {
		document.forms['group_form'].exit_after_save.value = "0";
		return acf_check(1);
	}

	acf_inputs_test[acf_inputs_test.length] = Array('ggname', 'Enter group name');
	acf_inputs_catch[acf_inputs_catch.length] = 'ggname';
]]>
</script>

<passthru name="mode">admin</passthru>
<passthru name="method">%method%</passthru>
<passthru name="group_id">%group_id%</passthru>
<passthru name="exit_after_save"></passthru>


</form>

END;

$FORMS['login'] = <<<END
<form method='get' action='%pre_lang%/admin/users/login_do/'>

	<table width='30%' border='0' cellpadding='0' cellspacing='0' class="log_frm">

		<tr>

			<td style='vertical-align: middle' class='ftext log_frml'>Логин</td>
			<td class="log_frm">
				<input quant='no'>
					<id><![CDATA[login_field]]></id>
					<name><![CDATA[login]]></name>
				</input>
			</td>

		</tr>

		<tr>
			<td style='vertical-align: middle' class='ftext log_frml'>Пароль</td>
			<td class="log_frm"><password quant='no' name="password" id="password_field" /></td>
		</tr>

		<tr>
			<td style='vertical-align: middle' class='ftext log_frml'>Скин</td>
			<td class="log_frm">
				<select quant='no'>
					<id><![CDATA[skin_field]]></id>
					<name><![CDATA[skin_sel]]></name>

					%skins%
				</select>
			</td>
		</tr>

		<tr>
			<td></td>
			<td><submit title='Войти' id="submit_field" /></td>
		</tr>

	</table>

	<passthru name="from_page"><![CDATA[%from_page%]]></passthru>

</form>

END;


$FORMS['users_list_new'] = <<<END

<form action="%pre_lang%/admin/users/users_list/">

<tinytable>
	<col width="250">

		<imgButton>
			<src><![CDATA[/images/cms/admin/%skin_path%/ico_user_add.%ico_ext%]]></src>
			<link><![CDATA[%pre_lang%/admin/users/add_user/%group_id%/]]></link>
			<title><![CDATA[%users_add_user%]]></title>
		</imgButton>

	</col>

	<col>

		<table border="0">
			<tr>
				<td style="vertical-align: middle;">
					<span class='shadow'><![CDATA[Поиск пользователя]]>&nbsp;&nbsp;</span>
				</td>

				<td width="60%" style="vertical-align: middle">
					<input quant='no' class='' style='width: 90%; vertical-align: middle;'>
						<name><![CDATA[user_search]]></name>
						<value><![CDATA[%user_search%]]></value>
					</input>
				</td>

				<td style="vertical-align: middle">
					<submit title='%users_search%'/>
				</td>
			</tr>
		</table>
	</col>

</tinytable>
</form>

<br />

%pages%

<tablegroup>
	<hrow>
		<hcol style="text-align: left">
			%users_users%
		</hcol>
		<hcol style="text-align: left">
			%users_is_ingroups%
		</hcol>

		<hcol style="width: 100px">
			Активность
		</hcol>

		<hcol>
			Редактировать
		</hcol>

		<hcol>
			%delete%
		</hcol>
	</hrow>

%users_list%
</tablegroup>

END;


$FORMS['groups_list_new'] = <<<END

<form action="%pre_lang%/admin/users/groups_list/">

<tinytable>
	<col width="250">
		<imgButton>
			<src><![CDATA[/images/cms/admin/%skin_path%/ico_group_add.%ico_ext%]]></src>
			<link><![CDATA[%pre_lang%/admin/users/add_group/]]></link>
			<title><![CDATA[%users_add_group%]]></title>
		</imgButton>
	</col>

	<col>

		<table border="0">
			<tr>
				<td style="vertical-align: middle;">
					<span class='shadow'><![CDATA[Поиск группы пользователей]]>&nbsp;&nbsp;</span>
				</td>

				<td width="60%" style="vertical-align: middle">
					<input quant='no' class='' style='width: 90%; vertical-align: middle;'>
						<name><![CDATA[group_search]]></name>
						<value><![CDATA[%group_search%]]></value>
					</input>
				</td>

				<td style="vertical-align: middle">
					<submit title='%users_search%'/>
				</td>
			</tr>
		</table>
	</col>
</tinytable>

</form>

<br />



<tablegroup>
	<hrow>
		<hcol style="text-align: left; height: 20px;">Группы пользователей</hcol>
		<hcol>Настройки</hcol>
		<hcol>Удалить</hcol>
	</hrow>

%groups_list%

	<row>
		<col style="background-color: #FFF; padding: 0px; padding-top: 1px; font-size: 0px;"></col>
		<col style="background-color: #FFF; padding: 0px; font-size: 0px;"></col>
		<col style="background-color: #FFF; padding: 0px; font-size: 0px;"></col>
	</row>


	<row>
		<col style="width: 70%;">

<table border="0">
 <tr>
  <td width="25" style="text-align: center; vertical-align: middle;">
   <img src="/images/cms/admin/%skin_path%/ico_vgroup.%ico_ext%" />
  </td>
  <td style="font-family: Tahoma; font-size: 11px; vertical-align: middle;">

   <a href='%pre_lang%/admin/users/users_list/outgroup/' title='%users_group_view%'>Пользователи вне групп (%out_num%)</a>
  </td>
 </tr>
</table>

		</col>
		<col style="text-align: center"></col>
		<col style="text-align: center"></col>
	</row>

	<row>
		<col style="width: 70%;">

<table border="0">
 <tr>
  <td width="25" style="text-align: center; vertical-align: middle;">
   <img src="/images/cms/admin/%skin_path%/ico_vgroup.%ico_ext%" />
  </td>
  <td style="font-family: Tahoma; font-size: 11px; vertical-align: middle;">

   <a href='%pre_lang%/admin/users/users_list/all/' title='%users_group_view%'>Все пользователи (%total_num%)</a>
  </td>
 </tr>
</table>

		</col>
		<col style="text-align: center"></col>
		<col style="text-align: center"></col>
	</row>




</tablegroup>

END;


$FORMS['config'] = <<<END

<form method="post" action="%pre_lang%/admin/users/config_do/">

<tablegroup>
	<row>
		<col style="width: 50%">Группа пользователей по-умолчанию</col>
		<col style="width: 50%">
			<select quant="no">
				<name><![CDATA[def_group]]></name>
%groups_list%
			</select>
		</col>

	</row>


	<row>
		<col style="width: 50%">Пользователь-гость</col>
		<col style="width: 50%">
			<select quant="no">
				<name><![CDATA[guest_id]]></name>
%guest_user%
			</select>
		</col>

	</row>

</tablegroup>

<p><submit title="Сохранить" /></p>

</form>

END;


?>