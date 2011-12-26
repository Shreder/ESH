<form method="POST" action="/administrator/?page=<?php print $THIS_PAGE; ?>&action=edit<?php if ( isset($THIS_PAGE_DATA["data"]["id"]) ) print "&amp;id=".$THIS_PAGE_DATA["data"]["id"]; ?>">
<input type="hidden" name="_action" value="faq">
<input type="hidden" name="id" value="<?php if ( isset($THIS_PAGE_DATA["data"]["id"]) ) print $THIS_PAGE_DATA["data"]["id"]; ?>">
<table style="width: 100%;">
<tr>
	<td style="padding-bottom: 30px; text-align: center;"><span style="font-size: 20px; font-weight: bold;"><?php print $THIS_PAGE_DATA["parent_title"].": ".$THIS_PAGE_DATA["data"]["title"]; ?></span><? print $THIS_PAGE_DATA["save"]["_main"]; ?></td>
</tr>
<tr>
	<td><table class="table_det">
	<tr>
		<td style="width: 20%;">Вопрос: *</td>
		<td><input type="text" size="70" name="question" value="<?php print htmlspecialchars($THIS_PAGE_DATA["data"]["question"]); ?>"><? print $THIS_PAGE_DATA["save"]["question"]; ?></td>
	</tr>
	<tr>
		<td>Ответ:</td>
		<td><textarea name="answer" cols="50" rows="20"><?php print htmlspecialchars($THIS_PAGE_DATA["data"]["answer"]); ?></textarea></td>
	</tr>
	<tr>
		<td>Статус:</td>
		<td><select name="show" size="1">
			<option value="1"<?php if ($THIS_PAGE_DATA["data"]["show"]=="1") { print " selected"; } ?>>Активен</option>
			<option value="0"<?php if ($THIS_PAGE_DATA["data"]["show"]=="0") { print " selected"; } ?>>Неактивен</option>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; padding-top: 10px;"><input type="submit" value="Сохранить" style="width: 100px;" onclick="this.disabled=true; this.form.submit();"></td>
	</tr>
	</table></td>
</tr>
</table>
</form>