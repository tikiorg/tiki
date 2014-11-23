{* $Id$ *}

{title help="Users+Management" admpage="login" url="tiki-adminusers.php"}{tr}Admin Users{/tr}{/title}

<div class="t_navbar form-group">
	{if $tiki_p_admin eq 'y'} {* only full admins can manage groups, not tiki_p_admin_users *}
		{button href="tiki-admingroups.php" class="btn btn-default" _text="{tr}Admin Groups{/tr}"}
	{/if}
	{button class="btn btn-default" _text="{tr}Admin Users{/tr}"}
	{if $tiki_p_admin eq 'y'}
	{permission_link mode=button label="{tr}Manage permissions{/tr}"}
	{/if}
	{if isset($userinfo.userId)}
		{button href="?add=1" class="btn btn-default" _text="{tr}Add a New User{/tr}"}
	{/if}
	{if $prefs.feature_invite eq 'y' and $tiki_p_invite eq 'y'}
		{button href="tiki-list_invite.php" class="btn btn-default" _text="{tr}Invitation List{/tr}"}
	{/if}
</div>

{if $prefs.feature_intertiki eq 'y' and ($prefs.feature_intertiki_import_groups eq 'y' or $prefs.feature_intertiki_import_preferences eq 'y')}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
		{if $prefs.feature_intertiki_import_groups eq 'y'}{tr}Since this Tiki site is in slave mode and imports groups, the master groups will be automatically reimported at each login{/tr}{/if}
		{if $prefs.feature_intertiki_import_preferences eq 'y'}{tr}Since this Tiki site is in slave mode and imports preferences, the master user preferences will be automatically reimported at each login{/tr}{/if}
	{/remarksbox}
{/if}

{if $tikifeedback}
	{remarksbox type="feedback" title="{tr}Feedback{/tr}"}
	{section name=n loop=$tikifeedback}
	{tr}{$tikifeedback[n].mes|escape}{/tr}
	<br>
	{/section}{/remarksbox}
{/if}

{if !empty($added) or !empty($discarded) or !empty($discardlist)}
	{remarksbox type="feedback" title="{tr}Batch Upload Results{/tr}"}
		{tr}Updated users{/tr} {$added}
		{if $discarded != ""}- {tr}Rejected users{/tr} {$discarded}{/if}
		<br>
		<br>
		{if $discardlist != ''}
			<div class="table-responsive">
				<table class="table normal">
					<tr>
						<th>{tr}Username{/tr}</th>
						<th>{tr}Reason{/tr}</th>
					</tr>
					{section name=reject loop=$discardlist}
						<tr class="odd">
							<td class="username">{$discardlist[reject].login}</td>
							<td class="text">{$discardlist[reject].reason}</td>
						</tr>
					{/section}
				</table>
			</div>
		{/if}

		{if $errors}
			<br>
			{section name=ix loop=$errors}
				{$errors[ix]}<br>
			{/section}
		{/if}
	{/remarksbox}
{/if}

{tabset name='tabs_adminuers'}

	{* ---------------------- tab with list -------------------- *}
	{tab name="{tr}Users{/tr}"}
		<h2>{tr}Users{/tr}</h2>
		{if !$tsOn}
			<form method="get" class="form-horizontal small" action="tiki-adminusers.php">
				<div class="form-group">
					<label class="control-label col-sm-4" for="find">{tr}Find{/tr}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control input-sm" id="find" name="find" value="{$find|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="numrows">{tr}Number of displayed rows{/tr}</label>
					<div class="col-sm-8">
						<input class="form-control input-sm" type="number" id="numrows" name="numrows" value="{$numrows|escape}">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-8 col-sm-offset-4">
						<a href="javascript:toggleBlock('search')" class="link">{icon _id='add' alt="{tr}more{/tr}"}&nbsp;{tr}More Criteria{/tr}</a>
					</div>
				</div>
				{autocomplete element='#find' type='username'}
				<div class="col-sm-12" id="search" {if $filterGroup or $filterEmail}style="display:block;"{else}style="display:none;"{/if}>
					<div class="form-group">
						<label class="control-label col-sm-4" for="filterGroup">{tr}Group (direct){/tr}</label>
						<div class="col-sm-8">
							<select class="form-control input-sm" name="filterGroup" id="filterGroup">
								<option value=""></option>
								{section name=ix loop=$all_groups}
									{if $all_groups[ix] != 'Registered' && $all_groups[ix] != 'Anonymous'}
										<option value="{$all_groups[ix]|escape}" {if $filterGroup eq $all_groups[ix]}selected{/if}>{$all_groups[ix]|escape}</option>
									{/if}
								{/section}
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-8">
							<div class="checkbox">
								<label>
									<input id="filterEmailNotConfirmed" name="filterEmailNotConfirmed" type="checkbox"{if !empty($smarty.request.filterEmailNotConfirmed)} checked="checked"{/if}>{tr}Email not confirmed{/tr}
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-8">
							<div class="checkbox">
								<label>
									<input id="filterNeverLoggedIn" name="filterNeverLoggedIn" type="checkbox"{if !empty($smarty.request.filterNeverLoggedIn)} checked="checked"{/if}>{tr}Never logged in{/tr}
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-8">
							<div class="checkbox">
								<label>
									<input id="filterNotValidated" name="filterNotValidated" type="checkbox"{if !empty($smarty.request.filterNotValidated)} checked="checked"{/if}>{tr}User not validated{/tr}
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-8 col-sm-offset-4">
						<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
						<input type="submit" class="btn btn-default btn-sm" value="{tr}Find{/tr}" name="search">
					</div>
				</div>
			</form>
		{/if}
		{if ($cant > $numrows or !empty($initial)) && !$tsOn}
			{initials_filter_links}
		{/if}

	<form class="form-horizontal" name="checkform" method="post" action="{$smarty.server.PHP_SELF|escape}">
		<div id="{$ts_tableid}-div" {if $tsOn}style="visibility:hidden;"{/if}>
			<div class="table-responsive user-table">
            	<table id="{$ts_tableid}" class="table normal table-striped table-hover">
					{* Note: th element ids here need to match those at /lib/core/Table/Settings/TikiAdminusers.php
					for tablesorter to work properly *}
					<thead>
						<tr>
							<th id="checkbox" {if $prefs.mobile_mode eq "y"}style="width:40px;"{else}class="auto"{/if}>
								{if $users}
								   {select_all checkbox_names='checked[]'}
								{/if}
							</th>
							<th id="user">{self_link _sort_arg='sort_mode' _sort_field='login'}{tr}User{/tr}{/self_link}</th>
							{if $prefs.login_is_email neq 'y'}
								<th id="email">{self_link _sort_arg='sort_mode' _sort_field='email'}{tr}Email{/tr}{/self_link}</th>
							{/if}
							{if $prefs.auth_method eq 'openid'}
								<th id="openid">{self_link _sort_arg='sort_mode' _sort_field='openID'}{tr}OpenID{/tr}{/self_link}</th>
							{/if}
							<th id="lastlogin">{self_link _sort_arg='sort_mode' _sort_field='currentLogin'}{tr}Last login{/tr}{/self_link}</th>
							<th id="registered">{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Registered{/tr}{/self_link}</th>
							<th id="groups">{tr}Groups{/tr}</th>
							<th id="actions">{tr}Actions{/tr}</th>
						</tr>
					</thead>
					<tbody>
					{section name=user loop=$users}
						{if $users[user].editable}
							{capture assign=username}{$users[user].user|escape}{/capture}
								<tr>
									<td class="checkbox-cell">
										{if $users[user].user ne 'admin'}
											<input type="checkbox" name="checked[]" value="{$users[user].user|escape}" {if isset($users[user].checked) && $users[user].checked eq 'y'}checked="checked" {/if}>
										{/if}
									</td>

									<td class="username">
										{capture name=username}{$users[user].user|username}{/capture}
										<a class="link" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}&amp;user={$users[user].userId}{if $prefs.feature_tabs ne 'y'}#2{/if}" title="{tr}Edit Account Settings{/tr} {$smarty.capture.username}">
										{$users[user].user|escape}
										</a>
										{if $prefs.user_show_realnames eq 'y' and $smarty.capture.username ne $users[user].user}
											<div class="subcomment">
												{$smarty.capture.username|escape}
											</div>
										{/if}
									</td>

									{if $prefs.login_is_email ne 'y'}
										<td class="email">{$users[user].email}</td>
									{/if}
									{if $prefs.auth_method eq 'openid'}
										<td class="text">{$users[user].openid_url|default:"{tr}N{/tr}"}</td>
									{/if}
									<td class="text">
										{if $users[user].currentLogin eq ''}
											{capture name=when}{$users[user].age|duration_short}{/capture}
											{tr}Never{/tr} <em>({tr _0=$smarty.capture.when}Registered %0 ago{/tr})</em>
										{else}
											{$users[user].currentLogin|tiki_short_datetime}
										{/if}

										{if $users[user].waiting eq 'u'}
											<br>
											{tr}Need to validate email{/tr}
										{/if}
									</td>
									<td class="text">
										{$users[user].registrationDate|tiki_short_datetime}
									</td>

									<td class="text">
										{foreach from=$users[user].groups key=grs item=what name=gr}
											<div style="white-space:nowrap">
												{if $grs != "Anonymous" and ($tiki_p_admin eq 'y' || in_array($grs, $all_groups))}
													{if $tiki_p_admin eq 'y'}
														<a class="link" {if isset($link_style)}{$link_style}{/if} href="tiki-admingroups.php?group={$grs|escape:"url"}" title={if $what eq 'included'}"{tr}Edit Included Group{/tr}"{else}"{tr}Edit Group{/tr} {$grs|escape}"{/if}>{$grs|escape}</a>
													{else}
														{$grs|escape}
													{/if}
													{if $what eq 'included'}<span class="label label-info">{tr}Included{/tr}</span>{/if}
													{if $grs eq $users[user].default_group}<small>({tr}default{/tr})</small>{/if}
													{if $what ne 'included' and $grs != "Registered"}
														{capture assign=title}{tr _0=$username _1=$grs|escape}Remove %0 from %1{/tr}{/capture}{*FIXME*}
														{self_link _class='link' user=$users[user].user action='removegroup' group=$grs _icon='cross' _title=$title}{/self_link}
													{else}
														{icon _id='bullet_white'}
													{/if}
													{if !$smarty.foreach.gr.last}<br>{/if}
												{/if}
											</div>
										{/foreach}
									</td>

									<td class="action">

										<a class="link" href="tiki-assignuser.php?assign_user={$users[user].user|escape:url}" title="{tr}Assign to group{/tr}">{capture assign=alt}{tr _0=$username}Assign %0 to groups{/tr}{/capture}{*FIXME*}{icon _id='group_key' alt=$alt}</a>

										<a class="link" href="{query _type='relative' user=$users[user].userId}" title="{tr _0=$username}Edit Account Settings: %0{/tr}">{capture assign=alt}{tr _0=$username}Edit Account Settings: %0{/tr}{/capture}{*FIXME*}{icon _id='page_edit' alt=$alt}</a>

										{if $prefs.feature_userPreferences eq 'y' || $user eq 'admin'}
											<a class="link" href="tiki-user_preferences.php?userId={$users[user].userId}" title="{tr _0=$username}Change user preferences: %0{/tr}">{capture assign=alt}{tr _0=$username}Change user preferences: %0{/tr}{/capture}{icon _id='wrench' alt=$alt}</a>
										{/if}
										{if $users[user].user eq $user or $users[user].user_information neq 'private' or $tiki_p_admin eq 'y'}
											{capture assign=title}{tr _0=$username}User Information: %0{/tr}{/capture}{*FIXME*}
											<a class="link" href="tiki-user_information.php?userId={$users[user].userId}" title="{$title}"{if $users[user].user_information eq 'private'} style="opacity:0.5;"{/if}>{icon _id='help' alt=$title}</a>
										{/if}

										{if $users[user].user ne 'admin'}
											<a class="link" href="{$smarty.server.PHP_SELF}?{query action=delete user=$users[user].user}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
											{if $users[user].waiting eq 'a'}
												<a class="link" href="tiki-login_validate.php?user={$users[user].user|escape:url}&amp;pass={$users[user].valid|escape:url}" title="{tr _0=$users[user].user|username}Validate user: %0{/tr}">{capture assign=alt}{tr _0=$users[user].user|username}Validate user: %0{/tr}{/capture}{*FIXME*}{icon _id='accept' alt=$alt}</a>
											{/if}
											{if $users[user].waiting eq 'u'}
												<a class="link" href="tiki-confirm_user_email.php?user={$users[user].user|escape:url}&amp;pass={$users[user].provpass|md5|escape:url}" title="{tr _0=$users[user].user|username}Confirm user email: %0{/tr}">{capture assign=alt}{tr _0=$username}Confirm user email: %0{/tr}{/capture}{*FIXME*}{icon _id='email_go' alt=$alt}</a>
											{/if}
											{if $prefs.email_due > 0 and $users[user].waiting ne 'u' and $users[user].waiting ne 'a'}
												<a class="link" href="tiki-adminusers.php?user={$users[user].user|escape:url}&amp;action=email_due" title="{tr}Invalidate email{/tr}">{icon _id='email_cross' alt="{tr}Invalidate email{/tr}"}</a>
											{/if}
										{/if}
										{if !empty($users[user].openid_url)}
											{self_link userId=$users[user].userId action='remove_openid' _title="{tr}Remove link with OpenID account{/tr}" _icon="img/icons/openid_remove"}{/self_link}
										{/if}
										{if $prefs.mobile_mode eq "y"}</div>{/if} {* mobile *}
									</td>
								</tr>
							{/if}
						{sectionelse}
							{norecords _colspan=8}
						{/section}
						</tbody>
					</table>
				</div>
				{if $users}
					<div class="form-group" id="submit_mult">
						<label>{tr}Perform action with checked{/tr}</label>
						<select class="submit_mult" name="submit_mult">
							<option value="" selected="selected">-</option>
							<option value="remove_users" >{tr}Remove{/tr}</option>
							{if $prefs.feature_banning == 'y'}
								<option value="ban_ips">{tr}Ban IPs{/tr}</option>
								<option value="remove_users_and_ban">{tr}Remove users and Ban IPs{/tr}</option>
							{/if}
							{if $prefs.feature_wiki_userpage == 'y'}
								<option value="remove_users_with_page">{tr}Remove users and their userpages{/tr}</option>
								{if $prefs.feature_banning == 'y'}
									<option value="remove_users_with_page_and_ban">{tr}Remove users, their userpages and Ban IPs{/tr}</option>
								{/if}
							{/if}
							<option value="assign_groups" >{tr}Change group assignments{/tr}</option>
							<option value="set_default_groups">{tr}Set default groups{/tr}</option>
							{if $prefs.feature_wiki == 'y'}
								<option value="emailChecked">{tr}Send wiki page content by email{/tr}</option>
							{/if}
						</select>
						<button type="submit" style="display: none" class="btn btn-default btn-sm submit_mult">{tr}OK{/tr}</button>
					</div>
					<div id="gm" style="display:none">
						<h4>{tr}Change group assignments for selected users{/tr}</h4>
						<div class="form-group">
							<label class="control-label col-sm-2">{tr}Action{/tr}</label>
							<div class="col-sm-4">
								<select class="gm" name="group_management" disabled="disabled" class="form-control">
									<option value="add">{tr}Assign selected{/tr}</option>
									<option value="remove">{tr}Remove selected{/tr}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{tr}Groups{/tr}</label>
							<div class="col-sm-10">
								<select name="checked_groups[]" multiple="multiple" size="20" class="form-control">
									{section name=ix loop=$all_groups}
										{if $all_groups[ix] != 'Anonymous' && $all_groups[ix] != 'Registered'}
											<option value="{$all_groups[ix]|escape}">{$all_groups[ix]|escape}</option>
										{/if}
									{/section}
								</select>
								{if $prefs.jquery_ui_chosen neq 'y'}<div class="help-block">{tr}Use Ctrl+Click to select multiple options{/tr}</div>{/if}
							</div>
						</div>
						<div class="form-group">
							<div class="submit col-sm-4 col-sm-offset-2">
								<button type="submit" class="btn btn-default btn-sm gm" disabled="disabled">{tr}OK{/tr}</button>
								<button type="button" style="display: none" class="btn btn-default cancel-choice">{tr}Cancel{/tr}</button>
							</div>
						</div>
					</div>
					<div id="dg" style="display:none">
						<h4>{tr}Set default groups for selected users{/tr}</h4>
						<div class="form-group">
							<label class="control-label col-sm-2">{tr}Group{/tr}</label>
							<div class="col-sm-4">
								<select class="dg" name="checked_group" disabled="disabled" size="20" class="form-control">
									{section name=ix loop=$all_groups}
										{if $all_groups[ix] != 'Anonymous'}
											<option value="{$all_groups[ix]|escape}">{$all_groups[ix]|escape}</option>
										{/if}
									{/section}
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="submit col-sm-4 col-sm-offset-2">
								<button type="submit" disabled="disabled" class="btn btn-default btn-sm dg">{tr}OK{/tr}</button>
								<button type="button" style="display: none" class="btn btn-default cancel-choice">{tr}Cancel{/tr}</button>
								<input type="hidden" class="dg" disabled="disabled" name="set_default_groups" value="y">
							</div>
						</div>
					</div>
					<div id="emc" style="display:none">
						<h4>{tr}Send wiki page content by email to selected users{/tr}</h4>
						<div class="form-group">
							<label class="control-label col-sm-2">{tr}Email Template{/tr}</label>
							<div class="col-sm-10">
								<input class="emc form-control" type="text" disabled="disabled" name="wikiTpl">
								<div class="help-block">{tr}Template wiki page:
										The wiki page must have a page description, which is used as the subject of the email.
										Enable the page descriptions feature at Configuration Panels &gt; Wiki.{/tr}
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{tr}Bcc{/tr}</label>
							<div class="col-sm-10">
								<input class="emc form-control" disabled="disabled" type="text" name="bcc">
								<div class="help-block">{tr}Enter a valid email to send a blind copy to (optional).{/tr}</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-2 submit">
								<button type="submit" disabled="disabled" class="btn btn-default btn-sm emc">{tr}OK{/tr}</button>
								<button type="button" style="display: none" class="btn btn-default cancel-choice">{tr}Cancel{/tr}</button>
								<input class="emc" disabled="disabled" type="hidden" name="emailChecked" value="y">
							</div>
						</div>
					</div>
	{jq}
		$('select.submit_mult').change(function() {
			if ($.inArray(this.value, ['assign_groups', 'set_default_groups', 'emailChecked']) > -1) {
				$('div#submit_mult').hide();
				$('.submit_mult').prop('disabled', true).trigger("chosen:updated");
				$('button.cancel-choice').show();
				if (this.value == 'assign_groups') {
					$('div#gm').show();
					$('.gm').prop('disabled', false).trigger("chosen:updated");
				} else if (this.value == 'set_default_groups') {
					$('div#dg').show();
					$('.dg').prop('disabled', false).trigger("chosen:updated");
				} else if (this.value == 'emailChecked') {
					$('div#emc').show();
					$('.emc').prop('disabled', false).trigger("chosen:updated");
				}
			} else if ($.inArray(this.value, ['ban_ips', 'remove_users', 'remove_users_and_ban', 'remove_users_with_page', 'remove_users_with_page_and_ban']) > -1) {
				$('button.submit_mult').show();
			}
		});

		$('button.cancel-choice').click(function() {
			$('div#gm, div#dg, div#emc').hide();
			$('.gm, dg, .emc').prop('disabled', true).trigger("chosen:updated");
			$('.submit_mult').prop('disabled', false).trigger("chosen:updated");
			$('select.submit_mult').val('').trigger("chosen:updated");
			$('div#submit_mult').show();
			$('button.cancel-choice').hide();
		});
	{/jq}
				{/if}
			</div>
			<input type="hidden" name="find" value="{$find|escape}">
			<input type="hidden" name="numrows" value="{$numrows|escape}">
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
			<input type="hidden" {if $tsOn}id="{$ts_offsetid|escape}" {/if}name="offset" value="{$offset|escape}">
			<input type="hidden" {if $tsOn}id="{$ts_countid|escape}" {/if}name="count" value="{$cant|escape}">
		</form>
		{if !$tsOn}
			{pagination_links cant=$cant step=$numrows offset=$offset}{/pagination_links}
		{/if}
	{/tab}


	{* ---------------------- tab with form -------------------- *}
	<a name="2" ></a>
	{if isset($userinfo.userId) && $userinfo.userId}
		{capture assign=add_edit_user_tablabel}{tr}Edit user{/tr} <i>{$userinfo.login|escape}</i>{/capture}
	{else}
		{assign var=add_edit_user_tablabel value="{tr}Add a New User{/tr}"}
	{/if}

	{tab name=$add_edit_user_tablabel}
		{if $prefs.feature_user_encryption eq 'y'}
			{remarksbox type="warning" title="{tr}Warning: User Encryption is Active{/tr}"}
			{tr}The feature User Encryption stores encrypted user information, such as password used to connect to externalsystems.
				If the password is changed, it will destroy the user's decryption key, and make the data unreadable.
				The user will be forced to re-enter the passwords and other data that may be encrypted.{/tr}</a>.
			{/remarksbox}
		{/if}
		{if isset($userinfo.userId) && $userinfo.userId}
			<h2>{tr}Edit user{/tr} {$userinfo.login|escape}</h2>
			{if $userinfo.login ne 'admin' and $userinfo.editable}
				{assign var=thisloginescaped value=$userinfo.login|escape:'url'}
				{button href="tiki-assignuser.php?assign_user=$thisloginescaped" _text="{tr}Assign user to Groups{/tr}"}
			{/if}
		{else}
			<h2>{tr}Add a New User{/tr}</h2>
		{/if}
		{if $userinfo.editable}
			<form class="form form-horizontal" action="tiki-adminusers.php" method="post" enctype="multipart/form-data" name="RegForm" autocomplete="off">
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label" for="login">{if $prefs.login_is_email eq 'y'}{tr}Email{/tr}{else}{tr}User{/tr}{/if}</label>
					<div class="col-sm-7 col-md-6">
						{if $userinfo.login neq 'admin'}
							<input type="text" id='login' class="form-control" name='login' value="{$userinfo.login|escape}">
							{if $prefs.login_is_email eq 'y'}
								<br>
								<em>{tr}Use the email as username{/tr}.</em>
							{elseif $prefs.lowercase_username eq 'y'}
								<br>
								<em>{tr}Lowercase only{/tr}</em>.
							{/if}
							{if isset($userinfo.userId) && $userinfo.userId}
								<p>
									{icon _id='exclamation' alt="{tr}Warning{/tr}" style="vertical-align:middle"}
									<em>{tr}Warning: changing the username could require the user to change his password (for user registered with an old Tiki&lt;=1.8){/tr}</em>
									{if $prefs.feature_intertiki_server eq 'y'}
										<br>
										<i>{tr}Warning: it will mess with slave intertiki sites that use this one as master{/tr}</i>
									{/if}
								</p>
							{/if}
						{else}
							<input type="hidden" class="form-control" name='login' value="{$userinfo.login|escape}">{$userinfo.login}
						{/if}
					</div>
				</div>

				{*
					No need to specify user password or to ask him to change it, if :
					--> Tiki is using the Tiki + PEAR Auth systems
					--> AND Tiki won't create the user in the Tiki auth system
					--> AND Tiki won't create the user in the ldap
				*}
				{if $prefs.auth_method eq 'ldap' and ( $prefs.ldap_create_user_tiki eq 'n' or $prefs.ldap_skip_admin eq 'y' ) and $prefs.ldap_create_user_ldap eq 'n' and $userinfo.login neq 'admin' and $auth_ldap_permit_tiki_users eq 'n'}
					<div class="form-group">
						<div class="col-sm-offset-2">
							<b>{tr}No password is required{/tr}</b>
							<br>
							<i>{tr}Tiki is configured to delegate the password managment to LDAP.{/tr}</i>
						</div>
					</div>
				{else}
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label" for="pass1">{tr}New Password{/tr}</label>
						<div class="col-sm-7 col-md-6">
							<input type="password" class="form-control" placeholder="New Password" name="pass" id="pass1"
									onkeypress="regCapsLock(event)" onkeyup="runPassword(this.value, 'mypassword');{if 0 and $prefs.feature_ajax eq 'y'}check_pass();{/if}">
						</div>
						<div class="col-md-4">
							<div id="mypassword_text"></div>
							<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div>
						</div>
						<p class="col-md-4 col-sm-10 help-block">{tr}Minimum 5 characters long.{/tr}</p>
					</div>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label" for="pass2">{tr}Repeat Password{/tr}</label>
						<div class="col-sm-7 col-md-6">
							<input type="password" class="form-control" name="pass2" id="pass2" placeholder="Repeat Password">
						</div>
					</div>
					{if ! ( $prefs.auth_method eq 'ldap' and ( $prefs.ldap_create_user_tiki eq 'n' or $prefs.ldap_skip_admin eq 'y' ) and $prefs.ldap_create_user_ldap eq 'n' )}
						<div class="form-group">
							<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
								<span id="genPass">{button href="#" _onclick="genPass('genepass');runPassword(document.RegForm.genepass.value, 'mypassword');checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');return false;" _text="{tr}Generate a password{/tr}"}</span>
							</div>
						</div>
						<div id="genepass_div" class="form-group" style="display: none">
							<label class="col-sm-3 col-md-2 control-label" for="pass2">{tr}Generated Password{/tr}</label>
							<div class="col-sm-7 col-md-6">
								<input id='genepass' class="form-control" name="genepass" type="text" tabindex="0">
							</div>
						</div>

						{jq}
							$("#genPass").click(function () {
							$('#pass1, #pass2').val('');
							$('#mypassword_text, #mypassword2_text').hide();
							$("#genepass_div").show();
							});
							$("#pass1, #pass2").change(function () {
							$('#mypassword_text, #mypassword2_text').show();
							document.RegForm.genepass.value='';
							$("#genepass_div").hide();
							});
						{/jq}
					{/if}
					{if $userinfo.login neq 'admin' && $prefs.change_password neq 'n'}
						<div class="form-group">
							<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="pass_first_login"{if isset($userinfo.pass_confirm) && $userinfo.pass_confirm eq '0'} checked="checked"{/if}>
										{tr}User must change password at next login{/tr}
									</label>
								</div>
							</div>
						</div>
					{/if}
				{/if}
				{if $prefs.login_is_email neq 'y'}
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label" for="pass1">{tr}Email{/tr}</label>
						<div class="col-sm-7 col-md-6">
							<input type="text" class="form-control" id="email" name="email" size="30" value="{$userinfo.email|escape}">
						</div>
					</div>
				{/if}
				{if $userinfo.login neq 'admin' and ($prefs.validateUsers eq 'y' or $prefs.validateRegistration eq 'y')}
					<div class="form-group">
						<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="need_email_validation" {if ($userinfo.login eq '' and ($prefs.validateUsers eq 'y' or $prefs.validateRegistration eq 'y')) or $userinfo.provpass neq ''}checked="checked"{/if}>
									{tr}Send an email to the user in order to allow him to validate his account.{/tr}
								</label>
								{if empty($prefs.sender_email)}
									<div class="help-block"><span class="highlight">{tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a>{/tr}</span></div>
								{/if}
							</div>
						</div>
					</div>
				{/if}
				{if isset($userinfo.userId) && $userinfo.userId != 0}
					<table class="table table-striped table-condensed small">

					{if $userinfo.created neq $userinfo.registrationDate}
						<tr>
							<td>{tr}Created{/tr}</td>
							<td>{$userinfo.created|tiki_long_datetime}</td>
						</tr>
					{/if}
					<tr>
						<td>{tr}Registered{/tr}</td><td>{if $userinfo.registrationDate}{$userinfo.registrationDate|tiki_long_datetime}{/if}</td>
					</tr>
					<tr>
						<td>{tr}Pass confirmed{/tr}</td><td>{if isset($userinfo.pass_confirm) && $userinfo.pass_confirm}{$userinfo.pass_confirm|tiki_long_datetime|default:'Never'}{/if}</td>
					</tr>

					{if $prefs.email_due > 0}
						<tr>
							<td style="white-space: nowrap;">{tr}Email confirmed{/tr}</td>
							<td>
								{if $userinfo.email_confirm}
									({tr _0=$userinfo.daysSinceEmailConfirm}%0 days ago{/tr})
								{else}
									{tr}Never{/tr}
								{/if}
							</td>
						</tr>
					{/if}
					<tr>
						<td>{tr}Current Login{/tr}</td>
						<td>{if $userinfo.currentLogin}{$userinfo.currentLogin|tiki_long_datetime|default:'Never'}{/if}</td>
					</tr>
					<tr>
						<td>{tr}Last Login{/tr}</td>
						<td>{if $userinfo.lastLogin}{$userinfo.lastLogin|tiki_long_datetime|default:'Never'}{/if}</td>
					</tr>
					</table>
				{/if}

				<div class="form-group">
					<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
						{if isset($userinfo.userId) && $userinfo.userId}
							<input type="hidden" name="user" value="{$userinfo.userId|escape}">
							<input type="hidden" name="edituser" value="1">
							<input type="submit" class="btn btn-primary" name="save" value="{tr}Save{/tr}">
						{else}
							<input type="submit" class="btn btn-primary" name="newuser" value="{tr}Add{/tr}">
						{/if}
					</div>
				</div>

				{if $prefs.userTracker eq 'y'}
					{if $userstrackerid and $usersitemid}
						{tr}User tracker item : {$usersitemid}{/tr}
						{button href="tiki-view_tracker_item.php?trackerId=$userstrackerid&amp;itemId=$usersitemid&amp;show=mod" _text="{tr}Edit Item{/tr}"}
					{/if}
				{/if}
			</form>
		{else}
			{tr}You do not have permission to edit this user{/tr}
		{/if}
	{/tab}

	{* ---------------------- tab with upload -------------------- *}
	{tab name="{tr}Import{/tr}"}
		<h2>{tr}Batch upload (CSV file){/tr}</h2>

		<form class="form-horizontal" action="tiki-adminusers.php" method="post" enctype="multipart/form-data">
			{ticket}
			<div class="form-group">
				<label for="csvlist" class="control-label col-md-3">{tr}CSV File{/tr}</label>
				<div class="col-md-9">
					<input type="file" id="csvlist" name="csvlist">
					<div class="help-block">
						{tr}CSV file layout{/tr} {tr}login,password,email,groups,default_group,realName<br>user1,pass1,email1,group1,group1<br>user2,pass2,email2,"group1,group2",group1{/tr}<br><br>{tr}Only login, password, email are mandatory.Use an empty password for automatic password generation. Use same login and email if the login use email. Groups are separated by comma. With group name with comma, double the comma.{/tr}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label">{tr}Existing Users{/tr}</label>
				<div class="col-md-9">
					<label class="radio-inline">
						<input type="radio" name="overwrite" value="y">
						{tr}Overwrite{/tr}
					</label>
					<label class="radio-inline">
						<input type="radio" name="overwrite" value="n" checked>
						{tr}Don't overwrite{/tr}
					</label>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-9 col-md-offset-3">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="overwriteGroup">
							{tr}Overwrite groups{/tr}
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-9 col-md-offset-3">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="createGroup">
							{tr}Create groups{/tr}
						</label>
					</div>
				</div>
			</div>
			{if $prefs.change_password neq 'n'}
				<div class="form-group">
					<div class="col-md-9 col-md-offset-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="forcePasswordChange">
								{tr}User must change password at first login{/tr}
							</label>
						</div>
					</div>
				</div>
			{/if}
			<div class="form-group">
				<div class="col-md-9 col-md-offset-3">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="notification">
							{tr}Send an email to the user in order to allow him to validate his account.{/tr}
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-9 col-md-offset-3">
					<input type="submit" class="btn btn-primary" name="batch" value="{tr}Add{/tr}">
				</div>
			</div>
		</form>
		{if $tiki_p_admin eq 'y'} {* only full admins can manage groups, not tiki_p_admin_users *}
			{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}You can export users of a group by clicking on that group at <a href="tiki-admingroups.php">admin->groups</a>{/tr}{/remarksbox}
		{/if}
	{/tab}

{/tabset}
