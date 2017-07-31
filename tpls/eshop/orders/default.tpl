<?php

$FORMS = Array();

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

%lines%
</table>

END;


$FORMS['orders_block_line'] = <<<END

	<tr class="basketRow">
		<td>
			<a href="%link%">№%id%</a>
		</td>

		<td>
			%system convertDate(%time%, 'd.m.Y H:i')%
		</td>

		<td>
			<span class="smallRedPrice">%total% руб</span>
		</td>

		<td>
			<span class="smallRedPrice">%status%</span>
		</td>

		<td>
			
		</td>

	</tr>

	<tr>
		<td colspan="5" class="spacer"></td>
	</tr>

END;


$FORMS['orders_block_empty'] = <<<END
Нет заказов
END;


$FORMS['order_block'] = <<<END

<table border="0" width="100%">
	<tr>
		<td>
			<b>Заказ</b>
		</td>

		<td>
			#%id%
		</td>
	</tr>

	<tr>
		<td>
			<b>Дата заказа</b>
		</td>

		<td>
			%system convertDate(%order_time%, 'd-m-Y H:m:i')%
		</td>   
	</tr>

	<tr>
		<td>
			<b>Статус заказа</b>
		</td>

		<td>
			%status%
		</td>   
	</tr>

</table>

<br /><br />

<table border="0" width="100%">

	<tr class="basketHeader">
		<td style="width: 100px;">
			Товары в заказе
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
	</tr>

	<tr>
		<td colspan="4" class="spacer"></td>
	</tr>
%lines%

	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="4" class="spacer"></td>
	</tr>



	<tr>
		<td>
			<b>Итого</b>
		</td>

		<td colspan="3">
			<span class="smallRedPrice">%order_price%</span>
		</td>
	</tr>


	<tr>
		<td colspan="4" class="spacer"></td>
	</tr>



</table>

<p><b>Пометки к заказу:</b><br />
%customer_comments%</p>


<p><b>Адрес доставки: </b>%data getAllGroupsOfObject('%delivery_address_id%', 'delivery_address')% <br />
</p>


<p><a href="%pre_lang%/eshop/order_cancel/%id%">Отменить заказ</a><p>

END;

$FORMS['order_block_line'] = <<<END

	<tr>
		<td>
			<a href="%link%">%title%</a>
		</td>

		<td>
			<span class="smallRedPrice">%price%</span>
		</td>

		<td>
			%num% шт
		</td>

		<td>
			<span class="smallRedPrice">%price_total%</span>
		</td>
	</tr>


	<tr>
		<td colspan="4" class="spacer"></td>
	</tr>

END;
?>