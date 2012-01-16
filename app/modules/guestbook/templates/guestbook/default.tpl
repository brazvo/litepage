{title}<?=$title?>{/title}

{content}
<div class="content-inner single guestbook">
  <div class="main-upper">
    <h2 class="title"><?=$title?></h2>
  </div>
  <div class="main-lower">
    <?if(Application::$add):?>
	<div class="gb-add-container">
	  <p class="gb-add-link"><a class="gb-add-form" href="#gb-addfrm">[ Pridať odkaz &#9660; ]</a></p>
	  <div style="display:none"><div id="gb-addfrm" class="gb-addfrm"><?=$form?></div></div>
	  <hr>
	</div>
	<?endif;?>
	
	<?if(Application::$view):?>
	  <?if($messages):?>
		<?=$paginator?>
		<div class="gb-messages">
	      <?$odd = true?>
	      <?foreach($messages as $message):?>
		  <?if($odd):
		    $cls_odd = ' odd';
		    $odd=false;
		    else:
		    $cls_odd = '';
		    $odd=true;
		    endif;?>
		  <div class="gb-message-cont<?=$cls_odd?>">
		    <?if(Application::$edit):?>
		    <a class="gb-edit-links" href="<?=BASEPATH?>/guestbook/edit/<?=$message['id']?>">[ Upraviť ]</a>&nbsp;&nbsp;
		    <?endif;?>
		    <?if(Application::$delete):?>
		    <a class="gb-edit-links delete" href="<?=BASEPATH?>/guestbook/delete/<?=$message['id']?>">[ Vymazať ]</a>&nbsp;&nbsp;
		    <?endif;?>
		    <div><span class="gb-date"><?=slovdate($message['datetime'])?></span> | <span class="gb-from"><?=$message['name']?></span></div>
		    <?if(SUBJECT_WANTED):?>
		    <div class="gb-subject"><span>Predmet: </span><span><?=$message['subject']?></span></div>
		    <?endif;?>
		    <div class="gb-message"><?=$message['message']?></div>
		  </div>
		  <?endforeach;?>
		</div>
		<hr>
	  <?else:?>
	    <div class="gb-messages">Kniha odkazov zatiaľ neobsahuje žiadne odkazy.</div>
	  <?endif;?>
	  <?=$paginator?>
	<?else:?>
	<p>Nemáte právo na prezeranie tohto obsahu.</p>
	<?endif;?>
  </div>
</div>
<script type="text/javascript">
/* <![CDATA[ */
/*jQuery(document).ready(function($){
  $('div.gb-addfrm').css({'display':'none'});
  $('p.gb-add-link').click(function(){
    if($('div.gb-addfrm').is(':hidden')){
	  $('div.gb-addfrm').slideDown();
	}
	else{
	  $('div.gb-addfrm').slideUp();
	}
  });
});*/
/* ]]> */
</script>
<script type="text/javascript">
/* <![CDATA[ */
// content of your Javascript goes here
jQuery(document).ready(function($){
  $("a.gb-add-form").click(function(){return false});
  $("a.gb-add-form").fancybox();
});
/* ]]> */
</script>
{/content}