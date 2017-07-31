<?php

$FORMS = Array();


$FORMS['confs_block'] = <<<CONFS_BLOCK

					<div id="forum" class="block">
						<a href="%pre_lang%/talks/" class="go">Перейти в Форум</a>
						<h2>Форум</h2>
						%lines%
					</div>

CONFS_BLOCK;

$FORMS['confs_block_line'] = <<<CONFS_LINE

						<div class="item">
							<a href="%link%">%name% (%topics_count%)</a>
							<p>%descr%</p>
							%forum conf_last_message(%id%, 'home')%
						</div>

CONFS_LINE;


$FORMS['conf_last_message'] = <<<END

<br />
<a href="%link%">%name%</a><br />

%system convertDate(%publish_time%, 'd.m.Y в H:i')%<br />
%users viewAuthor(%author_id%, 'default')%

END;

?>
