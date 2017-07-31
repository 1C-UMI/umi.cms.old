<?php

$FORMS = Array();

$FORMS['comments_block'] = <<<COMMENT_BLOCK

<div style="clear: both;"></div>
<h3>Комментарии</h3>
<a name="comments"></a>
<div class="spacer"></div>

%lines%

%system numpages(%total%, %per_page%, 'default')%

<br />
%add_form%

COMMENT_BLOCK;


$FORMS['comments_block_line'] = <<<COMMENT_LINE_USER

<p><b class="s_num">%num%.</b> <b>%title%</b> - %users viewAuthor(%author_id%)%<br />
<i>%system convertDate(%publish_time%, 'Y-m-d в H:i')%</i></p>
<p>%message%</p>

COMMENT_LINE_USER;



$FORMS['comments_block_add_user'] = <<<ADD_FORM_USER

<form method="post" action="%action%">

<table cellspacing="5" cellpadding="0" border="0">
	<tr>
		<td>
			Заголовок комментария:<br />
			<input type="text" name="title" size="50" style="width: 350px;" class="textinputs" />
		</td>
	</tr>

	<tr>
		<td>
			Текст комментария:<br />
			<textarea name="comment" style="width: 350px; height: 120px;" class="textinputs"></textarea>
		</td>
	</tr>

	<tr>
		<td>
			<input type="submit" value="Добавить комментарий" />
		</td>
	</tr>
</table>

</form>

ADD_FORM_USER;


$FORMS['comments_block_add_guest'] = <<<ADD_FORM_GUEST

<form method="post" action="%action%">

<table cellspacing="5" cellpadding="0" border="0" width="100%">
	<tr>
		<td nowrap="nowrap">
			Заголовок комментария:<br />
			<input type="text" name="title" style="width: 350px;" class="textinputs" />
		</td>
	</tr>

	<tr>
		<td nowrap="nowrap">
			Ваш ник:<br />
			<input type="text" name="author_nick" style="width: 350px;" class="textinputs" />
		</td>
	</tr>

	<tr>
		<td nowrap="nowrap">
			Ваш e-mail:<br />
			<input type="text" name="author_email" style="width: 350px;" class="textinputs" />
		</td>
	</tr>

	<tr>
		<td nowrap="nowrap">
			Текст комментария:<br />
			<textarea name="comment" style="width: 350px;height:120px" class="textinputs"></textarea>
		</td>
	</tr>

	<tr>
		<td>
			%system captcha('default')%
		</td>
	</tr>

	<tr>
		<td>
			<input type="submit" value="Добавить комментарий" />
		</td>
	</tr>
</table>


</form>

ADD_FORM_GUEST;


?>