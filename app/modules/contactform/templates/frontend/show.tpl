{* SMARTY *}
{block name="content"}
<div class="content-inner single contact">
  <div class="main-upper">
    <div class="main-upper-inner">
      <h1 class="title">{$title}</h1>
      {if Application::$edit}
      <a class="cf-edit-links" href="{Application::link("contactform/administrate/edit/"|cat:Application::$id)}?destination={$destination}">[ {$smarty.const.EDIT} ]</a>&nbsp;&nbsp;
      {/if}
    </div>
  </div>
  {block::get('map')}
  <div class="main-lower">
    <div class="main-lower-inner">
	{$content}
    </div>
  </div>
</div>
{if $hide}
    {literal}
<script type="text/javascript">
/* <![CDATA[ */
// content of your Javascript goes here
$('div.cf-show-lnk').css({'cursor':'pointer'});
$('div.cf-form-container').css({'display':'none'});
$('div.cf-show-lnk').click(function(){
  if($('div.cf-form-container').is(':hidden')){
    $('div.cf-form-container').slideDown();
  }
  else{
    $('div.cf-form-container').slideUp();
  }
});
/* ]]> */
</script>
    {/literal}
{/if}
{/block}