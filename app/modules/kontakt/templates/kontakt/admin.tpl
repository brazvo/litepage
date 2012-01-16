{title}<?=$title?>{/title}

{content}
<div class="content-inner single contact admin">
  <div class="main-upper">
    <h2 class="title"><?=$title?></h2>
  </div>
  <div class="main-lower">
    <div class="cf-adm-links">
        <?if(Application::$edit):?>
	<a href="<?=BASEPATH?>/kontakt/settings">[ <?=KONTAKT_TPL_MESSAGES_SETTINGS_LNK?> ]</a>&nbsp;
        <?endif;?>
	<a href="<?=BASEPATH?>/kontakt">[ <?=KONTAKT_TPL_MESSAGES_HISTORY_LNK?> ]</a>&nbsp;
	<?if(Application::$add):?>
	<a href="<?=BASEPATH?>/kontakt/add">[ <?=KONTAKT_TPL_ADD_FORM_LNK?> ]</a>
	<?endif;?>
	</div>
	<?if($content):?>
	<table class="admin-table" cellspacing="0" width="100%">
	  <thead>
	    <tr>
		  <td><?=NAME?></td>
		  <td colspan="2"><?=ACTIONS?></td>
		</tr>
	  </thead>
	  <tbody>
	  <?foreach($content as $row):?>
	  <tr>
	    <?if(trim($row['path_alias']) == ''):?>
	    <td><a href="<?=BASEPATH?>/kontakt/show/<?=$row['id']?>"><?=$row['title']?></a></td>
		<?else:?>
		<td><a href="<?=BASEPATH?>/<?=$row['path_alias']?>"><?=$row['title']?></a></td>
		<?endif;?>
		<?if(Application::$edit):?>
		<td style="width:20px;text-align:center"><a class="edit" href="<?=BASEPATH?>/kontakt/edit/<?=$row['id']?>?destination=kontakt/admin"><img src="<?=BASEPATH?>/images/icon-edit.jpg" alt="<?=EDIT?>" title="<?=EDIT?>" /></a></td>
		<?else:?>
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="<?=BASEPATH?>/images/icon-edit-gr.jpg" alt="<?=EDIT?>" title="<?=EDIT?>" /></span></td>
		<?endif;?>
		<?if(Application::$delete):?>
		<td style="width:20px;text-align:center"><a class="delete" href="<?=BASEPATH?>/kontakt/delete/<?=$row['id']?>"><img src="<?=BASEPATH?>/images/icon-delete.jpg" alt="<?=DELETE?>" title="<?=DELETE?>" /></a></td>
		<?else:?>
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="<?=BASEPATH?>/images/icon-delete-gr.jpg" alt="<?=DELETE?>" title="<?=DELETE?>" /></span></td>
		<?endif;?>
	  </tr>
	  <?endforeach;?>
	  </tbody>
	</table>
	<?else:?>
	  <?=KONTAKT_TPL_NO_FORMS_YET?>
	<?endif;?>
  </div>
</div>
{/content}