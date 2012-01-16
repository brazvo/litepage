{* smarty *}
{block name="content"}
<div class="content-inner single novinky">
    <div class="main-upper">
        <h1 class="title">{$title}</h1>
    </div>
    <div class="main-lower">
        {if Application::$add}
        <a class="news-add-link" href="{Application::link('novinky/administrate/add')}">[ Pridať správu ]</a>
        {/if}
        {if Application::$view}
            {if $messages}
                {$paginator}
                <div class="news-messages">
                {foreach from=$messages item=message}
                    <div class="news-message-cont {cycle values="odd,even"}">
                        <div class="news-title"><h3 class="title">{$message['title']}</h3></div>
                        {if Application::$edit}
                        <a class="news-edit-links" href="{Application::link('novinky/administrate/edit/'|cat:$message['id'])}">[ Upraviť ]</a>&nbsp;&nbsp;
                        {/if}
                        {if Application::$delete}
                        <a class="news-edit-links delete" href="{Application::link('novinky/administrate/delete/'|cat:$message['id'])}">[ Vymazať ]</a>&nbsp;&nbsp;
                        {/if}
                        <div><span class="news-date">{slovdate($message['datetime'])}</span></div>
                        <div class="news-message">{$message['message']}</div>
                    </div>
                {/foreach}
                </div>
                <hr>
            {else}
            <div class="news-messages">Zatiaľ nie su žiadne novinky.</div>
            {/if}
            {$paginator}
        {else}
        <p>Nemáte právo na prezeranie tohto obsahu.</p>
        {/if}
    </div>
</div>
{/block}