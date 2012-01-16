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
    <?=$content?>
  </div>
</div>
{/content}