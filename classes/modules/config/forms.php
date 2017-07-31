<?php

$FORMS = Array();

$FORMS['mainpage'] = <<<END
%installform%
  %settings%
END;

$FORMS['settings'] = <<<END

<tablegroup>
	<header>
		<hcol>Список установленных модулей</hcol>
		<hcol>Удалить</hcol>
	</header>

	%modules%
</tablegroup>
END;

$FORMS['add_module'] = <<<END
<form method="post" action="%pre_lang%/admin/config/add_module_do/">
	<input   br="yes" style="width: 80%">
		<name><![CDATA[module_path]]></name>
		<title><![CDATA[Путь до инсталляционного файла]]></title>
		<value><![CDATA[classes/]]></value>
	</input>
		<submit title="Установить" />
	<passthru name="mode">admin</passthru>
	<passthru name="module">config</passthru>
	<passthru name="method">add_module_do</passthru>

</form>
<br />
END;

$FORMS['globals'] = <<<END
<br />
<form method='post' action="%pre_lang%/admin/config/main_do/">
<table width='100%' cellspacing='0' border='0'>

 <tr>
  <td style="width: 50%;" class='ftext'>Название сайта</td>
  <td style="width: 50%;">
  	<input br='no' quant='no' style='width: 90%' >
		<name><![CDATA[site_name]]></name>
		<value><![CDATA[%site_name%]]></value>
	</input>

  </td>
 </tr>

 <tr>
  <td class='ftext'>E-mail администратора</td>
  <td>
  	<input br='no' quant='no' style='width: 90%' >
  		<name><![CDATA[admin_email]]></name>
		<value><![CDATA[%admin_email%]]></value>
	</input>

  </td>
 </tr>

 <tr>
  <td class='ftext'>Лицензионный ключ</td>
  <td>
  	<input br='no' quant='no' style='width: 90%' >
  		<name><![CDATA[keycode]]></name>
		<value><![CDATA[%keycode%]]></value>
	</input>

  </td>
 </tr>


 <tr>
  <td class='ftext'>Разрешить браузерам кешировать страницы<br/></td>
  <td style="padding-left: 3px;">
  	<checkbox selected="%chache_browser%">
		<name><![CDATA[chache_browser]]></name>
		<value><![CDATA[1]]></value>
	</checkbox>
  </td>
 </tr>


 <tr>
  <td class='ftext'>Отключить автокоррекцию адресов<br/></td>
  <td style="padding-left: 3px;">
  	<checkbox selected="%disable_url_autocorrection%">
		<name><![CDATA[disable_url_autocorrection]]></name>
		<value><![CDATA[1]]></value>
	</checkbox>
  </td>
 </tr>


</table>

 <p align='right'><submit title='Сохранить' /></p>

 <passthru name='mode'>admin</passthru>
 <passthru name='module'>config</passthru>
 <passthru name='method'>main_do</passthru>
</form>
END;


$FORMS['langs'] = <<<END
<form method='post' action='/admin/config/langs/'>

<tablegroup>
	<hrow>
		<hcol>Список языков</hcol>
		<hcol style="width: 100px;">Префикс</hcol>
		<hcol style="width: 150px;">Активный</hcol>
		<hcol style="width: 100px;">Удалить</hcol>
	</hrow>

%rows%

	<row>
		<col>
			<input   quant='no' style='width: 90%' >
				<name><![CDATA[new_name]]></name>
			</input>

		</col>

		<col>
			<input   quant='no' style='width: 90%' >
				<name><![CDATA[new_prefix]]></name>
			</input>

		</col>

		<col style='text-align: center'>
			<radio  >
				<name><![CDATA[default]]></name>
				<value><![CDATA[NEW]]></value>
			</radio>
		</col>

		<col style='text-align: center'>
		</col>
	</row>
</tablegroup>

<p align='right'><submit title='Сохранить' /></p>

</form>
END;

$FORMS['lang_row'] = <<<END

	<row>
		<col>
			<input   quant='no' style='width: 90%'>

					<name><![CDATA[names[%language_prefix%]]]></name>
					<title><![CDATA[]]></title>
					<value><![CDATA[%language_name%]]></value>
				</input>

		</col>

		<col>
			<input   quant='no' style='width: 90%'>
					<name><![CDATA[prefixes[%language_prefix%]]]></name>
					<title><![CDATA[]]></title>
					<value><![CDATA[%language_prefix%]]></value>
			</input>

		</col>

		<col style='text-align: center'>
			<radio  >
				<name><![CDATA[default]]></name>
				<value><![CDATA[%language_prefix%]]></value>
			</radio>

		</col>

		<col style='text-align: center'>
			<checkbox  >
				<name><![CDATA[del[%language_prefix%]]]></name>
				<value><![CDATA[1]]></value>
			</checkbox>

		</col>
	</row>

END;


$FORMS['domains'] = <<<END
<form action="%pre_lang%/admin/config/domains_do/">

<tablegroup>
	<hrow>
		<hcol>Адрес домена</hcol>
		<hcol style="width: 200px">Язык по-умолчанию</hcol>
		<hcol>Зеркала</hcol>
		<hcol>Удалить</hcol>
	</hrow>

%rows%

	<row>
		<col>
			<input quant="no" style="width: 94%" >
					<name><![CDATA[domain_hosts_new]]></name>
					<value><![CDATA[]]></value>
			</input>

		</col>

		<col align="center">
			<select quant="no" >
				<name><![CDATA[domain_langs_new]]></name>
				%new_lang%
			</select>
		</col>

		<col></col>

		<col></col>
	</row>
</tablegroup>

<p align="right"><submit title="%save%" /></p>

</form>
END;


$FORMS['domain_mirrows'] = <<<END

<form action="%pre_lang%/admin/config/domain_mirrows_do/%domain_id%/">

	<tablegroup>
			<hrow>
				<hcol>
					Адрес домена
				</hcol>

				<hcol style="width: 100px;">
					Удалить
				</hcol>
			</hrow>
%rows%

			<row>
				<col>
					<input type="text"  quant="no" style="width: 94%;">
						<name><![CDATA[mirrow_hosts_new]]></name>
					</input>

				</col>

				<col></col>
			</row>
	</tablegroup>

<p align="right"><submit title="%save%" /></p>

</form>

END;



$FORMS['memcached'] = <<<END

<form action="%pre_lang%/admin/config/memcached_do">

	<tablegroup>
		<row>
			<col>
				Статус:
			</col>

			<col>
				%status%
			</col>

		</row>

		<row>
			<col>
				Использовать memcached:
			</col>

			<col>
				<checkbox   selected="%is_enabled%">
					<name><![CDATA[is_enabled]]></name>
					<value><![CDATA[1]]></value>
				</checkbox>

			</col>

		</row>

		<row>
			<col>
				Хост:
			</col>

			<col>
				<input type="text"  quant="no" style="width: 250px;">
					<name><![CDATA[host]]></name>
					<value><![CDATA[%host%]]></value>
				</input>

			</col>

		</row>

		<row>
			<col>
				Порт:
			</col>

			<col>
				<input type="text"  quant="no" style="width: 250px;">
					<name><![CDATA[port]]></name>
					<value><![CDATA[%port%]]></value>
				</input>

			</col>

		</row>
	</tablegroup>

	<p align="right"><submit title="Сохранить" /></p>
</form>

END;


$FORMS['mails'] = <<<END

<form method="post" action="%pre_lang%/admin/config/mails_do/">

	<tablegroup>
		<row>
			<col>
				E-mail отправителя:
			</col>

			<col>
				<input type="text"  quant="no" style="width: 250px;">
					<name><![CDATA[email_from]]></name>
					<value><![CDATA[%email_from%]]></value>
				</input>

			</col>
		</row>

		<row>
			<col>
				Имя отправителя:
			</col>

			<col>
				<input type="text"  quant="no" style="width: 250px;">
					<name><![CDATA[fio_from]]></name>
					<value><![CDATA[%fio_from%]]></value>
				</input>

			</col>
		</row>

	</tablegroup>

<p align="right"><submit title="Сохранить" /></p>
</form>
END;


?>