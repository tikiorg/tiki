{title help="UserList"}{tr}User List{/tr}{/title}

{$cant_users} {if !$find}{tr}users registered{/tr}{else} {tr}Users{/tr} {tr}like{/tr} "{$find}"{/if}

{include file='find.tpl' autocomplete="username"}

<table bgcolor="#ffffff" class="normal">
	<tr>
		<th><a href="tiki-list_users.php?offset={$offset}&amp;find={$find}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}" class="userlistheading" style="color: White;">{tr}User{/tr}</a>&nbsp;</th>
		{if $prefs.feature_community_list_name eq 'y' and $prefs.user_show_realnames neq 'y'}
			<th><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;find={$find}&amp;sort_mode={if $sort_mode eq 'pref:realName_desc'}pref:realName_asc{else}pref:realName_desc{/if}" style="color: White;">{tr}Real Name{/tr}</a>&nbsp;</th>
		{/if}
		{if $prefs.feature_score eq 'y' and $prefs.feature_community_list_score eq 'y'}
			<th><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;find={$find}&amp;sort_mode={if $sort_mode eq 'score_desc'}score_asc{else}score_desc{/if}" style="color: White;">{tr}Score{/tr}</a>&nbsp;</th>
		{/if}
		{if $prefs.feature_community_list_country eq 'y'}
			<th><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;find={$find}&amp;sort_mode={if $sort_mode eq 'pref:country_desc'}pref:country_asc{else}pref:country_desc{/if}" style="color: White;">{tr}Country{/tr}</a>&nbsp;</th>
		{/if}
		{if $prefs.feature_community_list_distance eq 'y'}<th>{tr}Distance (km){/tr}&nbsp;</th>{/if}
	</tr>
	{cycle values="odd,even" print=false}
	{section name=changes loop=$listusers}
		<tr class="{cycle}">
			<td class="username">&nbsp;{$listusers[changes].login|userlink}&nbsp;</td>
			{if $prefs.feature_community_list_name eq 'y' and $prefs.user_show_realnames neq 'y'}
				<td class="text">&nbsp;{$listusers[changes].realName}&nbsp;</td>
			{/if}
			{if $prefs.feature_score eq 'y' and $prefs.feature_community_list_score eq 'y'}
				<td class="integer">&nbsp;{$listusers[changes].score}&nbsp;</td>
			{/if}
			{if $prefs.feature_community_list_country eq 'y'}
				<td class="text">
					{if $listuserscountry[changes] == "None" || $listuserscountry[changes] == "Other" || $listuserscountry[changes] == ""}
						{html_image file='img/flags/Other.gif' hspace='4' vspace='1' alt="{tr}Flag{/tr}" title="{tr}Flag{/tr}"}
					{else}
						{html_image file="img/flags/$listuserscountry[changes].gif" hspace='4' vspace='1' alt="{tr}Flag{/tr}" title="{tr}Flag{/tr}"}&nbsp;{tr}{$listuserscountry[changes]}{/tr}
					{/if}&nbsp;
				</td>
			{/if}
			{if $prefs.feature_community_list_distance eq 'y'}
				<td class="integer">&nbsp;{$listdistance[changes]}&nbsp;</td>
			{/if}
		</tr>
	{sectionelse}
		{norecords _colspan=5}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
