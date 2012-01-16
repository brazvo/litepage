{title}<?=$title?>{/title}

{content}
<div class="content-inner single novinky">
  <div class="main-upper">
    <h2 class="title"><?=$title?></h2>
  </div>
  <div class="main-lower">
    <?if(Application::$add):?>
	<a class="news-add-link" href="<?=BASEPATH?>/novinky/add">[ Pridať správu ]</a>
	<?endif;?>
	<?if(Application::$view):?>
	  <?if($messages):?>
		<?=$paginator?>
		<div class="news-messages">
	      <?$odd = true?>
	      <?foreach($messages as $message):?>
		  <?if($odd):
		    $cls_odd = ' odd';
		    $odd=false;
		    else:
		    $cls_odd = '';
		    $odd=true;
		    endif;?>
		  <div class="news-message-cont<?=$cls_odd?>">
		    <div class="news-title"><h3 class="title"><?=$message['title']?></h3></div>
		    <?if(Application::$edit):?>
		    <a class="news-edit-links" href="<?=BASEPATH?>/novinky/edit/<?=$message['id']?>">[ Upraviť ]</a>&nbsp;&nbsp;
		    <?endif;?>
		    <?if(Application::$delete):?>
		    <a class="news-edit-links delete" href="<?=BASEPATH?>/novinky/delete/<?=$message['id']?>">[ Vymazať ]</a>&nbsp;&nbsp;
		    <?endif;?>
		    <div><span class="news-date"><?=slovdate($message['datetime'])?></span></div>
		    <div class="news-message"><?=$message['message']?></div>
		  </div>
		  <?endforeach;?>
		</div>
		<hr>
	  <?else:?>
	    <div class="news-messages">Zatiaľ nie su žiadne novinky.</div>
	  <?endif;?>
	  <?=$paginator?>
	<?else:?>
	<p>Nemáte právo na prezeranie tohto obsahu.</p>
	<?endif;?>
  </div>
</div>
{/content}