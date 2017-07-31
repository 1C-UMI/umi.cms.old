<?php

$FORMS = Array();

$FORMS['search_form'] = <<<END

					<form id="search" class="block" method="get" action="%pre_lang%/search/search_do/">
						<input type="text" class="textinputs" style="width:180px;" name="search_string" value="%last_search_string%" onfocus="javascript: if(this.value == '%last_search_string%') this.value = '';" onblur="javascript: if(this.value == '') { this.value = '%last_search_string%';}"/>
						<input type="submit" value="Искать"/>
					</form>

END;

?>