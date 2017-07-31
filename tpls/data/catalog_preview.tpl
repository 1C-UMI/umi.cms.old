<?php

$FORMS = Array();

$FORMS['img_file'] = <<<END
	%system makeThumbnail(%filepath%, 150, 'auto', 'catalog_preview')%
END;


?>