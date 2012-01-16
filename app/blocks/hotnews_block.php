<?php
$row = db::fetch("SELECT value FROM hotnews_settings WHERE constant='HN_MESSAGES_IN_BLOCK'");
$limit = $row['value'];
$row = db::fetch("SELECT value FROM hotnews_settings WHERE constant='HN_SHOW_DATE'");
$showdate = $row['value'];
$row = db::fetch("SELECT value FROM hotnews_settings WHERE constant='HN_CHARACTERS_IN_BLOCK'");
$charlimit = $row['value'];
$rows = db::fetchAll("SELECT * FROM hotnews ORDER BY datetime DESC LIMIT $limit");
?>
<div class="block-inner">
  <div class="block-title"><h3 class="title">Novinky</h3></div>
  <hr />
  <?foreach($rows as $message):?>
  <div class="block-news-title">
    <span><b><?=$message['title']?></b></span><br/>
	<?if($showdate):?>
	<span style="font-size:8pt"><i><?=slovdate($message['datetime'])?></i></span>
	<?endif;?>
  </div>
  <div class="block-news-message-container" style="font-size:8pt">
    <?if(strlen(utf8_decode($message['message'])) > $charlimit):?>
    <span class="block-news-message"><?=substr($message['message'],0,$charlimit)?>...</span><br/>
	<span><a class="block-news-link-more" href="<?=BASEPATH?>/novinky/frontend/show/<?=$message['id']?>">Čítať ďalej</a></span>
	<?else:?>
	<span class="block-news-message"><?=$message['message']?></span>
	<?endif;?>
  </div>
  <hr/>
  <?endforeach;?>  
</div>