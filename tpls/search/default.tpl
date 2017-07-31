<?php

$FORMS = Array();


$FORMS['search_block'] = <<<END

%search insert_form('searchform')%

<p>Найдено %total% страниц.</p>

%lines%

<p>%system numpages(%total%, %per_page%)%</p>

END;

$FORMS['search_block_line'] = <<<END

<p>
	<span class="s_num">%num%.</span> <a href='%link%'><b>%name%</b></a>
	%context%
</p>


END;

$FORMS['search_empty_result'] = <<<END
	%search insert_form('searchform')%

	<p>
		Извините. По данному запросу ничего не найдено.
	</p>
END;

?>