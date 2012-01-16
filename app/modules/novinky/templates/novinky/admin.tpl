{title}<?=$title?>{/title}

{content}
<div class="content-inner single">
  <div class="main-upper">
    <h2 class="title"><?=$title?></h2>
  </div>
  <div class="main-lower">
    <div class="news-adm-links">
    <?if(Application::$edit):?>
	<a href="<?=BASEPATH?>/novinky/settings">[ Nastavenia ]</a>&nbsp;&nbsp;
	<?endif;?>
	<?if(Application::$view):?>
	<a href="<?=BASEPATH?>/novinky">[ Novinky ]</a>&nbsp;&nbsp;
	<?endif;?>
	</div>
  </div>
</div>
{/content}