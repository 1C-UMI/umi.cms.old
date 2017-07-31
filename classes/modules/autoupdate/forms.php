<?php

$FORMS = Array();

$FORMS['versions'] = <<<VERSIONS

<tablegroup>
	<row>
		<col style="width: 50%;">
			<![CDATA[Редакция системы]]>
		</col>

		<col>
			<![CDATA[%system_edition%]]>
		</col>
	</row>


	<row>
		<col>
			<![CDATA[Дата последнего обновления]]>
		</col>

		<col>
			<![CDATA[%last_updated%]]>
		</col>
	</row>


	<row>
		<col>
			<![CDATA[Версия системы]]>
		</col>

		<col>
			<![CDATA[%system_version%]]>
		</col>
	</row>


	<row>
		<col>
			<![CDATA[Номер сборки]]>
		</col>

		<col>
			<![CDATA[%system_build%]]>
		</col>
	</row>
</tablegroup>


VERSIONS;


?>