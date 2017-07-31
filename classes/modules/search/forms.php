<?php

$FORMS = Array();

$FORMS['config'] = <<<END
<form method='post' action='%pre_lang%/admin/search/config_do/'>

<tablegroup>

 <hrow>
  <hcol style="width: 50%;">Параметр</hcol>
  <hcol>Значение</hcol>
 </hrow>

 <row>
  <col>Кол-во результатов на страницу</col>
  <col>
  	<input   quant='no' size='5'>
  		<name><![CDATA[per_page]]></name>
		<value><![CDATA[%per_page%]]></value>
  	</input>
  </col>
 </row>

</tablegroup>

<p><submit title='Сохранить' /></p>
</form>
END;


$FORMS['control'] = <<<END

<tablegroup>
	<hrow>
		<hcol colspan="2" style="text-align: center;">
			<![CDATA[Отчет о состоянии поисковой базы]]>
		</hcol>
	</hrow>

	<row>
		<col style="width: 50%;">
			<![CDATA[Проиндексировано страниц]]>
		</col>

		<col>
			<![CDATA[%index_pages%]]>
		</col>
	</row>


	<row>
		<col>
			<![CDATA[Проиндексировано слов]]>
		</col>

		<col>
			<![CDATA[%index_words%]]>
		</col>
	</row>


	<row>
		<col>
			<![CDATA[Проиндексировано уникальных слов]]>
		</col>

		<col>
			<![CDATA[%index_words_uniq%]]>
		</col>
	</row>


	<row>
		<col>
			<![CDATA[Дата последней переиндексации]]>
		</col>

		<col>
			<![CDATA[%index_last%]]>
		</col>
	</row>
</tablegroup>

<p align="right">
	<button title="Очистить индексную таблицу">
		<onclick><![CDATA[
			if(confirm("Вы уверены?")) {
				window.location = "%pre_lang%/admin/search/truncate/";
			}
		]]></onclick>
	</button>

	<button title="Переиндексировать вручную">
		<onclick><![CDATA[
			window.location = "%pre_lang%/admin/search/reindex/";
		]]></onclick>
	</button>
</p>

END;

?>