{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h2 class="title">{$title}</h2>
	<p><a class="add-link" href="{Application::link("admin/content-types/fields/"|cat:Application::$id)}">[ Pridať alebo upraviť polia ]</a></p>
  </div>
  <div class="main-lower">
        {$editform}
  </div>
</div>
{/block}