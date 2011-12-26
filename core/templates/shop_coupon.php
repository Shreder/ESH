<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>&quot;Экстрим-Шуз&quot;: <?=$this->params_array["shop_item"]["name"]?>, артикул <?php print $this->params_array["shop_item"]["articul"];
	if (!empty($this->params_array["shop_item_size"])): ?>, размер <?php print $this->params_array["shop_item_size"]; endif;
	if (!empty($this->params_array["shop_item_num"])): ?>, количество <?php print $this->params_array["shop_item_num"]; endif; ?></title>
<link href="/styles/coupon.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="main">
	<p class="img"><img src="/images/coupon_bg.gif" alt="" /></p>
	<p><span class="caption">Вы выбрали товар:</span></p>
	<p><span class="desc"><?=$this->params_array["shop_item"]["name"]?>,<br /></span>
		<span class="desc2">артикул <strong><?php print $this->params_array["shop_item"]["articul"]; ?></strong><?php
			if (!empty($this->params_array["shop_item_size"])): ?>, размер <strong><?php print $this->params_array["shop_item_size"]; ?></strong><?php endif;
			if (!empty($this->params_array["shop_item_num"])): ?>, количество <strong><?php print $this->params_array["shop_item_num"]; ?></strong><?php endif; ?></span></p>
	<p><img src="/images/shop_items/coupon/<?=$this->params_array["shop_item"]["articul_lat"]?>.jpg" alt="" /></p>
	<p><span class="code">Код заказа:<br /><?=$this->params_array["coupon_number"]?></span></p>
	<p class="bt"><button id="btp" onclick="this.style.display='none'; window.print();">Распечатать</button></p>
</div>
</body>
</html>