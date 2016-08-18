{* $Id$ *}
{tikimodule error=$module_params.error title=$tpl_module_title name="users_list" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<table class="normal">
		{if $module_params_users_list.heading ne 'n'}
			<tr>
				{if $module_params_users_list.login ne 'n'}<td class="heading">{tr}Login{/tr}</td>{/if}
				{if $module_params_users_list.realName eq 'y'}<td class="heading">{tr}Real Name{/tr}</td>{/if}
				{if $module_params_users_list.email eq 'y'}<td class="heading">{tr}Email{/tr}</td>{/if}
				{if $module_params_users_list.lastLogin eq 'y'}<td class="heading">{tr}Last login{/tr}</td>{/if}
				{if $module_params_users_list.groups eq 'y'}<td class="heading">{tr}Groups{/tr}</td>{/if}
				{if $module_params_users_list.avatar eq 'y'}<td class="heading">{tr}Profile picture{/tr}</td>{/if}
				{if $module_params_users_list.userPage eq 'y'or $module_params_users_list.log eq 'y'}<td class="heading"></td>{/if}
			</tr>
		{/if}

		{cycle print=false values="even,odd"}
		{section name=ix loop=$users}
			<tr class="{cycle}">
				{if $module_params_users_list.login ne 'n'}
					<td>
						{if $users[ix].info_public ne 'n'}<a class="link" href="tiki-user_information.php?view_user={$users[ix].user|escape:"url"}" title="{tr}view{/tr}">{$users[ix].user}</a>{else}{$users[ix].user}{/if}
					</td>
				{/if}
				{if $module_params_users_list.realName eq 'y'}
					<td>
						{if $users[ix].info_public ne 'n'}
							<a class="link" href="tiki-user_information.php?view_user={$users[ix].user|escape:'url'}" title="{tr}view{/tr}">
						{/if}
						{if empty($users[ix].realName) and $module_params_users_list.login eq 'n'}
							{$users[ix].user}
						{else}
							{$users[ix].realName}
						{/if}
						{if $users[ix].info_public ne 'n'}
							</a>
						{/if}
					</td>
				{/if}
				{if $module_params_users_list.email eq 'y'}
					<td>{$users[ix].email}</td>{/if}
				{if $module_params_users_list.lastLogin eq 'y'}
					<td>
						{if $users[ix].currentLogin eq ''}{tr}Never{/tr}
							<i>({$users[ix].age|duration_short})</i>
						{else}
							{$users[ix].currentLogin|dbg|tiki_long_datetime}
						{/if}
					</td>
				{/if}
				{if $module_params_users_list.groups eq 'y'}
					<td>
						{foreach from=$users[ix].groups key=grs item=what}
							{if $users[ix].groups.$grs ne "Anonymous" and $users[ix].groups.$grs ne "Registered"}
								{if $what eq 'included'}<i>{/if}{$users[ix].groups.$grs}{if $what eq 'included'}</i>{/if}
								{if $users[ix].groups.$grs eq $users[ix].default_group} {tr}default{/tr}{/if}<br />{/if}
						{/foreach}
					</td>
				{/if}
				{if $module_params_users_list.avatar eq 'y'}
					<td>
						{if $users[ix].info_public ne 'n'}
							<a class="link" href="tiki-user_information.php?view_user={$users[ix].user|escape:'url'}" title="{tr}view{/tr}">{$users[ix].avatar}</a>
						{else}
							{$users[ix].avatar}
						{/if}
					</td>
				{/if}
				{if $module_params_users_list.userPage eq 'y' or $module_params_users_list.log eq 'y'}
					<td>
						{if $module_params_users_list.userPage eq 'y' and $users[ix].userPage}
							<a href="tiki-index.php?page={$users[ix].userPage}" title="{$users[ix].userPage}"><img src="pics/icons/magnifier.png" width="16" height="16" border="0" alt="{$users[ix].userPage}" /></a>
						{/if}
						{if $module_params_users_list.log eq 'y'}
							<a href="tiki-admin_actionlog.php?selectedUsers[]={$users[ix].user|escape:"url"}&amp;list=y" title="{tr}Logs{/tr}"><img src="pics/icons/table.png" width="16" height="16" border="0" alt="{tr}Logs{/tr}" /></a>
						{/if}
					</td>
				{/if}
			</tr>
		{/section}

	</table>
{/tikimodule}
