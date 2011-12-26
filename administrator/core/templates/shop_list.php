<table class="table_list_main">
<tr>
	<td style="padding-bottom: 30px; text-align: center; font-size: 20px; font-weight: bold;"><?php print $THIS_PAGE_DATA["parent_title"]; ?>: Список товаров по <?=$THIS_PAGE_DATA["list"]["list_type_text"]?></td>
</tr>
<tr>
	<td style="padding-top: 20px; padding-bottom: 20px; padding-right: 5px; text-align: right;"><a class="a_add" href="/administrator/?page=<?php print $THIS_PAGE; ?>&amp;action=edit">Добавить товар</a></td>
</tr>
<tr>
	<td style="text-align: center;">
		<form method="POST" action="">
		<input type="hidden" name="_action" value="shop_list">
		<table class="table_list">
		<tr style="background-color: #CCCCCC; font-weight: bold;">
			<td style="width: 1%;">№</td>
			<td style="width: 1%;">Изображение</td>
			<td>Название</td>
			<td style="width: 1%;">Сортировка</td>
			<td style="width: 1%;">Цена</td>
			<td style="width: 1%;">В наличии</td>
			<td style="width: 1%;">Показывать</td>
		</tr>
<?php
			for ( $i=0; $i<sizeof($THIS_PAGE_DATA["list"]["id"]); $i++ )
			{
?>
		<tr<?php if($i%2!=0): ?> class="trd"<?php endif; ?> id="trid<?=$i?>">
			<td><?php print $i+1; ?><input type="hidden" name="item[]" value="<?=$THIS_PAGE_DATA["list"]["id"][$i]?>" /></td>
			<td><a class="a_actions" href="/administrator/?page=<?=$THIS_PAGE?>&amp;action=edit&amp;id=<?=$THIS_PAGE_DATA["list"]["id"][$i]?>"><img src="/images/shop_items/small/<?=$THIS_PAGE_DATA["list"]["image"][$i]?>.jpg" alt="" /></a></td>
			<td style="text-align: left;" nowrap><a class="a_actions" href="/administrator/?page=<?=$THIS_PAGE?>&amp;action=edit&amp;id=<?=$THIS_PAGE_DATA["list"]["id"][$i]?>"><?=$THIS_PAGE_DATA["list"]["name"][$i]?> (<?=$THIS_PAGE_DATA["list"]["articul"][$i]?>)</a></td>
			<td>
				<button type="button" onclick="move_items_by_list(get_parent_node_by_tag_name(this, 'TR'), 1, 'TR', 'trid'); return false;" style="margin: 5px 0px; width: 60px;">вверх</button><br />
				<button type="button" onclick="move_items_by_list(get_parent_node_by_tag_name(this, 'TR'), 2, 'TR', 'trid'); return false;" style="margin: 5px 0px; width: 60px;">вниз</button><br />
			</td>
			<td><input type="text" name="price[<?=$THIS_PAGE_DATA["list"]["id"][$i]?>]" size="8" value="<?=sprintf("%.2f", $THIS_PAGE_DATA["list"]["price"][$i])?>" style="text-align: right;" ></td>
			<td><input type="checkbox" name="available[<?=$THIS_PAGE_DATA["list"]["id"][$i]?>]" value="1"<?php if ($THIS_PAGE_DATA["list"]["available"][$i]>0) print " checked"; ?>></td>
			<td><input type="checkbox" name="active[<?=$THIS_PAGE_DATA["list"]["id"][$i]?>]" value="1"<?php if ($THIS_PAGE_DATA["list"]["active"][$i]>0) print " checked"; ?>></td>
		</tr>
<?php
			}
?>
		</table>
		<div style="margin-top: 20px; text-align: center;"><input type="submit" value="Сохранить" style="width: 100px;" onclick="this.disabled=true; this.form.submit();"></div>
		</form>
	</td>
</tr>
<tr>
	<td style="padding-top: 20px; padding-bottom: 20px; padding-right: 5px; text-align: right;"><a class="a_add" href="/administrator/?page=<?php print $THIS_PAGE; ?>&amp;action=edit">Добавить товар</a></td>
</tr>
</table>