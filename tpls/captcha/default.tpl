<?php

$FORMS = Array();

$FORMS['captcha'] = <<<CAPTCHA

	<table border="0" cellpadding="2">
		<tr>
			<td width="200">
				Введите текст на картинке
			</td>

			<td>
				<img src="/captcha.php" style="border: #000 1px solid;" alt="" /><br />
				<input type="text" id="%input_id%" name="captcha" class="textinputs" style="width:119px" />
			</td>
		</tr>
	</table>

CAPTCHA;
?>