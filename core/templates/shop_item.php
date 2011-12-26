<?php
	$item = &$this->params_array["shop_item"];
	$item_sizes = &$this->params_array["shop_item_sizes"];
	$item_images = &$this->params_array["shop_item_images"];
?>
<h1><?=htmlspecialchars(stripslashes($item["name"].(($item["articul"]!="") ? " (".$item["articul"].")" : "")))?></h1>
<?php
	if ( sizeof($item_images) > 0 ):
?>
<div class="item_image"><img id="img_loader" src="/images/loader2.gif" alt="" style="position: absolute; display: none;" /><img id="full_item_image" src="/images/shop_items/full/<?=$item_images[0]?>.jpg" alt="<?=htmlspecialchars(stripslashes($item["name"]." (".$item["articul"].")"))?>" /></div>
<?php
	endif;
	if ( sizeof($item_images) > 1 ):
?>
<div><div style="margin-top: 20px; padding: 0px 5px; width: 670px; height: 70px; overflow: auto; white-space: nowrap;">
<?php
			foreach ($item_images as $item_image):
?>
<img src="/images/shop_items/micro/<?=$item_image?>.jpg" alt="" align="middle" class="change_full_item_image" />
<?php
			endforeach;
?>
</div></div>
<?php
	endif;
?>
<h4>Характеристики товара</h4>
<table class="item_full_params">
<tr><td class="pn">Категория: </td><td><a href="/catalog/category/<?=$item["category_uri"]?>/"><?=$item["category_name"]?></a></td></tr>
<tr><td class="pn">Брэнд: </td><td><a href="/catalog/brand/<?=$item["brand_uri"]?>/"><?=$item["brand_name"]?></a></td></tr>
<?php if (!empty($item["size"])): ?><tr><td class="pn">Размеры: </td><td><?=$item["size"]?></td></tr><?php endif; ?>
<?php if (!empty($item["height"])): ?><tr><td class="pn">Высота: </td><td><?=$item["height"]?></td></tr><?php endif; ?>
<?php if (!empty($item["material"])): ?><tr><td class="pn">Материал: </td><td><?=nl2br(htmlspecialchars(stripslashes($item["material"])))?></td></tr><?php endif; ?>
<?php if (!empty($item["color"])): ?><tr><td class="pn">Цвет: </td><td><?=htmlspecialchars(stripslashes($item["color"]))?></td></tr><?php endif; ?>
<tr><td class="pn">Цена: </td><td><span class="main_param"><?=number_format($item["price"], 2, ".", " ")?> руб.</span></td></tr>
</table>
<?php
	if ( !empty($item["description"]) ):
?>
<h4>Описание товара</h4>
<div class="item_description"><p><?=str_replace("\n", "</p><p>", htmlspecialchars(stripslashes($item["description"])))?></p></div>
<?php
	endif;
?>
<h4>Добавить в корзину</h4>
<div>
	<form name="fai" method="post" action="">
		<input type="hidden" name="_action" value="manage_cart" />
		<input type="hidden" name="uin" value="<?=$item["uin"]?>" />
		<input type="hidden" name="increment" value="1" />
		<table class="item_full_params">
		<?php if ( sizeof($item_sizes)>0 ): ?><tr><td class="pn">Размер:</td><td><select name="size"><?php foreach ($item_sizes as $size): $size = trim($size); ?><option value="<?=$size?>"><?=$size?></option><?php endforeach; ?></select></td></tr><?php endif; ?>
		<?php if($item["available"]>0): ?><tr><td class="pn">Количество:</td><td><input type="text" name="num" value="1" /></td></tr><?php endif; ?>
		<tr><td colspan="2" class="tsb"><div><?php if($item["available"]>0): ?><button name="sb" type="submit">Добавить</button><?php else: ?><span style="font-size: larger; font-weight: bold;">Нет в наличии</span><?php endif; ?></div></td></tr>
		</table>
	</form>
</div>
<?php /* ?>
<h4>Распечатать купон</h4>
<div>
	<form method="post" action="/catalog/coupon/" target="_blank">
		<input type="hidden" name="_action" value="create_coupon" />
		<input type="hidden" name="articul" value="<?=$item["articul_lat"]?>" />
		<table class="item_full_params">
		<?php if ( sizeof($item_sizes)>0 ): ?><tr><td class="pn">Размер:</td><td><select name="size"><?php foreach ($item_sizes as $size): $size = trim($size); ?><option value="<?=$size?>"><?=$size?></option><?php endforeach; ?></select></td></tr><?php endif; ?>
		<tr><td class="pn">Количество:</td><td><input type="text" name="num" value="1" /></td></tr>
		<tr><td colspan="2" style="text-align: center"><button name="sb2" type="submit">Распечатать</button></td></tr>
		</table>
	</form>
</div>
<?php */ ?>