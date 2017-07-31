<?php

$FORMS = Array();


$FORMS['search_block'] = <<<END

<form method="get">
	<div>
		<div style="padding-bottom:5px; font-weight:bold;">Фильтр по товарам</div>

		<table border="0" cellspacing="3">
			%lines%
		</table>

	</div>

	<p><input type="submit" class="filter_btn" value="Подобрать" />&nbsp;&nbsp;&nbsp;<input class="filter_btn" type="reset" value="Сбросить" class="filter_btn" /></p>
</form>


END;


$FORMS['search_block_line'] = <<<END

	<tr>
		%selector%
	</tr>

END;



$FORMS['search_block_line_relation'] = <<<END

<td style=" width: 100px;">
	%title%
</td>
<td>
	<select name="fields_filter[%name%]" style="border: 1px solid #CCCCCC;" class="textinputs" style="width:205px"><option />%items%</select>
</td>

END;


$FORMS['search_block_line_text'] = <<<END

<td>
	%title%
</td>

<td>
	<input type="text" name="fields_filter[%name%]" class="textinputs" value="%value%" />
</td>

END;

$FORMS['search_block_line_price'] = <<<END

<td>
	%title%
</td>

<td>
	от <input type="text" name="fields_filter[%name%][0]" class="textinputs" style="width:81px;" value="%value_from%" size="12" />
	до <input type="text" name="fields_filter[%name%][1]" class="textinputs" style="width:81px;" value="%value_to%" size="12" />
</td>


END;

$FORMS['search_block_line_boolean'] = <<<END

<td>
	<label for="fields_filter[%name%]" style="">%title%</label>
</td>

<td>
	<input type="checkbox" name="fields_filter[%name%]" id="fields_filter[%name%]" %checked% value="1" /> 
</td>

END;


?>
