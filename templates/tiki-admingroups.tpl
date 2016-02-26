{* $Id$ *}

{title help="Groups+Management" admpage="login"}{tr}Admin groups{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-adminusers.php" class="btn btn-default" _type="link" _icon_name="user" _text="{tr}Admin Users{/tr}"}
	{button href="tiki-admingroups.php?clean=y" class="btn btn-default" _type="link" _icon_name="trash" _text="{tr}Clear cache{/tr}"}
	{if $groupname}
		{if $prefs.feature_tabs ne 'y'}
			{button href="tiki-admingroups.php?add=1&amp;cookietab=2#tab2" class="btn btn-default" _icon_name="create" _text="{tr}Add New Group{/tr}"}
		{else}
			{button href="tiki-admingroups.php?add=1&amp;cookietab=2" class="btn btn-default" _icon_name="create" _text="{tr}Add New Group{/tr}"}
		{/if}
	{/if}
	<button class="btn btn-default btn-link">
		{permission_link mode=text}
	</button>
	{if $prefs.feature_invite eq 'y' and $tiki_p_invite eq 'y'}
		{button href="tiki-list_invite.php" class="btn btn-default" _type="link" _icon_name="thumbs-up" _text="{tr}Invitation List{/tr}"}
	{/if}
</div>

{tabset name='tabs_admingroups'}

	{tab name="{tr}List{/tr}"}
		{* ----------------------- tab with list --------------------------------------- *}
		<h2>{tr}List of existing groups{/tr}</h2>

		{include file='find.tpl' find_show_num_rows='y'}

		{if $cant_pages > $maxRecords or !empty($initial) or !empty($find)}
			{initials_filter_links}
		{/if}

		{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}
		<form name="checkform" method="post">
			<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
				<table class="table table-striped table-hover">
					<tr>
						<th style="width: 20px;">{select_all checkbox_names='checked[]'}</th>
						<th>{self_link _sort_arg='sort_mode' _sort_field='id'}{tr}ID{/tr}{/self_link}</th>
						<th>{self_link _sort_arg='sort_mode' _sort_field='groupName'}{tr}Name{/tr}{/self_link}</th>
						<th>{tr}Inherits Permissions from{/tr}</th>

						{if $prefs.useGroupHome eq 'y'}
							<th>{self_link _sort_arg='sort_mode' _sort_field='groupHome'}{tr}Homepage{/tr}{/self_link}</th>
						{/if}

						<th>{self_link _sort_arg='sort_mode' _sort_field='userChoice'}{tr}User Choice{/tr}{/self_link}</th>
						<th></th>
					</tr>

					{section name=user loop=$users}
						<tr>
							<td class="checkbox-cell">
								{if $users[user].groupName ne 'Admins' and $users[user].groupName ne 'Anonymous' and $users[user].groupName ne 'Registered'}
									<input type="checkbox" name="checked[]" value="{$users[user].groupName|escape}">
								{/if}
							</td>
							<td class="id">{$users[user].id|escape}</td>
							<td class="text">
								<a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}&amp;cookietab=2{if $prefs.feature_tabs ne 'y'}#tab2{/if}" title="{tr}Edit{/tr}">{$users[user].groupName|escape}</a>
								<div class="text">{tr}{$users[user].groupDesc|escape|nl2br}{/tr}</div>
							</td>
							<td class="text">
								{foreach $users[user].included as $incl}
									<div>
										{if in_array($incl, $users[user].included_direct)}
											{$incl|escape}
										{else}
											<i>{$incl|escape}</i>
										{/if}
									</div>
								{/foreach}
							</td>

							{if $prefs.useGroupHome eq 'y'}
								<td class="text">
									<a class="link" href="tiki-index.php?page={$users[user].groupHome|escape:"url"}" title="{tr}Group Homepage{/tr}">{tr}{$users[user].groupHome}{/tr}</a>
								</td>
							{/if}

							<td class="text">{tr}{$users[user].userChoice}{/tr}</td>
							<td class="action">
								{capture name=group_actions}
									{strip}
										{$libeg}<a href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}&amp;cookietab=2{if $prefs.feature_tabs ne 'y'}#tab2{/if}">
											{icon name="edit" _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>{$liend}
										{$libeg}{permission_link mode=text group=$users[user].groupName count=$users[user].permcant}{$liend}
										{if $users[user].groupName ne 'Anonymous' and $users[user].groupName ne 'Registered' and $users[user].groupName ne 'Admins'}
											{$libeg}<a href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;group={$users[user].groupName|escape:"url"}">
												{icon name="remove" _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
											</a>{$liend}
										{/if}
									{/strip}
								{/capture}
								{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
								<a
									class="tips"
									title="{tr}Actions{/tr}"
									href="#"
									{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.group_actions|escape:"javascript"|escape:"html"}{/if}
									style="padding:0; margin:0; border:0"
								>
									{icon name='wrench'}
								</a>
								{if $js === 'n'}
									<ul class="dropdown-menu" role="menu">{$smarty.capture.group_actions}</ul></li></ul>
								{/if}
							</td>
						</tr>
					{/section}
				</table>
			</div>
			<div class="form-group" >
				<div class="input-group col-sm-6">
					<label for="submit_mult" class="control-label sr-only">{tr}Select action to perform with checked{/tr}</label>
						<select name="submit_mult" class="form-control">
							<option value="" selected="selected">{tr}Select action to perform with checked{/tr}...</option>
							<option value="remove_groups" >{tr}Remove{/tr}</option>
						</select>
					<div class="input-group-btn">
						<input type="submit" class="btn btn-primary" value="{tr}OK{/tr}">
					</div>
				</div>
			</div>
		</form>
		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{if $groupname}
		{capture assign=tabaddeditgroup_admgrp}{tr}Edit group{/tr} <i>{$groupname|escape}</i>{/capture}
	{else}
		{assign var=tabaddeditgroup_admgrp value="{tr}Add a New Group{/tr}"}
	{/if}

	{tab name="{$tabaddeditgroup_admgrp}"}
		{* ----------------------- tab with form --------------------------------------- *}

		{if !empty($user) and $prefs.feature_user_watches eq 'y' && !empty($groupname)}
			<div class="pull-right">
				{if not $group_info.isWatching}
					{self_link watch=$groupname _class="tips" _title="{$groupname}:{tr}Group is NOT being monitored. Click icon to START monitoring.{/tr}"}
						{icon name='watch' alt="{tr}Group is NOT being monitored. Click icon to START monitoring.{/tr}"}
					{/self_link}
				{else}
					{self_link unwatch=$groupname _class="tips" _title="{$groupname}:{tr}Group IS being monitored. Click icon to STOP monitoring.{/tr}"}
						{icon name='stop-watching' alt="{tr}Group IS being monitored. Click icon to STOP monitoring.{/tr}"}
					{/self_link}
				{/if}
			</div>
		{/if}
		<h2>{$tabaddeditgroup_admgrp}</h2>

		<form class="form-horizontal" action="tiki-admingroups.php" method="post">
			<div class="form-group">
				<label for="groups_group" class="control-label col-md-3">{tr}Group{/tr}</label>
				<div class="col-md-9">
					{if $groupname neq 'Anonymous' and $groupname neq 'Registered' and $groupname neq 'Admins'}
						<input type="text" name="name" id="groups_group" value="{$groupname|escape}" class="form-control">
					{else}
						<input type="hidden" name="name" id="groups_group" value="{$groupname|escape}">
						{$groupname|escape}
					{/if}
				</div>
			</div>
			<div class="form-group">
				<label for="groups_desc" class="control-label col-md-3">{tr}Description{/tr}</label>
				<div class="col-md-9">
					<textarea rows="5" name="desc" id="groups_desc" class="form-control">{$groupdesc|escape}</textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="groups_inc" class="control-label col-md-3">{tr}Inheritance{/tr}</label>
				<div class="col-md-9">
					{if $inc|@count > 20 and $hasOneIncludedGroup eq "y"}
						<ul>
							{foreach key=gr item=yn from=$inc}
								{if $yn eq 'y'}
									<li>{$gr|escape}</li>
								{/if}
							{/foreach}
						</ul>
					{/if}
					<select name="include_groups[]" id="groups_inc" multiple="multiple" size="8" class="form-control">
						{if !empty($groupname)}<option value="">{tr}None{/tr}</option>{/if}
						{foreach key=gr item=yn from=$inc}
							<option value="{$gr|escape}" {if $yn eq 'y'} selected="selected"{/if}>{$gr|truncate:"52"|escape}</option>
						{/foreach}
					</select>
					<div class="help-block">
						<p>{tr}Permissions will be inherited from these groups.{/tr}</p>
						{if $prefs.jquery_ui_chosen neq 'y'}
							<p>{tr}Use Ctrl+Click to select multiple options{/tr}</p>
						{/if}
					</div>
					{if $indirectly_inherited_groups|@count > 0}
						<p>{tr}Indirectly included groups:{/tr}</p>
						<ul>
							{foreach $indirectly_inherited_groups as $gr}
								<li>{$gr|escape}</li>
							{/foreach}
						</ul>
					{/if}
				</div>
			</div>
			{if $prefs.useGroupHome eq 'y'}
				<div class="form-group">
					<label for="groups_home" class="control-label col-md-3">{tr}Group Home{/tr}</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="home" id="groups_home" value="{$grouphome|escape}">
						{autocomplete element='#groups_home' type='pagename'}
						<div class="help-block">
							{tr}Use wiki page name or full URL.{/tr}
							{tr}For other Tiki features, use links relative to the Tiki root (such as <em>/tiki-forums.php</em>).{/tr}
						</div>
					</div>
				</div>
			{/if}
			{if $prefs.feature_categories eq 'y'}
				<div class="form-group">
					<label for="groups_defcat" class="control-label col-md-3">{tr}Default Category{/tr}</label>
					<div class="col-md-9">
						<select name="defcat" id="groups_defcat" class="form-control">
							<option value="" {if ($groupdefcat eq "") or ($groupdefcat eq 0)} selected="selected"{/if}>{tr}none{/tr}</option>
							{foreach $categories as $id=>$category}
								<option value="{$id|escape}" {if $id eq $groupdefcat}selected="selected"{/if}>{$category.categpath|escape}</option>
							{/foreach}
						</select>
						<div class="help-block">
							{tr}Default category assigned to uncategorized objects edited by a user with this default group.{/tr}
						</div>
					</div>
				</div>
			{/if}
			{if $prefs.useGroupTheme eq 'y'}
				<div class="form-group">
					<label for="groups_theme" class="control-label col-md-3">{tr}Group theme{/tr}</label>
					<div class="col-md-9">
						<select name="theme" id="groups_theme" class="form-control">
							<option value="" {if $grouptheme eq ""} selected="selected"{/if}>{tr}none{/tr} ({tr}Use site default{/tr})</option>
							{foreach from=$group_themes key=theme item=theme_name}
								<option value="{$theme|escape}" {if $grouptheme eq $theme}selected="selected"{/if}>{$theme_name}</option>
							{/foreach}
						</select>
					</div>
				</div>
			{/if}
			{if $prefs.groupTracker eq 'y'}
				<div class="form-group">
					<label for="groupstracker" class="control-label col-md-3">{tr}Group Information Tracker{/tr}</label>
					<div class="col-md-9">
						<select name="groupstracker" id="groupstracker" class="form-control">
							<option value="0">{tr}Choose a group tracker ...{/tr}</option>
							{foreach key=tid item=tit from=$trackers}
								<option value="{$tid}"{if $tid eq $grouptrackerid} {assign var="ggr" value="$tit"}selected="selected"{/if}>{$tit|escape}</option>
							{/foreach}
						</select>
						{if $grouptrackerid}
							<div>
								<select name="groupfield" class="form-control">
									<option value="0">{tr}choose a field ...{/tr}</option>
									{section name=ix loop=$groupFields}
										<option value="{$groupFields[ix].fieldId}"{if $groupFields[ix].fieldId eq $groupfieldid} selected="selected"{/if}>{$groupFields[ix].name|escape}</option>
									{/section}
								</select>
							</div>
						{/if}
						{if $grouptrackerid}
							{button href="tiki-admin_tracker_fields.php?trackerId=$grouptrackerid" _text="{tr}Admin{/tr} $ggr"}
						{else}
							{button href="tiki-list_trackers.php" _text="{tr}Admin{/tr} $ggr"}
						{/if}
					</div>
				</div>
			{/if}
			{if $prefs.userTracker eq 'y'}
				<div class="form-group">
					<label for="userstracker" class="control-label col-md-3">{tr}Users Information Tracker{/tr}</label>
					<div class="col-md-9">
						<select name="userstracker" id="userstracker" class="form-control">
							<option value="0">{tr}choose a users tracker ...{/tr}</option>
							{foreach key=tid item=tit from=$trackers}
								<option value="{$tid}"{if $tid eq $userstrackerid} {assign var="ugr" value="$tit"}selected="selected"{/if}>{$tit|escape}</option>
							{/foreach}
						</select>
						{if $userstrackerid or $prefs.javascript_enabled eq 'y'}
							<div>
								<select name="usersfield"{if empty($userstrackerid) and $prefs.javascript_enabled eq 'y' and $prefs.jquery_ui_chosen neq 'y'} style="display: none;"{/if} class="form-control">
									<option value="0">{tr}Choose a field ...{/tr}</option>
									{section name=ix loop=$usersFields}
										<option value="{$usersFields[ix].fieldId}"{if $usersFields[ix].fieldId eq $usersfieldid} selected="selected"{/if}>{$usersFields[ix].fieldId} - {$usersFields[ix].name|escape}</option>
									{/section}
								</select>
							</div>
							{jq}
								$("#userstracker").change(function () {
									$.getJSON($.service('tracker', 'list_fields'), {trackerId: $(this).val()}, function (data) {
										if (data && data.fields) {
											var $usersfield = $('select[name=usersfield]');
											$usersfield.empty().append('<option value="0">{tr}choose a field ...{/tr}</option>');
											var sel = '';
											$(data.fields).each(function () {
												if (this.type === 'u' && this.options_array[0] == 1) {
													sel = ' selected="selected"';
												} else {
													sel = '';
												}
												$usersfield.append('<option value="' + this.fieldId + '"' + sel + '>' + this.fieldId + ' - ' + this.name + '</option>');
											});
											if (jqueryTiki.chosen) {
												$usersfield.trigger("chosen:updated");
											} else {
												$usersfield.show();
											}
										}
									});
								});
							{/jq}
						{/if}
						{if $userstrackerid}
							{button href="tiki-admin_tracker_fields.php?trackerId=$userstrackerid" _text="{tr}Admin{/tr} $ugr"}
						{else}
							{button href="tiki-list_trackers.php" _text="{tr}Admin{/tr} $ugr"}
						{/if}
					</div>
				</div>
				<div class="form-group">
					<label for="registrationUserFieldIds" class="control-label col-md-3">{tr}Registration Fields{/tr}</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="registrationUsersFieldIds" value="{$registrationUsersFieldIds|escape}">
						<div class="help-block">
							<p>{tr}Users Information Tracker Fields Asked at Registration Time{/tr}</p>
							<p>{tr}fieldIds separated with colons (:){/tr}</p>
						</div>
					</div>
				</div>
				{if $prefs.feature_wizard_user eq 'y' and $groupname == 'Registered'}
					<div class="form-group">
						<label for="groups_group" class="control-label col-md-3">{tr}User Wizard Fields{/tr}</label>
						<div class="col-md-9">
							{tr}By default, the same fields as in Registration are used.{/tr} {tr _0="tiki-admin.php?page=login"}You can choose in the <a href="%0">Login admin panel</a> to show different fields in User Wizard than the ones asked at Registration Time{/tr}.</td>
						</div>
					</div>
				{/if}
			{/if}
			{if $groupname neq 'Anonymous' and $groupname neq 'Registered' and $groupname neq 'Admins'}
				<div class="form-group">
					<div class="col-md-9 col-md-offset-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="userChoice"{if $userChoice eq 'y'} checked="checked"{/if}>
								{tr}User can assign himself or herself to the group{/tr}
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="groups_group" class="control-label col-md-3">{tr}Expiry{/tr}</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="expireAfter" value="{$group_info.expireAfter|escape}">
						<div class="help-block">
							{tr}Amount of days after which the group will be unassigned from the users.{/tr}
						</div>
						<p>{tr}Or, users are automatically unassigned from the group at an anniversary date{/tr}</p>
						<input type="text" name="anniversary" class="form-control" value="{$group_info.anniversary|escape}">
						<div class="help-block">{tr}MMDD for annual or DD for monthly{/tr}</div>
					</div>
				</div>
				<div class="form-group">
					<label for="prorateInterval" class="control-label col-md-3">{tr}Pro-Rate Membership{/tr}</label>
					<div class="col-md-9">
						<select name="prorateInterval" class="form-control">
							<option value="day" {if $group_info.prorateInterval eq 'day'}selected="selected"{/if}>{tr}Day{/tr}</option>
							<option value="month" {if $group_info.prorateInterval eq 'month'}selected="selected"{/if}>{tr}Month{/tr}</option>
							<option value="year" {if $group_info.prorateInterval eq 'year'}selected="selected"{/if}>{tr}Year{/tr}</option>
						</select>
						<div class="help-block">
							{tr}Payment for membership extension is prorated at a minimum interval.{/tr}
						</div>
					</div>
				</div>
			{/if}
			<div class="form-group">
				<label for="groups_group" class="control-label col-md-3">{tr}Group{/tr}</label>
				<div class="col-md-9">
					<input class="form-control" type="text" size="40" name="emailPattern" value="{$group_info.emailPattern|escape}">
					<div class="help-block">
						<p>{tr}Users are automatically assigned at registration in the group if their emails match the pattern.{/tr}</p>
						<p>{tr}Example:{/tr} /@(tw.org$)|(tw\.com$)/</p>
					</div>
				</div>
			</div>

			{if $group ne ''and $groupname neq 'Anonymous'}
				<div class="form-group">
					<label for="groups_group" class="control-label col-md-6">{tr}Assign group <em>management</em> permissions{/tr}</label>
					<div class="col-md-6">
						{self_link _script="tiki-objectpermissions.php" objectType="group" objectId=$groupname objectName=$groupname permType="group"}
							{icon _text="{tr}Assign Permissions{/tr}" name="key"}
						{/self_link}
					</div>
				</div>
			{/if}

			<div class="submit form-group">
				<div class="col-md-9 col-md-offset-3">
					{if $group ne ''}
						<input type="hidden" name="olgroup" value="{$group|escape}">
						<input type="submit" class="btn btn-primary" name="save" value="{tr}Save{/tr}">
					{else}
						<input type="submit" class="btn btn-primary" name="newgroup" value="{tr}Add{/tr}">
					{/if}
				</div>
			</div>
			<br><br>

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
				<br><br>
			{/if}
		</form>
	{/tab}


	{if $groupname}
		{tab name="{tr}Members{/tr}"}
		{* ----------------------- tab with memberlist --------------------------------------- *}
			<h2>{tr}Members List:{/tr} {$groupname|escape}</h2>
			<form name="checkform" method="post">
				<input type="hidden" name="group" value="{$group|escape}">
				<div class="table-responsive">
					<table class="table">
						<tr>
							<th class="auto">{if $memberslist}{select_all checkbox_names='members[]'}{/if}</th>
							<th>{self_link _sort_arg='sort_mode_member' _sort_field='login'}{tr}User{/tr}{/self_link}</th>
							<th>{self_link _sort_arg='sort_mode_member' _sort_field='created'}{tr}Assign{/tr}{/self_link}</th>
							<th>{self_link _sort_arg='sort_mode_member' _sort_field='expire'}{tr}Expire{/tr}{/self_link}</th>
							<th>{tr}Action{/tr}</th>
						</tr>

						<tr>
							{foreach from=$memberslist item=member}
								<tr>
								<td class="checkbox-cell"><input type="checkbox" name="members[]" value="{$member.userId}"></td>
								<td class="username">{$member.login|userlink}</td>
								<td class="date">{$member.created|tiki_short_datetime}</td>
								<td class="date">{if !empty($member.expire)}{$member.expire|tiki_short_datetime}{/if}</td>
								<td class="action">
									<a href="tiki-adminusers.php?user={$member.userId|escape:"url"}&amp;cookietab=2{if $prefs.feature_tabs ne 'y'}#tab2{/if}"
									   class="link tips"
									   title="{$member.login}:{tr}Edit user{/tr}">
										{icon name="edit"}
									</a>
									{if $groupname neq 'Registered'}
										<a href="tiki-adminusers.php?user={$member.login|escape:"url"}&amp;action=removegroup&amp;group={$groupname|escape:url}"
										   class="link tips"
										   title="{$member.login}:{tr}Remove from group{/tr}">
											{icon name="remove"}
										</a>
									{/if}
								</td>
								</tr>
							{/foreach}
					</table>
				</div>

				{if $groupname neq 'Registered'}
				<label>{tr}Perform action with checked:{/tr}
					<select name="submit_mult_members">
						<option value="" />
						<option value="unassign">{tr}Unassign{/tr}</option>
					</select>
				</label>
				<input type="submit" class="btn btn-default btn-sm" name="unassign_members" value="{tr}OK{/tr}">
				{/if}
			</form>

			{pagination_links cant=$membersCount step=$prefs.maxRecords offset=$membersOffset offset_arg='membersOffset'}{/pagination_links}

			<div class="box">{$membersCount} {tr}users in group{/tr} {$groupname|escape}</div>

			<h2>{tr}Banned members List:{/tr} {$groupname|escape}</h2>
			<div class="table-responsive">
				<table class="table">
					<tr>
						<th>{tr}User{/tr}</th>
						<th>{tr}Action{/tr}</th>
					</tr>

					<tr>
						{foreach from=$bannedlist item=member}
							<tr>
							<td class="username">{$member|userlink}</td>
							<td class="action">
								{self_link user=$member|escape:"url" action=unbanuser group=$groupname|escape:url _title="{tr}Unban user{/tr}"}
									{icon name="remove"}
								{/self_link}
							</td>
							</tr>
						{/foreach}
				</table>
			</div>
			{if ! empty($userslist)}
				<h2>{tr}Add or Ban members to:{/tr} {$groupname|escape}</h2>
				<form method="post" action="tiki-admingroups.php">
					<p>
						<input type="hidden" name="group" value="{$groupname|escape}">
						<select name="user">
							{foreach from=$userslist item=iuser}
								<option>{$iuser|escape}</option>
							{/foreach}
						</select>
						<input type="submit" class="btn btn-default btn-sm" name="adduser" value="{tr}Add to group{/tr}">
						<input type="submit" class="btn btn-default btn-sm" name="banuser" value="{tr}Ban user from group{/tr}">
					</p>
				</form>
			{/if}
		{/tab}
	{/if}

	{if $groupname}
		{tab name="{tr}Import/Export{/tr}"}
			{* ----------------------- tab with import/export --------------------------------------- *}
			<form method="post" action="tiki-admingroups.php" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" name="group" value="{$groupname|escape}">
				{if $errors}
					<div class="simple highlight">
						{foreach from=$errors item=e}
							{$e}<br>
						{/foreach}
					</div>
				{/if}

				<h2>{tr}Download CSV export{/tr}</h2>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{tr}Charset encoding{/tr}</label>
                    <div class="col-sm-7">
                        <select name="encoding" class="form-control">
                            <option value="UTF-8" selected="selected">{tr}UTF-8{/tr}</option>
                            <option value="ISO-8859-1">{tr}ISO-8859-1{/tr}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{tr}Fields{/tr}</label>
                    <div class="col-sm-7">
                       <div class="col-sm-12">
                           <input type="checkbox" name="username" checked="checked"> {tr}Username{/tr}
                       </div>
                        <div class="col-sm-12">
                            <input type="checkbox" name="email"> {tr}Email{/tr}
                        </div>
                        <div class="col-sm-12">
                            <input type="checkbox" name="lastLogin"> {tr}Last login{/tr}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-7">
                        <input type="submit" class="btn btn-default btn-sm" name="export" value="{tr}Export{/tr}">
                    </div>
                </div>
                <br>
				<h2>{tr}Batch upload (CSV file){/tr}</h2>
                <br>
				<h3>{tr}Assign users to group:{/tr} {$groupname|escape} </h3>
				{remarksbox type="tip" title="{tr}Tip{/tr}"}
					{tr}Each user in the file must already exist.{/tr}<br>{tr}To create users or/and assign them to groups, got to <a href="tiki-adminusers.php">admin->users</a>{/tr}
				{/remarksbox}
                <div class="form-group">
                    <label class="col-sm-3 control-label">{tr}CSV File{/tr}<a title="{tr}Help{/tr}" {popup text='user<br>user1<br>user2'}>{icon name='help'}</a></label>
                    <div class="col-sm-7">
                        <input name="csvlist" type="file" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-7">
                        <input type="submit" class="btn btn-default btn-sm" name="import" value="{tr}Import{/tr}">
                    </div>
                </div>
			</form>
		{/tab}
	{/if}

{/tabset}
