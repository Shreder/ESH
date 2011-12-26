<form enctype="multipart/form-data" method="POST" action="/administrator/?page=<?php print $THIS_PAGE; ?>&action=edit<?php if ( isset($THIS_PAGE_DATA["data"]["id"]) ) print "&amp;id=".$THIS_PAGE_DATA["data"]["id"]; ?>">
<input type="hidden" name="_action" value="shop">
<input type="hidden" name="id" value="<?php if ( isset($THIS_PAGE_DATA["data"]["id"]) ) print $THIS_PAGE_DATA["data"]["id"]; ?>">
<input type="hidden" name="uin" value="<?php if ( isset($THIS_PAGE_DATA["data"]["uin"]) ) print $THIS_PAGE_DATA["data"]["uin"]; ?>">
<table style="width: 100%;">
<tr>
	<td style="padding-bottom: 30px; text-align: center;"><span style="font-size: 20px; font-weight: bold;"><?php print $THIS_PAGE_DATA["parent_title"].":".( !empty($THIS_PAGE_DATA["data"]["id"]) ? " ".$THIS_PAGE_DATA["data"]["name"]." (".$THIS_PAGE_DATA["data"]["articul"].")" : " Новый товар" ); ?></span><? print $THIS_PAGE_DATA["save"]["_main"]; ?></td>
</tr>
<tr>
	<td><table class="table_det">
	<tr>
		<td>Название: *</td>
		<td><input type="text" name="name" size="70" value="<?php print htmlspecialchars($THIS_PAGE_DATA["data"]["name"]); ?>" ><? print $THIS_PAGE_DATA["save"]["name"]; ?></td>
	</tr>
	<tr>
		<td>Артикул: *</td>
		<td><input type="text" name="articul" size="70" value="<?php print htmlspecialchars($THIS_PAGE_DATA["data"]["articul"]); ?>" ><? print $THIS_PAGE_DATA["save"]["articul"]; ?></td>
	</tr>
	<tr>
		<td>Цена: *</td>
		<td><input type="text" name="price" size="20" value="<?php print sprintf("%.2f", $THIS_PAGE_DATA["data"]["price"]); ?>" ><? print $THIS_PAGE_DATA["save"]["price"]; ?></td>
	</tr>
	<tr>
		<td>Наличие:</td>
		<td><input type="checkbox" name="available" value="1"<?=( ( !isset($THIS_PAGE_DATA["data"]["uin"]) || (bool)$THIS_PAGE_DATA["data"]["available"] ) ? " checked" : "")?>><? print $THIS_PAGE_DATA["save"]["available"]; ?></td>
	</tr>
	<tr>
		<td>Категория: *</td>
		<td><select name="category">
		<?php
			for ($i=0; $i<sizeof($THIS_PAGE_DATA["data"]["categories_list"]["id"]); $i++)
			{
				print "<option value=\"".$THIS_PAGE_DATA["data"]["categories_list"]["id"][$i]."\" ".( ($THIS_PAGE_DATA["data"]["categories_list"]["id"][$i] == $THIS_PAGE_DATA["data"]["category"]) ? " selected" : "").">".$THIS_PAGE_DATA["data"]["categories_list"]["name"][$i]."</option>";
			}
		?>
		</select></td>
	</tr>
	<tr>
		<td>Брэнд: *</td>
		<td><select name="brand">
		<?php
			for ($i=0; $i<sizeof($THIS_PAGE_DATA["data"]["brands_list"]["id"]); $i++)
			{
				print "<option value=\"".$THIS_PAGE_DATA["data"]["brands_list"]["id"][$i]."\" ".( ($THIS_PAGE_DATA["data"]["brands_list"]["id"][$i] == $THIS_PAGE_DATA["data"]["brand"]) ? " selected" : "").">".$THIS_PAGE_DATA["data"]["brands_list"]["name"][$i]."</option>";
			}
		?>
		</select></td>
	</tr>
	<tr>
		<td>Размеры:</td>
		<td><input type="text" name="size" size="70" value="<?php print htmlspecialchars($THIS_PAGE_DATA["data"]["size"]); ?>" ><? print $THIS_PAGE_DATA["save"]["size"]; ?></td>
	</tr>
	<tr>
		<td>Высота:</td>
		<td><input type="text" name="height" size="70" value="<?php print htmlspecialchars($THIS_PAGE_DATA["data"]["height"]); ?>" ></td>
	</tr>
	<tr>
		<td>Материал:</td>
		<td><textarea name="material" cols="60" rows="5"><?php print htmlspecialchars($THIS_PAGE_DATA["data"]["material"]); ?></textarea></td>
	</tr>
	<tr>
		<td>Цвет:</td>
		<td><input type="text" name="color" size="70" value="<?php print htmlspecialchars($THIS_PAGE_DATA["data"]["color"]); ?>" ></td>
	</tr>
	<tr>
		<td>Описание:</td>
		<td><textarea name="description" cols="80" rows="20"><?php print htmlspecialchars($THIS_PAGE_DATA["data"]["description"]); ?></textarea></td>
	</tr>
<?php
	if (sizeof($THIS_PAGE_DATA["data"]["images"]["num"])>0):
?>
	<tr>
		<td>Изображения:</td>
		<td>
<?php
			for ($i=0; $i<sizeof($THIS_PAGE_DATA["data"]["images"]["num"]); $i++):
?>
			<div class="images_list" id="divid<?=$i?>"><strong>Изображение <?=($i+1)?>:</strong><br>
				<table class="table_det2">
				<tr>
					<td><a href="/images/shop_items/full/<?=$THIS_PAGE_DATA["data"]["images"]["filename"][$i]?>.jpg" target="_blank"><img src="/images/shop_items/small/<?=$THIS_PAGE_DATA["data"]["images"]["filename"][$i]?>.jpg" alt=""></a></td>
					<td>
						<button type="button" onclick="move_items_by_list(get_parent_node_by_tag_name(this, 'DIV'), 1, 'DIV', 'divid'); return false;" style="margin: 2px 0px; width: 60px;">вверх</button><br>
						<button type="button" onclick="move_items_by_list(get_parent_node_by_tag_name(this, 'DIV'), 2, 'DIV', 'divid'); return false;" style="margin: 2px 0px; width: 60px;">вниз</button>
					</td>
					<td><input type="checkbox" name="item_images_active[<?=$THIS_PAGE_DATA["data"]["images"]["num"][$i]?>]" value="1"<?=((bool)$THIS_PAGE_DATA["data"]["images"]["active"][$i] ? " checked" : "")?>> активно</td>
					<td><input type="checkbox" name="item_images_delete[<?=$THIS_PAGE_DATA["data"]["images"]["num"][$i]?>]" value="1"> <strong>удалить</strong></td>
				</tr>
				</table>
				<input type="hidden" name="item_images[]" value="<?=$THIS_PAGE_DATA["data"]["images"]["num"][$i]?>">
			</div>
<?php
			endfor;
?>
		</td>
	</tr>
<?php
	endif;
?>
	<tr>
		<td>Добавить изображение:</td>
		<td>
			<div class="images_list">
				<table class="table_det2">
				<tr>
					<td>Большое:</td>
					<td><input type="file" name="new_image_full" size="70"></td>
				</tr>
				<tr>
					<td>Малое:</td>
					<td><input type="file" name="new_image_small" size="70"></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="checkbox" name="new_active" value="1" checked> сделать активным</td>
				</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; padding-top: 10px;"><input type="submit" value="Сохранить" style="width: 100px;" onclick="this.disabled=true; this.form.submit();"></td>
	</tr>
	</table></td>
</tr>
</table>
</form>