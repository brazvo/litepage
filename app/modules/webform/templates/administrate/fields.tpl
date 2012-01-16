{* smarty *}
{block name="content"}
<div class="content-inner single webform">
    <div class="main-upper">
        <h1 class="title">{$title}</h1>
    </div>
    <div class="main-lower">
        {$navigation}
        {if $table}
        <form id="priority-frm" method="post" action="{$smarty.const.BASEPATH}/webform/administrate/order-fields">
            <input type="hidden" name="contid" value="{$id}" />
            <table cellspacing="0" border="0" class="admin-table table-fields">
              <thead>
              <tr>
                    <td>{$smarty.const.NAME}</td>
                    <td>{$smarty.const.TYPE}</td>
                    <td>{$smarty.const.MACHINE_NAME}</td>
                    <td>{$smarty.const.PRIORITY}</td>
                    <td colspan="2" style="text-align:center">{$smarty.const.ACTIONS}</td>
              </tr>
              <thead>
              <tbody>
              {foreach from=$table item=row}
              <tr>
                <td>{$row['label']}</td>
                    <td>{$row['field_type']}</td>
                    <td>{$row['frm_name']}</td>
                    <td>
                      <input type="hidden" name="ids[]" value="{$row['id']}" />
                      <input name="priority_{$row['id']}" type="text" size="2" value="{$row['priority']}" />
                    </td>
                    <td style="width:30px;text-align:center"><a class="edit" href="{Application::link("webform/administrate/editfield/"|cat:$row.id)}"><img src="{$smarty.const.BASEPATH}/images/icon-edit.jpg" alt="{$smarty.const.SETTINGS}" title="{$smarty.const.SETTINGS}" /></a></td>
                    <td style="width:30px;text-align:center"><a class="delete" href="{Application::link("webform/administrate/deletefield/"|cat:$row.id)}"><img src="{$smarty.const.BASEPATH}/images/icon-delete.jpg" alt="{$smarty.const.DELETE}" title="{$smarty.const.DELETE}" /></a></td>
              </tr>
              {/foreach}
              <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><input type="submit" value="{$smarty.const.MAKE_ORDER}" /></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
              </tr>
          </tbody>
        </table>
        </form>
        {/if}
        <h3>{$smarty.const.WF_FRM_ADD_NEW_FIELD}</h3>
        {$form}
    </div>
</div>
{/block}