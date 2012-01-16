{title}<?=$title?>{/title}

{content}
<div class="content-inner single webform">
  <div class="main-upper">
    <h2 class="title"><?=$title?></h2>
  </div>
  <div class="main-lower">
	<?if(Application::$view):?>
	  <?if($messages):?>
		<?=$paginator?>
		<div class="wf-messages">
	      <?$odd = true?>
	      <?foreach($messages as $message):?>
		  <?if($odd):
		    $cls_odd = ' odd';
		    $odd=false;
		    else:
		    $cls_odd = '';
		    $odd=true;
		    endif;?>
		  <div class="wf-message-cont<?=$cls_odd?>">
		    <?if(Application::$delete):?>
		    <a class="wf-edit-links delete" href="<?=BASEPATH?>/webform/deleteMessage/<?=$message['id']?>">[ <?=DELETE?> ]</a>&nbsp;&nbsp;
		    <?endif;?>
		    <div><span class="wf-sent"><?=WF_RENDER_DEFAULT_SEND_FROM?><b><?=$frmnames[$message['webform_id']]?></b></span></div>
			<div><span class="wf-date"><?=slovdate($message['datetime'])?></span></div>
			<?if($message['html_content']):?>
		    <div class="wf-content"><?=$message['html_content']?></div>
			<?endif;?>
		  </div>
		  <?endforeach;?>
		</div>
		<hr>
	  <?else:?>
	    <div class="wf-messages">Databáza správ zatiaľ neobsahuje žiadne správy.</div>
	  <?endif;?>
	  <?=$paginator?>
	<?else:?>
	<p>Nemáte právo na prezeranie tohto obsahu.</p>
	<?endif;?>
  </div>
</div>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
  $('div.wf-addfrm').css({'display':'none'});
  $('p.wf-add-link').click(function(){
    if($('div.wf-addfrm').is(':hidden')){
	  $('div.wf-addfrm').slideDown();
	}
	else{
	  $('div.wf-addfrm').slideUp();
	}
  });
});
/* ]]> */
</script>
{/content}