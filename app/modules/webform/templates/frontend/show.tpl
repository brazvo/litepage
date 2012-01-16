{* smarty *}
{block name="content"}
<div class="content-inner webform" id="webform-{Application::$id}">
    <div class="main-upper">
    {if Application::$edit}
        <a class="wf-edit-links" href="{Application::link("webform/administrate/edit/")}{Application::$id}?destination={PathAlias::getAlias("webform/frontend/show/"|cat:Application::$id)}">[ {$smarty.const.EDIT} ]</a>&nbsp;&nbsp;
        <a class="wf-edit-links" href="{Application::link("webform/administrate/fields/")}{Application::$id}">[ {$smarty.const.FIELDS} ]</a>&nbsp;&nbsp;
    {/if}
    {if Application::$delete}
        <a class="wf-edit-links delete" href="{Application::link("webform/administrate/delete/")}{Application::$id}">[ {$smarty.const.DELETE} ]</a>&nbsp;&nbsp;
    {/if}
        <h1 class="title">{$title}</h1>
    </div>
    <div class="main-lower">
        {$content}
    </div>
</div>
{if $hide}
    {literal}
<script type="text/javascript">
/* <![CDATA[ */
// content of your Javascript goes here
$('div.wf-show-lnk').css({'cursor':'pointer'});
$('div.wf-form-container').css({'display':'none'});
$('div.wf-show-lnk').click(function(){
  if($('div.wf-form-container').is(':hidden')){
    $('div.wf-form-container').slideDown();
  }
  else{
    $('div.wf-form-container').slideUp();
  }
});
/* ]]> */
</script>
    {/literal}
{/if}
{/block}