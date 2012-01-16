{title}Produkty{/title}

{content}
<div class="content-inner single">
   <div class="produkty main-upper">
    <div class="product-categories">
      <a class="cat-link" href="<?=BASEPATH?>/admin/produkty/categories">[ Kategórie > ]</a>&nbsp;&nbsp;
	  <?if(Application::$add):?>
	  <a href="<?=BASEPATH?>/admin/produkty/add">[ Pridať produkt ]</a>
	  <?endif;?>
	  <?=block::getMenu('produkty', 'admin/produkty/list')?>
    </div>
	
   </div>
   <div class="produkty main-lower">
     <?foreach($titles as $cat_id => $title):?>
	 <?if(Application::$add):?>
     <div class="cont-edit"><a href="<?=BASEPATH?>/admin/produkty/add/<?=$cat_id?>">[ Pridať produkt do tejto kategórie ]</a></div>
	 <?endif;?>
	 <h3 class="title"><?=$title?></h3>
	   
	   <?foreach($products[$cat_id] as $product):?>
	      <div class="cont-edit">
		    <?if(Application::$edit):?>
		    <a href="<?=BASEPATH?>/admin/produkty/edit/<?=$product['id']?>">[ Editovať ]</a>&nbsp;&nbsp;
			<?endif;?>
			<?if(Application::$delete):?>
			<a class="delete" href="<?=BASEPATH?>/admin/produkty/delete/<?=$product['id']?>">[ Odstrániť ]</a>
			<?endif;?>
		  </div>
	      <table class="products" cellspacing="0" cellpadding="0" border="0" style="width:630px">
		    <tr>
			  <?if($product['image']):?>
			  <td class="product-image" valign="top" style="width:<?=THUMB_SIZE?>px">
			    <a class="product-view" href="<?=BASEPATH?>/images/products/<?=$product['image']?>">
			      <img src="<?=BASEPATH?>/images/products/thumb_<?=$product['image']?>" alt="" style="border:none" />
				</a>
			  </td>
			  <?endif;?>
			  <td class="product" valign="top">
				<table class="product-inner" cellspacing="0" cellpadding="0" border="0" style="width:100%; height:100%">
				 <tr>
				   <td class="product-header">
				     <?=$product['name']?>&nbsp;&nbsp;
					 <?if($product['new']):?>
					   <span style="color:red; font-size:9pt">NOVINKA</span>&nbsp;&nbsp;
					 <?endif;?>
					 <?if($product['inaction']):?>
					   <span style="color:red; font-size:9pt">AKCIA</span>
					 <?endif;?>
				   </td>
				 </tr>
				 <?if($product['description']):?>
				 <tr>
				   <td class="product-description"><?=$product['description']?>&nbsp;</td>
				 </tr>
				 <?endif;?>
				 <?if($product['attributes']):?>
				 <tr>
				   <td class="product-attributes"><?=$product['attributes']?>&nbsp;</td>
				 </tr>
				 <?endif;?>
				 <?if(DISPLAY_PRICE):?>
				 <tr>
				    <td class="product-price">
					<?if(VAT_PAYER):?>
					  <?if(PRICES_WITH_VAT):?>
					    <span class="price-without-vat"><span class="label">Cena bez DPH:</span> <? echo round($product['price'] / ($product['vat'] / 100 + 1) , 2)?> €</span>&nbsp;&nbsp;
						<span class="vat"><span class="label">DPH:</span>  <?=(int)$product['vat']?>%</span>&nbsp;&nbsp;
						<span class="price-with-vat"><span class="label">Cena s DPH:</span> <?=$price = $product['price']?> €</span>
					  <?else:?>
					    <span class="price-without-vat"><span class="label">Cena bez DPH:</span> <?=$product['price']?> €</span>&nbsp;&nbsp;
						<span class="vat"><span class="label">DPH:</span>  <?=(int)$product['vat']?>%</span>&nbsp;&nbsp;
						<span class="price-with-vat"><span class="label">Cena s DPH:</span> <? echo $price = round($product['price'] * ($product['vat'] / 100 + 1) , 2)?> €</span>
					  <?endif;?>
					<?else:?>
					  <span class="price-with-vat"><span class="label">Cena:</span> <?=$price = $product['price']?> €</span>  
					<?endif;?>
					</td>
				 </tr>
				 <?endif;?>
				 <?if($product['inaction']):?>
				 <tr>
				   <td class="product-action"><span class="action-label">Zľava: </span> <span class="action-discount"><?=$product['discount']?>%</span> <span class="action-save">Ušetríte -<?echo round($price * ($product['discount'] / 100),3)?> €</span></td>
				 </tr>
				 <?endif;?>
				</table>
			  </td>
			</tr> 
		  </table>
	   <?endforeach?>
	   
     <?endforeach?>
   </div>
</div>
<script type="text/javascript">
/* <![CDATA[ */
// content of your Javascript goes here
$("div.product-categories a.cat-link").click(function(){
  if($("ul.menu-produkty").is(":hidden")){
    $("ul.menu-produkty").show(200);
  }
  else{
    $("ul.menu-produkty").hide(200);
  }
  
  return false;
});

$("ul.menu-produkty").mouseleave(function(){
  $(this).hide(200);
});
/* ]]> */
</script>
<script type="text/javascript">
  /* <![CDATA[ */
	$("a.product-view").fancybox();
  /* ]]> */
</script>
{/content}