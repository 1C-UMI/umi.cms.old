<?php
	$FORMS = Array();

	$FORMS['pages_block'] = <<<END
	<div>%pages%</div>
END;

	$FORMS['pages_item'] = <<<END

	<a href="%link%">%num%</a>%quant%

END;

	$FORMS['pages_item_a'] = <<<END
	<a href="%link%"><b>%num%</b></a>%quant%
END;

	$FORMS['pages_quant'] = <<<END
&nbsp;&nbsp;&nbsp;
END;

	$FORMS['pages_block_empty'] = <<<END
END;


	$FORMS['order_by'] = <<<END
	<a href="%link%">%title%</a>
END;

	$FORMS['order_by_a'] = <<<END
	<span style="color: #000;"><b>%title%</b></span>
END;
?>