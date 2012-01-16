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
        {/if}
	<a href="{Application::link("contactform/administrate/default")}">[ {$smarty.const.KONTAKT_TPL_MESSAGES_HISTORY_LNK} ]</a>&nbsp;
	{if Application::$add}
	<a href="{Application::link("contactform/administrate/add")}">[ {$smarty.const.KONTAKT_TPL_ADD_FORM_LNK} ]</a>
	{/if}
	</div>
	{if $content}
	<table class="admin-table" cellspacing="0" width="100%">
	  <thead>
	    <tr>
		  <td>{$smarty.const.NAME}</td>
		  <td colspan="2">{$smarty.const.ACTIONS}</td>
		</tr>
	  </thead>
	  <tbody>
	  {foreach $content as $row}
	  <tr>

                <td><a href="{PathAlias::getAlias("contactform/frontend/show/"|cat:$row.id)}">{$row['title']}</a></td>
		{if Application::$edit}
		<td style="width:20px;text-align:center"><a class="edit" href="{Application::link("contactform/administrate/edit/"|cat:$row.id)}?destination=contactform"><img src="{Application::imgSrc("icon-edit.jpg")}" alt="{$smarty.const.EDIT}" title="{$smarty.const.EDIT}" /></a></td>
		{else}
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="{Application::imgSrc("icon-edit-gr.jpg")}" alt="{$smarty.const.EDIT}" title="{$smarty.const.EDIT}" /></span></td>
		{/if}
		{if Application::$delete}
		<td style="width:20px;text-align:center"><a class="delete" href="{Application::link("contactform/administrate/delete/"|cat:$row.id)}"><img src="{Application::imgSrc("icon-delete.jpg")}" alt="{$smarty.const.DELETE}" title="{$smarty.const.DELETE}" /></a></td>
		{else}
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="{Application::imgSrc("icon-delete-gr.jpg")}" alt="{$smarty.const.DELETE}" title="{$smarty.const.DELETE}" /></span></td>
		{/if}
	  </tr>
	  {/foreach}
	  </tbody>
	</table>
	{else}
	  {$smarty.const.KONTAKT_TPL_NO_FORMS_YET}
	{/if}
  </div>
</div>
{/block}