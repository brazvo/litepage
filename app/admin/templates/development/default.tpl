{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h1 class="title">{$title}</h1>
  </div>
  <div class="main-lower">
    {$flushForm}
    <br/><br/>
    {$content}
  </div>
</div>
{/block}