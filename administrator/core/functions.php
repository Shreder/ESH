<?php
	
	function post_handler()
	{
		if ( !isset($_SESSION["login"]) || !isset($_SESSION["password_hash"]) )
		{
			if ( isset($_POST["login"]) && isset($_POST["password"]) )
			{
				$_SESSION["post_action"] = "__auth";
				$_SESSION["login_temp"] = $_POST["login"];
				$_SESSION["password_hash_temp"] = md5($_POST["password"]);
			}
			else
			{
				$_SESSION["auth_error"] = "Вход в систему не выполнен.";
			}
			session_commit();
			$location_string = $GLOBALS["admin_host_global"];
		}
		else
		{	
			$_SESSION["post_action"] = $_POST["_action"];
			
			$post_data_array = array();
			foreach ( $_POST as $post_key => $post_value )
			{
				if ( ($post_key != "_action") && !(bool)preg_match( "/[^A-Za-z0-9_]$/", $post_key ) ) $post_data_array[$post_key] = $post_value;
			}
			$_SESSION["post_data"] = $post_data_array;
			
			$post_files_array = array();
			if ( sizeof($_FILES)>0 )
			{
				foreach ( $_FILES as $upload_file_key => $upload_file_data )
					if ( move_uploaded_file($_FILES[$upload_file_key]["tmp_name"], $GLOBALS["temp_files_upload_dir"].$_FILES[$upload_file_key]["name"]) )
					{
						$post_files_array[$upload_file_key]["filename"] = $_FILES[$upload_file_key]["name"];
						$post_files_array[$upload_file_key]["error"] = $_FILES[$upload_file_key]["error"];
					}
				$_SESSION["post_files"] = $post_files_array;
			}
			
			session_commit();
			$location_string = $GLOBALS["admin_host_global"]."?".$_SERVER["QUERY_STRING"];
		}

		header( "Location: ".$location_string );
		exit();
	}
	
	function select_page_function()
	{
		global $list_service_types;
		
		if ( isset($_SESSION["post_action"]) )
		{
			switch ( $_SESSION["post_action"] )
			{
				case "__auth":
					Auth();
					break;
				case "users":
					$GLOBALS["THIS_PAGE_DATA"]["save"] = users_save();
					break;
				case "news":
					$GLOBALS["THIS_PAGE_DATA"]["save"] = news_save();
					break;
				case "shop":
					$GLOBALS["THIS_PAGE_DATA"]["save"] = shop_save();
					break;
				case "shop_list":
					$GLOBALS["THIS_PAGE_DATA"]["save"] = shop_list_save();
					break;
				case "faq":
					$GLOBALS["THIS_PAGE_DATA"]["save"] = faq_save();
					break;
			}
		}
		unset($_SESSION["post_action"]);
		unset($_SESSION["post_data"]);
		unset($_SESSION["post_files"]);
		$uploaded_files = scandir($GLOBALS["temp_files_upload_dir"]);
		foreach ($uploaded_files as $uploaded_file) 
			if ( !in_array($uploaded_file, array(".", "..", ".htaccess")) ) @unlink ($GLOBALS["temp_files_upload_dir"].$uploaded_file);
		
		switch ( $GLOBALS["THIS_PAGE"] )
		{
			case "_logout":
				Logout();
				break;
			case "users":
				$GLOBALS["THIS_PAGE_DATA"]["parent_title"] = "Пользователи";
				switch ( $_GET["action"] )
				{
					case "edit":
						$GLOBALS["THIS_PAGE_DATA"]["data"] = users_detalize();
						$GLOBALS["THIS_PAGE_TEMPLATE"] = "users_detalize.php";
						break;
					case "delete":
						users_delete();
						header( "Location: ".$GLOBALS["admin_host_global"]."?page=".$GLOBALS["THIS_PAGE"] );
						exit();
						break;
					default:
						$GLOBALS["THIS_PAGE_DATA"]["list"] = users_list();
						$GLOBALS["THIS_PAGE_TEMPLATE"] = "users_list.php";
				}
				break;
			case "news":
				$GLOBALS["THIS_PAGE_DATA"]["parent_title"] = "Новости";
				switch ( $_GET["action"] )
				{
					case "edit":
						$GLOBALS["THIS_PAGE_DATA"]["data"] = news_detalize();
						$GLOBALS["THIS_PAGE_TEMPLATE"] = "news_detalize.php";
						break;
					case "delete":
						news_delete();
						header( "Location: ".$GLOBALS["admin_host_global"]."?page=".$GLOBALS["THIS_PAGE"] );
						exit();
						break;
					default:
						$GLOBALS["THIS_PAGE_DATA"]["list"] = news_list();
						$GLOBALS["THIS_PAGE_TEMPLATE"] = "news_list.php";
				}
				break;
			case "shop":
				$GLOBALS["THIS_PAGE_DATA"]["parent_title"] = "Товары";
				switch ( $_GET["action"] )
				{
					case "edit":
						$GLOBALS["THIS_PAGE_DATA"]["data"] = shop_detalize();
						$GLOBALS["THIS_PAGE_TEMPLATE"] = "shop_detalize.php";
						break;
					case "delete":
						shop_delete();
						header( "Location: ".$GLOBALS["admin_host_global"]."?page=".$GLOBALS["THIS_PAGE"] );
						exit();
						break;
					default:
						$GLOBALS["THIS_PAGE_DATA"]["list"] = shop_list();
						$GLOBALS["THIS_PAGE_TEMPLATE"] = "shop_list.php";
				}
				break;
			case "faq":
				$GLOBALS["THIS_PAGE_DATA"]["parent_title"] = "Вопросы и ответы";
				switch ( $_GET["action"] )
				{
					case "edit":
						$GLOBALS["THIS_PAGE_DATA"]["data"] = faq_detalize();
						$GLOBALS["THIS_PAGE_TEMPLATE"] = "faq_detalize.php";
						break;
					case "delete":
						faq_delete();
						header( "Location: ".$GLOBALS["admin_host_global"]."?page=".$GLOBALS["THIS_PAGE"] );
						exit();
						break;
					default:
						$GLOBALS["THIS_PAGE_DATA"]["list"] = faq_list();
						$GLOBALS["THIS_PAGE_TEMPLATE"] = "faq_list.php";
				}
				break;
			default:
				$GLOBALS["THIS_PAGE_TEMPLATE"] = "";
		}
	}
	
	function is_login()
	{
		global $DB_RESOURCE;
		
		$is_login = false;
		if ( isset($_SESSION["login"]) && isset($_SESSION["password_hash"]) )
		{
			$res_user_valid = @mysql_query("SELECT COUNT(*) FROM users WHERE login = '".$_SESSION["login"]."' AND password = '".$_SESSION["password_hash"]."' AND active > 0", $DB_RESOURCE);
			if ( ( ($row_user_valid = mysql_fetch_row($res_user_valid)) !== false ) && ( $row_user_valid[0] > 0 ) ) $is_login = true;
		}
		return $is_login;
	}
	
	function Auth()
	{
		global $DB_RESOURCE;
		
		if ( isset($_SESSION["login_temp"]) && isset($_SESSION["password_hash_temp"]) )
		{
			if ( (bool)preg_match( "/[^\w]$/", $_SESSION["login_temp"] ) )
			{
				$_SESSION["auth_error"] = "Логин введён неверно!";
			}
			else
			{
				$res_user = @mysql_query("SELECT * FROM users WHERE login = '".$_SESSION["login_temp"]."' AND active > 0 LIMIT 1", $DB_RESOURCE);
				if ( ($row_user = mysql_fetch_assoc($res_user)) !== false )
				{
					if ( $row_user["password"]==$_SESSION["password_hash_temp"] )
					{
						$_SESSION["login"] = $_SESSION["login_temp"];
						$_SESSION["password_hash"] = $_SESSION["password_hash_temp"];
						unset($_SESSION["login_temp"]);
						unset($_SESSION["password_hash_temp"]);
					}
					else
					{
						$_SESSION["auth_error"] = "Неверный пароль.";
					}
				}
				else
				{
					$_SESSION["auth_error"] = "Пользователь не активен или не существует.";
				}
			}
		}
		else
		{
			$_SESSION["auth_error"] = "Вход в систему не выполнен.";
		}
	}
	
	function Logout()
	{
		session_destroy();
		header( "Location: ".$GLOBALS["admin_host_global"] );
	}
	
	function nonindex_array_to_js ($array)
	{
		return "['".implode("', '", $array)."']";
	}
	
	function get_error_text ($text, $is_br=true)
	{
		return (bool)strlen($text) ? ($is_br ? "<br />" : "")."<span style=\"color: red;\">".$text."</span>" : "";
	}
	
	function set_shop_params_lists()
	{
		global $DB_RESOURCE, $THIS_PAGE_DATA;
		
		$res_categories = @mysql_query("SELECT id, name FROM shop_categories ORDER BY sort ASC", $DB_RESOURCE);
		while ( ($row_categories=mysql_fetch_assoc($res_categories)) !== false )
		{
			$THIS_PAGE_DATA["shop_categories_list"]["id"][] = $row_categories["id"];
			$THIS_PAGE_DATA["shop_categories_list"]["name"][] = $row_categories["name"];
		}
		
		$res_brands = @mysql_query("SELECT id, name FROM shop_brands ORDER BY sort ASC", $DB_RESOURCE);
		while ( ($row_brands=mysql_fetch_assoc($res_brands)) !== false )
		{
			$THIS_PAGE_DATA["shop_brands_list"]["id"][] = $row_brands["id"];
			$THIS_PAGE_DATA["shop_brands_list"]["name"][] = $row_brands["name"];
		}
	}
	
	function Translite ($str)
	{
		static $tbl = array(
			"а"=>"a", "б"=>"b", "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e", "ж"=>"g", "з"=>"z",
			"и"=>"i", "й"=>"y", "к"=>"k", "л"=>"l", "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p",
			"р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u", "ф"=>"f", "ы"=>"i", "э"=>"e", "А"=>"A",
			"Б"=>"B", "В"=>"V", "Г"=>"G", "Д"=>"D", "Е"=>"E", "Ж"=>"G", "З"=>"Z", "И"=>"I",
			"Й"=>"Y", "К"=>"K", "Л"=>"L", "М"=>"M", "Н"=>"N", "О"=>"O", "П"=>"P", "Р"=>"R",
			"С"=>"S", "Т"=>"T", "У"=>"U", "Ф"=>"F", "Ы"=>"I", "Э"=>"E", "ё"=>"yo", "х"=>"h",
			"ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"shch", "ъ"=>"", "ь"=>"", "ю"=>"yu", "я"=>"ya",
			"Ё"=>"YO", "Х"=>"H", "Ц"=>"TS", "Ч"=>"CH", "Ш"=>"SH", "Щ"=>"SHCH", "Ъ"=>"", "Ь"=>"",
			"Ю"=>"YU", "Я"=>"YA"
		);
		return strtr($str, $tbl);
	}
	
	function get_articul_lat ($str)
	{
		
	}
	
	function connect_db()
	{
		global $DB_RESOURCE, $THIS_PAGE_DATA;
		
		if(is_null($DB_RESOURCE)) $DB_RESOURCE = @mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD);
		if($DB_RESOURCE !== false)
		{
			@mysql_select_db(DB_NAME, $DB_RESOURCE);
			@mysql_query("SET NAMES `utf8`", $DB_RESOURCE);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function disconnect_db()
	{
		global $DB_RESOURCE;
		
		if($DB_RESOURCE !== false) @mysql_close($DB_RESOURCE);
	}

?>