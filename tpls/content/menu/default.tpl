<?php

$FORMS = Array();


$FORMS['menu_block_level1'] = <<<END
			<div id="menu">
%lines%
			</div>

END;

$FORMS['menu_line_level1'] = <<<END
				<a href="%link%">%text%</a>

END;

$FORMS['menu_line_level1_a'] = <<<END
				<a class="active" href="%link%">%text%</a>

END;


?>