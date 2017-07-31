<?php

$FORMS = Array();

$FORMS['img_file'] = <<<END
	%system makeThumbnail(%filepath%, 200, 'auto', 'catalog_view')%
END;


?>