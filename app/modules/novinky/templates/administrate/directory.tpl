{* smarty *}
{block name="content"}
<div class="content-inner single">
    <div class="main-upper">
        <h2 class="title">{$title}</h2>
    </div>
    <div class="main-lower">
    <div class="news-adm-links">
        {if Application::$edit}
        <a href="{Application::link("novinky/administrate/settings")}">[ Nastavenia ]</a>&nbsp;&nbsp;
        {/if}
        {if Application::$view}
        <a href="{Application::link("novinky/administrate")}">[ Novinky ]</a>&nbsp;&nbsp;
        {/if}
        </div>
    </div>
</div>
{/block}