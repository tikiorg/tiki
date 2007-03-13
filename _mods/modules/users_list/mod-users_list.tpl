<table class="normal">
<tr>
{if $show_login}<td class="heading">{tr}Login{/tr}</td>{/if}
{if $show_realName}<td class="heading">{tr}Real Name{/tr}</td>{/if}
{if $show_email}<td class="heading">{tr}Email{/tr}</td>{/if}
{if $show_lastLogin}<td class="heading">{tr}Last login{/tr}</td>{/if}
{if $show_groups}<td class="heading">{tr}Groups{/tr}</td>{/if}
{if $show_avatar}<td class="heading">{tr}Avatar{/tr}</td>{/if}
<td class="heading">&nbsp;</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$users}
<tr class="{cycle}">
{if $show_login}<td><a class="link" href="tiki-view_user_information.php?user={$users[user].user|escape:"url"}" title="{tr}view{/tr}">{$users[user].user}</a></td>{/if}
{if $show_realName}<td>{$users[user].realName}</td>{/if}
{if $show_email}<td>{$users[user].email}</td>{/if}
{if $show_lastLogin}<td>{if $users[user].currentLogin eq ''}{tr}Never{/tr} <i>({$users[user].age|duration_short})</i>{else}{$users[user].currentLogin|dbg|tiki_long_datetime}{/if}</td>{/if}
{if $show_groups}<td>
	{foreach from=$users[user].groups key=grs item=what}
	{if $grs != "Anonymous"}{if $what eq 'included'}<i>{/if}{$grs}{if $what eq 'included'}</i>{/if}{if $grs eq $users[user].default_group} {tr}default{/tr}{/if}<br />{/if}
	{/foreach}
</td>{/if}
{if $show_avatar}<td>{$users[user].avatar}</td>{/if}
<td>
{if $show_userPage}<a href="tiki-index.php?page={$users[user].userPage}">{$users[user].userPage}</a><br />{/if}
{if $show_log && $feature_actionlog eq 'y'}<a href="tiki-admin_actionlog.php?selectedUsers[]={$user|escape:"url"}&amp;list=y">{tr}Logs{/tr}</a>{/if}
</td>
</tr>
{/section}
</table>