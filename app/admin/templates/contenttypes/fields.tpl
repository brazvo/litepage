{* smarty *}
{block name="content"}
<div class="content-inner single admin">
    <div class="main-upper">
        <h2 class="title">{$title}</h2>
    </div>
    <div class="main-lower">
    {if $table}
    <form id="priority-frm" method="post" action="{Application::link("admin/content-types/order")}">
        <input type="hidden" name="contid" value="{$id}" />
        <table cellspacing="0" border="0" class="admin-table table-fields">
          <thead>
          <tr>
            <td>Názov</td>
                <td>Typ</td>
                <td>Strojové meno</td>
                <td>Priorita</td>
                <td colspan="2" style="text-align:center">Akcie</td>
          </tr>
          <thead>
          <tbody>
        {foreach from=$table item=row}
          <tr>
            <td>{$row.label}</td>
                <td>{$row.field_type}</td>
                <td>{$row.frm_name}</td>
                <td>
                  <input type="hidden" name="ids[]" value="{$row.id}" />
                  <input name="priority_{$row.id}" type="text" size="2" value="{$row.priority}" />
                </td>
                {if $row.editable}
                <td style="width:30px;text-align:center"><a class="edit" href="{Application::link("admin/content-types/editfield/")}{$row.id}"><img src="{Application::imgSrc("icon-edit.jpg")}" alt="Nastavenia" title="Nastavenia" /></a></td>
                {else}
                <td style="width:30px;text-align:center"><span style="color:silver;"><img src="{Application::imgSrc("icon-edit-gr.jpg")}" alt="Nastavenia" title="Nastavenia" /></span></td>
                {/if}
                {if $row.basic}
                <td style="width:30px;text-align:center"><span style="color:silver;"><img src="{Application::imgSrc("icon-delete-gr.jpg")}" alt="Výmaz" title="Výmaz" /></span></td>
                {else}
                <td style="width:30px;text-align:center"><a class="delete" href="{Application::link("admin/content-types/deletefield/")}{$row.id}"><img src="{Application::imgSrc("icon-delete.jpg")}" alt="Odstrániť" title="Odstrániť" /></a></td>
                {/if}
          </tr>
          {/foreach}
          <tr>
            <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><input type="submit" value="Zoradiť" /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
          </tr>
      </tbody>
    </table>
        </form>
    {/if}
    <h3>Pridať nové pole</h3>
    {$form}
    </div>
</div>
{/block}