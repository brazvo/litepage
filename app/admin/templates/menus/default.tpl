{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h2 class="title">{$title}</h2>
  </div>
  <div class="main-lower">
    {if Application::$add}
    <a class="add-link" href="{Application::link("admin/menus/add")}">[ {$smarty.const.MENUS_ADMIN_ADD_LINK} ]</a>
    {/if}
	<div class="menus-administration">
	<table class="admin-table admin-content" cellspacing="0" cellpadding="0" border="0" style="width:100%">
	  <thead>
	    <tr>
		  <td>{$smarty.const.MENU}</td>
		  <td>{$smarty.const.MACHINE_NAME}</td>
		  <td style="text-align:center;width:30px">{$smarty.const.LANGUAGE}</td>
		  <td colspan="3" style="text-align:center">{$smarty.const.ACTIONS}</td>
		</tr>
	  </thead>
	 {foreach from=$menus item=menu}
	  <tr>
	    <td>{$menu.name}</td>
		<td>{$menu.machine_name}</td>
		<td style="text-align:center;width:30px">{$menu.lang}</td>
		{if Application::$edit}
		<td style="width:20px;text-align:center"><a class="edit" href="{Application::link("admin/menus/edit/"|cat:$menu.id)}"><img src="{$smarty.const.BASEPATH}/images/icon-edit.jpg" alt="{$smarty.const.EDIT}" title="{$smarty.const.EDIT}" /></a></td>
	        {else}
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="{$smarty.const.BASEPATH}/images/icon-edit-gr.jpg" alt="{$smarty.const.EDIT}" title="{$smarty.const.EDIT}" /></span></td>
		{/if}
		{if $menu.system}
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="{$smarty.const.BASEPATH}/images/icon-delete-gr.jpg" alt="{$smarty.const.DELETE}" title="{$smarty.const.DELETE}" /></span></td>
		{else}
		  {if Application::$delete}
		  <td style="width:20px;text-align:center"><a class="delete" href="{Application::link("admin/menus/delete/"|cat:$menu.id)}"><img src="{$smarty.const.BASEPATH}/images/icon-delete.jpg" alt="{$smarty.const.DELETE}" title="{$smarty.const.DELETE}" /></a></td>
		  {else}
		  <td style="width:20px;text-align:center"><span style="color:silver"><img src="{$smarty.const.BASEPATH}/images/icon-delete-gr.jpg" alt="{$smarty.const.DELETE}" title="{$smarty.const.DELETE}" /></span></td>
		  {/if}
		{/if}
		{if $items_perm}
		<td style="width:20px;text-align:center"><a class="edit" href="{Application::link("admin/menuitems/edititems/"|cat:$menu.id)}"><img src="{$smarty.const.BASEPATH}/images/icon-menu.jpg" alt="<{$smarty.const.ITEMS}" title="{$smarty.const.ITEMS}" /></a></td>
		{else}
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="{$smarty.const.BASEPATH}/images/icon-menu-gr.jpg" alt="<{$smarty.const.ITEMS}" title="<{$smarty.const.ITEMS}" /></span></td>
		{/if}
             </tr>
	{/foreach}
	</table>
	</div>
  </div>
</div>
{/block}