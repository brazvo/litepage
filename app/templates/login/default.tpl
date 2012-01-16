{block name="content"}
<div class="content-inner single">
  <div class="main-upper">
    <h1 class="title">{$title}</h1>
  </div>
  <div class="main-lower">
        {$content}
	{if $formError}{$formError}{/if}
        {$loginForm}
  </div>
</div>
{/block}