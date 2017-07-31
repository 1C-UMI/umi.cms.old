<?php

$FORMS = Array();

$FORMS['groups_block'] = <<<END

<br />
<!--	<a href="%pre_lang%/eshop/delivery_edit/%id%/">-->
	%lines%
<!--	</a>-->

END;

$FORMS['groups_line'] = <<<END

	%data getPropertyGroupOfObject('%id%', '%group_id%', '%template%')%
END;


$FORMS['group'] = <<<END

		%lines%

END;

$FORMS['group_line'] = <<<END


%data getPropertyOfObject('%id%', '%prop_id%', '%template%')%<br />

END;




$FORMS['int'] = <<<END

%title%: %value%

END;

$FORMS['string'] = <<<END

%title%: %value%

END;

$FORMS['text'] = <<<END

%title%: %value%

END;


$FORMS['relation'] = <<<END
%title%: %value%
END;

$FORMS['date'] = <<<END
%title%: %value%
END;

$FORMS['boolean_yes'] = <<<END
%title%: Да
END;

$FORMS['boolean_no'] = <<<END
%title%: Нет
END;


$FORMS['wysiwyg'] = <<<END
%title%: %value%
END;



$FORMS['relation_mul_block'] = <<<END
%title%: %items%
END;

$FORMS['relation_mul_item'] = <<<END
%value%%quant%
END;
$FORMS['relation_mul_quant'] = <<<END
, 
END;


$FORMS['symlink_block'] = <<<END
%title%: %items%
END;

$FORMS['symlink_item'] = <<<END
<a href="%link%">%value%(</a>%quant%
END;


$FORMS['symlink_quant'] = <<<END
, 
END;


?>