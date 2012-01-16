{title}<?=$title?>{/title}

{content}
<div class="content-inner single webform">
  <div class="main-upper">
    <h2 class="title"><?=$title?></h2>
  </div>
  <div class="main-lower">
  <?if($table):?>
    <form id="priority-frm" method="post" action="<?=BASEPATH?>/webform/orderFields">
	<input type="hidden" name="contid" value="<?=$id?>" />
    <table cellspacing="0" border="0" class="admin-table table-fields">
	  <thead>
	  <tr>
	    <td><?=NAME?></td>
		<td><?=TYPE?></td>
		<td><?=MACHINE_NAME?></td>
		<td><?=PRIORITY?></td>
		<td colspan="2" style="text-align:center"><?=ACTIONS?></td>
	  </tr>
	  <thead>
	  <tbody>
      <?foreach($table as $row):?>
	  <tr>
	    <td><?=$row['label']?></td>
		<td><?=$row['field_type']?></td>
		<td><?=$row['frm_name']?></td>
		<td>
		  <input type="hidden" name="ids[]" value="<?=$row['id']?>" />
		  <input name="priority_<?=$row['id']?>" type="text" size="2" value="<?=$row['priority']?>" />
		</td>
		<td style="width:30px;text-align:center"><a class="edit" href="<?=BASEPATH?>/webform/editfield/<?=$row['id']?>"><img src="<?=BASEPATH?>/images/icon-edit.jpg" alt="<?=SETTINGS?>" title="<?=SETTINGS?>" /></a></td>
		<td style="width:30px;text-align:center"><a class="delete" href="<?=BASEPATH?>/webform/deletefield/<?=$row['id']?>"><img src="<?=BASEPATH?>/images/icon-delete.jpg" alt="<?=DELETE?>" title="<?=DELETE?>" /></a></td>
	  </tr>
	  <?endforeach;?>
	  <tr>
	    <td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input type="submit" value="<?=MAKE_ORDER?>" /></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
      </tbody>
    </table>
	</form>
  <?endif;?>
    <h3><?=WF_FRM_ADD_NEW_FIELD?></h3>
    <?=$form?>
  </div>
</div>
{/content}