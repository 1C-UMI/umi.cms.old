<?php

$FORMS = Array();

$FORMS['backup_history'] = <<<END
<setgroup name="История изменений" id="backup" form="no">

<tablegroup>
    <header>
	<hcol>№</hcol>
	<hcol>Дата и время изменения</hcol>
	<hcol>Автор</hcol>
	<hcol>Откатить</hcol>
    </header>

%rows%

</tablegroup>

</setgroup>
END;


$FORMS['config'] = <<<END

<form action="%pre_lang%/admin/backup/config_do/">

	<setgroup name="Настройка бэкапа контента" id="params_cont" form="no">
		<tablegroup>
			<row>
				<col style="width: 50%">Вести резервное копирование</col>
				<col><checkbox name="enabled" value="1" selected="%enabled%">
							<name><![CDATA[enabled]]></name>
							<value><![CDATA[1]]></value>
					</checkbox></col>
			</row>

			<row>
				<col>Максимальное кол-во хранимых событий</col>
				<col>
					<input quant="no" name="max_save_actions">
						<name><![CDATA[max_save_actions]]></name>
						<value><![CDATA[%max_save_actions%]]></value>
					</input>
				</col>
			</row>

			<row>
				<col>Максимальное время хранения события (дней)</col>
				<col><input quant="no" name="max_timelimit">
						<name><![CDATA[max_timelimit]]></name>
						<value><![CDATA[%max_timelimit%]]></value>
					</input>
				</col>
			</row>
		</tablegroup>

	<p align="left"><submit title="Сохранить" /></p>

	</setgroup>

</form>

END;


?>