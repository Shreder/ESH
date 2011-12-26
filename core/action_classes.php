<?php
	
	class Action_Manage_Cart extends Action
	{
		protected function processActionData()
		{
			$to_cart = $this->getInputParams();
			
			if ( is_numeric($to_cart["uin"]) && ((int)$to_cart["uin"] > 0) && is_numeric($to_cart["num"]) && ((int)$to_cart["num"] > 0) )
			{
				$query_test_item = "SELECT si.size FROM shop_items AS si WHERE si.available > 0 AND si.uin = ".$to_cart["uin"]." LIMIT 1";
				$test_item = $this->getPDOInstance()->query($query_test_item)->fetch(PDO::FETCH_ASSOC);
				if ( sizeof($test_item)>0 )
				{
					$sizes = array_map(trim, explode(",", $test_item["size"]));
					if ( (sizeof($sizes)==0) || in_array((string)$to_cart["size"], $sizes) )
					{
						$is_in_cart = false;
						foreach ((array)$_SESSION["shop_cart"] as $cart_index => $cart_item)
						{
							if ( ($to_cart["uin"] == $cart_item["uin"]) &&  ($to_cart["size"] == $cart_item["size"]) )
							{
								$is_in_cart = true;
								break;
							}
						}
						
						if ( $is_in_cart )
						{
							if ( (int)$to_cart["increment"]==1 ) $_SESSION["shop_cart"][$cart_index]["num"] += (int)$to_cart["num"];
							else $_SESSION["shop_cart"][$cart_index]["num"] = (int)$to_cart["num"];
						}
						else
						{
							$_SESSION["shop_cart"][] = array(
								"uin"			=> (int)$to_cart["uin"],
								"size"			=> $to_cart["size"],
								"num"			=> (int)$to_cart["num"]
							);
						}
					}
				}
			}
		}
	}
	
	class Action_Delete_From_Cart extends Action
	{
		protected function processActionData()
		{
			$is_in_cart = false;
			foreach ((array)$_SESSION["shop_cart"] as $cart_index => $cart_item)
			{
				if ( ($this->getInputParam("uin") == $cart_item["uin"]) &&  ($this->getInputParam("size") == $cart_item["size"]) )
				{
					$is_in_cart = true;
					break;
				}
			}
			if ($is_in_cart) unset($_SESSION["shop_cart"][$cart_index]);
		}
	}
	
	class Action_Clear_Cart extends Action
	{
		protected function processActionData()
		{
			unset($_SESSION["shop_cart"]);
		}
	}
	
	class Action_Shop_Order extends Action
	{
		protected function processActionData()
		{
			$request_errors = array();
			
			if ( !(bool)strlen($this->getInputParam("contact_name")) ) $request_errors["contact_name"] = "Это поле обязательно для заполнения";
			if ( !(bool)strlen($this->getInputParam("contact_email")) ) $request_errors["contact_email"] = "Это поле обязательно для заполнения";
			if ( !(bool)strlen($this->getInputParam("contact_tel")) ) $request_errors["contact_tel"] = "Это поле обязательно для заполнения";
			else
			{
				$email_parts = explode("@", $this->getInputParam("contact_email"));
				if ( (sizeof ($email_parts)!=2) || (bool)preg_match( "/[^A-Za-z0-9\-\_\.]+/", $email_parts[0]) || (bool)preg_match( "/[^a-z0-9\-\.]+/", $email_parts[1]) ) $request_errors["contact_email"] = "Значение поля введено некорректно";
				elseif ( sizeof((array)dns_get_record($email_parts[1]))==0 ) $request_errors["contact_email"] = "Невозможно определить сервер, обслуживающий ваш e-mail адрес";
			}
			if ( !(bool)strlen($this->getInputParam("shipping_address")) ) $request_errors["shipping_address"] = "Это поле обязательно для заполнения";
			if ( !(bool)strlen($this->getInputParam("control_code")) ) $request_errors["control_code"] = "Контрольный код не введён";
			elseif ( $this->getInputParam("control_code") != $_SESSION["captcha_keystring"] ) $request_errors["control_code"] = "Неверно введён контрольный код";
			if ( !in_array($this->getInputParam("delivery_type"), array("cash", "bank")) ) $request_errors["delivery_type"] = "Неверно введён способ доставки и оплаты";
			
			$uins = $this->getInputParam("order_uin");
			$sizes = $this->getInputParam("order_size");
			$nums = $this->getInputParam("order_num");
			foreach ($uins as $value)
			{
				if ( !is_numeric($value) || ((int)$value < 1) )
				{
					$request_errors["order_uin"] = "Ошибки в идентификаторах товаров!";
					break;
				}
			}
			
			if ( empty($request_errors["order_uin"]) )
			{
				$query_cart_list = "SELECT si.uin, si.articul, si.name, si.price, si.size "
					."FROM shop_items AS si WHERE si.uin IN (".(count($uins)>0 ? implode(", ", $uins) : 0).") "
					."ORDER BY si.id ASC";
				$cart_list = $this->getPDOInstance()->query($query_cart_list)->fetchAll(PDO::FETCH_ASSOC);
				
				$uins_in_query = array();
				foreach ($cart_list as $cart_list_item)
					$uins_in_query[] = $cart_list_item["uin"];
				
				foreach ($sizes as $key => $value)
				{
					$cart_sizes = explode(",", $cart_list[array_search($uins[$key], $uins_in_query)]["size"]);
					foreach ($cart_sizes as &$cart_size) $cart_size = trim($cart_size);
					
					if ( !empty($value) && !in_array(trim($value), $cart_sizes) )
					{
						$request_errors["order_size"] = "Ошибки в размерах товаров!";
						break;
					}
				}
				foreach ($nums as $value)
				{
					if ( !is_numeric($value) || ((int)$value < 1) )
					{
						$request_errors["order_num"] = "Ошибки в количестве товаров!";
						break;
					}
				}
			}
			
			if ( sizeof($request_errors) > 0 )
			{
				$request_errors["main_msg"] = "Форма заказа заполнена с ошибками";
			}
			else
			{
				$items_list = "";
				$values_list = array();
				$counter = 0;
				$all_sum = 0;
				$current_datetime = time();
				foreach ($uins as $key => $value)
				{
					$counter++;
					$uiq = array_search($value, $uins_in_query);
					$sum = $nums[$key] * $cart_list[$uiq]["price"];
					$all_sum += $sum;
					$items_list .= "\n".$counter.". ".stripslashes($cart_list[$uiq]["name"].(($cart_list[$uiq]["articul"]!="") ? " (".$cart_list[$uiq]["articul"].")" : "")).(!empty($sizes[$key]) ? ", размер ".$sizes[$key] : "").", кол-во ".$nums[$key].", стоимость ".sprintf("%.2f", $sum)." руб.";
					$values_list[] = "%uin%, ".$value.", '".$sizes[$key]."'";
				}
				
				$this->getPDOInstance()->exec("LOCK TABLES shop_orders WRITE, shop_order_items WRITE");
				do
				{
					$order_number = mt_rand(1, PHP_INT_MAX);
					$isset_number_row = $this->getPDOInstance()->query("SELECT COUNT(uin) FROM shop_orders WHERE uin = ".$order_number)->fetch(PDO::FETCH_NUM);
				}
				while ($isset_number_row[0] > 0);
				
				$insert_order_query = "INSERT INTO shop_orders (uin, date, delivery_type, delivery_address, contact_name, contact_tel, contact_email, amount) VALUES "
					."(".$order_number.", '"
					.date("Y-m-d H:i:s", $current_datetime)."', '"
					.$this->getInputParam("delivery_type")."', '"
					.addslashes($this->getInputParam("shipping_address"))."', '"
					.addslashes($this->getInputParam("contact_name"))."', '"
					.addslashes($this->getInputParam("contact_tel"))."', '"
					.addslashes($this->getInputParam("contact_email"))."', '".$all_sum."')";
				$insert_status = (bool)$this->getPDOInstance()->exec($insert_order_query);
				
				for ($i=0; $i<count($values_list); $i=$i+10)
				{
					$vs = array_slice($values_list, $i, 10);
					$insert_status = $insert_status && (bool)$this->getPDOInstance()->exec("INSERT INTO shop_order_items (order_uin, item_uin, item_size) VALUES (".str_replace("%uin%", $order_number, implode("), (", $vs)).")");
				}
				
				$this->getPDOInstance()->exec("UNLOCK TABLES");
				
				///////////////
				
				$to_owner_text = "Время совершения заказа: ".date("d.m.Y H:i", $current_datetime)."\n"
					."Общая стоимость: ".sprintf("%.2f", $all_sum)." рублей\n"
					."Выбранный способ доставки и оплаты: ".($this->getInputParam("delivery_type")=="cash" ? "Наличными курьеру при доставке в Москве" : "Банковским переводом")."\n\n"
					."Контактное лицо: ".$this->getInputParam("contact_name")."\n"
					."Контактный телефон: ".$this->getInputParam("contact_tel")."\n"
					."Контактный e-mail: ".$this->getInputParam("contact_email")."\n\n"
					."Контактный e-mail: ".$this->getInputParam("shipping_address")."\n\n\n"
					."Детали заказа:\n".$items_list;
					
				$mime_mail = new Mail_mime("\n");
				$headers = array(
					"From"		=> mb_convert_encoding("Интернет-магазин \"Экстрим-Шуз\" <info@extreme-shoes.ru>", "KOI8-R", "UTF-8"),
					"Subject"	=> mb_convert_encoding("Заказ #".$order_number, "KOI8-R", "UTF-8")
				);
				$encoding_headers = array(
					"head_charset"		=> "koi8-r",
					"text_charset"		=> "koi8-r"
				);
				$mime_mail->setTXTBody(mb_convert_encoding($to_owner_text, "KOI8-R", "UTF-8"));
				$mail_body = $mime_mail->get((array)$encoding_headers);
				$mail_headers = $mime_mail->headers($headers);
				
				$mail = Mail::factory("mail");
				$owner_mail_status = $mail->send("kulbatsky@rezobuv.ru", $mail_headers, $mail_body);
				
				////////////
				
				$to_client_text = "Здравствуйте! Вы только что сделали заказ в интернет-магазине \"Экстрим-Шуз\"!\n"
					."Номер Вашего заказа: ".$order_number."\n\n"
					."Время совершения заказа: ".date("d.m.Y H:i", $current_datetime)."\n"
					."Общая стоимость: ".sprintf("%.2f", $all_sum)." рублей\n"
					."Выбранный способ доставки и оплаты: ".($this->getInputParam("delivery_type")=="cash" ? "Наличными курьеру при доставке в Москве" : "Банковским переводом")."\n\n\n"
					."Детали заказа:\n".$items_list."\n\n\n"
					."В ближайшее время наши менеджеры обязательно свяжутся с Вами для уточнения деталей доставки.\nСпасибо за покупку!";
					
				$mime_mail = new Mail_mime("\n");
				$headers = array(
					"From"		=> mb_convert_encoding("Интернет-магазин \"Экстрим-Шуз\" <info@extreme-shoes.ru>", "KOI8-R", "UTF-8"),
					"Subject"	=> mb_convert_encoding("Ваш заказ #".$order_number, "KOI8-R", "UTF-8")
				);
				$encoding_headers = array(
					"head_charset"		=> "koi8-r",
					"text_charset"		=> "koi8-r"
				);
				$mime_mail->setTXTBody(mb_convert_encoding($to_client_text, "KOI8-R", "UTF-8"));
				$mail_body = $mime_mail->get((array)$encoding_headers);
				$mail_headers = $mime_mail->headers($headers);
				
				$mail = Mail::factory("mail");
				$client_mail_status = $mail->send($this->getInputParam("contact_email"), $mail_headers, $mail_body);
				
				///////////
				
				if ($insert_status && $owner_mail_status && $client_mail_status)
				{
					$this->setResultParam("order_status", true);
					unset($_SESSION["shop_cart"]);
				}
				else
				{
					$request_errors["main_msg"] = "При обработке заказа возникли ошибки";
				}
			}
			
			$this->setResultParam("request_data", $this->getInputParams());
			$this->setResultParam("order_number", $order_number);
			$this->setResultParam("request_errors", $request_errors);
		}
	}