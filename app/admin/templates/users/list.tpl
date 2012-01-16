{* smarty *}
{block name="content"}
<div class="content-inner single admin">
  <div class="main-upper">
    <h1 class="title">{$title}</h1>
  </div>
  <div class="main-lower">
    <table class="admin-table admin-contents" style="width:100%" cellspacing="0">
	  <thead>
	  <tr>
                <td>Priezvisko a Meno</td>
		<td>Postavenie</td>
		<td>Posledné prihlásenie</td>
		<td colspan="2" style="text-align:center">{$smarty.const.ACTIONS}</td>
	  </tr>
	  </thead>
	  <tbody>
	  {foreach from=$users item=user}
	  <tr>
	    <td>{$user.surname}&nbsp;{$user.name}</td>
		<td style="width:100px">
		  {if $user.role == 'admin'}Administrátor{/if}
		  {if $user.role == 'editor'}Editor{/if}
		  {if $user.role == 'user'}Užívateľ{/if}
		</td>
		<td style="width:130px">{slovdate($user.last_login)}</td>
		{if Application::$edit}
		<td style="width:20px;text-align:center"><a class="edit" href="{Application::link("admin/users/edit/"|cat:$user.id)}"><img src="{Application::imgSrc("icon-edit.jpg")}" alt="Úprava" title="Úprava" /></a></td>
		{else}
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="{Application::imgSrc("icon-edit-gr.jpg")}" alt="Úprava" title="Úprava" /></span></td>
		{/if}
		{if Application::$delete}
		<td style="width:20px;text-align:center"><a class="delete" href="{Application::link("admin/users/delete/"|cat:$user.id)}"><img src="{Application::imgSrc("icon-delete.jpg")}" alt="Odstrániť" title="Odstrániť" /></a></td>
		{else}
		<td style="width:20px;text-align:center"><span style="color:silver"><img src="{Application::imgSrc("icon-delete-gr.jpg")}" alt="Odstrániť" title="Odstrániť" /></span></td>
		{/if}
	  </tr>
	  {/foreach}
	  </tbody>
	</table>
  </div>
</div>
{/block}