{* SMARTY *}
{block name="content"}
<div class="content-inner single contact admin">
  <div class="main-upper">
    <h1 class="title">{$title}</h1>
  </div>
  <div class="main-lower">
    <div class="cf-adm-links">
        {if Application::$edit}
	<a href="{Application::link("contactform/administrate/settings")}">[ {$smarty.const.KONTAKT_TPL_MESSAGES_SETTINGS_LNK} ]</a>&nbsp;
	<a href="{Application::link("contactform/administrate/default")}">[ {$smarty.const.KONTAKT_TPL_MESSAGES_HISTORY_LNK} ]</a>&nbsp;
	{/if}
	{if Application::$add}
	<a href="{Application::link("contactform/administrate/add")}">[ {$smarty.const.KONTAKT_TPL_ADD_FORM_LNK} ]</a>
	{/if}
	</div>
       {$content}
  </div>
</div>
{/block}