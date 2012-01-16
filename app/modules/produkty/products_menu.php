<div class="block-inner">
  <?if(block::getMenu('produkty')):?>
	  <div class="block-title"><h3 class="title">Sortiment</h3></div>
	  <?=block::getMenu('produkty', 'produkty/zoznam')?>
  <?endif;?>
</div>
