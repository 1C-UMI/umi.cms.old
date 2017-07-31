<?php

$FORMS = Array();

$FORMS['category'] = <<<END

<p>%descr%</p>

%catalog getCategoryList('default', '%category_id%')%
%catalog getObjectsList('default', '%category_id%')%
END;






$FORMS['category_block'] = <<<END

<h3>Подразделы</h3>
<ul>
	%lines%
</ul>


END;


$FORMS['category_block_empty'] = <<<END

END;


$FORMS['category_block_line'] = <<<END

<li><a href="%link%"><b>%text%</b></a></li>

END;




$FORMS['objects_block'] = <<<END

<table style="width: 100%;">
	<tr>
		<td>
			%catalog search('%category_id%', 'price_props good_props short_view short_params', 'search')%
		</td>
	</tr>
</table>

%system numpages(%total%, %per_page%, 'catalog')%

%lines%

<div style="clear: both;"></div>

%system numpages(%total%, %per_page%, 'catalog')%

<br /><br />

END;


$FORMS['objects_block_line'] = <<<END
%catalog viewObject(%id%, 'preview')%

END;



$FORMS['view_block'] = <<<END

%data getProperty(%id%, 'izobrazhenie', 'catalog_view')%


%data getPropertyGroup(%id%, 'short_params', 'catalog_full')%

<div style="clear: both;"></div>

<div style="margin-top: 20px;">
%data getPropertyGroup(%id%, 'extended_props imported', 'catalog_full')%
</div>


%data getProperty(%id%, 'opisanie', 'catalog_opisanie')%


%comments insert('%id%')%

END;

?>