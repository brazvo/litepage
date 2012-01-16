{title}<?=$title?>{/title}

{content}
<div class="content-inner single webform">
  <div class="main-upper">
    <h2 class="title"><?=$title?></h2>
  </div>
  <div class="main-lower">
    <div class="cf-adm-links">
    <?if(Application::$edit):?>
	<a href="<?=BASEPATH?>/webform/settings">[ <?=WF_ADMIN_TPL_MESSAGES_SETTINGS?> ]</a>&nbsp;
	<a href="<?=BASEPATH?>/webform">[ <?=WF_ADMIN_TPL_MESSAGES_HISTORY?> ]</a>&nbsp;
	<?endif;?>
	<?if(Application::$add):?>
	<a href="<?=BASEPATH?>/webform/add">[ <?=WF_ADMIN_TPL_ADD_FORM?> ]</a>
	<?endif;?>
	</div>
	<?if($content):?>
	<table class="admin-table" cellspacing="0" width="100%">
	  <thead>
	    <tr>
		  <td><?=NAME?></td>
		  <td colspan="3"><?=ACTIONS?></td>
		</tr>
	  </thead>
	  <tbody>
	  <?foreach($content as $row):?>
	  <tr>
	    <?if(trim($row['path_alias']) == ''):?>
	    <td><a href="<?=BASEPATH?>/webform/show/<?=$row['id']?>"><?=$row['title']?></a></td>
		<?else:?>
		<td><a href="<?=BASEPATH?>/<?=$row['path_alias']?>"><?=$row['title']?></a></td>
		<?endif;?>
		<?if(Application::$edit):?>
		<td style="width:20px;text-align:center"><a class="edit" href="<?=BASEPATH?>/webform/fields/<?=$row['id']?>"><?=FIELDS?></a></td>
		<td style="width:20px;text-align:center"><a class="edit" href="<?=BASEPATH?>/webform/edit/<?=$row['id']?>?destination=webform/admin"><img src="<?=BASEPATH?>/images/icon-edit.jpg" alt="<?=EDIT?>" title="<?=EDIT?>" /></a></td>
		<?else:?>
		<td style="width:20px;text-align:center"><span style="color:silver"><?=FIELDS?></span></td>
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="<?=BASEPATH?>/images/icon-edit-gr.jpg" alt="<?=EDIT?>" title="<?=EDIT?>" /></span></td>
		<?endif;?>
		<?if(Application::$delete):?>
		<td style="width:20px;text-align:center"><a class="delete" href="<?=BASEPATH?>/webform/delete/<?=$row['id']?>"><img src="<?=BASEPATH?>/images/icon-delete.jpg" alt="<?=DELETE?>" title="<?=DELETE?>" /></a></td>
		<?else:?>
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="<?=BASEPATH?>/images/icon-delete-gr.jpg" alt="<?=DELETE?>" title="<?=DELETE?>" /></span></td>
		<?endif;?>
	  </tr>
	  <?endforeach;?>
	  </tbody>
	</table>
	<?else:?>
	<?=WF_ADMIN_TPL_NO_FORMS?>
	<?endif;?>
  </div>
</div>
{/content}