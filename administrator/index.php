<?php

	header("Pragma: no-cache");
	header("Cache-Control: no-cache, must-revalidate");
	
	require_once($_SERVER["DOCUMENT_ROOT"]."/administrator/core/config.php");
	
	session_start();
	connect_db();
	
	if ( sizeof($_POST)>0 ) post_handler();
	set_shop_params_lists();
	select_page_function();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Панель администрирования</title>
<link rel="stylesheet" type="text/css" media="all" href="style.css">
<script type="text/javascript">
	
	function get_parent_node_by_tag_name ( node, tag_name )
	{
		var t = node;
		do
		{
			if ( !t || !t.parentNode ) return false;
			t = t.parentNode;
		}
		while ( t.tagName != tag_name );
		
		return t;
	}
	
	function move_items_by_list ( move_node, dir, node_tag_name, node_id_prefix )
	{
		if ( !move_node ) return;
		
		var parent_node = move_node.parentNode;
		
		var move_node_to = move_node;
		switch ( dir )
		{
			case 1:
				var get_node_sibling = function ( node ) { return node.previousSibling; }
				var move_tr_node = function ( node_from, node_to )
				{
					parent_node.insertBefore(node_from.cloneNode(true), node_to);
				}
				break;
			case 2:
				var get_node_sibling = function ( node ) { return node.nextSibling; }
				var move_tr_node = function ( node_from, node_to )
				{
					if ( node_to.nextSibling ) parent_node.insertBefore(node_from.cloneNode(true), node_to.nextSibling);
					else parent_node.appendChild(node_from.cloneNode(true));
				}
				break;
			default:
				return;
		}
		
		do
		{
			move_node_to = get_node_sibling(move_node_to);
			if ( !move_node_to ) return;
		}
		while ( move_node_to.tagName != node_tag_name );
		if ( move_node_to.id.substr(0, node_id_prefix.length) != node_id_prefix ) return;
		
		move_tr_node(move_node, move_node_to);
		parent_node.removeChild(move_node);
	}
	
</script>
</head>

<body>

<?php
	if ( is_login() )
	{
?>
<table style="width: 100%; height: 100%;">
	<tr>
		<td style="width: 200px; background-color: #CCCCCC; vertical-align: top;">
			<table class="table_menu">
			<tr>
				<td class="td_menu_head"><a class="a_menu" href="/administrator/?page=users">Пользователи</a></td>
			</tr>
			<tr>
				<td style="height: 20px;"></td>
			</tr>
			<tr>
				<td class="td_menu_head"><a class="a_menu" href="/administrator/?page=faq">Вопросы и ответы</a></td>
			</tr>
			<tr>
				<td style="height: 20px;"></td>
			</tr>
			<tr>
				<td class="td_menu_head">Товары по категориям</td>
			</tr>
<?php for ($i=0; $i < sizeof ($THIS_PAGE_DATA["shop_categories_list"]["id"]); $i++): ?>
			<tr>
				<td class="td_menu"><a class="a_menu" href="/administrator/?page=shop&amp;list_type=category&amp;list_id=<?=$THIS_PAGE_DATA["shop_categories_list"]["id"][$i]?>"><?=$THIS_PAGE_DATA["shop_categories_list"]["name"][$i]?></a></td>
			</tr>
<?php endfor; ?>
			<tr>
				<td style="height: 20px;"></td>
			</tr>
			<tr>
				<td class="td_menu_head">Товары по брэндам</td>
			</tr>
<?php for ($i=0; $i < sizeof ($THIS_PAGE_DATA["shop_brands_list"]["id"]); $i++): ?>
			<tr>
				<td class="td_menu"><a class="a_menu" href="/administrator/?page=shop&amp;list_type=brand&amp;list_id=<?=$THIS_PAGE_DATA["shop_brands_list"]["id"][$i]?>"><?=$THIS_PAGE_DATA["shop_brands_list"]["name"][$i]?></a></td>
			</tr>
<?php endfor; ?>
			<tr>
				<td style="height: 20px;"></td>
			</tr>
			<tr>
				<td class="td_menu_head"><a class="a_menu" href="/administrator/?page=_logout">Выход</a></td>
			</tr>
			</table>
		</td>
		<td style="text-align: center; vertical-align: top; background-color: #EEEEEE; padding: 5px;"><?php
			if (!empty($THIS_PAGE_TEMPLATE)) 
			{
				require_once($_SERVER["DOCUMENT_ROOT"]."/administrator/core/templates/".$THIS_PAGE_TEMPLATE);
			}
			else
			{
				print "&nbsp;";
			}
		?></td>
	</tr>
	<tr>
		<td style="background-color: #CCCCCC; vertical-align: bottom;">
		</td>
		<td style="background-color: #EEEEEE;">
		</td>
	</tr>
</table>
<?php
	}
	else
	{
?>
<table style="width: 100%; height: 100%;">
<tr>
	<td align="center">
		<form method="POST" action="/administrator/">
		<table>
<?php
		if ( isset($_SESSION["auth_error"]) && !empty($_SESSION["auth_error"]) )
		{
?>
		<tr>
			<td colspan="2" style="color: red; font-weight: bold; padding: 5px;"><?php print $_SESSION["auth_error"]; ?></td>
		</tr>
<?php
		}
?>
		<tr>
			<td style="text-align: right; padding: 5px;">Логин:</td>
			<td style="padding: 5px;"><input type="text" name="login" value="<?php print $_SESSION["login_temp"]; ?>" style="width: 200px;"></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 5px;">Пароль:</td>
			<td style="padding: 5px;"><input type="password" name="password" style="width: 200px;"></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right; padding: 5px;"><input type="submit" value="Войти" style="width: 100px;" onclick="this.disabled=true; this.form.submit();"></td>
		</tr>
		</table>
		</form>
	</td>
</tr>
</table>
<?php
		unset($_SESSION["auth_error"]);
		session_unset();
	}
	disconnect_db();
	unset($_SESSION["page_errors"]);
?>

</body>
</html>