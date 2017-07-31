<?php

$FORMS = Array();

$FORMS['perm_panel'] = <<<END

<setgroup name="Права доступа" id="perms" form="no">

<table width='100%' border='0'>
 <tr>
  <td style="width: 50%;">

<tablegroup style="width: 100%;">
	<hrow>
		<hcol><![CDATA[Группы]]></hcol>
		<hcol><![CDATA[Просмотр]]></hcol>
		<hcol><![CDATA[Редактирование]]></hcol>
	</hrow>

%perms_groups%

	<row>
		<col>
			<b><![CDATA[Выделить все]]></b>
		</col>

		<col style='text-align: center; vertical-align: middle;'>
			<checkbox selected="%is_all_read_groups%">
				<id><![CDATA[groups_allr]]></id>

				<onclick><![CDATA[javascript: select_all("groups", "read", this);]]></onclick>
			</checkbox>
		</col>

		<col style='text-align: center; vertical-align: middle;'>
			<checkbox selected="%is_all_edit_groups%">
				<id><![CDATA[groups_alle]]></id>

				<onclick><![CDATA[javascript: select_all("groups", "edit", this);]]></onclick>
			</checkbox>
		</col>
	</row> 
</tablegroup>


  </td>
  <td style="width: 10px;">&#160;&#160;</td>
  <td style="width: 50%;">

<tablegroup style="width: 100%;">
	<hrow>
		<hcol><![CDATA[Пользователи вне групп]]></hcol>
		<hcol><![CDATA[Просмотр]]></hcol>
		<hcol><![CDATA[Редактирование]]></hcol>
	</hrow>

%perms_users%

	<row>
		<col><b><![CDATA[Выделить все]]></b></col>

		<col style='text-align: center; vertical-align: middle;'>
			<checkbox selected="%is_all_read_users%">
				<id><![CDATA[users_allr]]></id>

				<onclick><![CDATA[javascript: select_all("users", "read", this);]]></onclick>
			</checkbox>
		</col>

		<col style='text-align: center; vertical-align: middle;'>
			<checkbox selected="%is_all_edit_users%">
				<id><![CDATA[users_alle]]></id>

				<onclick><![CDATA[javascript: select_all("users", "edit", this);]]></onclick>
			</checkbox>
		</col>
	</row>
</tablegroup>

  </td>
 </tr>
</table>

<p align="right">%save_n_save%</p>
</setgroup>


<script>
<![CDATA[
genetic_arr = new Array();
%genetic_arr%

groups_arr = new Array();
%gr_arr%



function perm_switchu(mode, obj) {
	u_id = obj.name;
	u_id = u_id.replace('perms_users_read[','');
	u_id = u_id.replace('perms_users_edit[','');
	u_id = u_id.replace(']','');

	if(mode == 'edit' && obj.checked) {
		ro = document.getElementById('pur' + u_id);
		ro.checked = true;
	}

	if(mode == 'read' && !obj.cheked) {
		ro = document.getElementById('pue' + u_id);
		ro.checked = false;
	}
}

function perm_switch(mode, obj) {
	//alert(genetic_arr);
	g_id = obj.name;
	g_id = g_id.replace('perms_groups_read[','');
	g_id = g_id.replace('perms_groups_edit[','');
	g_id = g_id.replace(']','');

	for(i = 0; i < genetic_arr.length; i++) {
		if(!genetic_arr[i])
			continue;

		tmp = genetic_arr[i];
		genetic_arr2 = tmp.split(';');
		for(n = 0; n < genetic_arr2.length; n++) {
			if(g_id == genetic_arr2[n]) {
				user_id = i;

				uco = null;
				if(mode == 'read') {
					uco = document.getElementById('pur' + user_id);
					if(!obj.checked) {
						tobj = document.getElementById('pue' + user_id);;
						tobj.checked = false;
					}
				}
				if(mode == 'edit') {
					uco = document.getElementById('pue' + user_id);
					if(obj.checked) {
						tobj = document.getElementById('pur' + user_id);;
						tobj.checked = true;
					}
				}
				if(uco)
					uco.checked = obj.checked;
			}
		}
	}
	if(mode == 'edit' && obj.checked) {
		eobj = document.getElementById('pgr' + g_id);
		eobj.checked = true;
	}

	if(mode == 'read' && !obj.checked) {
		eobj = document.getElementById('pge' + g_id);
		eobj.checked = false;
	}
}

function select_all(type, mode, obj) {
	if(type == 'users') {
		if(mode == 'read') {
			//users_alle
			uale = document.getElementById('users_alle');
			if(!obj.checked);
				uale.checked = false;

			for(i = 0; i < genetic_arr.length; i++) {
				if(!genetic_arr[i])
					continue;
				check_r = document.getElementById('pur' + i);
				check_e = document.getElementById('pue' + i);
				check_r.checked = obj.checked;
				if(!obj.checked)
					check_e.checked = obj.checked;
			}
		}

		if(mode == 'edit') {
			ualr = document.getElementById('users_allr');
			if(obj.checked);
				ualr.checked = true;

			for(i = 0; i < genetic_arr.length; i++) {
				if(!genetic_arr[i])
					continue;
				check_r = document.getElementById('pur' + i);
				check_e = document.getElementById('pue' + i);
				check_e.checked = obj.checked;
				if(obj.checked)
					check_r.checked = obj.checked;
			}			
		}
		return true;
	}

	if(type == 'groups') {
		if(mode == 'read') {
			for(a = 0; a < groups_arr.length; a++) {
				gobj = document.getElementById('pgr' + groups_arr[a]);
				gobj.checked = obj.checked;

				if(!obj.checked) {
					gobj = document.getElementById('pge' + groups_arr[a]);
					gobj.checked = false;

					gobj = document.getElementById('groups_alle');
					gobj.checked = false;

				}
			}
		}

		if(mode == 'edit') {
			for(a = 0; a < groups_arr.length; a++) {
				gobj = document.getElementById('pge' + groups_arr[a]);
				gobj.checked = obj.checked;

				if(obj.checked) {
					gobj = document.getElementById('pgr' + groups_arr[a]);
					gobj.checked = obj.checked;

					gobj = document.getElementById('groups_allr');
					gobj.checked = true;

				} else {
				}


			}
		}
	}


}

]]>
</script>

END;
?>