<?php

	class Database
	{
		protected $db_type;
		protected $db_host;
		protected $db_port;
		protected $db_name;
		protected $db_username;
		protected $db_password;
		protected $db_default_encoding;
		
		protected $connect_resource = null;
		protected $PDO_instance = null;
		
		public function __construct($db_item)
		{
			include_once("config/".$db_item.".php");
			$this->db_type = DB_TYPE;
			$this->db_host = DB_HOST;
			$this->db_port = DB_PORT;
			$this->db_name = DB_NAME;
			$this->db_username = DB_USERNAME;
			$this->db_password = DB_PASSWORD;
			$this->db_default_encoding = DB_DEFAULT_ENCODING;
		}
		
		public function createResource()
		{
			switch ($this->db_type)
			{
				case "mysql":
					if ( is_null($this->connect_resource) )
					{
						$temp_resource = @mysql_connect($this->db_host.(!empty($this->db_port) ? ":".$this->db_port : ""), $this->db_username, $this->db_password);
						if ($temp_resource !== false)
						{
							$this->connect_resource = $temp_resource;
							mysql_select_db($this->db_name, $this->connect_resource);
							mysql_query("SET NAMES `".$this->db_default_encoding."`", $this->connect_resource);
						}
					}
					return $this->connect_resource;
			}
		}
		
		public function createPDOInstance()
		{
			switch ($this->db_type)
			{
				case "mysql":
					if ( is_null($this->PDO_instance) )
					{
						try
						{
							$temp_instance = new PDO("mysql:dbname=".$this->db_name.";host=".$this->db_host.(!empty($this->db_port) ? ";port=".$this->db_port : ""),
								$this->db_username, $this->db_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$this->db_default_encoding));
							$this->PDO_instance = $temp_instance;
						}
						catch (PDOException $e)
						{
						}
					}
					return $this->PDO_instance;
			}
		}
		
		public static function getResource($db_config = "db")
		{
			$db = new self($db_config);
			return $db->createResource();
		}
		
		public static function getPDOInstance($db_config = "db")
		{
			$db = new self($db_config);
			return $db->createPDOInstance();
		}
		
	}

	class Template
	{
		protected $template_name;
		protected $template_fullpath;
		protected $params_array;
		protected $result_string;
		
		protected $is_valid_result_string;
		
		protected function processTemplate()
		{
			if (!empty($this->template_name) && file_exists($this->template_fullpath) )
			{
				ob_start();
				include_once($this->template_fullpath);
				$this->result_string = ob_get_clean();
				$this->is_valid_result_string = true;
			}
		}
		
		public function __construct($template_name, $params = array())
		{
			$this->template_name = $template_name;
			$this->template_fullpath = TEMPLATES_PATH.$this->template_name.".php";
			$this->params_array = (array)$params;
			$this->is_valid_result_string = false;
		}
		
		public function setTemplateName($template_name)
		{
			$this->template_name = $template_name;
			$this->template_fullpath = TEMPLATES_PATH.$this->template_name.".php";
			$this->is_valid_result_string = false;
		}
		
		public function addParam($param_name, $param_value)
		{
			$this->params_array[$param_name] = &$param_value;
			$this->is_valid_result_string = false;
		}
		
		public function addParams($params)
		{
			$this->params_array = array_merge($this->params_array, (array)$params);
			$this->is_valid_result_string = false;
		}
		
		public function getResultString()
		{
			if ( !$this->is_valid_result_string ) $this->processTemplate();
			return $this->result_string;
		}
	}
	
	abstract class Page
	{
		protected $main_template_name = "";
		
		private $main_template_instance;
		private $pdo_instance;
		
		public function __construct()
		{
			$this->pdo_instance = Database::getPDOInstance();
			
			$this->main_template_instance = new Template($this->main_template_name);
		}
		
		abstract protected function processPageData();
		
		protected function getMainTemplateInstance()
		{
			return $this->main_template_instance;
		}
		
		protected function getPDOInstance()
		{
			return $this->pdo_instance;
		}
		
		public function postProcessData()
		{
		}
		
		public function getResultPageDump()
		{
			$this->processPageData();
			$this->postProcessData();
			return $this->main_template_instance->getResultString();
		}
	}
	
	abstract class Standart_Page extends Page
	{
		protected $page_template_name = "";
		protected $main_template_name = "main";		
		private $page_template_instance;
		
		protected $shop_menu = array();
		
		public function __construct()
		{
			parent::__construct();
			
			$this->page_template_instance = new Template($this->page_template_name);
			
			$this->shop_menu = array(
				"category"		=> $this->getPDOInstance()->query("SELECT sc.name2 as name, sc.uri, (SELECT COUNT(si.id) FROM shop_items AS si WHERE si.category=sc.id AND si.active>0) AS count FROM shop_categories AS sc ORDER BY sc.sort ASC")->fetchAll(PDO::FETCH_ASSOC),
				"brand"			=> $this->getPDOInstance()->query("SELECT sb.name2 as name, sb.uri, (SELECT COUNT(si.id) FROM shop_items AS si WHERE si.brand=sb.id AND si.active>0) AS count FROM shop_brands AS sb ORDER BY sb.sort ASC")->fetchAll(PDO::FETCH_ASSOC)
			);
			$this->getMainTemplateInstance()->addParam("shop_menu", $this->shop_menu);
			
			$this->getMainTemplateInstance()->addParam("brands_menu", $this->shop_menu["brand"]);
		}
		
		protected function getPageTemplateInstance()
		{
			return $this->page_template_instance;
		}
		
		public function postProcessData()
		{
			$shop_cart_items_count = 0;
			foreach ( (array)$_SESSION["shop_cart"] as $shop_cart_item ) $shop_cart_items_count += $shop_cart_item["num"];
			$this->getMainTemplateInstance()->addParam("shop_cart_items_count", $shop_cart_items_count);
		}
		
		public function getResultPageDump()
		{
			$this->processPageData();
			$this->postProcessData();
			$this->getMainTemplateInstance()->addParam("page_content", $this->getPageTemplateInstance()->getResultString());
			return $this->getMainTemplateInstance()->getResultString();
		}
	}
	
	class AJAX_Page extends Page
	{
		private $data;
		
		public function __construct($data)
		{
			$this->data = $data;
		}
		
		protected function processPageData()
		{
		}
		
		public function postProcessData()
		{
		}
		
		public function getResultPageDump()
		{
			return json_encode($this->data);
		}
	}
	
	abstract class Action
	{
		const SHIPPING_PRICE = 300.00;
		
		private $input_params_array;
		private $result_data_array;
		
		private $is_processed;
		
		private $pdo_instance;
		
		public function __construct($params)
		{
			$this->input_params_array = (array)$params;
			$this->is_processed = false;
			$this->pdo_instance = Database::getPDOInstance();
		}
		
		public function addParam($name, $value)
		{
			$this->input_params_array[$name] = $value;
			$this->is_processed = false;
		}
		
		public function addParams($params)
		{
			$this->input_params_array = array_merge($this->input_params_array, (array)$params);
			$this->is_processed = false;
		}
		
		protected function getInputParam($name)
		{
			return $this->input_params_array[$name];
		}
		
		protected function getInputParams()
		{
			return $this->input_params_array;
		}
		
		protected function setResultParam($name, $value)
		{
			$this->result_data_array[$name] = $value;
		}
		
		protected function setResultParams($params)
		{
			$this->result_data_array = array_merge($this->result_data_array, (array)$params);
		}
		
		protected function getPDOInstance()
		{
			return $this->pdo_instance;
		}
		
		abstract protected function processActionData();
		
		
		protected function postProcessData()
		{
			$shop_cart_items_all_num = 0;
			$shop_cart_items_all_sum = 0;
			foreach ( (array)$_SESSION["shop_cart"] as $shop_cart_item )
			{
				$shop_cart_items_all_num += $shop_cart_item["num"];
				$shop_cart_items_all_sum += $shop_cart_item["price"] * $shop_cart_item["num"];
			}
			//$this->setResultParam("request_data", $this->getInputParams());
			$this->setResultParam("shop_cart_items_all_num", $shop_cart_items_all_num);
			$this->setResultParam("shop_cart_items_all_sum", $shop_cart_items_all_sum);
		}
		
		
		public function doAction()
		{
			$this->result_data_array = array();
			$this->processActionData();
			$this->postProcessData();
			$this->is_processed = true;
		}
		
		public function getResultData()
		{
			if (!$this->is_processed) $this->doAction();
			return $this->result_data_array;
		}
	}