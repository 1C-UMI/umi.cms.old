<?php

$FORMS = Array();


$FORMS['compare_block'] = <<<END

<table width="100%" cellspacing="0" cellpadding="0">
%headers%
%lines%
</table>

END;

$FORMS['compare_block_header'] = <<<END
	<tr>
		<td style="vertical-align: bottom;">
			
		</td>
		%items%
	</tr>

	<tr>
		<td colspan="100"><div class="compareSpacer"></div></td>
	</tr>

END;

$FORMS['compare_block_header_item'] = <<<END
	<td valign="bottom">
		%catalog viewObject('%id%', 'compare')%
	</td>
END;

$FORMS['compare_block_line'] = <<<END
	<tr style="background-color: %bg%;">
		<td style="width: 200px; height: 40px; vertical-align: middle; font-weight: bold;">
			%title%
		</td>
		%items%
	</tr>

	<tr>
		<td colspan="100"><div class="compareSpacer"></div></td>
	</tr>


END;

$FORMS['compare_block_line_item'] = <<<END
		<td style="width: 200px; height: 40px; vertical-align: middle; font-weight: bold;">
			%data getProperty('%id%', '%name%', 'compare')%
		</td>

END;

$FORMS['compare_list_block'] = <<<END

<h2>Сравнение</h2>

%lines%


<a href="%link%">Сравнить</a>

END;

$FORMS['compare_list_block_line'] = <<<END

<p><a href="%link%">%name%</a></p>

END;
?>
