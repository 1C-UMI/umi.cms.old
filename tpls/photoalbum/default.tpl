<?php

$FORMS = Array();

$FORMS['albums_list_block'] = <<<END

Список альбомов:

%lines%

END;

$FORMS['albums_list_block_empty'] = <<<END

Фотоальбомов нет.

END;


$FORMS['albums_list_block_line'] = <<<END

<li>
	<a href="%link%">%name%</a>
</li>

END;


$FORMS['album_block'] = <<<END

%lines%

END;


$FORMS['album_block_empty'] = <<<END

<p>Фотогалерея пуста.</p>
%user_id%

END;


$FORMS['album_block_line'] = <<<END

<table width="100%">
	<tr>
		<td rowspan="2" style="width: 150px;">
			<a href="%link%">
				%data getProperty('%id%', 'photo', 'preview_image')%
			</a>
		</td>

		<td>
			<a href="%link%">
				%name%
			</a>
		</td>
	</tr>

	<tr>
		<td>
			%descr%
		</td>
	</tr>
</table>

<br /><br />

END;


$FORMS['photo_block'] = <<<END

<table width="100%">
	<tr>
		<td style="width: 250px;">
			%data getProperty('%id%', 'photo', 'view_image')%
		</td>

		<td>
			<p>Теги: %tags%</p>
			%descr%
		</td>
	</tr>
</table>

END;

?>