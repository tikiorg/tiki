<div id="tiki-top_menu"><img src="styles/tikipedia/user.gif" height="16" width="16" alt="{tr}user{/tr}" title="{tr}user{/tr} {$page_user}" />
{if $user}
{tr}logged as{/tr}: {$user}&nbsp;<a class="tikitopmenu" href="tiki-logout.php">{tr}Logout{/tr}</a>
{if $feature_userPreferences eq 'y'}<a class="tikitopmenu" href="tiki-my_tiki.php">{tr}MyTiki{/tr}</a>{/if}
{else}
<a href="tiki-login.php">{tr}Login{/tr}</a>
{/if}
{if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'}
  &#160;//&#160;<a href="javascript:toggle('debugconsole');">{tr}debug{/tr}</a>
{/if}
</div>