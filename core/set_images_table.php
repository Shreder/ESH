<?php

	header("Content-type: text/plain");
	define("IMAGES_PATH", "D:/WWW/extreme-shoes/htdocs/images/shop_items");
	define("IMAGES_PATH_NEW", "D:/WWW/extreme-shoes/htdocs/images/shop_items2");
	
	ini_set("max_execution_time", 120);
	$pdo = new PDO("mysql:dbname=extreme_shoes;host=localhost", "root", "", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$insert_queries = array();
	
	$items_stmt = $pdo->prepare("SELECT id, uin, articul_lat FROM shop_items");
	$items_stmt->setFetchMode(PDO::FETCH_ASSOC);
	$items_stmt->execute();
	foreach ($items_stmt as $item)
	{	
		$insert_queries[] = "INSERT INTO shop_images (item_uin, main, active) VALUES (".$item["uin"].", 1, 1)";
		copy(IMAGES_PATH."/small/".$item["articul_lat"].".jpg", IMAGES_PATH_NEW."/small/".$item["uin"]."_1.jpg");
		copy(IMAGES_PATH."/full/".$item["articul_lat"].".jpg", IMAGES_PATH_NEW."/full/".$item["uin"]."_1.jpg");
		$pdo->exec($insert_queries[count($insert_queries)-1]);
	}
	//var_dump($insert_queries);