{title}<?=$title?>{/title}

{content}
<div class="content-inner single novinky">
  <div class="main-upper">
    <h2 class="title"><?=$title?></h2>
	<?if(Application::$edit):?>
	    <a class="news-edit-links" href="<?=BASEPATH?>/novinky/edit/<?=$id?>">[ Upraviť ]</a>&nbsp;&nbsp;
	<?endif;?>
	<?if(Application::$delete):?>
	    <a class="news-edit-links delete" href="<?=BASEPATH?>/novinky/delete/<?=$id?>">[ Vymazať ]</a>&nbsp;&nbsp;
	<?endif;?>
  </div>
  <div class="main-lower">
    <p><i><?=$date?></i></p>
	<p><?=$message?></p>
  </div>
</div>
{/content}