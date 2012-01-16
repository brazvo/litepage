{title}<?=$title?>{/title}

{content}
<div class="content-inner single">
  <div class="main-upper">
    <h2 class="title"><?=$title?></h2>
  </div>
  <div class="main-lower">
    <?if($image):?>
	<p><img src="<?=BASEPATH?>/images/products/thumb_<?=$image?>" /><p>
	<?endif;?>
    <p><?=$content?></p>
  </div>
</div>
{/content}