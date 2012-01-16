{* smarty *}
{block name="content"}
<div class="content-inner single webform">
  <div class="main-upper">
    <h1 class="title">{$title}</h1>
  </div>
  <div class="main-lower">
      
      {$navigation}
      
	{if Application::$view}
	  {if $messages}
		{$paginator}
		<div class="wf-messages">
                  {foreach from=$messages item=message}
		  <div class="wf-message-cont {cycle values="odd,even"}">
		    {if Application::$delete}
		    <a class="wf-edit-links delete" href="{Application::link("webform/administrate/delete-message"|cat:$message['id'])}">[ {$smarty.const.DELETE} ]</a>&nbsp;&nbsp;
		    {/if}
		    <div><span class="wf-sent">{$smarty.const.WF_RENDER_DEFAULT_SEND_FROM}<b>{$frmnames[$message['webform_id']]}</b></span></div>
			<div><span class="wf-date">{slovdate($message['datetime'])}</span></div>
			{if $message['html_content']}
		    <div class="wf-content">{$message['html_content']}</div>
			{/if}
		  </div>
		  {/foreach}
		</div>
		<hr>
	  {else}
	    <div class="wf-messages">Databáza správ zatiaľ neobsahuje žiadne správy.</div>
	  {/if}
	  {$paginator}
	{else}
	<p>Nemáte právo na prezeranie tohto obsahu.</p>
	{/if}
  </div>
</div>
{literal}
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
  $('div.wf-addfrm').css({'display':'none'});
  $('p.wf-add-link').click(function(){
    if($('div.wf-addfrm').is(':hidden')){
	  $('div.wf-addfrm').slideDown();
	}
	else{
	  $('div.wf-addfrm').slideUp();
	}
  });
});
/* ]]> */
</script>
{/literal}
{/block}