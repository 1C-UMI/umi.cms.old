<?php

$FORMS = Array();

$FORMS['img_file'] = <<<END
	<p>
		<a href="%src%" target="_blank">%system makeThumbnail(%filepath%, 120, 'auto', 'avatar')%</a>
	</p>
END;


?>