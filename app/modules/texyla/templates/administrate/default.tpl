{block name="content"}
<div class="content-inner error">
  <div class="main-upper">
        <h1 class="title">{$title}</h1>
	<p><a href="{Application::link("texyla/administrate/add")}">[ {$smarty.const.TEXYLA_ADMIN_LNK_ADD_TEXTAREA} ]</a></p>
  </div>
  <div class="main-lower">
    {if $textareas}
    <table cellspacing="0" cellpadding="3" class="admin-table" style="width:100%">
	  <thead>
	    <tr>
		  <td>{$smarty.const.TEXYLA_ADMIN_CSS_NAME}</td>
		  <td>{$smarty.const.TEXYLA_ADMIN_DESC}</td>
		  <td colspan="2">{$smarty.const.ACTIONS}</td>
		</tr>
	  </thead>
	  <tbody>
	  {foreach from=$textareas item=textarea}
	    <tr>
		  <td>{$textarea['textarea']}</td>
		  <td style="width:250px;text-align:left">{$textarea['description']}</td>
		  <td style="width:20px;text-align:center"><a class="edit" href="{Application::link("texyla/administrate/edit/"|cat:$textarea['id']|cat:"?destination=texyla/administrate")}"><img src="{Application::imgSrc("icon-edit.jpg")}" alt="{$smarty.const.EDIT}" title="{$smarty.const.EDIT}" /></a></td>
		  <td style="width:20px;text-align:center"><a class="delete" href="{Application::link("texyla/administrate/edit/"|cat:$textarea['id'])}"><img src="{Application::imgSrc("icon-delete.jpg")}" alt="{$smarty.const.DELETE}" title="{$smarty.const.DELETE}" /></a></td>
		</tr>
	  {/foreach}
	  </tbody>
	</table>
	{else}
	<p>{$smarty.const.TEXYLA_ADMIN_ADD_TEXTAREAS}</p>
	{/if}
  </div>
</div>
{/block}