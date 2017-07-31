<?php

$FORMS['filter_results'] = <<<END

	<tablegroup>
		<header>
			<hcol style="text-align: left">Имя</hcol>
			<hcol style="width: 100px">Изменить</hcol>
			<hcol style="width: 100px">Удалить</hcol>
		</header>
		%rows%
	</tablegroup>

END;

$FORMS['filter_results_row'] = <<<END

		<row>
			<col><a href="%edit_link%">%name%</a> %core getTypeEditLink(%type_id%)%</col>
			<col style="text-align: center">
				<a href="%edit_link%"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" /></a>
			</col>
			<col>%del_link%</col>
		</row>

END;

?>