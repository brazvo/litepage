{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h2 class="title">{$title}</h2>
  </div>
  <div class="main-lower">
       {foreach from=$types item=type}
		{if $type.perm.add}
		<p>[ <a href="{Application::link('admin/content/add/')}{$type.machine_name}">{$type.name}</a> ]<br/>
		- {$type.description}</p>
		{else}
		<!--p style="color:#999">[ {$type.name} ] (Nemáte povolenie)<br/>
		- {$type.description}</p-->
		{/if}
	{/foreach}
	<div class="backlink" onclick="self.history.back();"><< Späť</div>
  </div>
</div>
{/block}