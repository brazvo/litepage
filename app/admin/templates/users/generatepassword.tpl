{* smarty *}
{block name="content"}
<div class="content-inner single admin">
	<div class="main-upper">
		<h1 class="title">{$title}</h1>
	</div>
	<div class="main-lower">
		<p>{$message}</p>
		<a href="{Application::link("admin/users/generate-password-confirm/"|cat:$userId)}">[ {Application::getVar('yes')} ]</a>
		<a href="{Application::link("admin/users/edit/"|cat:$userId)}">[ {Application::getVar('no')} ]</a>
	</div>	
</div>
{/block}