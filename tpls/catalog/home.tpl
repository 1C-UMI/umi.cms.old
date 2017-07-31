<?php

$FORMS = Array();

$FORMS['category'] = <<<END

				<div id="shop" class="block">

					%catalog getCategoryList('home', '%category_id%')%

					<div class="third-column">
						<a href="%link%" class="go">Перейти в Каталог</a>
						<h2>Лучший выбор</h2>
						%catalog getObjectsList('home', '/market/videoprodukciya/zarubezhnoe_kino/', 2)%
					</div>
					<!-- dont kill this hack!!! -->
					<div style="clear:both"></div>

				</div>

	
END;


$FORMS['category_block'] = <<<END

					<div class="second-column">
						<h2>%h1%</h2>
						<ul>
							%lines%
						</ul>
					</div>

END;


$FORMS['category_block_empty'] = <<<END

END;


$FORMS['category_block_line'] = <<<END
					<li><a href="%link%">%text%</a></li>

END;



$FORMS['objects_block'] = <<<END
%lines%
END;


$FORMS['objects_block_line'] = <<<END
%catalog viewObject(%id%, 'home')%
END;


$FORMS['objects_block_empty'] = '';


$FORMS['view_block'] = <<<END
						<div class="item">
							<div class="description">
								<a href="%link%" class="name">%name%</a>
								<div class="comments">
									<a href="%link%#comments">Отзывы (%comments countComments(%id%)%)</a> | <a href="%link%#add_comment">Добавить комментарий</a>
								</div>
								<div class="price">
									Цена: <span class="old">%predyduwaya_cena%</span> <span class="new">%cena%</span>
								</div>
							</div>
							%data getProperty(%id%, 'specialnaya_cena', 'home')%
							%data getProperty(%id%, 'izobrazhenie', 'home')%
						</div>

END;

?>