{* smarty *}
{assign var=curyear value=date('Y')}
{assign var=copyrightFrom value='2010'}
{if $curyear == $copyrightFrom}
    {$crString = $curyear}
{else}
    {$crString = $copyrightFrom|cat:' - '|cat:$curyear}
{/if}

{if $smarty.const.FANCY_LOGIN_FORM}
    {$copypath = '#login-form'}
{else}
    {$copypath = $smarty.const.BASEPATH|cat:'/login'}
{/if}

{if Application::$logged['status']}
    {$copypath = $smarty.const.BASEPATH|cat:'/login'}
{/if}

<div class="footer-text">
  Copyright <a class="copyright login-form" href="{$copypath}">&copy;</a> {$crString} Branislav Zvolenský, Created by <a class="created-link" href="http://www.mbmartworks.sk/">Branislav Zvolenský</a>
</div>