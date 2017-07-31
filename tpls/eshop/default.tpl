<?php

$FORMS['my_cart'] = <<<END

<form method="post" action="%pre_lang%/eshop/my_cart_save/" name="eshop_cart_form">
%orderlist%
</form>

END;

$FORMS['my_cart_noitems'] = <<<END

<p align="left">Ваша корзина пуста</p>

END;

$FORMS['orderlist_block'] = <<<END

<script type="text/javascript">
	var orders = Array();
</script>

<table width="100%" cellspacing="0">
	<tr class="basketHeader">
		<td>
			Товары
		</td>

		<td>
			Цена
		</td>

		<td>
			Кол-во
		</td>

		<td>
			Сумма
		</td>

		<td>
			Статус
		</td>

		<td>
			Удалить
		</td>
	</tr>

	<tr>
		<td colspan="6" class="spacer"></td>
	</tr>

%items%

	<tr class="basketRow">
		<td colspan="6">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="6" class="spacer"></td>
	</tr>

	<tr class="basketRow">
		<td colspan="1">Стоимость заказа</td>
		<td class="basketPrice" id="eshop_order_price">%price_order%</td>
		<td colspan="4">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="6" class="spacer"></td>
	</tr>


	<tr class="basketRow">
		<td colspan="1">Скидка</td>
		<td class="basketPrice" id="eshop_dcard_discount">%dcard_discount%</td>
		<td colspan="4">&nbsp;</td>
	</tr>


	<tr>
		<td colspan="6" class="spacer"></td>
	</tr>


	<tr class="basketTotal">
		<td class="subHeader" style="font-size: 11px;">
			Всего к оплате
		</td>

		<td>
			<span class="redPrice" id="eshop_total_price">%price_total%</span>
		</td>

		<td colspan="2">
		</td>
		<td colspan="2">
			<a href="" onclick="javascript: return eshop_cart_post();" id="order_do_link" %order_link_disabled%><b>Оформить заказ</b></a>
		</td>
	</tr>


	<tr>
		<td colspan="6" class="spacer"></td>
	</tr>
</table>


%eshop address_choice()%

<br />
<h2>Комментарий к заказу</h2>

<textarea name="customer_comment" style="width: 500px; height: 120px;"></textarea>

%discount_card%

END;


$FORMS['discount_card'] = <<<END

<br />
<p>Номер вашей дисконтной карты: <input type="text" name="cardnum" value="%cardnum%" onchange="javascript: eshop_cart_cardnumchange_prepare(this);" /></p>


END;



$FORMS['orderlist_item'] = <<<END

<script type="text/javascript">
	orders[orders.length] = "%order_item_id%";
</script>

	<tr class="basketRow" id="orderitem_%order_item_id%">
		<td>
			<table cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: middle;"><a href="%link%">%title%</a></td><td style="vertical-align: middle;"><a href="%link%">%spec%</a></td></tr></table>
		</td>

		<td width="20%" class="basketPrice" id="eshop_single_price_%order_item_id%">%price%</td>

		<td width="15%">
			<input type="text" name="num[%order_item_id%]" value="%num%" id="%order_item_id%" size="5" onkeyup="javascript: eshop_cart_numchange_prepare(this);" />
		</td>

		<td width="15%">
			<span class="smallRedPrice" id="eshop_price_%order_item_id%">%price_total%</span>
		</td>

		<td width="12%">
			%status%
		</td>

		<td width="7%" align="center">
			<a href="" onclick="javascript: eshop_cart_removeRow(%order_item_id%); return false;"><img src="/images/del.gif" alt="Удалить" title="Удалить" border="0" /></a>
		</td>
	</tr>

	<tr id="orderitem_quant_%order_item_id%">
		<td colspan="6" class="spacer"></td>
	</tr>
END;


$FORMS['personal'] = <<<END


<h1>Товары в корзине</h1>
%eshop my_cart()%

<br />

<h1>Мои заказы</h1>
%eshop my_orders()%


<br /><br />
<p><a href="%pre_lang%/users/change_settings/"><u>Редактировать личные данные</u></a></p>

END;

$FORMS['orders_block'] = <<<END

<table width="100%" cellspacing="0">

	<tr class="basketHeader">
		<td style="width: 100px;">
			Номер заказа
		</td>

		<td>
			Дата заказа
		</td>

		<td>
			Общая сумма
		</td>

		<td>
			Статус
		</td>


		<td></td>
	</tr>

	<tr>
		<td colspan="6" class="spacer"></td>
	</tr>

%items%
</table>

END;

$FORMS['orders_item'] = <<<END

	<tr class="basketRow">
		<td>
			№%order_id%
		</td>

		<td>
			%order_date%
		</td>

		<td>
			<span class="smallRedPrice">%order_price% руб</span>
		</td>

		<td>
			<span class="smallRedPrice">%order_status%</span>
		</td>


		<td>
			%cancel_link%
		</td>

	</tr>

	<tr>
		<td colspan="5" class="spacer"></td>
	</tr>

END;


?>