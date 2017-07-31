<?php

$FORMS = Array();

$FORMS['img_file'] = <<<END

%system makeThumbnail('%filepath%', '200', 'auto', 'news.view')%

END;

?>