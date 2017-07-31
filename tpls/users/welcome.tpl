<?php

$FORMS = Array();

$FORMS['logged'] = <<<END

Здравстсвуйте<br />
	<b>%user_name%</b>
	<br /><br />
	<a href="%pre_lang%/eshop/personal/"><u>Персональный раздел</u></a><br />
	<a href="%pre_lang%/users/logout/"><u>Выйти из системы</u></a>

END;

$FORMS['login'] = <<<END

			<div id="login">
				<form method="post" action='%pre_lang%/users/login_do/'>
					<table cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td>
								%users_auth_login%:
							</td>

							<td>
								<input type="text" name="login" />
							</td>
						</tr>

						<tr>
							<td>
								%users_auth_password%:&nbsp;&nbsp;
							</td>

							<td>
								<input type="password" name="password" />
							</td>
						</tr>

						<tr>
							<td colspan="2">

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="left">
			<a href="%pre_lang%/users/registrate/" class="sub"><u>Регистрация</u></a><br />
			<a href="%pre_lang%/users/forget/" class="sub"><u>Забыли пароль?</u></a><br />
		</td>

		<td align="right">
			<input type="submit" value="%users_auth_enter%" />
		</td>
	</tr>
</table>
								
							</td>
						</tr>

<!--
						<tr>
							<td>
								<a href="%pre_lang%/users/registrate/" class="sub"><u>Регистрация</u></a><br />
							</td>

							<td align="right" rowspan="2">
								<input type="submit" value="%users_auth_enter%" />
							</td>
						</tr>

						<tr>
							<td>
								<a href="%pre_lang%/users/forget/" class="sub"><u>Забыли пароль?</u></a><br />
							</td>
						</tr>
-->
					</table>
					<input type="hidden" name="from_page" value="%from_page%" />
				</form>
			</div>

END;

?>