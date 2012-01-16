{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h2 class="title">{$title}</h2>
  </div>
  <div class="main-lower">
        {$content}
  </div>
  <div class="backlink" onclick="self.history.back();"><< Späť</div>
</div>
{/block}