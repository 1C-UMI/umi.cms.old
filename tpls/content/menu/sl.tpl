<?php

$FORM = Array();

$FORMS['menu_block_level1'] = <<<END
%lines%
END;

$FORMS['menu_line_level1'] = <<<END

END;

$FORMS['menu_line_level1_a'] = <<<END
%sub_menu%
END;



$FORMS['menu_block_level2'] = <<<END

					<ul id="submenu">
%lines%
					</ul>
END;

$FORMS['menu_line_level2'] = <<<END
						<li><a href="%link%">%text%</a></li>

END;

$FORMS['menu_line_level2_a'] = <<<END

						<li class="active"><a href="%link%">%text%</a></li>
%sub_menu%

END;


$FORMS['menu_block_level3'] = <<<END

						<li>
							<ul>
%lines%
							</ul>
						</li>
END;

$FORMS['menu_line_level3'] = <<<END
								<li><a href="%link%">%text%</a></li>

END;

$FORMS['menu_line_level3_a'] = <<<END
								<li class="active">%text%</li>
END;


?>
