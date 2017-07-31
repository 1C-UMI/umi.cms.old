<?php

$FORMS = Array();

$FORMS['block'] = <<<END

<ul>
	%items%
</ul>

END;

$FORMS['item'] = <<<END

	<li>
		<a href="%link%">%name%</a><br />
		%sub_items%
	</li>

END;

?>