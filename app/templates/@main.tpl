<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="sk" lang="sk" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$pagetitle}</title>
<?=$meta?>

<base href="<?=BASEPATH?>/" />

<!-- Scripts -->
<?=$js?>

<!-- CSS -->
<?=$css?>

<style>
/* override the root element to enable scrolling */
	.slide {
		position:relative;
		overflow:hidden;
		float:none;
		height:300px;
	}

	/* override single pane */
	.slide div {
		float:left;
		display:block;
		width:700px;
		font-size:14px;
	}

	/* our additional wrapper element for the items */
	.slide .items {
		width:20000em;
		position:absolute;
		clear:both;
		margin:0;
		padding:0;
	}

	.slide .less, .slide .less a {
		color:#999 !important;
		font-size:11px;
	}


</style>
{literal}
<script type="text/javascript">
jQuery(document).ready(function($){

	var baseHref = $("base").attr("href");
	
	<?if(Application::$defLanguage['code'] != Application::$language['code']):?>
	var lang = '<?=Application::$language['code']?>'+'/';
	// rewrite hrefs with lang code where is not request to file
	$(".content-inner a").each(function(){
		var oldlnk = $(this).attr("href");
		if(oldlnk.match(/\./) != '.'){
			var newlnk = oldlnk.replace(baseHref, baseHref+lang);
		    $(this).attr("href", newlnk);
		}
		
	});
	
	// rewrite form actions with lang code
	$("form").each(function(){
		var oldlnk = $(this).attr("action");
		var newlnk = oldlnk.replace(baseHref, baseHref+lang);
		$(this).attr("action", newlnk);
	});
	<?endif;?>
	
	$(function() {

		// select #flowplanes and make it scrollable. use circular and navigator plugins
		$(".slide").scrollable({ circular: true, mousewheel: true });
	});
	
});
</script>
{/literal}
<?php eval(Application::$headerAppModulesContent)?>
<?=$gAnalytics?>
</head>

<body class="thrColFixHdr lang-<?=Application::$language['code']?> <?=$cont->bodyclass?>">
	<div id="body-inner">
		<?=block::adminBar()?>
		<!-- #loader -->
		<div id="loader"><img src="<?=BASEPATH?>/images/loading.gif" alt="" /></div>
		<!--/#loader -->
		<div id="container">
		  <div id="container-inner">
			<div id="header">
			  <div id="header-inner">
				<?=block::logoBlock()?>
				<?=block::headerTitleBlock()?>
				<?=block::sloganBlock()?>
				<?=block::navBar()?>
			  </div>
			</div><!-- end #header -->
			<div id="sidebar1">
			  <div id="sidebar1-inner">
				<?=block::getMenu('primary_menu')?>
				<h3>Obsah tagu Sidebar1</h3>
				<p>Farba pozadia tohto tagu div sa bude zobrazovať iba v dĺžke obsahu. Ak chcete, aby sa dĺžka tohto tagu div upravovala podľa dĺžky kontajneru #mainContent, odkomentujte obslužnú rutinu v súbore general.js v adresáry js.</p>
			  </div>
			</div><!-- end #sidebar1 -->
			<div id="sidebar2">
			  <div id="sidebar2-inner">
				<?=block::getMenu('secondary_menu')?>
				<h3>Obsah tagu Sidebar1</h3>
				<p>Farba pozadia tohto tagu div sa bude zobrazovať iba v dĺžke obsahu. Ak chcete, aby sa dĺžka tohto tagu div upravovala podľa dĺžky kontajneru #mainContent, odkomentujte obslužnú rutinu v súbore general.js v adresáry js.</p>
			  </div>
			</div><!-- end #sidebar2 -->
			<div id="mainContent">
			  <div id="mainContent-inner">
				<?=Application::getErrors()?>
				<?=Application::getMessages()?>
				{block name="content"}{/block}
			  </div>
			</div><!-- end #mainContent -->
			<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
			<div id="footer">
			  <div id="footer-inner">
				<?include(INC_DIR.'/footer.inc');?>
			  </div>
			</div><!-- end #footer -->
		  </div><!-- end #container-inner -->
		</div><!-- end #container -->
	</div><!-- end #body-inner -->
<?php eval(Application::$bodyAppModulesContent)?>
</body>
</html>
