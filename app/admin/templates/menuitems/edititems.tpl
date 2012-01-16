{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h2 class="title">{$title}</h2>
	{if Application::$add}
	<a class="add-link" href="{Application::link("admin/menuitems/add/"|cat:$menu_id)}">[ Pridať položku ]</a>
	{/if}
  </div>
  <div class="main-lower">
    {$form}
  </div>
</div>
{/block}