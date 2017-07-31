<?php
$FORMS = Array();

$FORMS['visits'] = <<<END

<tablegroup>
	<header>
		<hcol style="width: 15px; text-align: left;">#</hcol>
		<hcol style="width: 100px; text-align: left;">%order_ip% IP</hcol>
		<hcol style="width: 100px; text-align: left;">%order_score% Запросов</hcol>
		<hcol style="width: 50px; text-align: left;">%</hcol>
		<hcol style="text-align: left;">%order_referer% Ссылающийся URL</hcol>
	</header>

%stat_rows%
</tablegroup>




END;

$FORMS['ref_pages'] = <<<END

<tablegroup>
	<header>
		<hcol style="width: 15px">#</hcol>
		<hcol>URL страницы</hcol>
		<hcol style="width: 100px">Переходов</hcol>
	</header>

%rows%
</tablegroup>

END;

$FORMS['total'] = <<<TOTAL

%time_range%
<br />
<tablegroup>
	<hrow>
		<hcol colspan="2">Сводная статистика %stat_period%</hcol>
	</hrow>

	<row>
		<col style="width: 50%;">
			<b>Посетители</b>
		</col>

		<col>
			<![CDATA[%visits_last%]]>
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			В среднем в будний день
		</col>

		<col>
			<![CDATA[%visits_routine%]]>
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			В среднем в выходной день
		</col>

		<col>
			<![CDATA[%visits_weekend%]]>
		</col>
	</row>


	<row>
		<col style="width: 50%;">
			<b>Хосты</b>
		</col>

		<col>
			<![CDATA[]]>
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			В среднем в будний день
		</col>

		<col>
			<![CDATA[%hosts_routine%]]>
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			В среднем в выходной день
		</col>

		<col>
			<![CDATA[%hosts_weekend%]]>
		</col>
	</row>


	<row>
		<col style="width: 50%;">
			<b>Среднее</b>
		</col>

		<col>
			<![CDATA[]]>
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			Время просмотра
		</col>

		<col>
			<![CDATA[%visit_time%]]>
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			Глубина просмотра
		</col>

		<col>
			<![CDATA[%visit_deep%]]>
		</col>
	</row>


	<row>
		<col style="width: 50%;">
			<b>Самый популярный</b>
		</col>

		<col>
			<![CDATA[]]>
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			Источник
		</col>

		<col>
			<![CDATA[%top_source%]]>
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			Точка входа
		</col>

		<col>
			%top_enter%
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			Точка выхода
		</col>

		<col>
			%top_exit%
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			Поисковое слово
		</col>

		<col>
			%top_keyword%
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			Поисковая система
		</col>

		<col>
			%top_searcher%
		</col>
	</row>


	<row>
		<col style="width: 50%; padding-left: 25px;">
			Популярная страница
		</col>

		<col>
			%top_page%
		</col>
	</row>
</tablegroup>

TOTAL;




$FORMS['popular_pages'] = <<<END

%time_range%
<br />

%pages%
<tablegroup>
	<hrow>
		<hcol style="width: 15px">#</hcol>
		<hcol style="width: 50%">Заголовок страницы</hcol>
		<hcol style="width: 50%">URL страницы</hcol>
		<hcol style="width: 100px">&#160;Запросов&#160;</hcol>
		<hcol style="width: 50px">%</hcol>
	</hrow>

%rows%
</tablegroup>

END;


$FORMS['visitors_by_date'] = <<<END

%pages%
<tablegroup>
	<hrow>
		<hcol style="width: 15px">#</hcol>
		<hcol>Краткая информация</hcol>
		<hcol style="width: 150px;">Первый визит</hcol>
		<hcol style="width: 150px;">Последний визит</hcol>
	</hrow>

%rows%
</tablegroup>

END;

$FORMS['visitors_common'] = <<<END

%time_range%
<br />
%pages%
<tablegroup>
	<hrow>
		<hcol colspan="2">Посетители за выбранный период</hcol>
	</hrow>

	<row>
		<col>
			В среднем в будний день
		</col>

		<col style="width: 150px;">
			<![CDATA[%routine%]]>
		</col>
	</row>


	<row>
		<col>
			В среднем в выходной день
		</col>

		<col style="width: 150px;">
			<![CDATA[%weekend%]]>
		</col>
	</row>
</tablegroup>
<br />
<tablegroup>
	<hrow>
		<hcol style="width: 15px">#</hcol>
		<hcol>Дата</hcol>
		<hcol style="width: 150px;">Посетителей</hcol>
	</hrow>

%rows%
</tablegroup>

END;

$FORMS['visitor'] = <<<END


<tablegroup>
	<row>
		<col style="width: 50%;">
			Первый визит:
		</col>

		<col>
			%first_visit%
		</col>
	</row>


	<row>
		<col>
			Последний визит:
		</col>

		<col>
			%last_visit%
		</col>
	</row>

	<row>
		<col>
			Количество посещений:
		</col>

		<col>
			%visit_count%
		</col>
	</row>

	<row>
		<col>
			OS:
		</col>

		<col>
			%os%
		</col>
	</row>

	<row>
		<col>
			Браузер:
		</col>

		<col>
			%browser%
		</col>
	</row>


	<row>
		<col>
			Версия JavaScript:
		</col>

		<col>
			%js_version%
		</col>
	</row>

	<row>
		<col>
			Первый раз пришел с сайта:
		</col>

		<col>
			<a href="http://%source_link%">%source_link%</a>
		</col>
	</row>

	<row>
		<col>
			Последний раз пришел с сайта:
		</col>

		<col>
			<a href="http://%last_source_link%">%last_source_link%</a>
		</col>
	</row>

	<row>
		<col>
			Данные о пользователе:
		</col>

		<col>
			<![CDATA[%user_info%]]>
		</col>
	</row>


	<row>
		<col>
			Теги:
		</col>

		<col>
			%tags%
		</col>
	</row>



</tablegroup>

<p />

<div style="clear: both;"></div>
<tablegroup>
	<hrow>
		<hcol style="width: 15px">#</hcol>
		<hcol>Заголовок страницы</hcol>
		<hcol>URL страницы</hcol>
	</hrow>

%rows%
</tablegroup>

END;


$FORMS['sources'] = <<<END

%time_range%
<br />
%pages%
<div style="clear: both;"></div>
<tablegroup>
	<row>
		<hcol style="width: 15px">#</hcol>
		<hcol>Ссылающийся домен</hcol>
		<hcol style="width: 150px">Кол-во переходов</hcol>
	</row>

%rows%
</tablegroup>

END;

$FORMS['sources_domain'] = <<<END

%time_range%
<br />
%pages%
<div style="clear: both;"></div>
<tablegroup>
	<row>
		<hcol style="width: 15px">#</hcol>
		<hcol>Ссылающaяся страница</hcol>
		<hcol style="width: 150px">Кол-во переходов</hcol>
	</row>

%rows%
</tablegroup>

END;

$FORMS['phrases'] = <<<END

%time_range%
<br />
%pages%
<tablegroup>
	<header>
		<hcol style="width: 15px">#</hcol>
		<hcol>Поисковая фраза</hcol>
		<hcol style="width: 150px">Переходы</hcol>
	</header>
%rows%
</tablegroup>


END;


$FORMS['engines'] = <<<END
%time_range%
<br />
%pages%
<tablegroup>
	<header>
		<hcol style="width: 15px">#</hcol>
		<hcol>Поисковая система</hcol>
		<hcol style="width: 150px">Переходы</hcol>
	</header>
%rows%
</tablegroup>


END;

$FORMS['engine'] = <<<END
%time_range%
<br />
%pages%
<tablegroup>
	<header>
		<hcol style="width: 15px">#</hcol>
		<hcol>Поисковые фразы</hcol>
		<hcol style="width: 150px">Переходы</hcol>
	</header>
%rows%
</tablegroup>


END;

$FORMS['time_range'] = <<<TIME_RANGE

<form method="post" action="?p=0">

<tablegroup style="width: 100%;">
	<hrow>
		<hcol colspan="3">За период</hcol>
	</hrow>

	<row>
		<col style="width: 40%;">
			<select quant="no" style="width: 45px;" name="fd">
				%from_day%
			</select>

			<select quant="no" style="width: 90px" name="fm">
				%from_month%
			</select>

			<select quant="no" style="width: 65px" name="fy">
				%from_year%
			</select>
		</col>

		<col style="width: 40%;">
			<select quant="no" style="width: 45px;" name="td">
				%to_day%
			</select>

			<select quant="no" style="width: 90px" name="tm">
				%to_month%
			</select>

			<select quant="no" style="width: 65px" name="ty">
				%to_year%
			</select>
		</col>

		<col>
			<submit title="Отфильтровать" />
		</col>
	</row>
</tablegroup>
</form>

TIME_RANGE;

?>