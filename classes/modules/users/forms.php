<?php

$FORMS = Array();



$FORMS['login'] = <<<END
<form method='post' action='%pre_lang%/users/login_do/'>

<table border='0'>
 <tr>
  <td>%users_auth_login%:</td>
  <td>
  	<input type="text" >
		<name><![CDATA[login]]></name>
	</input>
  </td>
 </tr>

 <tr>
  <td>%users_auth_password%:</td>
  <td>
  	<input type="password">
  		<name><![CDATA[password]]></name>
	</input>

  </td>
 </tr>

 <tr>
  <td colspan='2' align='right'>
  	<input type="submit">
		<value><![CDATA[%users_auth_enter%]]></value>
	</input>
  </td>
 </tr>

</table>

</form>
END;

$FORMS['forgot'] = <<<END

<form method='post' action='%pre_lang%/users/forgot_do/'>

<table cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td>
			Введите e-mail:
		</td>

		<td>
			<input type="text" >
					<name><![CDATA[email]]></name>
			</input>

		</td>
	</tr>

	<tr>
		<td>
			Или логин:
		</td>

		<td>
			 <input type="text"  />
					<name><![CDATA[login]]></name>
			</input>
		</td>
	</tr>
</table>
<p>
	<input type="submit" >
		<value><![CDATA[Ok]]></value>
	</input>
</p>
</form>

END;
?>