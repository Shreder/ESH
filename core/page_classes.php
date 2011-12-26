<?php

	class Page_404 extends Standart_Page
	{
		protected $page_template_name = "404";
		
		protected function processPageData()
		{
		}
	}
	
	class Page_Main extends Standart_Page
	{
		protected $page_template_name = "root";
		
		protected function processPageData()
		{
			$this->getMainTemplateInstance()->addParam("main_menu_num", 1);
		}
	}
	
	class Page_Shop_List extends Standart_Page
	{
		protected $page_template_name = "shop_list";
		
		protected $list_type;
		protected $list_param;
		
		public function __construct($list_type, $list_param = "")
		{
			parent::__construct();
			
			$this->list_type = in_array($list_type, array("brand", "category")) ? $list_type : "category";
			$this->list_param = $list_param;
		}
		
		protected function processPageData()
		{
			if ( !empty($this->list_param) )
				foreach ($this->shop_menu[$this->list_type] as $shop_menu_index => $shop_menu_value)
					if ( $this->shop_menu[$this->list_type][$shop_menu_index]["uri"] == $this->list_param )
					{
						$is_param_in_menu = true;
						break;
					}
			if ($is_param_in_menu !== true)
			{
				$this->getPageTemplateInstance()->setTemplateName("404");
				return;
			}
			
			$list_param = $this->shop_menu[$this->list_type][$shop_menu_index]["uri"];
			$list_name = $this->shop_menu[$this->list_type][$shop_menu_index]["name"];
			$list_table = ($this->list_type == "brand") ? "shop_brands" : "shop_categories";
			
			$query_list = "SELECT si.uin, si.articul, si.name, si.price, si.size, si.available, "
				."sc.name as category_name, sb.name as brand_name, sc.uri as category_uri, sb.uri as brand_uri, "
				."(SELECT CONCAT(item_uin, '_', image_num) FROM shop_images WHERE item_uin = si.uin AND active > 0 ORDER BY sort ASC LIMIT 1) AS image "
				//."FROM shop_items AS si INNER JOIN (shop_categories AS sc, shop_brands AS sb) ON (sc.id = si.category AND sb.id = si.brand) "
				."FROM shop_items AS si INNER JOIN shop_categories AS sc ON sc.id = si.category INNER JOIN shop_brands AS sb ON sb.id = si.brand "
				."WHERE si.{$this->list_type} = (SELECT {$list_table}.id FROM {$list_table} WHERE {$list_table}.uri = '{$list_param}' LIMIT 1) AND si.active > 0 "
				."ORDER BY si.sort_by_{$this->list_type} ASC";
			$stmt_list = $this->getPDOInstance()->query($query_list);
			$list_shop_items = is_object($stmt_list) ? $stmt_list->fetchAll(PDO::FETCH_ASSOC) : array();
			
			if ($this->list_type == "brand")
			{
				$query_brand_info = "SELECT sbi.text FROM shop_brands AS sb INNER JOIN shop_brand_info AS sbi ON sb.id = sbi.brand_id "
					."WHERE sb.uri = '{$list_param}'";
				$brand_info_pdo_statement = $this->getPDOInstance()->query($query_brand_info);
				
				$brand_info_row = $brand_info_pdo_statement->fetch(PDO::FETCH_NUM);
				$this->getPageTemplateInstance()->addParam("brand_text", $brand_info_row[0]);
			}
			
			$this->getPageTemplateInstance()->addParam("list_type", $this->list_type);
			$this->getPageTemplateInstance()->addParam("list_type_name", ($this->list_type == "brand") ? "Брэнд" : "Категория");
			$this->getPageTemplateInstance()->addParam("list_name", $list_name);
			$this->getPageTemplateInstance()->addParam("list_uri", $list_param);
			$this->getPageTemplateInstance()->addParam("list_shop_items", $list_shop_items);
			
			$this->getMainTemplateInstance()->addParam("page_title", $list_name);
			$this->getMainTemplateInstance()->addParam("include_thumbnailviewer", true);
		}
	}
	
	class Page_Shop_List_Search extends Standart_Page
	{
		protected $page_template_name = "shop_list";
		
		protected $list_param;
		
		public function __construct($list_param)
		{
			parent::__construct();
			
			$this->list_param = $list_param;
		}
		
		protected function processPageData()
		{
			$this->list_param = substr($this->list_param, 0, 255);
			$valid_param = ( ( strpos($this->list_param, "%") === false ) && ( strlen(utf8_decode($this->list_param)) > 2 ) );
			
			if ($valid_param)
			{
				$query_list = "SELECT si.uin, si.articul, si.name, si.price, si.size, si.available, "
					."sc.name as category_name, sb.name as brand_name, sc.uri as category_uri, sb.uri as brand_uri, "
					."(SELECT CONCAT(item_uin, '_', image_num) FROM shop_images WHERE item_uin = si.uin AND active > 0 ORDER BY sort ASC LIMIT 1) AS image "
					."FROM shop_items AS si INNER JOIN (shop_categories AS sc, shop_brands AS sb) ON (sc.id = si.category AND sb.id = si.brand) "
					."WHERE si.active > 0 AND ( si.name LIKE '%".addslashes($this->list_param)."%' OR si.articul LIKE '%".addslashes($this->list_param)."%' ) "
					."ORDER BY si.sort_by_category ASC, si.sort_by_brand ASC";
				$stmt_list = $this->getPDOInstance()->query($query_list);
				$list_shop_items = is_object($stmt_list) ? $stmt_list->fetchAll(PDO::FETCH_ASSOC) : array();
			}
			
			$this->getPageTemplateInstance()->addParam("list_type", "search");
			$this->getPageTemplateInstance()->addParam("search_param", $this->list_param);
			$this->getPageTemplateInstance()->addParam("list_shop_items", (array)$list_shop_items);
			
			$this->getMainTemplateInstance()->addParam("include_thumbnailviewer", true);
		}
	}
	
	class Page_Shop_Item extends Standart_Page
	{
		protected $page_template_name = "shop_item";
		
		protected $item_uin;
		
		public function __construct($item_uin)
		{
			parent::__construct();
			
			$this->item_uin = $item_uin;
		}
		
		protected function processPageData()
		{
			$query_item = "SELECT si.uin, si.articul, si.price, si.name, si.size, si.height, si.material, si.color, si.description, si.available, "
				."sc.name as category_name, sb.name as brand_name, sc.uri as category_uri, sb.uri as brand_uri "
				."FROM shop_items AS si INNER JOIN (shop_categories AS sc, shop_brands AS sb) ON (sc.id = si.category AND sb.id = si.brand) "
				."WHERE si.uin = {$this->item_uin} AND si.active > 0";
			$stmt_item = $this->getPDOInstance()->query($query_item);
			
			$query_item_images = "SELECT CONCAT(item_uin, '_', image_num) AS images FROM shop_images WHERE item_uin = {$this->item_uin} AND active > 0 ORDER BY sort ASC";
			$stmt_item_images = $this->getPDOInstance()->query($query_item_images);
			
			if ( $stmt_item->rowCount() > 0 )
			{
				$shop_item = $stmt_item->fetch(PDO::FETCH_ASSOC);
				$this->getPageTemplateInstance()->addParam("shop_item", $shop_item);
				$this->getPageTemplateInstance()->addParam("shop_item_sizes", (bool)strlen(trim($shop_item["size"])) ? explode(",", $shop_item["size"]) : array());
				$this->getPageTemplateInstance()->addParam("shop_item_images", (is_object($stmt_item_images) ? $stmt_item_images->fetchAll(PDO::FETCH_COLUMN) : array()));
				
				$this->getMainTemplateInstance()->addParam("page_title", $shop_item["name"].($shop_item["articul"]!="" ? " (".$shop_item["articul"].")" : ""));
			}
			else
			{
				$this->getPageTemplateInstance()->setTemplateName("404");
			}
		}
	}
	
	class Page_Shop_Cart extends Standart_Page
	{
		const SHIPPING_PRICE = 300.00;
		const MIN_ORDER_SUM = 500.00;
		
		protected $page_template_name = "shop_cart";
		
		protected $action_result_data;
		protected $cart_params;
		
		public function __construct($action_result_data = array())
		{
			parent::__construct();
			$this->action_result_data = $action_result_data;
			$this->cart_params = array();
		}
		
		protected function processCartData()
		{
			$this->cart_params["list"] = array();
			$this->cart_params["all_num"] = 0;
			$this->cart_params["all_sum"] = 0;
			
			if ( count((array)$_SESSION["shop_cart"]) == 0 ) return;
			
			$uins_in_cart = array();
			foreach ($_SESSION["shop_cart"] as $cart_index => $cart_item)
				$uins_in_cart[$cart_index] = (int)$cart_item["uin"];

			$query_cart_list = "SELECT si.uin, si.articul, si.name, si.price, si.size as sizes, "
				."sc.name as category_name, sb.name as brand_name, sc.uri as category_uri, sb.uri as brand_uri, "
				."(SELECT CONCAT(item_uin, '_', image_num) FROM shop_images WHERE item_uin = si.uin AND active > 0 ORDER BY sort ASC LIMIT 1) AS image "
				."FROM shop_items AS si INNER JOIN (shop_categories AS sc, shop_brands AS sb) ON (sc.id = si.category AND sb.id = si.brand) "
				."WHERE si.available > 0 AND si.active > 0 AND si.uin IN (".(count($uins_in_cart)>0 ? implode(", ", $uins_in_cart) : 0).") "
				."ORDER BY si.sort_by_category ASC, si.sort_by_brand ASC";
			$stmt_cart_list = $this->getPDOInstance()->query($query_cart_list);
			$cart_list = is_object($stmt_cart_list) ? $stmt_cart_list->fetchAll(PDO::FETCH_ASSOC) : array();
			
			$uins_in_query = array();
			foreach ($cart_list as $cart_list_item)
				$uins_in_query[] = $cart_list_item["uin"];
			
			foreach ($uins_in_cart as $in_cart_index => $in_cart_value)
			{
				if ( ($in_query_index = array_search($in_cart_value, $uins_in_query)) !== false )
				{
					$sum = $cart_list[$in_query_index]["price"] * $_SESSION["shop_cart"][$in_cart_index]["num"];
					$this->cart_params["list"][] = array_merge( $cart_list[$in_query_index], array_slice($_SESSION["shop_cart"][$in_cart_index], 1), array("sum" => $sum) );
					
					$this->cart_params["all_num"] += $_SESSION["shop_cart"][$in_cart_index]["num"];
					$this->cart_params["all_sum"] += $sum;
				}
				else
				{
					unset($_SESSION["shop_cart"][$in_cart_index]);
				}
			}
		}
		
		protected function processPageData()
		{
			$this->processCartData();
			
			$this->getPageTemplateInstance()->addParam("shop_cart_list", $this->cart_params["list"]);
			$this->getPageTemplateInstance()->addParam("items_all_num", $this->cart_params["all_num"]);
			$this->getPageTemplateInstance()->addParam("items_all_sum", $this->cart_params["all_sum"]);
			
			$this->getMainTemplateInstance()->addParam("main_menu_num", 2);
		}
	}
	
	class Page_Shop_Order extends Page_Shop_Cart
	{
		protected $page_template_name = "shop_order";
		
		protected function processPageData()
		{
			$this->processCartData();
			
			$this->getPageTemplateInstance()->addParam("shop_cart_list", $this->cart_params["list"]);
			$this->getPageTemplateInstance()->addParam("items_all_num", $this->cart_params["all_num"]);
			$this->getPageTemplateInstance()->addParam("items_all_sum", $this->cart_params["all_sum"]);
			$this->getPageTemplateInstance()->addParam("shipping_price", self::SHIPPING_PRICE);
			$this->getPageTemplateInstance()->addParam("min_order_sum", self::MIN_ORDER_SUM);
			$this->getPageTemplateInstance()->addParam("items_all_sum_with_shipping", $this->cart_params["all_sum"] + self::SHIPPING_PRICE);
			$this->getPageTemplateInstance()->addParam("order_data", $this->action_result_data["request_data"]);
			$this->getPageTemplateInstance()->addParam("order_errors", $this->action_result_data["request_errors"]);
			$this->getPageTemplateInstance()->addParam("order_status", $this->action_result_data["order_status"]);
			$this->getPageTemplateInstance()->addParam("order_number", $this->action_result_data["order_number"]);
		}
	}
	
	class Page_Order_Tracking extends Standart_Page
	{
		protected $page_template_name = "order_tracking";
		
		protected function processPageData()
		{
			$this->getMainTemplateInstance()->addParam("main_menu_num", 6);
		}
	}
	
	class Page_FAQ extends Standart_Page
	{
		protected $page_template_name = "faq";
		
		protected function processPageData()
		{
			$query_faq_list = "SELECT id, question, answer FROM faq WHERE active = 1 ORDER BY change_date DESC";
			$stmt_faq_list = $this->getPDOInstance()->query($query_faq_list);
			$list_faq_items = is_object($stmt_faq_list) ? $stmt_faq_list->fetchAll(PDO::FETCH_ASSOC) : array();
			
			$this->getPageTemplateInstance()->addParam("list_faq_items", $list_faq_items);
			
			$this->getMainTemplateInstance()->addParam("main_menu_num", 3);
		}
	}
	
	class Page_Contacts extends Standart_Page
	{
		protected $page_template_name = "contacts";
		
		protected function processPageData()
		{
			$this->getMainTemplateInstance()->addParam("main_menu_num", 4);
		}
	}
	
	class Page_How_Buy extends Standart_Page
	{
		protected $page_template_name = "how_buy";
		
		protected function processPageData()
		{
			$this->getMainTemplateInstance()->addParam("main_menu_num", 5);
		}
	}
	