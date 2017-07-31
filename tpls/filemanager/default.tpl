<?php

$FORMS = Array();

$FORMS['list_files'] = <<<END
		%lines%
	%system numpages(%total%, %per_page%, 'default')%

END;

$FORMS['list_files_row'] = <<<END
	<div id="download_info">
		<h3>%name%</h3>
		%desc% <br />
		<a href="%link%">Скачать</a>
	</div>
END;



$FORMS['shared_file'] = <<<END
	Имя файла: %file_name% <br />
	Размер файла: %file_size% Kb<br /><br />
	Если закачивание файла не начнется через 10 сек, кликните <a href="%download_link%">по этой ссылке</a>
	<script languge="text/javascript">
		window.setTimeout('document.location.href="%download_link%";', 10000);
	</script>
END;

$FORMS['broken_file'] = <<<END

	Файл не существует.

END;
?>