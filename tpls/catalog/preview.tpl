<?php

$FORMS = Array();

$FORMS['view_block_empty'] = <<<END

END;



$FORMS['view_block'] = <<<END


					<div class="item">
						<a href="%link%" class="title">%data getProperty(%id%, 'izobrazhenie', 'catalog_preview')%</a>

						<a href="%link%" class="title">%name%</a>
						<a href="%link%#comments" >(%comments countComments(%id%)%)</a>
						%data getPropertyGroup(%id%, 'short_params', 'catalog_params')%
					</div>

END;

?>