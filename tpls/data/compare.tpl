<?php

$FORMS = Array();

$FORMS['groups_block'] = <<<END

	%lines%

END;

$FORMS['groups_line'] = <<<END
	%data getPropertyGroup('%id%', '%group_id%', '%template%')%
END;






$FORMS['int'] = <<<END

%value%

END;

$FORMS['price'] = <<<END

%value%

END;


$FORMS['string'] = <<<END

%value%

END;

$FORMS['text'] = <<<END
END;


$FORMS['relation'] = <<<END
%value%
END;

$FORMS['file'] = <<<END
END;

$FORMS['img_file'] = <<<END
Filename: %filename%;<br />
Filepath: %filepath%;<br />
Filepath: %src%;<br />
Size: %size%<br />
Extension: %ext%
%width% %height%
<img src="%src%" width="%width%" height="%height%" />
END;

$FORMS['swf_file'] = <<<END
END;

$FORMS['date'] = <<<END
END;

$FORMS['boolean_yes'] = <<<END
Да
END;

$FORMS['boolean_no'] = <<<END
Нет
END;


$FORMS['wysiwyg'] = <<<END
END;



/* Multiple property blocks */

$FORMS['int_mul_block'] = <<<END

%items%

END;

$FORMS['string_mul_block'] = <<<END

%items%

END;

$FORMS['text_mul_block'] = <<<END
END;

$FORMS['relation_mul_block'] = <<<END
%items%
END;

$FORMS['file_mul_block'] = <<<END
END;

$FORMS['img_file_mul_block'] = <<<END
END;

$FORMS['swf_file_mul_block'] = <<<END
END;

$FORMS['date_mul_block'] = <<<END
END;

$FORMS['boolean_mul_block'] = <<<END
END;

$FORMS['wysiwyg_mul_block'] = <<<END
END;


/* Multiple property item */

$FORMS['int_mul_item'] = <<<END
%value%%quant%
END;

$FORMS['string_mul_item'] = <<<END
%value%%quant%
END;

$FORMS['text_mul_item'] = <<<END
END;

$FORMS['relation_mul_item'] = <<<END
%value%%quant%
END;

$FORMS['file_mul_item'] = <<<END
END;

$FORMS['img_file_mul_item'] = <<<END
END;

$FORMS['swf_file_mul_item'] = <<<END
END;

$FORMS['date_mul_item'] = <<<END
END;

$FORMS['boolean_mul_item'] = <<<END
END;

$FORMS['wysiwyg_mul_item'] = <<<END
END;


/* Multiple property quant */

$FORMS['int_mul_quant'] = <<<END
, 
END;

$FORMS['string_mul_quant'] = <<<END
, 
END;

$FORMS['text_mul_quant'] = <<<END
END;

$FORMS['relation_mul_quant'] = <<<END
, 
END;

$FORMS['file_mul_quant'] = <<<END
END;

$FORMS['img_file_mul_quant'] = <<<END
END;

$FORMS['swf_file_mul_quant'] = <<<END
END;

$FORMS['date_mul_quant'] = <<<END
END;

$FORMS['boolean_mul_quant'] = <<<END
END;

$FORMS['wysiwyg_mul_quant'] = <<<END
END;



$FORMS['symlink_block'] = <<<END
%items%
END;

$FORMS['symlink_item'] = <<<END
<a href="%link%">%value%</a>%quant%
END;


$FORMS['symlink_quant'] = <<<END
, 
END;


?>