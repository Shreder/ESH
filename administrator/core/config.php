<?php
/*
	if ( $_SERVER["REMOTE_ADDR"]!="127.0.0.1" )
	{
		if ( empty($_SERVER["HTTPS"]) || ($_SERVER["HTTPS"]=="off") )
		{
			header( "Location: http://".$_SERVER["HTTP_HOST"] );
			exit();
		}
		ini_set("session.cookie_secure", "On");
	}
*/

	ini_set("register_globals", "Off");
	//ini_set("display_errors", "On");
	
	require_once($_SERVER["DOCUMENT_ROOT"]."/administrator/core/config_db.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/administrator/core/functions.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/administrator/core/page_functions.php");
	
	$admin_host_local = "/".$_SERVER["HTTP_HOST"]."/administrator/";
	$admin_host_global = ( !empty($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"]!="off") ? "https://" : "http://" ).$_SERVER["HTTP_HOST"]."/administrator/";
	$temp_files_upload_dir = $_SERVER["DOCUMENT_ROOT"]."/administrator/temp_upload_files/";
	$files_dir = $_SERVER["DOCUMENT_ROOT"]."/docs/";
	$shop_images_dir = $_SERVER["DOCUMENT_ROOT"]."/images/shop_items/";
	$fckeditor_base_path = "/administrator/fckeditor/";
	$THIS_PAGE = isset($_GET["page"]) ? $_GET["page"] : "";
	$THIS_ACTION = isset($_GET["action"]) ? $_GET["action"] : "";
	$THIS_PAGE_DATA = array();

?>