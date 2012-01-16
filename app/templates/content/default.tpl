{* Smarty *}
{extends file="@main.tpl"}

{block name="content"}
<div class="content-inner">
    <div class="content-body {$class} {$class}_{$cid}">
        <div class="main-upper">

            <h1 class="title">{$title}</h1>
            {if Application::$edit}
                {$edit}
            {/if}
        </div>
        <div class="main-lower">
        {$content}
        </div>
    </div>
</div>
{/block}