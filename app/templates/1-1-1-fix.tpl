<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$sLang}" xml:lang="{$sLang}">
<head>
<title>{$pagetitle}</title>
{if $sHtmlMeta}
    {$sHtmlMeta}
{/if}

<base href="{$baseHref}/" />

{if $favicon}
    <link rel="shortcut icon" type="image/x-icon" href="{$favicon}"  />
{/if}
{if $sHtmlCss}
    {$sHtmlCss}
{/if}
{if $sHtmlJs}
    {$sHtmlJs}
{/if}

{* header modules html *}
{if isset($headerModulesHtml)}{$headerModulesHtml}{/if}
{$sHtmlGA}
</head>

<body class="oneColFixCtrHdr lang-{$sLang} {$bodyclass}">
	<div id="body-inner">
		{block::adminBar()}
		<!-- #loader -->
		<div id="loader"><img src="{basePath()}images/loading.gif" alt="" /></div>
		<!--/#loader -->
		<div id="container">
		  <div id="container-inner">
			<div id="header">
			  <div id="header-inner">
				{block::logoBlock()}
				{block::headerTitleBlock()}
				{block::sloganBlock()}
				{block::navBar()}
			  </div>
			</div><!-- end #header -->
			<div id="mainContent">
			  <div id="mainContent-inner">
				{$flashErrors}
				{$flashMessages}
				{block name="content"}{/block}
			  </div>
			</div><!-- end #mainContent -->
			<div id="footer">
			  <div id="footer-inner">
				{include file = $smarty.const.INC_DIR|cat:"/footer.inc"}
			  </div>
			</div><!-- end #footer -->
                        {if $smarty.const.SQL_BROWSER}
                            {block::get('sql_browser')}
                        {/if}
                        {if $smarty.const.APP_INFORMER}
                            {block::get('app_informer')}
                        {/if}
		  </div><!-- end #container-inner -->
		</div><!-- end #container -->
	</div><!-- end #body-inner -->
{* body modules html *}
{if isset($bodyModulesHtml)}{$bodyModulesHtml}{/if}
</body>
</html>
