{block name="content"}
<div class="content-inner single user-profile">
    <div class="main-upper">
        <h1 class="title">{$title}</h1>
    </div>
    <div class="main-lower">
        <h2>{$profile.name} {$profile.surname}</h2>
		
		<p><a href="http://www.ivao.aero/members/person/details.asp?id={$vid}" rel="external">Detail užívateľa na IVAO.areo</a></p>
        
        {if $profile.profile}
        <div class="profile">{$profile.profile}</div>
        {/if}
        
        {if $profile.staff_comment}
        <div class="staff-comment">{$profile.staff_comment}</div>
        {/if}

        {if $profile.isstaff}
        <div class="staff-member">
            <h3>{$smarty.const.USER_STAFF_MEMBER}</h3>
            <strong>{$smarty.const.USER_STAFF_POSITION}:</strong> <span style="cursor:default;" title="{$profile.staff_desc}">{$profile.staff_position}</span>
        </div>
        {/if}
        
        {if $blog}
        <div class="user-blog">
            <h3>{$smarty.const.USER_MY_BLOG}</h3>
            {$blog}
        </div>
        {/if}
        
        {if $photoalbums}
        <div class="user-photoalbums">
            <h3>{$smarty.const.USER_MY_PHOTOALBUMS}</h3>
            {$photoalbums}
        </div>
        {/if}
    </div>
</div>
{/block}