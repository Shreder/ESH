<form method="POST" action="/administrator/?page=<?php print $THIS_PAGE; ?>&action=edit<?php if ( isset($THIS_PAGE_DATA["data"]["id"]) ) print "&amp;id=".$THIS_PAGE_DATA["data"]["id"]; ?>">
<input type="hidden" name="_action" value="users">
<input type="hidden" name="id" value="<?php if ( isset($THIS_PAGE_DATA["data"]["id"]) ) print $THIS_PAGE_DATA["data"]["id"]; ?>">
<table style="width: 100%;">
<tr>
	<td style="padding-bottom: 30px; text-align: center;"><span style="font-size: 20px; font-weight: bold;"><?php print $THIS_PAGE_DATA["parent_title"].": ".$THIS_PAGE_DATA["data"]["title"]; ?></span><? print $THIS_PAGE_DATA["save"]["_main"]; ?></td>
</tr>
<tr>
	<td><table class="table_det">
	<tr>
		<td style="width: 20%;">Имя пользователя:</td>
		<td><input type="text" size="50" name="name" value="<?php print htmlspecialchars($THIS_PAGE_DATA["data"]["name"]); ?>"><? print $THIS_PAGE_DATA["save"]["name"]; ?></td>
	</tr>
	<tr>
		<td>Логин: *</td>
		<td><input type="text" size="50" name="login" value="<?php print $THIS_PAGE_DATA["data"]["login"]; ?>"><? print $THIS_PAGE_DATA["save"]["login"]; ?></td>
	</tr>
	<tr>
		<td>Пароль: **</td>
		<td><input type="password" size="50" name="password"><? print $THIS_PAGE_DATA["save"]["password"]; ?></td>
	</tr>
	<tr>
		<td>Повторить пароль: **</td>
		<td><input type="password" size="50" name="password_retype"><? print $THIS_PAGE_DATA["save"]["password_retype"]; ?></td>
	</tr>
	<tr>
		<td>Статус:</td>
		<td><select name="active" size="1">
			<option value="1"<?php if ($THIS_PAGE_DATA["data"]["active"]=="1") { print " selected"; } ?>>Активен</option>
			<option value="0"<?php if ($THIS_PAGE_DATA["data"]["active"]=="0") { print " selected"; } ?>>Неактивен</option>
		</select></td>
	</tr>
	<tr>
		<td>Дата последнего изменения:</td>
		<td><?php print $THIS_PAGE_DATA["data"]["change_date"]; ?></td>
	</tr>
	<tr>
		<td>IP-адрес, с которого было совершено последнее изменение:</td>
		<td><?php print $THIS_PAGE_DATA["data"]["change_ip"]; ?></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; padding-top: 10px;"><input type="submit" value="Сохранить" style="width: 100px;" onclick="this.disabled=true; this.form.submit();"></td>
	</tr>
	</table></td>
</tr>
</table>
</form>