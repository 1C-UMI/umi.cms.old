<?php

$FORMS = Array();


$FORMS['projects_block'] = <<<END

				<div id="faq" class="block">
					<a href="/umicms/" class="go">Перейти в F.A.Q.</a>
					%lines%
				</div>

END;

$FORMS['projects_block_empty'] = <<<END

END;

$FORMS['projects_block_line'] = <<<END
					<h2>%text%</h2>
					%faq project('home', %id%, 3)%

END;

$FORMS['categories_block'] = <<<END

%lines%

END;

$FORMS['categories_block_empty'] = <<<END
	<p>Вопросов по данному проекту пока нет.</p>
END;

$FORMS['categories_block_line'] = <<<END
%faq category('home', %id%)%

END;

$FORMS['questions_block'] = <<<END

%lines%

END;

$FORMS['questions_block_empty'] = <<<END
END;

$FORMS['questions_block_line'] = <<<END

					<div class="item">
						<a href="%link%">%question%</a>
						<p>%answer%</p>
					</div>

END;


?>