{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h1 class="title">{$title}</h1>
  </div>
  <div class="main-lower">
    {$filter}
	{if $nocontent}
	  <p>{$nocontent}</p>
	{else}
    <table class="admin-table admin-contents" style="width:100%" cellspacing="0">
	  <thead>
	  <tr>
                <td>{$smarty.const.LIST_CONT_TITLE}</td>
		<td>{$smarty.const.LIST_CONT_TYPE}</td>
		<td>{$smarty.const.LIST_LAST_UPD}</td>
		<td>{$smarty.const.LIST_LANG_ID}</td>
		<td>{$smarty.const.LIST_CREATED_BY}</td>
		<td colspan="2" style="text-align:center">{$smarty.const.ACTIONS}</td>
	  </tr>
	  </thead>
	  <tbody>
	  {foreach from=$content item=contItem}
              {assign var="path" value=PathAlias::getAlias('content/show/'|cat:$contItem.id)}
              <tr>
                <td><a href="{Application::link($path)}">{$contItem.content_title}</a></td>
                    <td>{$contItem.content_type_name}</td>
                    <td style="width:100px;text-align:center">{slovdate($contItem.last_update)}</td>
                    <td>{$contItem.lang}&nbsp;</td>
                    <td>{$contItem.user}&nbsp;</td>
                    {if $contItem.perm.edit}
                    <td style="width:20px;text-align:center"><a class="edit" href="{Application::link("admin/content/edit/")}{$contItem.id}?destination=admin/content/list"><img src="{Application::imgSrc("icon-edit.jpg")}" alt="Úprava" title="Úprava" /></a></td>
                    {else}
                    <td style="width:20px;text-align:center"><span style="color:silver"><img src="{Application::imgSrc("icon-edit-gr.jpg")}" alt="Úprava" title="Úprava" /></span></td>
                    {/if}
                    {if $contItem.perm.delete}
                    <td style="width:20px;text-align:center"><a class="delete" href="{Application::link("admin/content/delete/")}{$contItem.id}"><img src="{Application::imgSrc("icon-delete.jpg")}" alt="Odstrániť" title="Odstrániť" /></a></td>
                    {else}
                    <td style="width:20px;text-align:center"><span style="color:silver"><img src="{Application::imgSrc("icon-delete-gr.jpg")}" alt="Odstrániť" title="Odstrániť" /></span></td>
                    {/if}
              </tr>
	  {/foreach}
	  </tbody>
	</table>
            {$paginator}
	{/if}
  </div>
</div>
{/block}