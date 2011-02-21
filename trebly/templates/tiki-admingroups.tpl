{* $Id$ *}

{title help="Groups+Management" admpage="login"}{tr}Admin groups{/tr}{/title}

<div class="navbar">
	{button _text="{tr}Admin groups{/tr}"}
	{button href="tiki-adminusers.php" _text="{tr}Admin users{/tr}"}
	{button href="tiki-admingroups.php?clean=y" _text="{tr}Clear cache{/tr}"}
	{if $groupname}
		{if $prefs.feature_tabs ne 'y'}
			{button href="tiki-admingroups.php?add=1&amp;cookietab=2#tab2" _text="{tr}Add new group{/tr}"}
		{else}
			{button href="tiki-admingroups.php?add=1&amp;cookietab=2" _text="{tr}Add new group{/tr}"}
		{/if}
	{/if}
	{button href="tiki-objectpermissions.php" _text="{tr}Manage permissions{/tr}"}
</div>

{tabset name='tabs_admingroups'}

{tab name="{tr}List{/tr}"}
	{* ----------------------- tab with list --------------------------------------- *}
	<h2>{tr}List of existing groups{/tr}</h2>

	{include file='find.tpl' find_show_num_rows='y'}

	{if $cant_pages > $maxRecords or !empty($initial) or !empty($find)}
		{initials_filter_links}
	{/if}

	<table class="normal">
		<tr>
			<th style="width: 20px;">&nbsp;</th>
			<th>{tr}ID{/tr}</th>
			<th>
				<a href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}Name{/tr}</a>
			</th>
			<th>
				<a href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}Description{/tr}</a>
			</th>
			<th>{tr}Inherits Permissions from{/tr}</th>
			<th>{tr}User Choice{/tr}</th>
			<th>{tr}Permissions{/tr}</th>
			<th style="width: 20px;">&nbsp;</th>
		</tr>
		{cycle values="even,odd" print=false}
		{section name=user loop=$users}
			<tr class="{cycle}">
				<td class="icon">
					<a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}&amp;cookietab=2{if $prefs.feature_tabs ne 'y'}#tab2{/if}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
				</td>
				<td class="id">{$users[user].id|escape}</td>
				<td class="text">
					<a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}&amp;cookietab=2{if $prefs.feature_tabs ne 'y'}#tab2{/if}" title="{tr}Edit{/tr}">{$users[user].groupName|escape}</a>
				</td>
				<td class="text">{tr}{$users[user].groupDesc|escape|nl2br}{/tr}</td>
				<td class="text">
					{section name=ix loop=$users[user].included}
						{$users[user].included[ix]|escape}<br />
					{/section}
				</td>
				<td class="text">{tr}{$users[user].userChoice}{/tr}</td>
				<td class="text">
					<a class="link" href="tiki-objectpermissions.php?group={$users[user].groupName|escape:"url"}" title="{tr}Permissions{/tr}">{icon _id='key' alt="{tr}Permissions{/tr}"} {$users[user].permcant}</a>
				</td>
				<td class="action">
					{if $users[user].groupName ne 'Anonymous' and $users[user].groupName ne 'Registered' and $users[user].groupName ne 'Admins'}
						<a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;group={$users[user].groupName|escape:"url"}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
					{/if}
				</td>
			</tr>
		{/section}
	</table>

	{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
{/tab}

{if $groupname}
	{capture assign=tabaddeditgroup_admgrp}{tr}Edit group{/tr} <i>{$groupname|escape}</i>{/capture}
{else}
	{assign var=tabaddeditgroup_admgrp value="{tr}Add a New Group{/tr}"}
{/if}

{tab name=$tabaddeditgroup_admgrp}
{* ----------------------- tab with form --------------------------------------- *}

	<h2>{$tabaddeditgroup_admgrp}</h2>

	<form action="tiki-admingroups.php" method="post">
		<table class="formcolor">
			<tr>
				<td><label for="groups_group">{tr}Group:{/tr}</label></td>
				<td>
					{if $groupname neq 'Anonymous' and $groupname neq 'Registered' and $groupname neq 'Admins'}
						<input type="text" name="name" id="groups_group" value="{$groupname|escape}" style="width:95%" />
					{else}
						<input type="hidden" name="name" id="groups_group" value="{$groupname|escape}" />{$groupname}
					{/if}
				</td>
			</tr>
			<tr>
				<td><label for="groups_desc">{tr}Description:{/tr}</label></td>
				<td>
					<textarea rows="5" name="desc" id="groups_desc" style="width:95%">{$groupdesc|escape}</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<label for="groups_inc">{tr}Inherit permissions directly from following groups.{/tr}</label>
				</td>
				<td>
					{if $inc|@count > 20 and $hasOneIncludedGroup eq "y"}
						{foreach key=gr item=yn from=$inc}
							{if $yn eq 'y'}{$gr|escape} {/if}
						{/foreach}
						<br />
					{/if}
					<select name="include_groups[]" id="groups_inc" multiple="multiple" size="8">
						{if !empty($groupname)}<option value="">{tr}None{/tr}</option>{/if}
						{foreach key=gr item=yn from=$inc}
							<option value="{$gr|escape}" {if $yn eq 'y'} selected="selected"{/if}>{$gr|truncate:"52"|escape}</option>
						{/foreach}
					</select>
					<br />
					{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
				</td>
			</tr>

			<tr>
				<td>
					<label for="indirectly_inherited_groups">{tr}Also inheriting permissions from the following groups (indirect inheritance through the groups selected above).{/tr}</label>
				</td>
				<td>
					{if $indirectly_inherited_groups|@count > 0}
						{*	PROBLEM WITH FOREACH BELOW... *}
						{foreach key=num item=gr from=$indirectly_inherited_groups}
							{$gr};
						{/foreach}
					{else}
						{tr}None{/tr}						
					{/if}
				</td>
			</tr>

			{if $prefs.useGroupHome eq 'y'}
				<tr>
					<td>
						<label for="groups_home">{tr}Group Homepage or Url:{/tr}</label>
					</td>
					<td>
						<input type="text" style="width:95%" name="home" id="groups_home" value="{$grouphome|escape}" {if $prefs.useGroupHome ne 'y'}disabled="disabled" {/if}/>
						{remarksbox type="tip" title="{tr}Tip{/tr}"}
							{tr}Use wiki page name or full URL{/tr}. {tr}For other Tiki features, use relative links (such as <em>http:tiki-forums.php</em>).{/tr}
						{/remarksbox}
						{autocomplete element='#groups_home' type='pagename'}
					</td>
				</tr>
			{/if}
			{if $prefs.feature_categories eq 'y'}
				<tr>
					<td>
						<label for="groups_defcat">{tr}Default category assigned to uncategorized objects edited by a user with this default group:{/tr}</label>
					</td>
					<td>
						<select name="defcat" id="groups_defcat">
							<option value="" {if ($groupdefcat eq "") or ($groupdefcat eq 0)} selected="selected"{/if}>{tr}none{/tr}</option>
							{section name=ix loop=$categories}
								<option value="{$categories[ix].categId|escape}" {if $categories[ix].categId eq $groupdefcat}selected="selected"{/if}>{$categories[ix].categpath|escape}</option>
							{/section}
						</select>
					</td>
				</tr>
			{/if}
			{if $prefs.useGroupTheme eq 'y'}
				<tr>
					<td><label for="groups_theme">{tr}Group Theme:{/tr}</label></td>
					<td>
						<select name="theme" id="groups_theme" multiple="multiple" size="4">
							<option value="" {if $grouptheme eq ""} selected="selected"{/if}>{tr}none{/tr} ({tr}Use site default{/tr})</option>
							{section name=ix loop=$av_themes}
								<option value="{$av_themes[ix]|escape}" {if $grouptheme eq $av_themes[ix]}selected="selected"{/if}>{$av_themes[ix]}</option>
							{/section}
						</select>
					</td>
				</tr>
			{/if}
			
			{if $prefs.groupTracker eq 'y'}
				<tr>
					<td><label for="groupTracker">{tr}Group Information Tracker{/tr}</label></td>
					<td>
						<select name="groupstracker">
							<option value="0">{tr}choose a group tracker ...{/tr}</option>
							{foreach key=tid item=tit from=$trackers}
								<option value="{$tid}"{if $tid eq $grouptrackerid} {assign var="ggr" value="$tit"}selected="selected"{/if}>{$tit|escape}</option>
							{/foreach}
						</select>
						{if $grouptrackerid}
							<br />
							<select name="groupfield">
								<option value="0">{tr}choose a field ...{/tr}</option>
								{section name=ix loop=$groupFields}
									<option value="{$groupFields[ix].fieldId}"{if $groupFields[ix].fieldId eq $groupfieldid} selected="selected"{/if}>{$groupFields[ix].name|escape}</option>
								{/section}
							</select>
						{/if}

						{if $grouptrackerid}
							{button href="tiki-admin_tracker_fields.php?trackerId=$grouptrackerid" _text="{tr}Admin{/tr} $ggr"}
						{else}
							{button href="tiki-admin_trackers.php" _text="{tr}Admin{/tr} $ggr"}
						{/if}
					</td>
				</tr>
			{/if}

			{if $prefs.userTracker eq 'y'}
				<tr>
					<td><label for="userstracker">{tr}Users Information Tracker{/tr}</label></td>
					<td>
						<select name="userstracker">
							<option value="0">{tr}choose a users tracker ...{/tr}</option>
							{foreach key=tid item=tit from=$trackers}
								<option value="{$tid}"{if $tid eq $userstrackerid} {assign var="ugr" value="$tit"}selected="selected"{/if}>{$tit|escape}</option>
							{/foreach}
						</select>
						{if $userstrackerid}
							<br />
							<select name="usersfield">
								<option value="0">{tr}choose a field ...{/tr}</option>
								{section name=ix loop=$usersFields}
									<option value="{$usersFields[ix].fieldId}"{if $usersFields[ix].fieldId eq $usersfieldid} selected="selected"{/if}>{$usersFields[ix].fieldId} - {$usersFields[ix].name|escape}</option>
								{/section}
							</select>
						{/if}

						{if $userstrackerid}
							{button href="tiki-admin_tracker_fields.php?trackerId=$userstrackerid" _text="{tr}Admin{/tr} $ugr"}
						{else}
							{button href="tiki-admin_trackers.php" _text="{tr}Admin{/tr} $ugr"}
						{/if}
					</td>
				</tr>
				<tr>
					<td>{tr}Users Information Tracker Fields Asked at Registration Time<br />(fieldIds separated with :){/tr}</td>
					<td><input type="text" style="width:95%" name="registrationUsersFieldIds" value="{$registrationUsersFieldIds|escape}" /></td>
				</tr>
			{/if}

			<tr>
				<td>{tr}User can assign to the group himself{/tr}</td>
				<td><input type="checkbox" name="userChoice"{if $userChoice eq 'y'} checked="checked"{/if}/></td>
			</tr>

			<tr>
				<td>{tr}Users are automatically unassigned from the group after{/tr}</td>
				<td><input type="text" name="expireAfter" value="{$group_info.expireAfter|escape}" />{tr}Days{/tr}<br /><i>{tr}0 or empty for never{/tr}</i></td>
			</tr>
			<tr>
				<td>{tr}Users are automatically assigned at registration in the group if their emails match the pattern{/tr}</td>
				<td><input type="text" size="40" name="emailPattern" value="{$group_info.emailPattern|escape}" /><br />{tr}Example: {/tr}/@tw\.org$/ <br />{tr}Example:{/tr} /@(tw.org$)|(tw\.com$)/</td>
			</tr>

			{if $group ne ''}
				<tr>
					<td>
						{tr}Assign group <em>management</em> permissions:{/tr}
					</td>
					<td>
						{icon href="tiki-objectpermissions.php?objectType=group&objectId=$groupname&objectName=$groupname&permType=group" _text="{tr}Assign Permissions{/tr}" _id="key"}
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
						<input type="hidden" name="olgroup" value="{$group|escape}" />
					</td>
					<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
				</tr>
			{else}
				<tr>
					<td >&nbsp;</td>
					<td><input type="submit" name="newgroup" value="{tr}Add{/tr}" /></td>
				</tr>
			{/if}
		</table>
	</form>
	<br /><br />

	{if $prefs.groupTracker eq 'y'}
		{if $grouptrackerid and $groupitemid}
			{tr}Group tracker item : {$groupitemid}{/tr}
				{button href="tiki-view_tracker_item.php?trackerId=$grouptrackerid&amp;itemId=$groupitemid&amp;show=mod" _text="{tr}Edit Item{/tr}"}
		{elseif $grouptrackerid}
			{if $groupfieldid}
				{tr}Group tracker item not found{/tr}
				{button href="tiki-view_tracker.php?trackerId=$grouptrackerid" _text="{tr}Create Item{/tr}"}
			{else}
				{tr}Choose a field ...{/tr}
			{/if}
		{/if}
		<br /><br />
	{/if}
{/tab}


{if $groupname}
	{tab name="{tr}Members{/tr}"}
	{* ----------------------- tab with memberlist --------------------------------------- *}
		<h2>{tr}Members List:{/tr} {$groupname|escape}</h2>
		<table class="normal">
			<tr>
				<th>{self_link _sort_arg='sort_mode_member' _sort_field='login'}{tr}User{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode_member' _sort_field='created'}{tr}Assign{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode_member' _sort_field='expire'}{tr}Expire{/tr}{/self_link}</th>
				<th>{tr}Action{/tr}</th>
			</tr>
			{cycle values="even,odd" print=false}
			<tr>
				{foreach from=$memberslist item=member}
					<tr class="{cycle}">
					<td class="username">{$member.login|userlink}</td>
					<td class="date">{$member.created|tiki_short_datetime}</td>
					<td class="date">{if !empty($member.expire)}{$member.expire|tiki_short_datetime}{/if}</td>
					<td class="action">
						<a href="tiki-adminusers.php?user={$member.login|escape:"url"}&amp;action=removegroup&amp;group={$groupname|escape:url}" class="link" title="{tr}Remove from Group{/tr}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
						<a href="tiki-adminusers.php?user={$member.userId|escape:"url"}&amp;cookietab=2{if $prefs.feature_tabs ne 'y'}#tab2{/if}" class="link" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
					</td>
					</tr>
				{/foreach}
		</table>
		{pagination_links cant=$membersCount step=$prefs.maxRecords offset=$membersOffset offset_arg='membersOffset'}{/pagination_links}
		<div class="box">{$membersCount} {tr}users in group{/tr} {$groupname|escape}</div>
		{if ! empty($userslist)}
			<form method="post" action="tiki-admingroups.php">
				<p>
					<input type="hidden" name="group" value="{$groupname|escape}"/>
					<select name="user">
						{foreach from=$userslist item=iuser}
							<option>{$iuser|escape}</option>
						{/foreach}
					</select>
					<input type="submit" name="adduser" value="{tr}Add to group{/tr}"/>
				</p>
			</form>
		{/if}
	{/tab}
{/if}

{if $groupname}
	{tab name="{tr}Import/Export{/tr}"}
		{* ----------------------- tab with import/export --------------------------------------- *}
		<form method="post" action="tiki-admingroups.php" enctype="multipart/form-data">
			<input type="hidden" name="group" value="{$groupname|escape}" />
			{if $errors}
				<div class="simple highlight">
					{foreach from=$errors item=e}
						{$e}<br />
					{/foreach}
				</div>
			{/if}

			<h2>{tr}Download CSV export{/tr}</h2>
			<table class="formcolor">
				<tr>
					<td class="auto">{tr}Charset encoding:{/tr}</td>
					<td class="auto">
						<select name="encoding">
							<option value="UTF-8" selected="selected">{tr}UTF-8{/tr}</option>
							<option value="ISO-8859-1">{tr}ISO-8859-1{/tr}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="auto">{tr}Fields:{/tr}</td>
					<td class="auto">
						<input type="checkbox" name="username" checked="checked" />
						{tr}Username{/tr}
						<br />
						<input type="checkbox" name="email"/>{tr}Email{/tr}
						<br />
						<input type="checkbox" name="lastLogin" />{tr}Last login{/tr}
					</td>
				</tr>
				<tr>
					<td class="auto"></td>
					<td class="auto"><input type="submit" name="export" value="{tr}Export{/tr}" /></td>
				</tr>
			</table>

			<h2>{tr}Batch upload (CSV file){/tr}</h2>
			<h3>{tr}Assign users to group:{/tr} {$groupname|escape} </h3>
			{remarksbox type="tip" title="{tr}Tip{/tr}"}
				{tr}Each user in the file must already exist.{/tr}<br />{tr}To create users or/and assign them to groups, got to <a href="tiki-adminusers.php">admin->users</a>{/tr}
			{/remarksbox}
			<table class="formcolor">
				<tr>
					<td class="auto">
						{tr}CSV File{/tr}<a {popup text='user<br />user1<br />user2'}>{icon _id='help'}</a>
					</td>
					<td class="auto"><input name="csvlist" type="file" /></td>
				</tr>
				<tr>
					<td class="auto"></td>
					<td class="auto"><input type="submit" name="import" value="{tr}Import{/tr}" /></td>
				</tr>
			</table>
		</form>
	{/tab}
{/if}

{/tabset}
