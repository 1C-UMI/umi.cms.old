<?php

$FORMS = Array();

$FORMS['img_file'] = <<<END
	%system makeThumbnail(%filepath%, '70', '50', 'view')%
END;


$FORMS['boolean_yes'] = <<<END
<img class="special-price png" src="/images/icons/special_price.png" />
END;

$FORMS['boolean_no'] = <<<END
END;

$FORMS['string'] = <<<END
%value%
END;

?>