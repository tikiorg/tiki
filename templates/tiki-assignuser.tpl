{* $Id$ *}

{assign var=escuser value=$assign_user|escape:url}
{title}{tr}Assign User {$assign_user} to Groups{/tr}{/title}

<div class="navbar">
	{if $tiki_p_admin eq 'y'} {* only full admins can manage groups, not tiki_p_admin_users *}
		{button href="tiki-admingroups.php" _text="{tr}Admin groups{/tr}"}
	{/if}
	{if $tiki_p_admin eq 'y' or $tiki_p_admin_users eq 'y'}
		{button href="tiki-adminusers.php" _text="{tr}Admin users{/tr}"}
	{/if}
</div>

{if $prefs.feature_intertiki eq 'y' and $prefs.feature_intertiki_import_groups eq 'y'}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
		{tr}Since this Tiki site is in slave mode and imports groups, the master groups will be automatically reimported at each login{/tr}
	{/remarksbox}
{/if}
  
<h2>{tr}User Information{/tr}</h2>
<table class="formcolor">
	<tr><td>{tr}Login:{/tr}</td><td>{$user_info.login|escape}</td></tr>
	<tr><td>{tr}Email:{/tr}</td><td>{$user_info.email}</td></tr>
	<tr>
		<td>{tr}Groups:{/tr}</td><td>
			{foreach from=$user_info.groups item=what key=grp name=groups}
				{if $what eq 'included'}<i>{/if}{$grp|escape}{if $what eq 'included'}</i>{/if}
				{if $grp != "Anonymous" && $grp != "Registered" and $what neq 'included'}
					<a class="link" href="tiki-assignuser.php?{if $offset}offset={$offset}&amp;{/if}maxRecords={$prefs.maxRecords}&amp;sort_mode={$sort_mode}{if $assign_user}&amp;assign_user={$assign_user|escape:url}{/if}&amp;action=removegroup&amp;group={$grp|escape:url}" title="Remove">{icon _id='cross' alt="{tr}Remove{/tr}" style="vertical-align:middle"}</a>
				{/if}{if !$smarty.foreach.groups.last},{/if}&nbsp;&nbsp;
			{/foreach}
		</td>
	</tr>
	<form method="post" action="tiki-assignuser.php{if $assign_user}?assign_user={$assign_user|escape:'url'}{/if}">
		<tr>
			<td>{tr}Default Group:{/tr}</td>
			<td>
				<select name="defaultgroup">
					<option value=""></option>
					{foreach from=$user_info.groups key=name item=included}
						<option value="{$name|escape}" {if $name eq $user_info.default_group}selected="selected"{/if}>{$name|escape}</option>
					{/foreach}
				</select>
				<input type="hidden" value="{$user_info.login|escape}" name="login" />
				<input type="hidden" value="{$prefs.maxRecords}" name="maxRecords" />
				<input type="hidden" value="{$offset}" name="offset" />
				<input type="hidden" value="{$sort_mode}" name="sort_mode" />
				<input type="submit" value="{tr}Set{/tr}" name="set_default" />
			</td>
		</tr>
	</form>
</table>
<br />
<div align="left"><h2>{tr}Assign User {$assign_user|escape} to Groups{/tr}</h2></div>

{include file='find.tpl' find_show_num_rows='y'}

<table class="normal">
	<tr>
		<th><a href="tiki-assignuser.php?{if $assign_user}assign_user={$assign_user|escape:url}&amp;{/if}offset={$offset}&amp;maxRecords={$prefs.maxRecords}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}Name{/tr}</a></th>
		<th><a href="tiki-assignuser.php?{if $assign_user}assign_user={$assign_user|escape:url}&amp;{/if}offset={$offset}&amp;maxRecords={$prefs.maxRecords}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}Description{/tr}</a></th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{section name=user loop=$users}
		{if $users[user].groupName != 'Anonymous'}
			<tr class="{cycle}">
				<td class="text">
					{if $tiki_p_admin eq 'y'} {* only full admins can manage groups, not tiki_p_admin_users *}
						<a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}{if $prefs.feature_tabs ne 'y'}#2{/if}" title="{tr}Edit{/tr}">
					{/if}
					{$users[user].groupName|escape}
					{if $tiki_p_admin eq 'y'}
						</a>
					{/if}
				</td>
				<td class="text">{tr}{$users[user].groupDesc|escape}{/tr}</td>
				<td class="action">
					{if $users[user].what ne 'real'}
						<a class="link" href="tiki-assignuser.php?{if $offset}offset={$offset}&amp;{/if}maxRecords={$prefs.maxRecords}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;group={$users[user].groupName|escape:url}{if $assign_user}&amp;assign_user={$assign_user|escape:url}{/if}" title="{tr}Assign User to Group{/tr}">{icon _id='add' alt="{tr}Assign{/tr}"}</a>
					{elseif $users[user].groupName ne "Registered"}
						<a class="link" href="tiki-assignuser.php?{if $offset}offset={$offset}&amp;{/if}maxRecords={$prefs.maxRecords}&amp;sort_mode={$sort_mode}{if $assign_user}&amp;assign_user={$assign_user|escape:url}{/if}&amp;action=removegroup&amp;group={$users[user].groupName|escape:url}" title="unassign">{icon _id='cross' alt="{tr}Unassign{/tr}"}</a>
					{/if}
				</td>
			</tr>
		{/if}
	{/section}
</table>
{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
