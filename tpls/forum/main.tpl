<?php

$FORMS = Array();


$FORMS['confs_block'] = <<<CONFS_BLOCK

	<table border="0" width="100%">
		<tr><td height="14"><img src="/images/bg_forum_top.gif" width="100%" height="14" alt=""></td></tr>
		<tr><td class="bg_forum pad_left" width="289"><table border="0" width="289">
				<tr><td><table>
					<tr><td class="h1 grey">Форум</td>
						<td class="pad_forum"><h2><a href="%pre_lang%/talks/" class="blue">Перейти в форум</a></h2></td></tr>
					</table></td></tr>
				%lines%
			</table></td></tr>
		<tr><td height="11"><img src="/images/bg_forum_bot.gif" width="100%" height="11" alt=""></td></tr>
	</table>

CONFS_BLOCK;

$FORMS['confs_block_line'] = <<<CONFS_LINE

				<tr><td><table border="0" width="95%" class="bg_forum_gr">
							<tr><td><img src="/images/bg_forum_gr_t.gif" width="100%" height="8" alt=""></td></tr>
							<tr><td class="pad_left forum_text"><a href="%link%" class="blue"><b>%name% (%topics_count%)</b></a>

							<br>%descr%</td></tr>
							<tr><td>
							%forum conf_last_message(%id%, 'main')%

							<tr><td><img src="/images/bg_forum_gr_b.gif" width="100%" height="8" alt=""></td></tr>
					</table></td></tr>

CONFS_LINE;

$FORMS['topics_block'] = <<<TOPICS_BLOCK

<table cellspacing="1" cellpadding="0" width="100%">
	<tr>
		<td>
			<p>%nums%</p>
		</td>

		<td align="right">
			%users auth('welcome')%
		</td>
	</tr>
</table>
<br />

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tforum">
	<tr>
		<td class="hforum">Темы</td>
		<td class="hforum">Ответов</td>
		<td class="hforum">Последнее сообщение</td>
	</tr>
%lines%
</table>


<br />

%add_form%

<br />
%login_line%

TOPICS_BLOCK;


$FORMS['topics_block_line'] = <<<TOPICS_LINE

<tr>
	<td class="nforum">
		<p><a href="%link%">%name%</a><p>
	</td>

	<td class="nforum" align="center">
		%messages_count%
	</td>

	<td class="nforum">
		%forum topic_last_message(%id%, 'default')%
	</td>
</tr>

TOPICS_LINE;

$FORMS['messages_block'] = <<<MESSAGES_BLOCK


<table cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td>
			<p>%nums%</p>
		</td>

		<td align="right">
			%users auth('welcome')%
		</td>
	</tr>
</table>
<br />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td colspan="2" style="height: 1px; background-color: #89D817;"></td>
</tr>
%lines%
</table>

<p>%nums%</p>

<br />

%add_form%

<br />

%login_line%

MESSAGES_BLOCK;

$FORMS['messages_line'] = <<<MESSAGES_LINE

<tr>
	<td rowspan="2" class="f_num">%curr% %ancor%</td>
	<td class="f_mess">
		<span style="cursor: pointer;" onclick="javascript: return forum_toAuthor(this);" id="author_%curr%">%users viewAuthor(%author_id%)%</span> %system convertDate(%%publish_time%%, 'd.m.Y в H:i')%<br />
		<b>%name%</b><br /></br >
	</td>
</tr>

<tr>
	<td>
		<!-- %name% -->
		<div style="display: inline;" id="mess_%curr%">%body%<br /><br /></div>
		(<a href="#add" onmousedown="javascript: forum_quote(%curr%); return false;" onfocus="javascript: return false;">Цитировать</a>)<br /><br />
	</td>
</tr>

<tr>
	<td colspan="2" style="height: 1px; background-color: #89D817;"></td>
</tr>

MESSAGES_LINE;

$FORMS['author_guest'] = <<<GUEST

<img src="/images/cms/ico_forum_guest.gif" width="11" hspace="0" height="15"  alt="Незарегистрированный пользователь" title="Незарегистрированный пользователь" border="0"></a><img src="/images/spacer.gif" width="5" height="1" alt="" border="0">%nick% (Гость)

GUEST;

$FORMS['author_user'] = <<<USER

<img src="/images/cms/ico_forum_user.gif" width="11" hspace="0" height="15" border="0" alt="Зарегистрированный пользователь" title="Зарегистрированный пользователь"></a><img src="/images/spacer.gif" width="5" height="1" alt="" border="0">%lname% %fname% (%login%)

USER;

$FORMS['author_superwizer'] = <<<SV

<img src="/images/cms/ico_forum_sv.gif" width="11" hspace="0" height="15" border="0" alt="Администратор" title="Администратор"></a><img src="/images/spacer.gif" width="5" height="1" alt="" border="0">%lname% %fname% (%login%)

SV;


$FORMS['add_message_user'] = <<<ADD_MESSAGE_USER

<form method="post" action="%action%" onsubmit="javascript: return forum_check_form(this);">

<div class="forum_add" style="width: 100%;">
<a name="add"></a>
	<b><span style="color: #000;">Заголовок сообщения:</span></b><br />
	<input type="text" name="title" style="width: 90%; " value="Re: %topic_name%" /><br /><br />


	<b><span style="color: #000;">Ваши комментарии:</span><br /><br /></b>

	<textarea name="body" id="forum_body" style="width: 90%; height: 240px; font-size: 12px;"></textarea>
	<p><input type="submit" value="Отправить" /></p>

</div>

<input type="hidden" name="login" disabled="disabled" value="%login_def%" />

</form>

ADD_MESSAGE_USER;

$FORMS['add_message_guest'] = <<<ADD_MESSAGE_GUEST

<form method="post" action="%action%" onsubmit="javascript: return forum_check_form(this);">

<div class="forum_add" style="width: 100%;">
<a name="add"></a>
	<b><span style="color: #000;">Заголовок сообщения:</span></b><br />
	<input type="text" name="title" style="width: 90%; " value="Re: %topic_name%" /><br /><br />

	<b><span style="color: #000;">Ваше имя:</span></b><br />
	<input type="text" name="login" style="width: 90%; " value="%login_def%" /><br /><br />

	<b><span style="color: #000;">Ваш e-mail:</span></b><br />
	<input type="text" name="email" style="width: 90%; " value="%email_def%" /><br /><br />

	%captcha%

	<b><span style="color: #000;">Ваши комментарии:</span><br /><br /></b>

	<textarea name="body" id="forum_body" style="width: 90%; height: 240px; font-size: 12px;"></textarea>
	<p><input type="submit" value="Отправить" /></p>

</div>

</form>

ADD_MESSAGE_GUEST;

$FORMS['captcha_message_add'] = <<<CAPTCHA

	<table cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<b><img src="/captcha.php" style="border: #000 1px solid;" alt="" /></b>
			</td>

			<td>
				<input type="text" name="captcha" style="width: 120px; margin-left: 15px; margin-top: 10px;" value="" /><br /><br />
			</td>
		</tr>
	</table><br />


CAPTCHA;


$FORMS['add_topic_user'] = <<<ADD_TOPIC_USER

<form method="post" action="%action%" onsubmit="javascript: return forum_check_form(this);">

<div class="forum_add" style="width: 100%;">
<a name="add"></a>
	<b><span style="color: #000;">Название темы:</span><br /></b>
	<input type="text" name="title" style="width: 90%;" /><br /><br />

	<b><span style="color: #000;">Ваши комментарии:</span><br /><br /></b>

	<textarea name="body" id="forum_body" style="width: 90%; height: 240px; font-size: 12px;"></textarea>
	<p><input type="submit" value="Отправить" /></p>

</div>

<input type="hidden" name="login" disabled="disabled" value="%login_def%" />


</form>

ADD_TOPIC_USER;


$FORMS['add_topic_guest'] = <<<ADD_TOPIC_GUEST


<form method="post" action="%action%" onsubmit="javascript: return forum_check_form(this);">

<div class="forum_add" style="width: 100%;">
<a name="add"></a>

	<b><span style="color: #000;">Ваше имя:</span><br /></b>
	<input type="text" name="login" style="width: 90%;" value="%login_def%" /><br /><br />

	<b><span style="color: #000;">Ваше email:</span><br /></b>
	<input type="text" name="email" style="width: 90%;" value="%email_def%" /><br /><br />

	<b><span style="color: #000;">Название темы:</span><br /></b>
	<input type="text" name="title" style="width: 90%;" /><br /><br />

%captcha%

	<b><span style="color: #000;">Ваши комментарии:</span><br /><br /></b>

	<textarea name="body" id="forum_body" style="width: 90%; height: 240px; font-size: 12px;"></textarea>
	<p><input type="submit" value="Отправить" /></p>
</div>

</form>

ADD_TOPIC_GUEST;

$FORMS['captcha_topic_add'] = <<<CAPTCHA

	<table cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<b><img src="/captcha.php" style="border: #000 1px solid;" alt="" /></b>
			</td>

			<td>
				<input type="text" name="captcha" style="width: 120px; margin-left: 15px; margin-top: 10px;" value="" /><br /><br />
			</td>
		</tr>
	</table><br />


CAPTCHA;



$FORMS['add_topic_unauth'] = <<<ADD_TOPIC_UNAUTH

Для создания топиков необходимо <a href="%pre_lang%/forum/registrate/">зарегистрироваться</a>.

ADD_TOPIC_UNAUTH;

$FORMS['add_message_unauth'] = <<<ADD_MESSAGE_UNAUTH

Для добавления сообщений необходимо <a href="%pre_lang%/forum/registrate/">зарегистрироваться</a>.

ADD_MESSAGE_UNAUTH;

$FORMS['num_block'] = <<<NUM_BLOCK

Страницы: %items%&nbsp;&nbsp;&nbsp;%show_all%

NUM_BLOCK;

$FORMS['num_item'] = <<<NUM_ITEM

<a href="%link%"><b>%num%</b></a>%quant%

NUM_ITEM;

$FORMS['num_item_a'] = <<<NUM_ITEM_A

<span class="num_a">%num%</span>%quant%

NUM_ITEM_A;

$FORMS['num_quant'] = <<<NUM_QUANT

&nbsp;

NUM_QUANT;

$FORMS['num_show_all'] = <<<NUM_SHOW_ALL
<a href="%link%">Показать все</a>
NUM_SHOW_ALL;

$FORMS['asteriks'] = <<<ASTERIKS

<span style="color: red">*</span>

ASTERIKS;

$FORMS['registrate'] = <<<REGISTRATE

<p><span style="color: red; font-weight: bold">%err%</span></p>

<form action="%pre_lang%/forum/registrate_do/" method="post" onsubmit="javascript: return forum_check_reg_form();">
<table cellspacing="1" cellpadding="1" width="100%" border="0">
	<tr>
		<td width="30%">%asteriks%Логин: </td>
		<td><input type="text" name="login" value="%forum_login%" style="width: 200px" id="forum_login" /></td>
	</tr>

	<tr>
		<td>%asteriks%Пароль: </td>
		<td><input type="password" name="password" value="" style="width: 200px" id="forum_password" /></td>
	</tr>


	<tr>
		<td>%asteriks%Подтвердите пароль: </td>
		<td><input type="password" name="password_check" value="" style="width: 200px" id="forum_password_check" /></td>
	</tr>

	<tr>
		<td>%asteriks%E-mail: </td>
		<td><input type="text" name="email" value="%forum_email%" style="width: 200px" id="forum_email" /></td>
	</tr>

	<tr>
		<td>Имя: </td>
		<td><input type="text" name="fname" value="%forum_fname%" style="width: 200px" /></td>
	</tr>


	<tr>
		<td>Фамилия: </td>
		<td><input type="text" name="lname" value="%forum_lname%" style="width: 200px" /></td>
	</tr>

	<tr>
		<td>Отчество: </td>
		<td><input type="text" name="mname" value="%forum_mname%" style="width: 200px" /></td>
	</tr>

<!--
	<tr>
		<td>Телефон: </td>
		<td><input type="text" name="phone" value="%forum_phone%" style="width: 150px" /></td>
	</tr>
-->

</table>
<input type="hidden" name="phone" value="%forum_phone%" />
<p><input type="submit" value="Зарегистрироваться" /></p>


</form>

REGISTRATE;

$FORMS['reg_mail'] = <<<MAIL
<p>Уважаемый, %lname% %fname% %mname%,</br>
Вы зарегистрировались на форуме %domain%.</p>

<p>Ваш логин:		%login%<br />
Ваш пароль:		%password%
</p>

<p>Для активации аккаунта необходимо зайти по этой ссылке:</p>

<a href="%url%">%url%</a>
MAIL;

$FORMS['result'] = <<<RESULT

Регистрация прошла успешно. На ваш e-mail отправлено письмо с инструкциями по активации аккаунта.

RESULT;


$FORMS['conf_last_message'] = <<<END

<tr>
	<td class="pad_left forum_text">
		%users viewAuthor(%author_id%)%
		<br><img src="/images/spacer.gif" width="15" height="1" alt="" border="0">
		%system convertDate(%publish_time%, 'd.m.Y в H:i')%
	</td>
</tr>

END;

$FORMS['topic_last_message'] = <<<END
%message_title%<br />
%system convertDate(%publish_time%, 'd.m.Y в H:i')%<br />
%author%

END;


$FORMS['login_line'] = <<<END
<!--
<br />
<hr />
<br />

<p>!users auth('forum')!</p>
<p><a href="%pre_lang%/forum/registrate/">Зарегистрироваться</a></p>
-->

END;

$FORMS['topic_exists'] = <<<TE

<p>Топик с таким названием уже существует. Возможно, вы найдете ответ на свой вопрос тут: (<a href='%e_topic_url%'>%e_topic_name%</a>).</p>

<form method="post" action="%action%">
<p>Или задайте новое название для топика: <input type="text" name="title" value="%ptitle%" style="width: 200px" /></p>
<input type="submit" value="Создать" />
<input type="hidden" name="login" value="%plogin%" />
<input type="hidden" name="body" value="%pbody%" />
<input type="hidden" name="email" value="%pemail%" />

</form>

TE;

?>