{title}<?=$cont->title?>{/title}

{content}
<div class="content-inner error">
  <div class="main-upper">
    <h2><?=$cont->title?></h2>
	<p><a href="<?=BASEPATH?>/texyla/add">[ <?=TEXYLA_ADMIN_LNK_ADD_TEXTAREA?> ]</a></p>
  </div>
  <div class="main-lower">
    <?if($cont->textareas):?>
    <table cellspacing="0" cellpadding="3" class="admin-table" style="width:100%">
	  <thead>
	    <tr>
		  <td><?=TEXYLA_ADMIN_CSS_NAME?></td>
		  <td><?=TEXYLA_ADMIN_DESC?></td>
		  <td colspan="2"><?=ACTIONS?></td>
		</tr>
	  </thead>
	  <tbody>
	  <?foreach($cont->textareas as $textarea):?>
	    <tr>
		  <td><?=$textarea['textarea']?></td>
		  <td style="width:250px;text-align:left"><?=$textarea['description']?></td>
		  <td style="width:20px;text-align:center"><a class="edit" href="<?=BASEPATH?>/texyla/edit/<?=$textarea['id']?>?destination=texyla/admin"><img src="<?=BASEPATH?>/images/icon-edit.jpg" alt="<?=EDIT?>" title="<?=EDIT?>" /></a></td>
		  <td style="width:20px;text-align:center"><a class="delete" href="<?=BASEPATH?>/texyla/delete/<?=$textarea['id']?>"><img src="<?=BASEPATH?>/images/icon-delete.jpg" alt="<?=DELETE?>" title="<?=DELETE?>" /></a></td>
		</tr>
	  <?endforeach;?>
	  </tbody>
	</table>
	<?else:?>
	<p><?=TEXYLA_ADMIN_ADD_TEXTAREAS?></p>
	<?endif;?>
  </div>
</div>
{/content}