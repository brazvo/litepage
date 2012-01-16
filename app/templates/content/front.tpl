{* extends file="@main.tpl" *}

{block name="content"}
<div class="content-inner front">
    <div class="main-upper">
        <h1 class="title">{$title}</h1>
	{if Application::$edit}
            {$edit}
        {/if}
    </div>
    <div class="main-lower">
        {$content}
        {Block::get('hotnews_block')}
    </div>
</div>
{/block}