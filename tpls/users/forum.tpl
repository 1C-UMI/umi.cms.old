<?php

$FORMS = Array();

$FORMS['login'] = <<<END
<form method='post' action='%pre_lang%/users/login_do/'>

<table border='0'>
	<tr>
		<td>
			%users_auth_login%:
		</td>

		<td style="padding-right: 25px">
			<input type="text" name="login" style="font-size: 12px" />
		</td>

		<td>
			%users_auth_password%:
		</td>

		<td>
			<input type="password" name="password" style="font-size: 12px" />
		</td>

		<td style="padding-right: 25px" colspan='1' align='right'>
			<input type="submit" value="%users_auth_enter%" style="font-size: 12px" />
		</td>

	</tr>

</table>
<input type="hidden" name="from_page" value="%from_page%" />
</form>
END;

$FORMS['logged'] = <<<END

<p>%users_welcome% %user_name% %user_login%</p><br />

END;

?>