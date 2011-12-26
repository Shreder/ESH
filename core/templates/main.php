<?php
	$page_title = ($this->params_array["page_title"]!="" ? htmlspecialchars(stripslashes($this->params_array["page_title"]))." :: " : "")
		."&quot;Экстрим-Шуз&quot; - специализированная одежда и обувь для рыбаков и охотников";
	
	$main_menu_array = array(
		array(
			"text"		=> "Главная",
			"href"		=> "/",
			"num"		=> 1
		),
		array(
			"text"		=> "Как купить",
			"href"		=> "/how_buy/",
			"num"		=> 5
		),
		array(
			"text"		=> "Корзина<span id=\"sc_num\">".($this->params_array["shop_cart_items_count"]>0 ? " (".$this->params_array["shop_cart_items_count"].")" : "")."</span>",
			"href"		=> "/shop_cart/",
			"num"		=> 2
		),
		array(
			"text"		=> "Мой заказ",
			"href"		=> "/order_tracking/",
			"num"		=> 6
		),
		/*
		array(
			"text"		=> "F.A.Q.",
			"href"		=> "/faq/",
			"num"		=> 3
		),
		*/
		array(
			"text"		=> "Контакты",
			"href"		=> "/contacts/",
			"num"		=> 4
		),
	);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="content-type" content="text/xml; charset=utf-8" />
<title><?=$page_title?></title>
<link rel="stylesheet" media="all" type="text/css" href="/styles/style.css" />
<link rel="stylesheet" media="all" type="text/css" href="/styles/content.css" />
<script type="text/javascript" src="/script/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/script/main1.js"></script>
</head>

<body>
<div id="Main_Container"> <!-- container -->

<div id="top"></div>

<div id="Header_container">
<div id="Header">
<ul id="navigation">
<?php
	foreach ($main_menu_array as $main_menu_item)
	{
?><li<?=($this->params_array["main_menu_num"]==$main_menu_item["num"] ? " class=\"active\"" : "")?>><a href="<?=$main_menu_item["href"]?>"><span class="left_bg_menu"></span><span class="text_menu"><?=$main_menu_item["text"]?></span><span class="right_bg_menu"></span></a></li>
<?php
	}
?>
</ul>
<div id="search_field"><form method="get" action="/catalog/search/">Поиск товара: <input type="text" name="st" value="<?=htmlspecialchars($_GET["st"])?>" /></form></div>

</div>
</div>

<div id="Header2_container">
<div id="Header2">
<div class="logo">Экстрим-Шуз</div>
<div class="banner"><span>Интернет-магазин специализированной<br />одежды и обуви для рыбаков и охотников</span><span class="tel">(495) 000 00 00</span></div>
</div>
</div>

<div id="Container">

<div id="Menu">

<h2>Категории товаров</h2>
<ul class="subnav">
<?php
	$categories_menu = &$this->params_array["shop_menu"]["category"];
	$categories_count = sizeof($categories_menu);
	for ($i=0; $i < $categories_count; $i++)
	{
?>
<li><a href="/catalog/category/<?=$categories_menu[$i]["uri"]?>/"><?=$categories_menu[$i]["name"]." (".(int)$categories_menu[$i]["count"].")"?></a></li>
<?php
	}
?>
</ul>

<h2>Брэнды</h2>
<ul class="subnav" style="text-transform: uppercase;">
<?php
	$brands_menu = &$this->params_array["shop_menu"]["brand"];
	$brands_count = sizeof($brands_menu);
	for ($i=0; $i < $brands_count; $i++)
	{
?>
<li><a href="/catalog/brand/<?=$brands_menu[$i]["uri"]?>/"><?=$brands_menu[$i]["name"]." (".(int)$brands_menu[$i]["count"].")"?></a></li>
<?php
	}
?>
</ul>

<div class="informer">
	<a href="http://www.gismeteo.ru/city/daily/4368/" target="_blank"><img src="http://informer.gismeteo.ru/new/4368-13.GIF" alt="GISMETEO: Погода по г.Москва" title="GISMETEO: Погода по г.Москва" /></a>
</div>

</div>

<div id="Content">
<?=$this->params_array["page_content"]?>
</div>
<div class="spacer"></div>

<div id="logos">
<?php
	$brands_menu = &$this->params_array["brands_menu"];
	foreach ((array)$brands_menu as $brands_menu_item):
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/images/brand_logos/".$brands_menu_item["uri"].".png")):
?>
	<a href="/catalog/brand/<?=$brands_menu_item["uri"]?>/"><img src="/images/brand_logos/<?=$brands_menu_item["uri"]?>.png" alt="<?=$brands_menu_item["name"]?>" /></a>
<?php
		endif;
	endforeach;
?>
</div>

</div>

</div> <!-- container -->


<div id="Footer"> 

<div>
<p>Copyright &copy; &quot;Экстрим-Шуз&quot; 2010<?=((date("Y")>2010) ? " - ".date("Y") : "")?></p>
<p>
	<a href="http://validator.w3.org/check?uri=referer" target="_blank"><img src="http://www.w3.org/Icons/valid-xhtml10-blue.png" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
</p>
</div>

</div>
</body>

</html>