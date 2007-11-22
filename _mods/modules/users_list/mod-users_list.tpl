{* $Header: /cvsroot/tikiwiki/_mods/modules/users_list/mod-users_list.tpl,v 1.5 2007-11-22 16:57:23 sylvieg Exp $ *}
<table class="normal">
{if $module_params_users_list.heading ne 'n'}
<tr>
{if $module_params_users_list.login ne 'n'}<td class="heading">{tr}Login{/tr}</td>{/if}
{if $module_params_users_list.realName eq 'y'}<td class="heading">{tr}Real Name{/tr}</td>{/if}
{if $module_params_users_list.email eq 'y'}<td class="heading">{tr}Email{/tr}</td>{/if}
{if $module_params_users_list.lastLogin eq 'y'}<td class="heading">{tr}Last login{/tr}</td>{/if}
{if $module_params_users_list.groups eq 'y'}<td class="heading">{tr}Groups{/tr}</td>{/if}
{if $module_params_users_list.avatar eq 'y'}<td class="heading">{tr}Avatar{/tr}</td>{/if}
{if $module_params_users_list.userPage eq 'y'or $module_params_users_list.log eq 'y'}<td class="heading"></td>{/if}
</tr>
{/if}
{cycle print=false values="even,odd"}
{section name=user loop=$users}
<tr class="{cycle}">
{if $module_params_users_list.login ne 'n'}<td>{if $users[user].info_public ne 'n'}<a class="link" href="tiki-user_information.php?user={$users[user].user|escape:"url"}" title="{tr}view{/tr}">{$users[user].user}</a>{else}{$users[user].user}{/if}</td>{/if}
{if $module_params_users_list.realName eq 'y'}<td>{if $users[user].info_public ne 'n'}<a class="link" href="tiki-user_information.php?user={$users[user].user|escape:"url"}" title="{tr}view{/tr}">{$users[user].realName}</a>{else}{$users[user].realName}{/if}</td>{/if}
{if $module_params_users_list.email eq 'y'}<td>{$users[user].email}</td>{/if}
{if $module_params_users_list.lastLogin eq 'y'}<td>{if $users[user].currentLogin eq ''}{tr}Never{/tr} <i>({$users[user].age|duration_short})</i>{else}{$users[user].currentLogin|dbg|tiki_long_datetime}{/if}</td>{/if}
{if $module_params_users_list.groups eq 'y'}<td>
	{foreach from=$users[user].groups key=grs item=what}
	{if $grs ne "Anonymous" and $grs ne "Registered"}{if $what eq 'included'}<i>{/if}{$grs}{if $what eq 'included'}</i>{/if}{if $grs eq $users[user].default_group} {tr}default{/tr}{/if}<br />{/if}
	{/foreach}
</td>{/if}
{if $module_params_users_list.avatar eq 'y'}<td>{if $users[user].info_public ne 'n'}<a class="link" href="tiki-user_information.php?user={$users[user].user|escape:"url"}" title="{tr}view{/tr}">{$users[user].avatar}</a>{else>{$users[user].avatar}{/if}</td>{/if}
{if $module_params_users_list.userPage eq 'y' or  $module_params_users_list.log  eq 'y'}<td>
{if $module_params_users_list.userPage eq 'y' and $users[user].userPage}<a href="tiki-index.php?page={$users[user].userPage}" title="{$users[user].userPage}"><img src="pics/icons/magnifier.png" width="16" height="16" border="0" alt="{$users[user].userPage}" /></a>{/if}
{if $module_params_users_list.log  eq 'y'}<a href="tiki-admin_actionlog.php?selectedUsers[]={$user|escape:"url"}&amp;list=y" title="{tr}Logs{/tr}"><img src="pics/icons/table.png" width="16" height="16" border="0" alt="{tr}Logs{/tr}" /></a>{/if}
</td>
{/if}
</tr>
{/section}
</table>