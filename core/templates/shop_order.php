<?php
	$items = &$this->params_array["shop_cart_list"];
	$items_num = sizeof($items);
	$items_all_num = &$this->params_array["items_all_num"];
	$items_all_sum = &$this->params_array["items_all_sum"];
	$shipping_price = &$this->params_array["shipping_price"];
	$min_order_sum = &$this->params_array["min_order_sum"];
	$items_all_sum_with_shipping = &$this->params_array["items_all_sum_with_shipping"];
	$order_data = &$this->params_array["order_data"];
	$order_errors = &$this->params_array["order_errors"];
?>
<h1>Оформление заказа</h1>
<?php
	if ( $items_num > 0 ):
		if ( $items_all_sum >= $min_order_sum ):
?>
<h4>Ваш заказ</h4>
<form method="post" action="/shop_order/" onsubmit="this.sbo.disabled = true;">
<input type="hidden" name="_action" value="shop_order" />
<?php
			for ($i=0; $i<$items_num; $i++):
?>
<input type="hidden" name="order_uin[<?=$i?>]" value="<?=$items[$i]["uin"]?>" />
<input type="hidden" name="order_size[<?=$i?>]" value="<?=$items[$i]["size"]?>" />
<input type="hidden" name="order_num[<?=$i?>]" value="<?=$items[$i]["num"]?>" />
<?php
			endfor;
?>
<table class="shop_order">
<tr><td colspan="2"><ol>
<?php
			for ($i=0; $i<$items_num; $i++):
?>
<li>
	<?=htmlspecialchars(stripslashes($items[$i]["name"]))?>
	<span class="art">(<?=htmlspecialchars(stripslashes($items[$i]["articul"]))?>)</span>,
	<?php if (!empty($items[$i]["size"])): ?><span class="art">размер <?=htmlspecialchars(stripslashes($items[$i]["num"]))?></span>,<?php endif; ?>
	<span class="art">кол-во <?=htmlspecialchars(stripslashes($items[$i]["num"]))?>,</span>
	<span class="art">цена <?=htmlspecialchars(stripslashes(number_format($items[$i]["sum"], 2, ".", " ")))?> руб.</span>
</li>
<?php
			endfor;
?>
</ol></td></tr>
<tr>
	<td class="pn">Суммарная стоимость заказа: </td>
	<td><span class="main_param"><?=number_format($items_all_sum, 2, ".", " ")?> рублей</span></td>
</tr>
<?php /* ?>
<tr>
	<td class="pn">Стоимость доставки по г. Москва: </td>
	<td><?=number_format($shipping_price, 2, ".", " ")?> рублей</td>
</tr>
<tr>
	<td class="pn">Стоимость заказа вместе с доставкой: </td>
	<td><span class="main_param"><?=number_format($items_all_sum_with_shipping, 2, ".", " ")?> рублей</span></td>
</tr>
<?php */ ?>
<tr>
	<td class="pn">Способы доставки и оплаты: *</td>
	<td>
		<table class="delivery_opt">
		<tr>
			<td class="opt"><input type="radio" name="delivery_type" value="cash" id="d_op1"<?=(($order_data["delivery_type"]=="cash") || empty($order_data["delivery_type"]) ? " checked=\"checked\"" : "")?> /></td>
			<td><label for="d_op1">Налиными курьеру при доставке (только в Москве)</label></td></td>
		</tr>
		<tr>
			<td class="opt"><input type="radio" name="delivery_type" value="bank" id="d_op2"<?=($order_data["delivery_type"]=="bank" ? " checked=\"checked\"" : "")?> /></td>
			<td><label for="d_op2">Банковским переводом (100% предоплата)</label></td>
		</tr>
		</table>
		<?php if (!empty($order_errors["delivery_type"])): ?><br /><span class="error_text"><?=$order_errors["delivery_type"]?></span><?php endif; ?>
	</td>
</tr>
<tr>
	<td class="pn">Контактное лицо: *</td>
	<td><input type="text" name="contact_name" value="<?=htmlspecialchars($order_data["contact_name"])?>" /><?php if (!empty($order_errors["contact_name"])): ?><br />
	<span class="error_text"><?=$order_errors["contact_name"]?></span><?php endif; ?></td>
</tr>
<tr>
	<td class="pn">Контактный телефон *:</td>
	<td><input type="text" name="contact_tel" value="<?=htmlspecialchars($order_data["contact_tel"])?>" /><?php if (!empty($order_errors["contact_tel"])): ?><br />
	<span class="error_text"><?=$order_errors["contact_tel"]?></span><?php endif; ?></td>
</tr>
<tr>
	<td class="pn">Контактный e-mail: *</td>
	<td><input type="text" name="contact_email" value="<?=htmlspecialchars($order_data["contact_email"])?>" /><?php if (!empty($order_errors["contact_email"])): ?><br />
	<span class="error_text"><?=$order_errors["contact_email"]?></span><?php endif; ?></td>
</tr>
<tr>
	<td class="pn">Адрес доставки *:</td>
	<td><textarea name="shipping_address" cols="30" rows="10"><?=htmlspecialchars($order_data["shipping_address"])?></textarea><?php if (!empty($order_errors["shipping_address"])): ?><br />
	<span class="error_text"><?=$order_errors["shipping_address"]?></span><?php endif; ?></td>
</tr>

<tr>
	<td class="pn">Контрольный код: *</td>
	<td><img id="captcha" src="/kcaptcha/index.php" alt="" align="middle" />
	<span class="small_link"><button id="captcha_refresh" disabled="disabled">обновить код</button></span><br />
	<input type="text" name="control_code" />
	<?php if (!empty($order_errors["control_code"])): ?><br />
	<span class="error_text"><?=$order_errors["control_code"]?></span><?php endif; ?></td></tr>
<tr>
	<td colspan="2" class="bt"><span class="comment">* - поля, обязательные для заполнения</span><br /><br />
	<button type="submit" name="sbo">Заказать</button><?php if (!empty($order_errors["main_msg"])): ?><br /><br />
	<span class="main_error_text"><?=$order_errors["main_msg"]?></span><?php endif; ?></td></tr>
</table>
</form>
<?php
		else:
?>
<h5>Оформление заказа невозможно, т.к. минимальная сумма заказа составляет 500 рублей.</h5>
<?php
		endif;
	elseif ( $this->params_array["order_status"] === true ):
?>
<h5>Заказ обработан успешно! В ближайшее время наши менеджеры свяжутся с Вами.</h5>
<h5>Номер Вашего заказа: <?=$this->params_array["order_number"]?></h5>
<?php
	else:
?>
<h5>Корзина пуста. Оформление заказа невозможно.</h5>
<?php
	endif;
?>