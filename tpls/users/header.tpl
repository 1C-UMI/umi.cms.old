<?php

$FORMS = Array();

$FORMS['login'] = <<<END

				<form id="auth" action="%pre_lang%/users/login_do/" method="post">
					<label for="login">Логин:</label>
					<input type="text" id="login" name="login" class="input" value=""/>
					<label for="password">Пароль:</label>
					<input type="password" id="password" name="password" class="input" value=""/>
					<input type="submit" value="%users_auth_enter%"/>
					<input type="hidden" name="from_page" value="%from_page%" style="display:none;" />
				</form>

END;


$FORMS['logged'] = <<<END
			<div id="auth">
				<p>
					<b>
						%users_welcome%<br />
						%user_name% (%user_login%)
					</b>
				</p>
				<p>
					<a href="%pre_lang%/users/logout/" class="blue">Выйти</a> | <a href="%pre_lang%/users/settings/" class="blue">Настройка</a>
				</p>
			</div>

END;
?>