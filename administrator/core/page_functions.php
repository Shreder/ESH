<?php
	
	function users_list()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$result_data = array();
		
		$result = mysql_query("SELECT * FROM users ORDER BY change_date DESC", $DB_RESOURCE);
		
		while( ($row = mysql_fetch_assoc($result))!==false )
		{
			$result_data["id"][] = $row["id"];
			$result_data["name"][] = $row["name"];
			$result_data["login"][] = $row["login"];
			$result_data["status"][] = $row["active"]=="1" ? "Активен" : "Неактивен";
		}
		
		return $result_data;
	}
	
	function users_detalize()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$result_data = array();
		if ( isset($_GET["id"]) && (bool)strlen($_GET["id"]) )
		{
			if ( !is_numeric( $_GET["id"] ) ) return false;
			
			$result_data["title"] = "Пользователь ";
			$result = @mysql_query("SELECT * FROM users WHERE id = '".$_GET["id"]."' LIMIT 1", $DB_RESOURCE);
			if ( ($row = @mysql_fetch_assoc($result))!==false )
			{
				$result_data["id"] = $row["id"];
				$result_data["name"] = $row["name"];
				$result_data["login"] = $row["login"];
				$result_data["active"] = $row["active"];
				$result_data["change_date"] = date("d.m.Y H:i:s", strtotime($row["change_date"]));
				$result_data["change_ip"] = $row["change_ip"];
				$result_data["title"] .= $row["name"];
			}
		}
		else
		{
			$result_data["title"] = "Новый пользователь";
		}
		
		return $result_data; 
	}
	
	function users_save()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$error_msgs = array();
		$cur_date = date("Y-m-d H:i:s");
		
		if ( (bool)strlen($_SESSION["post_data"]["id"]) && !is_numeric($_SESSION["post_data"]["id"]) ) $error_msgs["_main"] = get_error_text("Ошибка сохранения данных пользователя");
		if ( !(bool)strlen($_SESSION["post_data"]["login"]) ) $error_msgs["login"] = get_error_text("Не введён логин");
		elseif ( (bool)preg_match( "/[^\w]$/", $_SESSION["post_data"]["login"] ) ) $error_msgs["login"] = get_error_text("Введены недопустимые символы");
		if ( (string)$_SESSION["post_data"]["password"]!=(string)$_SESSION["post_data"]["password_retype"] ) $error_msgs["password_retype"] = get_error_text("Пароль и подтверждение пароля не совпадают");
		elseif ( !(bool)strlen($_SESSION["post_data"]["id"]) && !(bool)strlen($_SESSION["post_data"]["password"]) ) $error_msgs["password"] = get_error_text("Пароль не может быть пустым");
		elseif ( (bool)preg_match( "/[^\w]$/", $_SESSION["post_data"]["password"] ) ) $error_msgs["password"] = get_error_text("Введены недопустимые символы");
		
		if ( !(bool)strlen($_SESSION["post_data"]["id"]) )
		{
			$res_useful_login = @mysql_query("SELECT count(*) FROM users WHERE login = '".$_SESSION["post_data"]["login"]."'", $DB_RESOURCE);
			if ( (($row_useful_login = mysql_fetch_row($res_useful_login))!==false) && ($row_useful_login[0] > 0) )
				$error_msgs["login"] = get_error_text("Такой пользователь уже существует");
		}
		
		if (sizeof($error_msgs)>0) return $error_msgs;
		
		if ( (bool)strlen($_SESSION["post_data"]["id"]) )
		{
			$save_query = "UPDATE users SET login = '".$_SESSION["post_data"]["login"]."', "
				.((bool)strlen($_SESSION["post_data"]["password"]) ? "password = '".md5($_SESSION["post_data"]["password"])."', " : "")
				."name = '".addslashes($_SESSION["post_data"]["name"])."', "
				."change_date = '".$cur_date."', "
				."change_ip = '".$_SERVER["REMOTE_ADDR"]."', "
				."active = '".($_SESSION["post_data"]["active"]=="1" ? 1 : 0)."' WHERE id = '".$_SESSION["post_data"]["id"]."' LIMIT 1";
		}
		else
		{
			$save_query = "INSERT INTO users (login, password, name, reg_date, change_date, change_ip, active) VALUES ("
				."'".$_SESSION["post_data"]["login"]."', "
				."'".md5($_SESSION["post_data"]["password"])."', "
				."'".addslashes($_SESSION["post_data"]["name"])."', "
				."'".$cur_date."', '".$cur_date."', "
				."'".$_SERVER["REMOTE_ADDR"]."', "
				."'".($_SESSION["post_data"]["active"]=="1" ? 1 : 0)."')";
		}
		@mysql_query($save_query, $DB_RESOURCE);
		if ( !(bool)strlen($_SESSION["post_data"]["id"]) ) $_GET["id"] = mysql_insert_id($DB_RESOURCE);
		
		if (mysql_errno($DB_RESOURCE)!=0)
		{
			return array("_main" => get_error_text("(".mysql_errno($DB_RESOURCE).") ".mysql_error($DB_RESOURCE)));
		}
		else
		{
			if ( (bool)strlen($_SESSION["post_data"]["id"]) && ($_SESSION["post_data"]["login"]==$_SESSION["login"]) && (bool)strlen($_SESSION["post_data"]["password"]) )
				$_SESSION["password_hash"] = md5($_SESSION["post_data"]["password"]);
			return array("_main" => get_error_text("Данные сохранены успешно"));
		}
	}
	
	function users_delete()
	{
		global $DB_RESOURCE;
		
		$error_msgs = array();
		
		if ( $DB_RESOURCE===false ) return false;
		if ( !isset($_GET["id"]) || empty($_GET["id"]) || !preg_match( "/^[A-Za-z0-9]+$/", $_GET["id"] ) )
		{
			$error_msgs["_main"] = "Ошибка записи данных пользователя";
			return array("errors" => $error_msgs);
		}
		@mysql_query("DELETE FROM users WHERE id = '".$_GET["id"]."' LIMIT 1", $DB_RESOURCE);
		
		return true;
	}
	
	function faq_list()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$result_data = array();
		
		$result = mysql_query("SELECT * FROM faq", $DB_RESOURCE);
		
		while( ($row = mysql_fetch_assoc($result))!==false )
		{
			$result_data["id"][] = $row["id"];
			$result_data["question"][] = $row["question"];
			$result_data["show"][] = $row["active"]=="1" ? "Активен" : "Неактивен";
		}
		
		return $result_data;
	}
	
	function faq_detalize()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$result_data = array();
		if ( isset($_GET["id"]) && (bool)strlen($_GET["id"]) )
		{
			if ( !is_numeric( $_GET["id"] ) ) return false;
			
			$result_data["title"] = "Вопрос ";
			$result = @mysql_query("SELECT * FROM faq WHERE id = '".$_GET["id"]."' LIMIT 1", $DB_RESOURCE);
			if ( ($row = @mysql_fetch_assoc($result))!==false )
			{
				$result_data["id"] = $row["id"];
				$result_data["question"] = $row["question"];
				$result_data["answer"] = $row["answer"];
				$result_data["show"] = $row["active"];
				$result_data["title"] .= $row["id"];
			}
		}
		else
		{
			$result_data["title"] = "Новый вопрос";
		}
		$result_data["fckeditor_textarea_names"] = nonindex_array_to_js(array("answer"));
		
		return $result_data; 
	}
	
	function faq_save()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$error_msgs = array();
		
		if ( (bool)strlen($_SESSION["post_data"]["id"]) && !is_numeric($_SESSION["post_data"]["id"]) ) $error_msgs["_main"] = get_error_text("Ошибка сохранения данных пользователя");
		if ( !(bool)strlen($_SESSION["post_data"]["question"]) ) $error_msgs["password"] = get_error_text("Вопрос не может быть пустым");
		
		if (sizeof($error_msgs)>0) return $error_msgs;
		
		if ( (bool)strlen($_SESSION["post_data"]["id"]) )
		{
			$save_query = "UPDATE `faq` SET `question` = '".addslashes($_SESSION["post_data"]["question"])."', "
				."`answer` = '".addslashes($_SESSION["post_data"]["answer"])."', "
				."`active` = '".($_SESSION["post_data"]["show"]=="1" ? 1 : 0)."' WHERE `id` = '".$_SESSION["post_data"]["id"]."' LIMIT 1";
		}
		else
		{
			$save_query = "INSERT INTO `faq` (`question`, `answer`, `active`) VALUES ("
				."'".addslashes($_SESSION["post_data"]["question"])."', "
				."'".addslashes($_SESSION["post_data"]["answer"])."', "
				.($_SESSION["post_data"]["show"]=="1" ? 1 : 0).")";
		}
		@mysql_query($save_query, $DB_RESOURCE);
		if ( !(bool)strlen($_SESSION["post_data"]["id"]) ) $_GET["id"] = mysql_insert_id($DB_RESOURCE);
		
		if (mysql_errno($DB_RESOURCE)!=0) return array("_main" => get_error_text("(".mysql_errno($DB_RESOURCE).") ".mysql_error($DB_RESOURCE)));
		else return array("_main" => get_error_text("Данные сохранены успешно"));
	}
	
	function faq_delete()
	{
		global $DB_RESOURCE;
		
		$error_msgs = array();
		
		if ( $DB_RESOURCE===false ) return false;
		if ( !isset($_GET["id"]) || empty($_GET["id"]) || !preg_match( "/^[A-Za-z0-9]+$/", $_GET["id"] ) )
		{
			$error_msgs["_main"] = "Ошибка записи данных пользователя";
			return array("errors" => $error_msgs);
		}
		@mysql_query("DELETE FROM faq WHERE id = '".$_GET["id"]."' LIMIT 1", $DB_RESOURCE);
		
		return true;
	}
	
	function news_list()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$result_data = array();
		
		$result = @mysql_query("SELECT * FROM news ORDER BY date DESC", $DB_RESOURCE);
		while( ($row = @mysql_fetch_assoc($result))!==false )
		{
			$result_data["id"][] = $row["id"];
			$result_data["small_text"][] = nl2br(stripslashes($row["small_text"]));
			$result_data["date"][] = date("d.m.Y", strtotime($row["date"]));
			$result_data["active"][] = $row["active"]=="1" ? "Да" : "Нет";
		}
		
		return $result_data;
	}
	
	function news_detalize()
	{ 
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$result_data = array();
		if ( isset($_GET["id"]) && (bool)strlen($_GET["id"]) )
		{
			if ( !is_numeric( $_GET["id"] ) ) return false;
			
			$result = @mysql_query("SELECT * FROM news WHERE id = '".$_GET["id"]."' LIMIT 1", $DB_RESOURCE);
			if ( ($row = @mysql_fetch_assoc($result))!==false )
			{
				$result_data["id"] = $row["id"];
				$result_data["small_text"] = stripslashes($row["small_text"]);
				$result_data["full_text"] = stripslashes($row["full_text"]);
				$change_date_array = explode("-", $row["date"]);
				$result_data["date_year"] = $change_date_array[0];
				$result_data["date_month"] = $change_date_array[1];
				$result_data["date_day"] = $change_date_array[2];
				$result_data["active"] = $row["active"];
				$result_data["title"] = "Новость от ".date("d.m.Y", strtotime($row["date"]));
			}
		}
		else
		{
			$result_data["title"] = "Новое объявление";
		}
		$result_data["fckeditor_textarea_names"] = nonindex_array_to_js(array("full_text"));
		
		return $result_data; 
	}
	
	function news_save()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$error_msgs = array();
		$cur_date = date("Y-m-d H:i:s");
		
		if ( (bool)strlen($_SESSION["post_data"]["id"]) && !is_numeric($_SESSION["post_data"]["id"]) ) $error_msgs["_main"] = get_error_text("Ошибка сохранения данных пользователя");
		if ( !checkdate((int)$_SESSION["post_data"]["date_month"], (int)$_SESSION["post_data"]["date_day"], (int)$_SESSION["post_data"]["date_year"]) )
			$error_msgs["date"] = get_error_text("Некорректно задана дата");
		
		if (sizeof($error_msgs)>0) return $error_msgs;
		
		if ( (bool)strlen($_SESSION["post_data"]["id"]) )
		{
			$save_query = "UPDATE news SET small_text = '".addslashes($_SESSION["post_data"]["small_text"])."', "
				."full_text = '".addslashes($_SESSION["post_data"]["full_text"])."', "
				."date = '".addslashes($_SESSION["post_data"]["date_year"])."-".
					addslashes($_SESSION["post_data"]["date_month"])."-".
					addslashes($_SESSION["post_data"]["date_day"])."', "
				."active = '".($_SESSION["post_data"]["active"]=="1" ? 1 : 0)."' WHERE id = '".$_SESSION["post_data"]["id"]."' LIMIT 1";
		}
		else
		{
			$save_query = "INSERT INTO news (small_text, full_text, date, active) VALUES ("
				."'".addslashes($_SESSION["post_data"]["small_text"])."', "
				."'".addslashes($_SESSION["post_data"]["full_text"])."', "
				."'".addslashes($_SESSION["post_data"]["date_year"])."-".
					addslashes($_SESSION["post_data"]["date_month"])."-".
					addslashes($_SESSION["post_data"]["date_day"])."', "
				."'".($_SESSION["post_data"]["active"]=="1" ? 1 : 0)."')";
		}
		@mysql_query($save_query, $DB_RESOURCE);
		if ( !(bool)strlen($_SESSION["post_data"]["id"]) ) $_GET["id"] = mysql_insert_id($DB_RESOURCE);
		if (mysql_errno($DB_RESOURCE)!=0) return array("_main" => get_error_text("(".mysql_errno($DB_RESOURCE).") ".mysql_error($DB_RESOURCE)));
		else return array("_main" => get_error_text("Данные сохранены успешно"));
	}
	
	function news_delete()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		if ( !isset($_GET["id"]) || !is_numeric($_GET["id"]) ) return false;
		
		@mysql_query("DELETE FROM news WHERE id = '".$_GET["id"]."' LIMIT 1", $DB_RESOURCE);
		
		return true;
	}
	
	function shop_list()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$result_data = array();
		
		if ( in_array($_GET["list_type"], array("category", "brand")) && is_numeric($_GET["list_id"]) )
		{
			$list_type_tables = array(
				"category"		=> "shop_categories",
				"brand"			=> "shop_brands"
			);
			
			$query_list = "SELECT si.id, si.uin, si.articul, si.name, "
				."si.sort_by_".$_GET["list_type"]." AS sort, si.price, si.available, si.active, "
				."(SELECT CONCAT(item_uin, '_', image_num) FROM shop_images WHERE item_uin = si.uin AND active > 0 ORDER BY sort ASC LIMIT 1) AS image "
				."FROM shop_items AS si INNER JOIN (shop_categories AS sc, shop_brands AS sb) ON (sc.id = si.category AND sb.id = si.brand) "
				."WHERE ".$_GET["list_type"]." = ".$_GET["list_id"]." "
				."ORDER BY si.sort_by_".$_GET["list_type"]." ASC";
				//print $query_list;
			$result = @mysql_query($query_list, $DB_RESOURCE);
			//print mysql_error($DB_RESOURCE);
			while( ($row = @mysql_fetch_assoc($result))!==false )
			{
				$result_data["id"][] = $row["id"];
				$result_data["articul"][] = stripslashes($row["articul"]);
				$result_data["category_name"][] = $row["category_name"];
				$result_data["brand_name"][] = $row["brand_name"];
				$result_data["sort"][] = $row["sort"];
				$result_data["price"][] = $row["price"];
				$result_data["available"][] = $row["available"];
				$result_data["active"][] = $row["active"];
				$result_data["image"][] = $row["image"];
				$result_data["name"][] = stripslashes($row["name"]);
			}
			
			$list_type_texts = array(
				"category"		=> "категории",
				"brand"			=> "брэнду"
			);
			$res_list_name = mysql_query("SELECT name FROM ".$list_type_tables[$_GET["list_type"]]." WHERE id = ".$_GET["list_id"], $DB_RESOURCE);
			if ( ($row_list_name=mysql_fetch_row($res_list_name)) !== false )
				$result_data["list_type_text"] = $list_type_texts[$_GET["list_type"]]." \"".$row_list_name[0]."\"";
		}
		
		return $result_data;
	}
	
	function shop_detalize()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$result_data = array();
		if ( isset($_GET["id"]) && (bool)strlen($_GET["id"]) )
		{
			if ( !is_numeric( $_GET["id"] ) ) return false;
			
			$query = "SELECT si.id, si.uin, si.articul, si.price, si.available, "
				."si.category, si.name, si.brand, si.size, si.height, si.material, "
				."si.color, si.description "
				."FROM shop_items AS si WHERE si.id = ".$_GET["id"]." LIMIT 1";
			
			$result = @mysql_query($query, $DB_RESOURCE);
			if ( ($row = @mysql_fetch_assoc($result))!==false )
			{
				$result_data["id"] = $row["id"];
				$result_data["articul"] = stripslashes($row["articul"]);
				$result_data["uin"] = $row["uin"];
				$result_data["name"] = stripslashes($row["name"]);
				$result_data["price"] = $row["price"];
				$result_data["available"] = $row["available"];
				$result_data["category"] = $row["category"];
				$result_data["brand"] = $row["brand"];
				$result_data["size"] = stripslashes($row["size"]);
				$result_data["height"] = stripslashes($row["height"]);
				$result_data["material"] = stripslashes($row["material"]);
				$result_data["color"] = stripslashes($row["color"]);
				$result_data["description"] = stripslashes($row["description"]);
				$result_data["images"] = array();
				
				$query_images = "SELECT image_num, CONCAT(item_uin, '_', image_num) AS filename, active FROM shop_images WHERE item_uin = ".$result_data["uin"]." ORDER BY sort ASC";
				$result_images = @mysql_query($query_images, $DB_RESOURCE);
				while ( ($row_image = @mysql_fetch_assoc($result_images))!==false )
				{
					$result_data["images"]["num"][] = $row_image["image_num"];
					$result_data["images"]["filename"][] = $row_image["filename"];
					$result_data["images"]["active"][] = $row_image["active"];
				}
			}
		}
		else
		{
			$result_data["title"] = "Новое объявление";
		}
		
		$result_categories = mysql_query("SELECT * FROM shop_categories ORDER BY sort ASC");
		while ( ($row_categories = @mysql_fetch_assoc($result_categories))!==false )
		{
			$result_data["categories_list"]["id"][] = $row_categories["id"];
			$result_data["categories_list"]["name"][] = $row_categories["name"];
		}
		
		$result_brands = mysql_query("SELECT * FROM shop_brands ORDER BY sort ASC");
		while ( ($row_brands = @mysql_fetch_assoc($result_brands))!==false )
		{
			$result_data["brands_list"]["id"][] = $row_brands["id"];
			$result_data["brands_list"]["name"][] = $row_brands["name"];
		}
		
		return $result_data; 
	}
	
	function shop_list_save()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		
		if ( in_array($_GET["list_type"], array("category", "brand")) )
		{
			$count = 0;
			foreach ( $_SESSION["post_data"]["item"] as $item_num => $item_id )
			{
				if ( !(bool)preg_match("/[^\d]/", $item_id) )
				{
					$save_query = "UPDATE shop_items SET active = ".(isset($_SESSION["post_data"]["active"][$item_id]) ? 1 : 0)
						.", available = ".(isset($_SESSION["post_data"]["available"][$item_id]) ? 1 : 0)
						.", sort_by_".$_GET["list_type"]." = ".($count++)
						.(!(bool)preg_match("/[^\d\.]/", $_SESSION["post_data"]["price"][$item_id]) ? ", price = '".floatval(trim($_SESSION["post_data"]["price"][$item_id]))."'" : "")
						." WHERE id = ".$item_id;
					@mysql_query($save_query, $DB_RESOURCE);
				}
			}
		}
	}
	
	function shop_save()
	{
		global $DB_RESOURCE;
		global $temp_files_upload_dir;
		global $shop_images_dir;
		
		if ( $DB_RESOURCE===false ) return false;
		
		$error_msgs = array();
		$cur_date = date("Y-m-d H:i:s");
		
		if ( (bool)strlen($_SESSION["post_data"]["id"]) && !is_numeric($_SESSION["post_data"]["id"]) ) $error_msgs["_main"] = get_error_text("Ошибка сохранения данных пользователя");
		if ( !(bool)strlen($_SESSION["post_data"]["name"]) ) $error_msgs["name"] = get_error_text("Не введёно название");
		if ( !(bool)strlen($_SESSION["post_data"]["articul"]) ) $error_msgs["articul"] = get_error_text("Не введён артикул");
		if ( !is_numeric($_SESSION["post_data"]["price"]) ) $error_msgs["price"] = get_error_text("Некорректно введена цена");
		if ( !is_numeric($_SESSION["post_data"]["category"]) ) $error_msgs["price"] = get_error_text("Некорректно задана категория");
		if ( !is_numeric($_SESSION["post_data"]["brand"]) ) $error_msgs["price"] = get_error_text("Некорректно задан брэнд");
		
		if (sizeof($error_msgs)>0) return $error_msgs;
		
		if ( (bool)strlen($_SESSION["post_data"]["id"]) )
		{
			$save_query = "UPDATE shop_items SET articul = '".addslashes(trim($_SESSION["post_data"]["articul"]))."', "
				."name = '".addslashes(trim($_SESSION["post_data"]["name"]))."', "
				."price = '".floatval(trim($_SESSION["post_data"]["price"]))."', "
				."available = ".(isset($_SESSION["post_data"]["available"])? 1 : 0).", "
				."category = ".$_SESSION["post_data"]["category"].", "
				."brand = ".$_SESSION["post_data"]["brand"].", "
				."size = '".addslashes(trim($_SESSION["post_data"]["size"]))."', "
				."height = '".addslashes(trim($_SESSION["post_data"]["height"]))."', "
				."material = '".addslashes(trim($_SESSION["post_data"]["material"]))."', "
				."color = '".addslashes(trim($_SESSION["post_data"]["color"]))."', "
				."description = '".addslashes(trim($_SESSION["post_data"]["description"]))."' "
				."WHERE id = '".$_SESSION["post_data"]["id"]."' LIMIT 1";
		}
		else
		{
			do
			{
				$new_item_number = mt_rand(1, PHP_INT_MAX);
				$res_isset_number = @mysql_query("SELECT COUNT(uin) FROM shop_items WHERE uin = ".$new_item_number, $DB_RESOURCE);
				$isset_number_row = mysql_fetch_row($res_isset_number);
			}
			while ($isset_number_row[0] > 0);
			$save_query = "INSERT INTO shop_items (uin, articul, name, price, available, category, brand, size, height, material, color, description) VALUES ("
				.$new_item_number.", "
				."'".addslashes(trim($_SESSION["post_data"]["articul"]))."', "
				."'".addslashes(trim($_SESSION["post_data"]["name"]))."', "
				."'".floatval(trim($_SESSION["post_data"]["price"]))."', "
				.(isset($_SESSION["post_data"]["available"])? 1 : 0).", "
				.$_SESSION["post_data"]["category"].", "
				.$_SESSION["post_data"]["brand"].", "
				."'".addslashes(trim($_SESSION["post_data"]["size"]))."', "
				."'".addslashes(trim($_SESSION["post_data"]["height"]))."', "
				."'".addslashes(trim($_SESSION["post_data"]["material"]))."', "
				."'".addslashes(trim($_SESSION["post_data"]["color"]))."', "
				."'".addslashes(trim($_SESSION["post_data"]["description"]))."')";
		}
		@mysql_query($save_query, $DB_RESOURCE);
		
		if ( !(bool)strlen($_SESSION["post_data"]["id"]) )
		{
			$new_item_id = mysql_insert_id($DB_RESOURCE);
			$_GET["id"] = $new_item_id;
		}
		$item_number = (bool)strlen($_SESSION["post_data"]["uin"]) ? (int)$_SESSION["post_data"]["uin"] : (int)$new_item_number;
		
		if ( sizeof((array)$_SESSION["post_data"]["item_images"]) > 0 )
		{
			$counter = 0;
			foreach ( $_SESSION["post_data"]["item_images"] as $item_image )
			{
				if ( isset($_SESSION["post_data"]["item_images_delete"][$item_image]) )
				{
					@mysql_query("DELETE FROM shop_images WHERE item_uin = ".$item_number." AND image_num = ".$item_image, $DB_RESOURCE);
					@unlink($shop_images_dir."small/".$item_number."_".$item_image.".jpg");
					@unlink($shop_images_dir."micro/".$item_number."_".$item_image.".jpg");
					@unlink($shop_images_dir."full/".$item_number."_".$item_image.".jpg");
				}
				else
				{
					@mysql_query("UPDATE shop_images SET "
						."active = ".(isset($_SESSION["post_data"]["item_images_active"][$item_image]) ? 1 : 0).", "
						."sort = ".(++$counter)." WHERE item_uin = ".$item_number." AND image_num = ".$item_image, $DB_RESOURCE);
				}
			}
		}
		
		if ( isset($_SESSION["post_files"]["new_image_full"]) &&
			preg_match("/.(?i:\.jpg|.jpeg)/", $_SESSION["post_files"]["new_image_full"]["filename"]) )
		{
			$new_image_full_src_path = $temp_files_upload_dir.$_SESSION["post_files"]["new_image_full"]["filename"];
			$new_image_full_src = imagecreatefromjpeg($new_image_full_src_path);
			if ($new_image_full_src!==false)
			{
				$last_sort_res = mysql_query("SELECT sort FROM shop_images WHERE item_uin = ".$item_number." ORDER BY sort DESC LIMIT 1", $DB_RESOURCE);
				$last_sort_row = (array)mysql_fetch_row($last_sort_res);
				
				@mysql_query("INSERT INTO shop_images (item_uin, sort, active) VALUES (".$item_number.", ".((int)$last_sort_row[0]+1).", ".(isset($_SESSION["post_data"]["new_active"]) ? 1 : 0).")", $DB_RESOURCE);
				$image_number = mysql_insert_id($DB_RESOURCE);
				
				$new_image_full_dst_path = $shop_images_dir."full/".$item_number."_".$image_number.".jpg";
				list($src_width, $src_height) = getimagesize($new_image_full_src_path);
				if ( $src_width>600 )
				{
					$dst_width = 600;
					$dst_height = round( ($src_height * 600) / $src_width );
					$new_image_full_dst = imagecreatetruecolor($dst_width, $dst_height);
					imagecopyresampled($new_image_full_dst, $new_image_full_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
					imagejpeg($new_image_full_dst, $new_image_full_dst_path);
					imagedestroy($new_image_full_dst);
				}
				else
				{
					copy($new_image_full_src_path, $new_image_full_dst_path);
				}
				
				if ( isset($_SESSION["post_files"]["new_image_small"]) &&
					preg_match("/.(?i:\.jpg|.jpeg)/", $_SESSION["post_files"]["new_image_small"]["filename"]) )
				{
					$new_image_small_src_path = $temp_files_upload_dir.$_SESSION["post_files"]["new_image_small"]["filename"];
				}
				else
				{
					$new_image_small_src_path = $temp_files_upload_dir.$_SESSION["post_files"]["new_image_full"]["filename"];
				}
				$new_image_small_src = imagecreatefromjpeg($new_image_small_src_path);
				if ($new_image_small_src===false)
				{
					$new_image_small_src_path = $temp_files_upload_dir.$_SESSION["post_files"]["new_image_full"]["filename"];
					$new_image_small_src = imagecreatefromjpeg($new_image_small_src_path);
				}
				
				list($src_width, $src_height) = getimagesize($new_image_small_src_path);
				$new_image_small_dst_path = $shop_images_dir."small/".$item_number."_".$image_number.".jpg";

				if ( !(($src_width>=$src_height) && ($src_width<=100)) && !(($src_width<$src_height) && ($src_height<=100)) )
				{
					if ($src_width>=$src_height)
					{
						$dst_width = 100;
						$dst_height = round( ($src_height * 100) / $src_width );
					}
					else
					{
						$dst_width = round( ($src_width * 100) / $src_height );
						$dst_height = 100;
					}
					$new_image_small_dst = imagecreatetruecolor($dst_width, $dst_height);
					imagecopyresampled($new_image_small_dst, $new_image_small_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
					imagejpeg($new_image_small_dst, $new_image_small_dst_path);
					imagedestroy($new_image_small_dst);
				}
				else
				{
					copy($new_image_small_src_path, $new_image_small_dst_path);
				}
				
				$new_image_micro_dst_path = $shop_images_dir."micro/".$item_number."_".$image_number.".jpg";
				if ( !(($src_width>=$src_height) && ($src_width<=50)) && !(($src_width<$src_height) && ($src_height<=50)) )
				{
					if ($src_width>=$src_height)
					{
						$dst_width = 50;
						$dst_height = round( ($src_height * 50) / $src_width );
					}
					else
					{
						$dst_width = round( ($src_width * 50) / $src_height );
						$dst_height = 50;
					}
					$new_image_micro_dst = imagecreatetruecolor($dst_width, $dst_height);
					imagecopyresampled($new_image_micro_dst, $new_image_small_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
					imagejpeg($new_image_micro_dst, $new_image_micro_dst_path);
					imagedestroy($new_image_micro_dst);
				}
				else
				{
					copy($new_image_small_src_path, $new_image_micro_dst_path);
				}
				
				imagedestroy($new_image_small_src);
				imagedestroy($new_image_full_src);
			}
		}
		
		if (mysql_errno($DB_RESOURCE)!=0) return array("_main" => get_error_text("(".mysql_errno($DB_RESOURCE).") ".mysql_error($DB_RESOURCE)));
		else return array("_main" => get_error_text("Данные сохранены успешно"));
	}
	
	function shop_delete()
	{
		global $DB_RESOURCE;
		
		if ( $DB_RESOURCE===false ) return false;
		if ( !isset($_GET["id"]) || !is_numeric($_GET["id"]) ) return false;
		
		@mysql_query("DELETE FROM shop_items WHERE id = '".$_GET["id"]."' LIMIT 1", $DB_RESOURCE);
		
		return true;
	}
	
?>