{title}Produkty{/title}

{content}
<div class="content-inner single">
   <div class="produkty main-upper">
      <div class="main-upper-inner">
	 <div class="product-categories">
	       <?if(Application::$add):?>
	       <a href="<?=BASEPATH?>/produkty/add">[ Pridať produkt ]</a>
	       <?endif;?>
	 </div>
      </div>
	
   </div>
   <div class="produkty main-lower">
    <?if($titles):?>
     <?foreach($titles as $cat_id => $title):?>
	 <h3 class="title"><?=$title?></h3>
	 <?if(Application::$add):?>
	 <div class="cont-edit"><a href="<?=BASEPATH?>/produkty/add/<?=$cat_id?>">[ Pridať produkt do tejto kategórie ]</a></div>
	 <?endif;?>
	 
	   <?foreach($products[$cat_id] as $product):?>
	      <div class="cont-edit">
		    <?if(Application::$edit):?>
		    <a href="<?=BASEPATH?>/produkty/edit/<?=$product['id']?>">[ Editovať ]</a>&nbsp;&nbsp;
			<?endif;?>
			<?if(Application::$delete):?>
			<a class="delete" href="<?=BASEPATH?>/produkty/delete/<?=$product['id']?>">[ Odstrániť ]</a>
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
					<?if($product['inaction']):?>
					<tr>
					  <td class="product-action"><span class="action-label">Zľava: </span> <span class="action-discount"><?=$product['discount']?>%</span> <span class="action-save">Ušetríte -<?echo round($price * ($product['discount'] / 100),3)?> €</span></td>
					</tr>
					<?endif;?>
				 <?endif;?>
				</table>
			  </td>
			</tr> 
		  </table>
	    <?endforeach?>
	   
     <?endforeach?>
	<?else:?>
	  <p>Katalóg produktov je zatiaľ prázdny.</p>
	  <?if(Application::$edit):?>
	  <p>Vytvorte najprv kategórie (ako položky menu Produkty) a potom do jednotlivých kategórií pridajte produkty.</p>
	  <?endif;?>
	<?endif;?>
   </div>
</div>
{/content}