<?php
$form = new Form('login', 'login-form', BASEPATH.'/login/check');
	
$form->addText('user', 'Užívateľské meno', '', '',29);
  $form->addRule('user', Form::FILLED, 'Zadajte užívateľské meno');
$form->addPassword('password', 'Heslo', '', '',29);
  $form->addRule('password', Form::FILLED, 'Zadajte heslo');
$form->addSubmit('login', 'Prihlásiť sa');
	
$loginform = $form->render();
?>
<?if(FANCY_LOGIN_FORM):?>
<div style="display:none;">
<?endif;?>
<div id="login-form" class="block-inner">
  <div class="block-title"><h3 class="title">Prihlásenie</h3></div>
  <div class="block-login-form" style="font-size:8pt">
    <?=$loginform?>
  </div>
</div>
<?if(FANCY_LOGIN_FORM):?>
</div>
<?if(!Application::$logged['status']):?>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
  //$("a.login-form").attr('href', '#login-form');
  //$("a.login-form").click(function(){return false});
  $("a.login-form").fancybox();
  $("#formLogin-user").focus();
});
/* ]]> */
</script>
<?endif;?>
<?endif;?>