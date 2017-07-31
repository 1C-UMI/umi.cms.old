<?php
	$FORMS = Array();

	$FORMS['pages_block'] = <<<END

	<div class="numpage">
		<div class="small">Страницы:&nbsp;&nbsp;%pages%</div>
		<div class="small">
				Сортировать по:&nbsp;&nbsp;

				%system order_by('cena', 11, 'default')%&nbsp;&nbsp;|
				%system order_by('name', 11, 'default')%&nbsp;&nbsp;
		</div>
	</div>

END;



	$FORMS['pages_item'] = <<<END
	<a href="%link%"><b>%num%</b></a>&nbsp;%quant%
END;

	$FORMS['pages_item_a'] = <<<END
	<span class="active_num">%num%</span>&nbsp;%quant%
END;

	$FORMS['pages_quant'] = <<<END
|
END;

	$FORMS['pages_block_empty'] = <<<END

	<div class="numpage">
		<div class="small">
				Сортировать по:&nbsp;&nbsp;

				%system order_by('cena', 11, 'default')%&nbsp;&nbsp;|
				%system order_by('name', 11, 'default')%&nbsp;&nbsp;
		</div>
	</div>


END;
?>