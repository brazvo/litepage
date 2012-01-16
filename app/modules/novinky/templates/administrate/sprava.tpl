{* smarty *}
{block name="content"}
<div class="content-inner single novinky">
    <div class="main-upper">
    <h1 class="title">{$title}</h1>
        {if Application::$edit}
            <a class="news-edit-links" href="{Application::link("novinky/administrate/edit/"|cat:$id}">[ Upraviť ]</a>&nbsp;&nbsp;
        {/if}
        {if Application::$delete}
            <a class="news-edit-links delete" href="{Application::link("novinky/administrate/delete/"|cat:$id}">[ Vymazať ]</a>&nbsp;&nbsp;
        {/if}
    </div>
    <div class="main-lower">
    <p><i>{$date}</i></p>
        <p>{$message}</p>
    </div>
</div>
{/block}