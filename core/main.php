<?php
	
	session_start();
	
	require_once("config/data.php");
	
	if ( $_SERVER["REQUEST_METHOD"] == "POST" )
	{
		if ( in_array("_action", array_keys($_POST)) )
		{
			$post_data_array = array();
			$_SESSION["post_data_action"] = $_POST["_action"];
			$_SESSION["post_data_ajax"] = in_array("_ajax", array_keys($_POST));
			foreach ( $_POST as $post_key => $post_value )
				if ( ($post_key != "_action") && ($post_key != "_ajax") && !(bool)preg_match( "/[^A-Za-z0-9_]$/", $post_key ) )
					$post_data_array[$post_key] = $post_value;
			$_SESSION["post_data"] = $post_data_array;
		}
		
		if (!isset($_SESSION["post_data_ajax"]) || !$_SESSION["post_data_ajax"])
		{
			header("Location: http://".MAIN_HOST.$_SERVER["REQUEST_URI"]);
			exit();
		}
	}
	
	require_once("PEAR/Mail.php");
	require_once("PEAR/mime.php");
	
	require_once("classes.php");
	require_once("page_classes.php");
	require_once("action_classes.php");
	
	header("Pragma: no-cache");
	header("Cache-Control: no-cache, must-revalidate");
	
	$request_parts = explode("?", $_SERVER["REQUEST_URI"]);
	
	$need_redirect = false;
	if ($_SERVER["HTTP_HOST"]!=MAIN_HOST) $need_redirect = true;
	if ( substr($request_parts[0], -1)!="/" ) $need_redirect = true;
	if( $need_redirect )
	{
		header("Location: http://".MAIN_HOST.rtrim("/", $request_parts[0])."/".(sizeof($request_parts)>1 ? implode("?", array_slice($request_parts, 1)) : ""));
		exit();
	}
	
	switch ($_SESSION["post_data_action"])
	{
		case "manage_cart":
			$action_object = new Action_Manage_Cart($_SESSION["post_data"]);
			break;
		case "delete_from_cart":
			$action_object = new Action_Delete_From_Cart($_SESSION["post_data"]);
			break;
		case "clear_cart":
			$action_object = new Action_Clear_Cart();
			break;
		case "shop_order":
			$action_object = new Action_Shop_Order($_SESSION["post_data"]);
			break;
	}
	
	$action_result = is_object($action_object) ? $action_object->getResultData() : array();
	
	$uri_parts = explode("/", $request_parts[0]);
	
	switch ( $uri_parts[1] )
	{
		case "":
			if (isset($_SESSION["post_data_ajax"]) && $_SESSION["post_data_ajax"])
				$page_object = new AJAX_Page($action_result);
			else
				$page_object = new Page_Main();
			break;
		case "catalog":
			switch ( $uri_parts[2] )
			{
				case "category":
				case "brand":
					if ( empty($uri_parts[3]) || preg_match("/[A-Za-z0-9_\-]{".strlen($uri_parts[3])."}/", $uri_parts[3]) )
						$page_object = new Page_Shop_List($uri_parts[2], $uri_parts[3]);
					break;
				case "search":
					if ( !empty($_GET["st"]) )
						$page_object = new Page_Shop_List_Search($_GET["st"]);
					break;
				case "item":
					if ( !empty($uri_parts[3]) && preg_match("/[\d]{".strlen($uri_parts[3])."}/", $uri_parts[3]) )
						$page_object = new Page_Shop_Item($uri_parts[3]);
					break;
			}
			break;
		case "shop_cart":
			$page_object = new Page_Shop_Cart($action_result);
			break;
		case "shop_order":
			$page_object = new Page_Shop_Order($action_result);
			break;
		case "order_tracking":
			$page_object = new Page_Order_Tracking();
			break;
		case "how_buy":
			$page_object = new Page_How_Buy();
			break;
		case "faq":
			$page_object = new Page_FAQ();
			break;
		case "contacts":
			$page_object = new Page_Contacts();
			break;
	}
	
	if (!is_object($page_object) || !($page_object instanceof Page)) $page_object = new Page_404();
	print $page_object->getResultPageDump();
	
	unset ($_SESSION["post_data_action"]);
	unset ($_SESSION["post_data_ajax"]);
	unset ($_SESSION["post_data"]);
	
	//print memory_get_peak_usage();
	