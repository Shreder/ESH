<?php
	$items = &$this->params_array["shop_cart_list"];
	$items_num = sizeof($items);
	$items_all_num = &$this->params_array["items_all_num"];
	$items_all_sum = &$this->params_array["items_all_sum"];
?>
<h1>Корзина</h1>
<h4><span id="info_param"><?=($items_all_num>0 ? "В корзине <span id=\"num_param\">".$items_all_num."</span> товаров на общую сумму <span id=\"sum_param\">".number_format($items_all_sum, 2, ".", " ")."</span> рублей" : "Корзина пуста")?></span></h4>
<?php
	if ($items_num >0):
		for ($i=0; $i<$items_num; $i++):
			$item_sizes = (bool)strlen(trim($items[$i]["sizes"])) ? explode(",", $items[$i]["sizes"]) : array();
?>
<div class="shop_cart_item">
<table>
<tr>
	<td class="image" rowspan="8"><a href="/catalog/item/<?=$items[$i]["uin"]?>/"><img src="/images/shop_items/small/<?=$items[$i]["image"]?>.jpg" alt="" /></a><br />
	<a href="/catalog/item/<?=$items[$i]["uin"]?>/">подробно</a></td>
	<td class="pn">Название: </td><td><span class="name_art_param"><span class="main_param"><?=htmlspecialchars(stripslashes($items[$i]["name"]))?> <span class="art">(<?=htmlspecialchars(stripslashes($items[$i]["articul"].""))?>)</span></span></span><br /></td>
</tr>
<tr><td class="pn">Категория: </td><td><a href="/catalog/category/<?=$items[$i]["category_uri"]?>/"><?=$items[$i]["category_name"]?></a></td></tr>
<tr><td class="pn">Брэнд: </td><td><a href="/catalog/brand/<?=$items[$i]["brand_uri"]?>/"><?=$items[$i]["brand_name"]?></a></td></tr>
<?php if (!empty($items[$i]["size"])): ?><tr><td class="pn">Выбранный размер: </td><td><?=$items[$i]["size"]?></td></tr><?php endif; ?>
<tr><td class="pn">Цена: </td><td><span class="price_param"><?=number_format($items[$i]["price"], 2, ".", " ")?></span> руб.</td></tr>
<tr><td class="pn">Количество: </td><td><span class="num_param"><?=$items[$i]["num"]?></span><form name="fp<?=$i?>" method="post" action="/shop_cart/">
	<input type="hidden" name="_action" value="manage_cart" />
	<input type="hidden" name="uin" value="<?=$items[$i]["uin"]?>" />
	<?php if (!empty($items[$i]["size"])): ?><input type="hidden" name="size" value="<?=$items[$i]["size"]?>" /><?php endif; ?>
	<input type="hidden" name="num" value="<?=$items[$i]["num"]+1?>" />&nbsp;<button name="sbp<?=$i?>" type="submit" class="sm">+1</button>
	</form><form name="fm<?=$i?>" method="post" action="/shop_cart/">
	<input type="hidden" name="_action" value="manage_cart" />
	<input type="hidden" name="uin" value="<?=$items[$i]["uin"]?>" />
	<?php if (!empty($items[$i]["size"])): ?><input type="hidden" name="size" value="<?=$items[$i]["size"]?>" /><?php endif; ?>
	<input type="hidden" name="num" value="<?=$items[$i]["num"]-1?>" />&nbsp;<button name="sbm<?=$i?>" type="submit" class="sm"<?php if ($items[$i]["num"] < 2): ?> disabled="disabled"<?php endif; ?>>&mdash;1</button>
	</form>
	<?php /* ?><form method="post" action="/shop_cart/" onsubmit="this.sb<?=$i?>.disabled = true;">
	<input type="hidden" name="_action" value="manage_cart" />
	<input type="hidden" name="uin" value="<?=$items[$i]["uin"]?>" />
	<?php if (!empty($items[$i]["size"])): ?><input type="hidden" name="size" value="<?=$items[$i]["size"]?>" /><?php endif; ?>
	<input type="text" name="num" value="<?=$items[$i]["num"]?>" size="3" />&nbsp;<button name="sb<?=$i?>" type="submit">Изменить</button>
	</form><?php */ ?>
	<form name="fd<?=$i?>" method="post" action="/shop_cart/">
	<input type="hidden" name="_action" value="delete_from_cart" />
	<input type="hidden" name="uin" value="<?=$items[$i]["uin"]?>" />
	<input type="hidden" name="size" value="<?=$items[$i]["size"]?>" />
	<button name="sbd<?=$i?>" type="submit">Удалить</button>
	</form>
</td></tr>
<tr><td class="pn">Сумма: </td><td><span class="sum_param"><?=number_format($items[$i]["sum"], 2, ".", " ")?></span> руб.</td></tr>
<?php /* ?>
<tr><td class="pn">Распечатать купон: </td><td><form method="post" action="/catalog/coupon/" target="_blank">
	<input type="hidden" name="_action" value="create_coupon" />
	<input type="hidden" name="articul" value="<?=$items[$i]["articul_lat"]?>" />
	<?php if (!empty($items[$i]["size"])): ?><input type="hidden" name="size" value="<?=$items[$i]["size"]?>" /><?php endif; ?>
	<input type="hidden" name="num" value="<?=$items[$i]["num"]?>" />
	<button name="sbc<?=$i?>" type="submit">Распечатать</button> <span class="faq_link"><a href="/faq/">Что это такое?</a></span>
</form></td></tr>
<?php */ ?>
</table>
</div>
<?php
		endfor;
?>
<div class="all_cart_act">
<form method="post" action="/shop_order/" onsubmit="this.sbo.disabled = true;">
<button name="sbo" type="submit">Оформить заказ</button>
</form>
<?php /* ?>
<form method="post" action="/" onsubmit="if (!confirm('Корзина будет полностью очищена.\nПродолжить?'))  { return false } else { this.sbc.disabled = true; }">
<input type="hidden" name="_action" value="clear_cart" />
<button name="sbc" type="submit">Очистить корзину</button>
</form>
<?php */ ?>
<form name="fc" method="post" action="/">
<input type="hidden" name="_action" value="clear_cart" />
<button name="sbc" type="submit">Очистить корзину</button>
</form>
</div>
<?php
	endif;
?>