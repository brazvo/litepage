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
	<a href="<?=BASEPATH?>/kontakt">[ <?=KONTAKT_TPL_MESSAGES_HISTORY_LNK?> ]</a>&nbsp;
	<?endif;?>
	<?if(Application::$add):?>
	<a href="<?=BASEPATH?>/kontakt/add">[ <?=KONTAKT_TPL_ADD_FORM_LNK?> ]</a>
	<?endif;?>
	</div>
	<?if(Application::$view):?>
	  <?if($messages):?>
		<?=$paginator?>
		<div class="cf-messages">
	      <?$odd = true?>
	      <?foreach($messages as $message):?>
		  <?if($odd):
		    $cls_odd = ' odd';
		    $odd=false;
		    else:
		    $cls_odd = '';
		    $odd=true;
		    endif;?>
		  <div class="cf-message-cont<?=$cls_odd?>">
		    <?if(Application::$delete):?>
		    <a class="cf-edit-links delete" href="<?=BASEPATH?>/kontakt/deleteMessage/<?=$message['id']?>">[ Vymazať ]</a>&nbsp;&nbsp;
		    <?endif;?>
		    <div><span class="cf-sent"><?=KONTAKT_TPL_SENT_FROM_FORM?> <b><?=$frmnames[$message['contact_frm_id']]?></b></span></div>
			<div><span class="cf-date"><?=slovdate($message['datetime'])?></span> | <span class="cf-from"><?=$message['name']?></span></div>
			<?if($message['company']):?>
		    <div class="cf-company"><span><?=KONTAKT_FRM_COMPANY_LABEL?>: </span><span><?=$message['company']?></span></div>
			<?endif;?>
			<?if($message['address']):?>
		    <div class="cf-address"><span><?=KONTAKT_FRM_ADDRESS_LABEL?>: </span><br/><span><?=preg_replace("/\r\n/","<br/>",$message['address'])?></span></div>
			<?endif;?>
			<?if($message['email']):?>
		    <div class="cf-email"><span>Email: </span><span><?=$message['email']?></span></div>
			<?endif;?>
			<?if($message['subject']):?>
		    <div class="cf-subject"><span><?=KONTAKT_FRM_SUBJECT_LABEL?>: </span><span><?=$message['subject']?></span></div>
			<?endif;?>
		    <div class="cf-message"><?=preg_replace("/\r\n/","<br/>",$message['message'])?></div>
		  </div>
		  <?endforeach;?>
		</div>
		<hr>
	  <?else:?>
	    <div class="cf-messages"><?=KONTAKT_TPL_DATABASE_EMPTY?></div>
	  <?endif;?>
	  <?=$paginator?>
	<?else:?>
	<p><?=KONTAKT_TPL_NO_PERMISION_FOR_CONTENT?></p>
	<?endif;?>
  </div>
</div>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
  $('div.cf-addfrm').css({'display':'none'});
  $('p.cf-add-link').click(function(){
    if($('div.cf-addfrm').is(':hidden')){
	  $('div.cf-addfrm').slideDown();
	}
	else{
	  $('div.cf-addfrm').slideUp();
	}
  });
});
/* ]]> */
</script>
{/content}