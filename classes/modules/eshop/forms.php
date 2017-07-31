<?php

$FORMS = Array();

$FORMS['orders'] = <<<ORDERS

<form method="post" action="%pre_lang%/admin/eshop/orders/">
	<table border="0" cellspacing="5">
		<tr>
			<td style="vertical-align: middle;">
				Фильтр по состоянию заказа:
			</td>

			<td style="vertical-align: middle;">
				<select  quant="no"  nobr="yes" name="order_status_filter">
					<name><![CDATA[order_status_filter]]></name>
					%order_status_filter_list%
				</select>
			</td>

			<td></td>
		</tr>

		<tr>
			<td>
				<p><submit title="Отфильтровать" /></p>
			</td>

			<td></td>

			<td></td>
		</tr>
	</table>
</form>


%pages%

<br />

<form method="post" action="%pre_lang%/admin/eshop/orders_do/">

	<tablegroup>
		<hrow>
			<hcol># Заказа</hcol>
			<hcol>Дата заказа</hcol>
			<hcol>Заказчик</hcol>
			<hcol>Статус</hcol>
			<hcol>Описание</hcol>
			<hcol>Наименования</hcol>
		</hrow>
		%rows%
	</tablegroup>

	<p align="right"><submit title="Сохранить состояния заказов" /></p>
</form>

ORDERS;


$FORMS['orders_order'] = <<<ORDERS_ORDER

<form method="post" action="%pre_lang%/admin/eshop/orders_order_do/%order_id%/" name="adding_new_page">

<passthru name="exit_after_save"></passthru>


<setgroup name="Состояние заказа #%order_id%" id="eshop_order_props" form="no">

	<tablegroup>
		<row>
			<col>Дата оформления заказа:</col>
			<col>%posttime%</col>
		</row>


		<row>
			<col>ФИО покупателя:</col>
			<col>%user_info%</col>
		</row>


		<row>
			<col>Статус:</col>
			<col>
				<select  quant="no" br="no">
					<name><![CDATA[status]]></name>
					%statuses%
				</select>

			</col>
		</row>

		<row>
			<col>Номер дисконтной карты:</col>
			<col>%discount_card_number%</col>
		</row>


		<row>
			<col style="vertical-align: top;">Адрес доставки:</col>
			<col>
				%delivery_address%
			</col>
		</row>


		<row>
			<col style="vertical-align: top;">Комментарий покупателя:</col>
			<col>
				<textarea name="comment" style="width: 100%; height: 150px"><![CDATA[%comment%]]></textarea>
			</col>
		</row>

		<row>
			<col>Пометки администратора:</col>
			<col>
				<textarea name="admin_comment" style="width: 100%; height: 150px"><![CDATA[%admin_comment%]]></textarea>

			</col>
		</row>
	</tablegroup>

	<p align="right">%save_n_save%</p>
</setgroup>

<setgroup name="Список товаров в заказе #%order_id%" id="eshop_order_list" form="no">
	<tablegroup>
		<hrow>
			<hcol style="width: 50%;">Наименование</hcol>
			<hcol style="width: 16%;">Количество</hcol>
			<hcol style="width: 16%;">Цена за единицу товара</hcol>
			<hcol style="width: 16%;">Цена за набор</hcol>
		</hrow>
		%rows%
	</tablegroup>

	<p align="right">%save_n_save%</p>
</setgroup>

<setgroup name="Кредитные свойства заказа #%order_id%" id="eshop_order_credit_props" form="no">
	<p align="center">Кредитные свойства недоступны.</p><br />
</setgroup>


<script type="text/javascript">
<![CDATA[

function init_me() {
	df = document.forms['adding_new_page'];
	def_value = df.name.value;
	def_alt = df.alt_name.value;
}

function save_with_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "1";
	return acf_check(1);
}
function save_without_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "0";
	return acf_check(1);
}


frm = document.forms['adding_new_page'];
frm.onsubmit = acf_check;


cifi_upload_text = '%news_cifi_upload_text%';
]]>
</script>

</form>


ORDERS_ORDER;


$FORMS['credits'] = <<<CREDITS

<table border="0">
	<tr>
		<td style="widht: 22px;">
			<img src="/images/cms/admin/%skin_path%/ico_add.%ico_ext%" alt="Добавить" title="Добавить" border="0" />
		</td>

		<td>
			&nbsp;<a href="%pre_lang%/admin/eshop/credits_add/">Добавить</a>
		</td>
	</tr>
</table>

<br />


<tablegroup>
	<hrow>
		<hcol>Название программы</hcol>
		<hcol>Минимальная сумма</hcol>
		<hcol>Кол-во месяцев</hcol>
		<hcol>Первый платеж</hcol>
		<hcol>Ежемесячный платеж</hcol>
		<hcol>Редактировать</hcol>
		<hcol>Удалить</hcol>
	</hrow>
	%rows%
</tablegroup>

CREDITS;

$FORMS['credits_add'] = <<<CREDITS_ADD

<form method="post" action="%pre_lang%/admin/eshop/%method%/%programm_id%" name="adding_new_page">

<passthru name="exit_after_save"></passthru>

<setgroup name="Редактор содержимого" id="add_credits_params" form="no">

<table border="0" width="100%" cellspacing="0" cellpadding="0">

 <tr>
  <td width="50%">
  	<input   class="" br="yes" quant="no" size="58" style="width:355px">
		<name><![CDATA[title]]></name>
		<title><![CDATA[Название программы]]></title>
		<value><![CDATA[%title%]]></value>
	</input>

  </td>
  <td width="50%">
  	<input   class="" br="yes" size="58" quant="no" style="width:355px">
		<name><![CDATA[min_price]]></name>
		<title><![CDATA[Минимальная сумма заказа]]></title>
		<value><![CDATA[%min_price%]]></value>
	</input>
  </td>
 </tr>

 <tr>
  <td width="50%">
  	<input   class="" br="yes" quant="no" size="58" style="width:355px">
		<name><![CDATA[first_pay]]></name>
		<title><![CDATA[Коэффициент расчета первого платежа от суммы заказа, %]]></title>
		<value><![CDATA[%first_pay%]]></value>
	</input>
  </td>
  <td width="50%">
  	<input   class="" br="yes" size="58" quant="no" style="width:355px">
		<name><![CDATA[months]]></name>
		<title><![CDATA[Количество месяцев]]></title>
		<value><![CDATA[%months%]]></value>
	</input>
  </td>

 </tr>

 <tr>
  <td width="50%">
  	<input   class="" br="yes" quant="no" size="58" style="width:355px">
		<name><![CDATA[month_pay]]></name>
		<title><![CDATA[Коэффициент расчета ежемесячного платежа от суммы заказа, %]]></title>
		<value><![CDATA[%month_pay%]]></value>
	</input>
  </td>

  <td width="50%"></td>
 </tr>


 <tr>
  <td colspan="2">
   Описание кредитной программы<br/>
   <textarea  style="width: 97%; height: 173px">
		<name><![CDATA[descr]]></name>
		<value><![CDATA[%descr%]]></value>
	</textarea>

  </td>
 </tr>

 <tr>
  <td colspan="2">
   <p align="right">%save_n_save%&nbsp;&nbsp;&nbsp;&nbsp;</p>
  </td>
 </tr>

</table>

</setgroup>

<script type="text/javascript">
<![CDATA[

function init_me() {
	df = document.forms['adding_new_page'];
	def_value = df.name.value;
	def_alt = df.alt_name.value;
}

function save_with_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "1";
	return acf_check(1);
}
function save_without_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "0";
	return acf_check(1);
}


frm = document.forms['adding_new_page'];
frm.onsubmit = acf_check;



cifi_upload_text = '%news_cifi_upload_text%';
]]>
</script>

</form>


CREDITS_ADD;


$FORMS['total'] = <<<TOTAL

<tablegroup>
	<hrow>
		<hcol colspan="2">Заказы</hcol>
	</hrow>

	<row>
		<col style="width: 30%;"><b>Ожидают обработки:</b></col>
		<col>%orders_waiting_check%</col>
	</row>

	<row>
		<col>За сегодняшний день:</col>
		<col>%orders_today%</col>
	</row>

	<row>
		<col>Со вчерашнего дня:</col>
		<col>%orders_yesterday%</col>
	</row>


	<row>
		<col>Еще раньше:</col>
		<col>%orders_before%</col>
	</row>

</tablegroup>

TOTAL;


$FORMS['stores'] = <<<STORES

<form method="post" action="%pre_lang%/admin/eshop/stores_do/">

<tablegroup>
	<hrow>
		<hcol style="width: 70px;">Id склада</hcol>
		<hcol>Название склада</hcol>
		<hcol style="width: 100px;">Удалить</hcol>
	</hrow>
	%rows%

	<row>
		<col>
			<input type="text" quant="no"  style="width: 60px;" >
				<name><![CDATA[new_id]]></name>
			</input>
		</col>

		<col>
			<input type="text" quant="no"  style="width: 97%;" >
				<name><![CDATA[new_name]]></name>
			</input>

		</col>
		<col></col>
	</row>
</tablegroup>

<p align="right"><submit title="Сохранить" /></p>

</form>

STORES;


$FORMS['goods_not_enought'] = <<<GOODS_NOT_ENOUGHT

<tablegroup>
	<hrow>
		<hcol>Название товара</hcol>
		<hcol style="width: 100px;">Необходимо</hcol>
		<hcol style="width: 100px;">Всего на складах</hcol>
	</hrow>
	%rows%
</tablegroup>

GOODS_NOT_ENOUGHT;

$FORMS['goods_to_send'] = <<<GOODS_TO_SEND

<tablegroup>
	<hrow>
		<hcol>Название товара</hcol>
		<hcol style="width: 150px;">Необходимо отправить</hcol>
	</hrow>

	%rows%
</tablegroup>

GOODS_TO_SEND;


$FORMS['discounts'] = <<<DISCOUNTS

	<table border="0">
		<tr>
			<td style="widht: 22px;">
				<img src="/images/cms/admin/%skin_path%/ico_add.%ico_ext%" alt="Добавить скидку" title="Добавить скидку" border="0" />
			</td>

			<td>
				&nbsp;<a href="%pre_lang%/admin/eshop/discounts_add/">Добавить скидку</a>
			</td>
		</tr>
	</table>

<br />


<tablegroup>
	<hrow>
		<hcol style="width: 200px;">Название скидки</hcol>
		<hcol>Категории/товары</hcol>
		<hcol style="width: 100px;">Размер скидки</hcol>
		<hcol style="width: 150px;">Срок действия скидки</hcol>
		<hcol style="width: 100px;">Удалить</hcol>
	</hrow>
	%rows%
</tablegroup>

DISCOUNTS;

$FORMS['discounts_add'] = <<<DISCOUNTS_ADD

<form method="post" action="%pre_lang%/admin/eshop/%method%/%discount_id%" name="adding_new_page">
	<passthru name="exit_after_save"></passthru>


	<setgroup name="Основные параметры" id="add_discounts" form="no">

		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%">
					<input   class="" br="yes" quant="no" size="58" style="width:355px">
						<name><![CDATA[title]]></name>
						<title><![CDATA[Название скидки]]></title>
						<value><![CDATA[%title%]]></value>
					</input>

				</td>

				<td width="50%">
					<input   class="" br="yes" size="58" quant="no" style="width:355px">
						<name><![CDATA[discount_size]]></name>
						<title><![CDATA[Размер скидки, %]]></title>
						<value><![CDATA[%discount_size%]]></value>
					</input>

				</td>
			</tr>

			<tr>
				<td width="50%">
					<input   class="" br="yes" quant="no" size="58" style="width:355px">
						<name><![CDATA[start_time]]></name>
						<title><![CDATA[Начало срока действия скидок]]></title>
						<value><![CDATA[%start_time%]]></value>
					</input>

				</td>

				<td width="50%">
					<input   class="" br="yes" quant="no" size="58" style="width:355px">
						<name><![CDATA[end_time]]></name>
						<title><![CDATA[Конец срока действия скидок]]></title>
						<value><![CDATA[%end_time%]]></value>
					</input>

				</td>
			</tr>


			<tr>
				<td width="50%"><br />
					<checkbox    selected="%is_active%">
					<name><![CDATA[is_active]]></name>
					<title><![CDATA[Скидка активна]]></title>
					<value><![CDATA[1]]></value>
				</checkbox>

				</td>

				<td width="50%">
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<p align="right">%save_n_save%&nbsp;&nbsp;&nbsp;&nbsp;</p>
				</td>
			</tr>
		</table>

	</setgroup>

	<setgroup name="Список категорий и объектов" id="add_discounts_list" form="no">
		<div id=""></div>

		<cat-getter title="Выберите объект или категорию" title_list="Список выбранных объектов и категорий" br="yes" name="d" id="d" style="width: 97%; height: 250px;">
			%cat_getter_items%
		</cat-getter>

		<p align="right">%save_n_save%&nbsp;&nbsp;&nbsp;&nbsp;</p>
	</setgroup>

<script type="text/javascript">
<![CDATA[

function init_me() {
	df = document.forms['adding_new_page'];
	def_value = df.name.value;
	def_alt = df.alt_name.value;
}

function save_with_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "1";
	return acf_check(1);
}
function save_without_exit() {
	document.forms['adding_new_page'].exit_after_save.value = "0";
	return acf_check(1);
}


frm = document.forms['adding_new_page'];
frm.onsubmit = acf_check;



cifi_upload_text = '%news_cifi_upload_text%';
]]>
</script>

</form>


DISCOUNTS_ADD;

$FORMS['dcards'] = <<<DCARDS

<form method="post" action="%pre_lang%/admin/eshop/dcards_do/">

<tablegroup>
	<hrow>
		<hcol>
			Первая цифра кода
		</hcol>

		<hcol>
			Размер скидки (%)
		</hcol>

		<hcol style="width: 100px;">
			Удалить
		</hcol>
	</hrow>
	%rows%

	<row>
		<col>
			<input type="text"  quant="no" >
				<name><![CDATA[card_num_new]]></name>
			</input>

		</col>

		<col>
			<input type="text"  quant="no" >
				<name><![CDATA[card_size_new]]></name>
			</input>

		</col>

		<col></col>
	</row>
</tablegroup>

<p><submit title="Сохранить" /></p>

</form>

DCARDS;


$FORMS['config'] = <<<END

<form method="post" action="%pre_lang%/admin/eshop/config_do/">
<setgroup name="Настройка магазина" id="params_cont" form="no">
<tablegroup>

	<row>
		<col style="width: 50%;">E-mail на который следует отправлять заказы</col>
		<col>
				<input  br="no" quant="no"  style="width: 350px;">
					<name><![CDATA[shop_email]]></name>
					<value><![CDATA[%shop_email%]]></value>
				</input>
		</col>
	</row>

	<row>
		<col style="width: 50%;">E-Mail для поля обратный адрес</col>
		<col>
				<input  br="no" quant="no"  style="width: 350px;">
					<name><![CDATA[from_email]]></name>
					<value><![CDATA[%from_email%]]></value>
				</input>
		</col>
	</row>

<row>
	<col>Скидка при сопутствующих товарах (%)</col>
	<col>
		<input  br="no" quant="no"  style="width: 350px;">
				<name><![CDATA[related_discount]]></name>
				<title><![CDATA[Конец срока действия скидок]]></title>
				<value><![CDATA[%related_discount%]]></value>
		</input>
</col>
</row>

</tablegroup>

<p align="left"><submit title="Сохранить" /></p>
</setgroup>
</form>

END;


$FORMS['price_down'] = <<<END

<tablegroup>
	<hrow>
		<hcol>Наименование</hcol>
		<hcol>Пользователь</hcol>
	</hrow>
	%rows%
</tablegroup>

END;



$FORMS['csv_import'] = <<<END

<form method="post" action="%pre_lang%/admin/eshop/csv_import_do/">

<script type="text/javascript">
<![CDATA[

cifi_upload_text = '%eshop_cifi_upload_text%';
]]>
</script>

	<div>
		Закачайте файл CSV:<br />
		%cifi_csvfile%
	</div>

	<div>

		<symlinkInput id="404" style="width: 370px; height:100px" quant="no" br="yes">
			<title><![CDATA[Выберите раздел сайта, в который нужно импортировать данные из файла]]></title>
			<tip><![CDATA[]]></tip>
			<values></values>
		</symlinkInput>
	</div>

<p align="left"><submit title="Импортировать" /></p>

</form>

END;

?>