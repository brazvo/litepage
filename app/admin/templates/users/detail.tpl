{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h1 class="title">{$title}</h1>
  </div>
  <div class="main-lower">
    <h3>{$userinfo.name} {$userinfo.surname}</h3>
	<p>
	Status: 
	{if $userinfo.role == 'admin'}Administrátor{/if}
        {if $userinfo.role == 'editor'}Editor{/if}
        {if $userinfo.role == 'user'}Užívateľ{/if}
	</p>
	<p>Posledné prihlásenie: {slovdate($userinfo.last_login)}</p>
	<h4>Zmena hesla</h4>
	{$pwdform}
  </div>
</div>
{/block}