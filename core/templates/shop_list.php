<?php
	$items = &$this->params_array["list_shop_items"];
	$items_num = sizeof($items);
	
	if ($this->params_array["list_type"] == "brand"):
?>
<h1>Товары &quot;<?=$this->params_array["list_name"]?>&quot;</h1>
<div class="head_brand_logo">
<?php
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/images/brand_logos/".$this->params_array["list_uri"].".png")):
?>
	<div><img src="/images/brand_logos/<?=$this->params_array["list_uri"]?>.png" alt="<?=$this->params_array["list_name"]?>" /></div>
<?php
		endif;
?>
	<p><?=str_replace("\n", "</p><p>", htmlspecialchars(stripslashes($this->params_array["brand_text"])))?></p>
</div>
<?php
	elseif ($this->params_array["list_type"] == "category"):
?>
<h1><?=$this->params_array["list_name"]?></h1>
<?php
	elseif ($this->params_array["list_type"] == "search"):
?>
<h1>Поиск товаров</h1>
<?php
		if ($items_num > 0):
?>
<div class="search_msg">Запрос успешно выполнен.</div>
<?php
		else:
?>
<div class="search_msg">По запросу ничего не найдено.</div>
<?php
		endif;
	else:
?>
<h1>Список товаров</h1>
<?php
	endif;
	
	if ($items_num > 0):
?>
<table class="shop_list_table">
<?php
		for ($i=0; $i<$items_num; $i=$i+3):
?>
<tr>
<?php
			for ($j=$i; $j<$i+3; $j++):
?>
	<td>
<?php
				if ( is_array($items[$j]) ):
					$item_sizes = (bool)strlen(trim($items[$j]["size"])) ? explode(",", $items[$j]["size"]) : array();
					if ( file_exists($_SERVER["DOCUMENT_ROOT"]."/images/shop_items/small/".$items[$j]["image"].".jpg") ):
?>
		<div><a href="/catalog/item/<?=$items[$j]["uin"]?>/"><img src="/images/shop_items/small/<?=$items[$j]["image"]?>.jpg" alt="" /></a></div>
<?php
					endif;
?>
		<div class="item_name"><a href="/catalog/item/<?=$items[$j]["uin"]?>/"><strong><?=htmlspecialchars(stripslashes($items[$j]["name"]))?><br /><span class="art">(<?=htmlspecialchars(stripslashes($items[$j]["articul"]))?>)</span></strong></a></div>
		<div>
			<form name="fal<?=$j?>" method="post" action="">
				<input type="hidden" name="_action" value="manage_cart" />
				<input type="hidden" name="uin" value="<?=$items[$j]["uin"]?>" />
				<input type="hidden" name="increment" value="1" />
				<table class="params">
				<tr>
					<td class="pn">Цена:</td>
					<td><?=number_format($items[$j]["price"], 2, ".", " ")?> руб.</td>
				</tr>
				<?php if ( sizeof($item_sizes)>0 ): ?><tr>
					<td class="pn">Размер:</td>
					<td><select name="size"><?php foreach ($item_sizes as $size): $size = trim($size); ?><option value="<?=$size?>"><?=$size?></option><?php endforeach; ?></select></td>
				</tr><?php endif; ?>
				<?php if($items[$j]["available"]>0): ?><tr>
					<td class="pn">Количество:</td>
					<td><input type="text" name="num" value="1" /></td>
				</tr><?php endif; ?>
				<tr><td colspan="2" class="tsb"><div><?php if($items[$j]["available"]>0): ?><button name="sb<?=$j?>" type="submit">Добавить в корзину</button><?php else: ?><span style="font-size: larger; font-weight: bold;">Нет в наличии</span><?php endif; ?></div></td></tr>
				</table>
			</form>
		</div>
<?php
				endif;
?>
	</td>
<?php
			endfor;
?>
</tr>
<?php
		endfor;
?>
</table>
<?php
	endif;
?>
<div class="list_all_num">Всего товаров: <?=$items_num?></div>