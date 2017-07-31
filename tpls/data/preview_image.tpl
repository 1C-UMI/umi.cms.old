<?php

$FORMS = Array();

$FORMS['img_file'] = <<<END
	%system makeThumbnail(%filepath%, 120, 'auto', 'view')%
END;


?>