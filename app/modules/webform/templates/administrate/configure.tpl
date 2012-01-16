{* smarty *}
{block name="content"}
<div class="content-inner single webform">
  <div class="main-upper">
    <h2 class="title">{$title}</h2>
  </div>
  <div class="main-lower">
      
      {$navigation}
      
	{if $content}
	<table class="admin-table" cellspacing="0" width="100%">
	  <thead>
	    <tr>
		  <td>{$smarty.const.NAME}</td>
		  <td colspan="3">{$smarty.const.ACTIONS}</td>
		</tr>
	  </thead>
	  <tbody>
	  {foreach from=$content item=row}
	  <tr>
	        <td><a href="{PathAlias::getAlias("webform/frontend/show/"|cat:$row.id)}">{$row['title']}</a></td>
		{if Application::$edit}
		<td style="width:20px;text-align:center"><a class="edit" href="{Application::link("webform/administrate/fields/"|cat:$row.id)}">{$smarty.const.FIELDS}</a></td>
		<td style="width:20px;text-align:center"><a class="edit" href="{Application::link("webform/administrate/edit/"|cat:$row.id|cat:"?destination=webform/administrate")}"><img src="{Application::imgSrc("icon-edit.jpg")}" alt="{$smarty.const.EDIT}" title="{$smarty.const.EDIT}" /></a></td>
		{else}
		<td style="width:20px;text-align:center"><span style="color:silver">{$smarty.const.FIELDS}</span></td>
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="{Application::imgSrc("icon-edit-gr.jpg")}" alt="{$smarty.const.EDIT}" title="{$smarty.const.EDIT}" /></span></td>
		{/if}
		{if Application::$delete}
		<td style="width:20px;text-align:center"><a class="delete" href="{Application::link("webform/administrate/delete/"|cat:$row.id)}"><img src="{Application::imgSrc("icon-delete.jpg")}" alt="{$smarty.const.DELETE}" title="{$smarty.const.DELETE}" /></a></td>
		{else}
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="{Application::imgSrc("icon-delete-gr.jpg")}" alt="{$smarty.const.DELETE}" title="{$smarty.const.DELETE}" /></span></td>
		{/if}
	  </tr>
	  {/foreach}
	  </tbody>
	</table>
	{else}
            {$smarty.const.WF_ADMIN_TPL_NO_FORMS}
	{/if}
  </div>
</div>
{/block}