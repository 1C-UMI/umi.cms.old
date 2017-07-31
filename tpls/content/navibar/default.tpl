<?php

$FORMS = Array();

$FORMS['navibar'] = <<<NAVIBAR
					<div id="navibar">
%elements%
					</div>
				
NAVIBAR;

$FORMS['navibar_empty'] = <<<NAVIBAR
NAVIBAR;

$FORMS['element'] = '<a href="%pre_lang%%link%">%text%</a>';

$FORMS['element_active'] = "%text%";

$FORMS['quantificator'] = " / ";

?>
