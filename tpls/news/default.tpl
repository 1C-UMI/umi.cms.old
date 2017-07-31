<?php
$FORMS = Array();

$FORMS['lastlist_block'] = <<<END

%items%


END;

$FORMS['lastlist_item'] = <<<END

					<div class="item">
						<span class="date">%system convertDate(%publish_time%, 'd.m.Y')%</span> | 
						<a href="%link%" class="title">%header%</a>

						%data getProperty(%id%, 'anons_pic', 'news.anons')%

						<p>%anons%</p>
						<div class="comments">
							<a href="%link%#comments" >Комментарии (%comments countComments(%id%)%)</a> | <a href="%link%#add_comment">Добавить комментарий</a>
						</div>
					</div>

END;

$FORMS['view'] = <<<END

<!-- img src="%publish_pic%" width="200" class="news_photo" / -->

%data getProperty(%id%, 'publish_pic', 'news.view')%

%content%

%news related_links(%id%)%

%comments insert('%id%')%

END;

$FORMS['related_block'] = <<<END

<div id="related_news">
	<p>Похожие новости:</p>
	<ul>
		%related_links%
	</ul>
</div>

END;

$FORMS['related_line'] = <<<END
	<li><a href="%link%"><b>%name%</b> (%system convertDate(%publish_time%, 'Y-m-d')%)</a></li>
END;



$FORMS['listlents_block'] = <<<END

<p>Рубрики новостей:</p>
<ul>
%items%
</ul>


END;

$FORMS['listlents_item'] = <<<END

	<li><a href="%link%" class="title">%header%</a></li>

END;

$FORMS['listlents_block_empty'] = <<<END
END;

?>