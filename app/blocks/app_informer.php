<?php
$vystup['Selektory'] = Environment::getSelectors();
$vystup['Jazyk'] = Environment::getLanguage();
$vystup['SA_MODS'] = Environment::get( 'standaloneModules' );
$vystup['APP_MODS'] =         Environment::get( 'applicationModules' );
$vystup['CE_MODS'] =        Environment::get( 'contExModules' );
$vystup['ME_MODS'] =         Environment::get( 'modExModules' );

$sel = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Selektory:');
    $sel->setCont($title);
    foreach($vystup['Selektory'] as $key => $val) {
        $sel->add($key.'<br/>');
    }
$pathPref = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Path prefix:');
    $pathPref->setCont($title);
    $pathPref->add( Environment::getPathPrefix() );

$mod = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Aktivovaný modul:');
    $mod->setCont($title);
    $mod->add( Environment::getModule() );
    
$lan = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Jazyk:');
    $lan->setCont($title);
    $lan->add( $vystup['Jazyk']['name'].' / '.$vystup['Jazyk']['code'] );
    
$cont = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Aktívny kontroler:');
    $cont->setCont($title);
    $cont->add( Environment::getController() );
    
$act = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Spustená akcia:');
    $act->setCont($title);
    $act->add( Environment::getAction() );
    
$iId = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Volané ID:');
    $iId->setCont($title);
    $iId->add( Environment::getId() );
    
$sam = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Aktívne samostané moduly:');
    $sam->setCont($title);
    foreach($vystup['SA_MODS'] as $val) {
        $sam->add($val.'<br/>');
    }

$apm = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Aktívne aplikačné moduly:');
    $apm->setCont($title);
    foreach($vystup['APP_MODS'] as $val) {
        $apm->add($val.'<br/>');
    }

$cem = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Moduly rozširujúce obsah:');
    $cem->setCont($title);
    foreach($vystup['CE_MODS'] as $val) {
        $cem->add($val.'<br/>');
    }
    
$mem = Html::elem('div')->setClass('item');
    $title = Html::elem('h3')->setCont('Moduly rozširujúce moduly:');
    $mem->setCont($title);
    foreach($vystup['ME_MODS'] as $val) {
        $mem->add($val.'<br/>');
    }
$cls = html::elem('div')->setCont('x')->setClass('close');
    
echo $sel . $pathPref . $mod . $lan . $cont . $act . $iId . $sam . $apm . $cem . $mem . $cls;
?>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('#app_informer .close').click(
        function(){
            $('#app_informer').fadeOut(400);
        }
    );
});
/* ]]> */
</script>