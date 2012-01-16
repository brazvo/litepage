{title}<?=$title?>{/title}

{content}
<div class="content-inner webform" id="webform-<?=Application::$id?>">
  <div class="main-upper">
    <?if(Application::$edit):?>
	<a class="wf-edit-links" href="<?=BASEPATH?>/webform/edit/<?=Application::$id?>">[ <?=EDIT?> ]</a>&nbsp;&nbsp;
	<a class="wf-edit-links" href="<?=BASEPATH?>/webform/fields/<?=Application::$id?>">[ <?=FIELDS?> ]</a>&nbsp;&nbsp;
	<?endif;?>
	<?if(Application::$delete):?>
	<a class="wf-edit-links delete" href="<?=BASEPATH?>/webform/delete/<?=Application::$id?>">[ <?=DELETE?> ]</a>&nbsp;&nbsp;
	<?endif;?>
    <h2 class="title"><?=$title?></h2>
  </div>
  <div class="main-lower">
	<p><?=$content?></p>
  </div>
</div>
<?if($hide):?>
<script type="text/javascript">
/* <![CDATA[ */
// content of your Javascript goes here
$('div.wf-show-lnk').css({'cursor':'pointer'});
$('div.wf-form-container').css({'display':'none'});
$('div.wf-show-lnk').click(function(){
  if($('div.wf-form-container').is(':hidden')){
    $('div.wf-form-container').slideDown();
  }
  else{
    $('div.wf-form-container').slideUp();
  }
});
/* ]]> */
</script>
<?endif;?>
{/content}