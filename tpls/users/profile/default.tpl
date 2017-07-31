<?php

$FORMS = Array();

$FORMS['profile_block'] = <<<END

					<div id="serarchpage">	
						<div id="search_results">
						
							<div style="float:left; border: 7px solid #FFCB00;">
								%data getPropertyOfObject(%avatar%, 'picture', 'avatar')%
							</div>

<div class="userinfo">							
							<div class="row1">
								<h2>Логин (ник):</h2>
								<h3>%login%</h3>
							</div>


%data getPropertyGroupOfObject(%id%, 'short_info more_info addon', 'profile')%
</div>

					</div>
					

END;

$FORMS['bad_user_block'] = <<<END

<p>Данного пользователя не существует</p>

END;

?>