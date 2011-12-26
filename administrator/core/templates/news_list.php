<table class="table_list_main">
<tr>
	<td style="padding-bottom: 30px; text-align: center; font-size: 20px; font-weight: bold;"><?php print $THIS_PAGE_DATA["parent_title"]; ?>: Список новостей</td>
</tr>
<tr>
	<td style="text-align: center;">
		<table class="table_list">
		<tr style="background-color: #CCCCCC; font-weight: bold;">
			<td style="width: 3%;">№</td>
			<td>Дата</td>
			<td>Краткий текст</td>
			<td>Активна</td>
			<td style="width: 20%;">Действия</td>
		</tr>
		<?php
			for ( $i=0; $i<sizeof($THIS_PAGE_DATA["list"]["id"]); $i++ )
			{
		?>
		<tr style="background-color: #DDDDDD;">
			<td><?php print $i+1; ?></td>
			<td><?php print $THIS_PAGE_DATA["list"]["date"][$i]; ?></td>
			<td><?php print $THIS_PAGE_DATA["list"]["small_text"][$i]; ?></td>
			<td><?php print $THIS_PAGE_DATA["list"]["active"][$i]; ?></td>
			<td><a class="a_actions" href="/administrator/?page=<?php print $THIS_PAGE; ?>&amp;action=edit&amp;id=<?php print $THIS_PAGE_DATA["list"]["id"][$i]; ?>">редактировать</a><br>
			<a class="a_actions" href="javascript:if(confirm('Удалить новость?')) window.location.href='/administrator/?page=<?php print $THIS_PAGE; ?>&amp;action=delete&amp;id=<?php print $THIS_PAGE_DATA["list"]["id"][$i]; ?>';">удалить</a></td>
		</tr>
		<?php
			}
		?>
		</table>
	</td>
</tr>
<tr>
	<td style="padding-top: 50px; padding-right: 5px; text-align: right;"><a class="a_add" href="/administrator/?page=<?php print $THIS_PAGE; ?>&amp;action=edit">Добавить новость</a></td>
</tr>
</table>