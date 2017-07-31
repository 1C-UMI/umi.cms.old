<?php

$FORMS = Array();

$FORMS['registrate_block'] = <<<REGISTRATE

<form action="%pre_lang%/users/registrate_do/" method="post" enctype="multipart/form-data">

<table border="0">
	<tr>
		<td width="200">
			Логин:
		</td>

		<td>
			<input type="text" name="login" class="textinputs" />
		</td>
	</tr>

	<tr>
		<td>
			Пароль:
		</td>

		<td>
			<input type="password" name="password" value="" class="textinputs" />
		</td>
	</tr>

	<tr>
		<td>
			Подтверждение пароля:
		</td>

		<td>
			<input type="password" name="password_confirm" value="" class="textinputs" />
		</td>
	</tr>


	<tr>
		<td>
			E-mail:
		</td>

		<td>
			<input type="text" name="email" value="" class="textinputs" />
		</td>
	</tr>


	%data getCreateForm(%type_id%, 'users', 'short_info more_info')%

</table>
	%system captcha()%


<p><input type="submit" value="Зарегистрироваться" /></p>


</form>

REGISTRATE;



$FORMS['settings_block'] = <<<REGISTRATE

<form action="%pre_lang%/users/settings_do/" method="post" enctype="multipart/form-data">

<table cellspacing="1" cellpadding="1" width="100%" border="0">
	<tr>
		<td>
			Логин:
		</td>

		<td>
			<input type="text" value="%login%" class="textinputs" disabled="disabled" />
		</td>
	</tr>

	<tr>
		<td>
			Пароль:
		</td>

		<td>
			<input type="password" name="password" value="" class="textinputs" />
		</td>
	</tr>

	<tr>
		<td>
			Подтвердите пароль:
		</td>

		<td>
			<input type="password" name="password_confirm" value="" class="textinputs" />
		</td>
	</tr>


	<tr>
		<td>
			E-mail:
		</td>

		<td>
			<input type="text" name="email" value="%e-mail%" class="textinputs" />
		</td>
	</tr>
%data getEditForm(%user_id%, 'users', 'short_info more_info')%
</table>

<p><input type="submit" value="Сохранить изменения" /></p>


</form>

REGISTRATE;






$FORMS['registrate_done_block'] = <<<END

Регистрация прошла успешно. На ваш e-mail отправлено письмо с инструкциями по активации аккаунта.

END;


$FORMS['activate_block'] = <<<END

<p>Аккаунт активирован.</p>

END;

$FORMS['activate_block_failed'] = <<<END

<p>Неверный код активации.</p>

END;


$FORMS['mail_registrated'] = <<<MAIL

	<p>
		Здравствуйте, %lname% %fname% %mname%,</br>
		Вы зарегистрировались на сайте <a href="http://%domain%">%domain%</a>.
	</p>


	<p>
		Логин: %login%<br />
		Пароль: %password%
	</p>


	<p>
		<div class="notice">
			Чтобы активировать Ваш аккаунт, необходимо перейти по ссылке, либо скопировать ее в адресную строку браузера:<br />
			<a href="%activate_link%">%activate_link%</a>
		</div>
	</p>

MAIL;

?>