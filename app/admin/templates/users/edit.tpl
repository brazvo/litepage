{* smarty *}
{block name="content"}
<div class="content-inner single admin">
	<div class="main-upper">
	<h1 class="title">{$title}</h1>
	{if Application::$logged['role'] == 'admin'}
	<a href="{Application::link("admin/users/usersPermisions/"|cat:$id)}">[ {$smarty.const.USERS_ADMIN_USERS_PERMISIONS_LNK} ]</a>
	{/if}
	{if Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'}
	{if !$locked}
	<a class="lock-user" href="{Application::link("admin/users/lock-user/"|cat:$id)}">[ {$smarty.const.USERS_ADMIN_USERS_LOCK_LNK} ]</a>
	{else}
	<a class="unlock-user" href="{Application::link("admin/users/unlock-user/"|cat:$id)}">[ {$smarty.const.USERS_ADMIN_USERS_UNLOCK_LNK} ]</a>
	{/if}
	<a class="generate-password" href="{Application::link("admin/users/generate-password/"|cat:$id)}">[ {$smarty.const.USERS_ADMIN_USERS_GENERATE_LNK} ]</a>
	{/if}
	</div>
	<div class="main-lower">
	{$content}
	</div>
	{literal}
	<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
			if( !$("#formEdit-user-isstaff").is(":checked") ) {
				$(".frm-control-staff").css({'height':'49px', 'overflow':'hidden'});
				$("#formEdit-user-staff").css({'height':'29px', 'overflow':'hidden'});
			}
			
			$("#formEdit-user-isstaff").click(function(){
				if( $(this).is(":checked") ) {
					$(".frm-control-staff").css({'height':'auto', 'overflow':'auto'});
					$("#formEdit-user-staff").css({'height':'auto', 'overflow':'auto'});
				}
				else {
					$(".frm-control-staff").css({'height':'49px', 'overflow':'hidden'});
					$("#formEdit-user-staff").css({'height':'29px', 'overflow':'hidden'});
				}
			});
				
			$('a.lock-user').click(function(){
				if( confirm('Naozaj zablokovať užívateľa?') ) {
					self.location = '{/literal}{Application::link("admin/users/lock-user-confirm/"|cat:$id)}{literal}';
				}
				return false;
			});
				
			$('a.unlock-user').click(function(){
				if( confirm('Naozaj odblokovať užívateľa?') ) {
					self.location = '{/literal}{Application::link("admin/users/unlock-user-confirm/"|cat:$id)}{literal}';
				}
				return false;
			});
				
			$('a.generate-password').click(function(){
				if( confirm('Naozaj vygenerovať nové heslo?') ) {
					self.location = '{/literal}{Application::link("admin/users/generate-password-confirm/"|cat:$id)}{literal}';
				}
				return false;
			});
		});
		/* ]]> */
	</script>
	{/literal}
</div>
{/block}