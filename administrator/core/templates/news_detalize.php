<form method="POST" action="/administrator/?page=<?php print $THIS_PAGE; ?>&action=edit<?php if ( isset($THIS_PAGE_DATA["data"]["id"]) ) print "&amp;id=".$THIS_PAGE_DATA["data"]["id"]; ?>">
<input type="hidden" name="_action" value="news">
<input type="hidden" name="id" value="<?php if ( isset($THIS_PAGE_DATA["data"]["id"]) ) print $THIS_PAGE_DATA["data"]["id"]; ?>">
<table style="width: 100%;">
<tr>
	<td style="padding-bottom: 30px; text-align: center;"><span style="font-size: 20px; font-weight: bold;"><?php print $THIS_PAGE_DATA["parent_title"].": ".$THIS_PAGE_DATA["data"]["title"]; ?></span><? print $THIS_PAGE_DATA["save"]["_main"]; ?></td>
</tr>
<tr>
	<td><table class="table_det">
	<tr>
		<td style="width: 20%;">Дата:</td>
		<td>Год: <select name="date_year" size="1">
		<?php
			for ( $i=date("Y")-5; $i<date("Y")+2; $i++ )
			{
				print "<option value=\"".$i."\"".( $i==(empty($THIS_PAGE_DATA["data"]["id"]) ? date("Y") : $THIS_PAGE_DATA["data"]["date_year"]) ? " selected" : "").">".$i."</option>\n";
			}
		?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		Месяц: <select name="date_month" size="1">
		<?php
			for ( $i=1; $i<=12; $i++ )
			{
				print "<option value=\"".$i."\"".( $i==(empty($THIS_PAGE_DATA["data"]["id"]) ? date("m") : $THIS_PAGE_DATA["data"]["date_month"]) ? " selected" : "").">".$i."</option>\n";
			}
		?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		День: <select name="date_day" size="1">
		<?php
			for ( $i=1; $i<=31; $i++ )
			{
				print "<option value=\"".$i."\"".( $i==(empty($THIS_PAGE_DATA["data"]["id"]) ? date("d") : $THIS_PAGE_DATA["data"]["date_day"]) ? " selected" : "").">".$i."</option>\n";
			}
		?>
		</select><? print $THIS_PAGE_DATA["save"]["date"]; ?>
		</td>
	</tr>
	<tr>
		<td>Краткий текст новости:</td>
		<td><textarea name="small_text" cols="70" rows="5"><?php print htmlspecialchars($THIS_PAGE_DATA["data"]["small_text"]); ?></textarea></td>
	</tr>
	<tr>
		<td>Полный текст новости:</td>
		<td><textarea name="full_text" cols="70" rows="20"><?php print $THIS_PAGE_DATA["data"]["full_text"]; ?></textarea></td>
	</tr>
	<tr>
		<td>Активна:</td>
		<td><select name="active" size="1">
			<option value="1"<?php if ($THIS_PAGE_DATA["data"]["active"]=="1") { print " selected"; } ?>>Да</option>
			<option value="0"<?php if ($THIS_PAGE_DATA["data"]["active"]=="0") { print " selected"; } ?>>Нет</option>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; padding-top: 10px;"><input type="submit" value="Сохранить" style="width: 100px;" onclick="this.disabled=true; this.form.submit();"></td>
	</tr>
	</table></td>
</tr>
</table>
</form>