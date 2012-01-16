{* SMARTY *}
{block name="content"}
<div class="content-inner single contact admin">
  <div class="main-upper">
    <h1 class="title">{$title}</h1>
  </div>
  <div class="main-lower">
    <div class="cf-adm-links">
    {if Application::$edit}
	<a href="{Application::link("contactform/administrate/settings")}">[ {$smarty.const.KONTAKT_TPL_MESSAGES_SETTINGS_LNK} ]</a>&nbsp;
	<a href="{Application::link("contactform/administrate/default")}">[ {$smarty.const.KONTAKT_TPL_MESSAGES_HISTORY_LNK} ]</a>&nbsp;
	{/if}
	{if Application::$add}
	<a href="{Application::link("contactform/administrate/add")}">[ {$smarty.const.KONTAKT_TPL_ADD_FORM_LNK} ]</a>
	{/if}
	</div>
	{if Application::$view}
	  {if $messages}
		{$paginator}
		<div class="cf-messages">
	        {foreach from=$messages item=message}
		  <div class="cf-message-cont {cycle values="odd,even"}">
		    {if Application::$delete}
		    <a class="cf-edit-links delete" href="{Application::link("contactform/administrate/delete-message"|cat:$message.id)}">[ {$smarty.const.DELETE} ]</a>&nbsp;&nbsp;
		    {/if}
		    <div><span class="cf-sent">{$smarty.const.KONTAKT_TPL_SENT_FROM_FORM} <b>{$frmnames[$message['contact_frm_id']]}</b></span></div>
			<div><span class="cf-date">{slovdate($message['datetime'])}</span> | <span class="cf-from">{$message['name']}</span></div>
			{if $message['company']}
		    <div class="cf-company"><span>{$smarty.const.KONTAKT_FRM_COMPANY_LABEL}: </span><span>{$message['company']}</span></div>
			{/if}
			{if $message['address']}
		    <div class="cf-address"><span>{$smarty.const.KONTAKT_FRM_ADDRESS_LABEL}: </span><br/><span>{preg_replace("/\r\n/","<br/>",$message['address'])}</span></div>
			{/if}
			{if $message['email']}
		    <div class="cf-email"><span>Email: </span><span>{$message['email']}</span></div>
			{/if}
			{if $message['subject']}
		    <div class="cf-subject"><span>{$smarty.const.KONTAKT_FRM_SUBJECT_LABEL}: </span><span>{$message['subject']}</span></div>
			{/if}
		    <div class="cf-message">{preg_replace("/\r\n/","<br/>",$message['message'])}</div>
		  </div>
		  {/foreach}
		</div>
		<hr>
	  {else}
	    <div class="cf-messages">{$smarty.const.KONTAKT_TPL_DATABASE_EMPTY}</div>
	  {/if}
	  {$paginator}
	{else}
	<p>{$smarty.const.KONTAKT_TPL_NO_PERMISION_FOR_CONTENT}</p>
	{/if}
  </div>
</div>
{literal}
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
  $('div.cf-addfrm').css({'display':'none'});
  $('p.cf-add-link').click(function(){
    if($('div.cf-addfrm').is(':hidden')){
	  $('div.cf-addfrm').slideDown();
	}
	else{
	  $('div.cf-addfrm').slideUp();
	}
  });
});
/* ]]> */
</script>
{/literal}
{/block}