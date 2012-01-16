{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h2 class="title">{$title}</h2>
	{if Application::$add}
	<p><a class="add-link" href="{Application::link("admin/content-types/add")}">[ Pridať typ obsahu ]</a></p>
	{/if}
  </div>
  <div class="main-lower">
        {$content}
  </div>
</div>
{/block}