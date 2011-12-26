<h1>FAQ (Частые вопросы и ответы)</h1>
<?php
	$items = &$this->params_array["list_faq_items"];
	$items_num = sizeof($items);
	for ( $i=0; $i < $items_num; $i++ ):
?>
<div class="faq_item">
<p class="question"><?=$items[$i]["question"]?></p>
<blockquote><?=nl2br($items[$i]["answer"])?></blockquote>
</div>
<?php
	endfor;
?>