<?php

$FORMS = Array();

$FORMS['login'] = <<<END

				<form id="auth" action="%pre_lang%/users/login_do/" method="post">
					<label for="login">Login:</label>
					<input type="text" id="login" name="login" class="input" value="demo"/>
					<label for="password">Password:</label>
					<input type="password" id="password" name="password" class="input" value="demo"/>
					<input type="submit" value="Login"/>
					<input type="hidden" name="from_page" value="%from_page%" />
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
					<a href="%pre_lang%/users/logout/" class="blue">Logout</a>
				</p>
			</div>

END;
?>