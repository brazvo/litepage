{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h1 class="title">{$title}</h1>
  </div>
  <div class="main-lower">
        {$content}
	<div class="backlink" onclick="self.history.back();"><< Späť</div>
  </div>
</div>
{/block}