{* $Id$ *}
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
{if !$tsAjax}
	{title help="Users Management" admpage="login" url="tiki-adminusers.php"}{tr}Admin Users{/tr}{/title}

	<div class="t_navbar margin-bottom-md">
		{if $tiki_p_admin eq 'y'} {* only full admins can manage groups, not tiki_p_admin_users *}
			{button href="tiki-admingroups.php" class="btn btn-default" _type="link" _icon_name="group" _text="{tr}Admin Groups{/tr}"}
		{/if}
		{if $tiki_p_admin eq 'y'}
			{permission_link mode=button_link}
		{/if}
		{if isset($userinfo.userId)}
			{button href="?add=1" class="btn btn-default" _text="{tr}Add a New User{/tr}"}
		{/if}
		{if $prefs.feature_invite eq 'y' and $tiki_p_invite eq 'y'}
			{button href="tiki-list_invite.php" _type="link" _icon_name="thumbs-up" _text="{tr}Invitation List{/tr}"}
		{/if}
	</div>

	{if $prefs.feature_intertiki eq 'y' and ($prefs.feature_intertiki_import_groups eq 'y' or $prefs.feature_intertiki_import_preferences eq 'y')}
		{remarksbox type="warning" title="{tr}Warning{/tr}"}
			{if $prefs.feature_intertiki_import_groups eq 'y'}{tr}Since this Tiki site is in slave mode and imports groups, the master groups will be automatically reimported at each login{/tr}{/if}
			{if $prefs.feature_intertiki_import_preferences eq 'y'}{tr}Since this Tiki site is in slave mode and imports preferences, the master user preferences will be automatically reimported at each login{/tr}{/if}
		{/remarksbox}
	{/if}
	{include file='utilities/feedback.tpl'}
	{if !empty($added) or !empty($discarded) or !empty($discardlist)}
		{remarksbox type="feedback" title="{tr}Batch Upload Results{/tr}"}
			{tr}Updated users{/tr} {$added}
			{if $discarded != ""}- {tr}Rejected users{/tr} {$discarded}{/if}
			<br>
			<br>
			{if $discardlist != ''}
				<div class="table-responsive">
					<table class="table">
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

			{if $batcherrors}
				<br>
				{section name=ix loop=$batcherrors}
					{$batcherrors[ix]}<br>
				{/section}
			{/if}
		{/remarksbox}
	{/if}
{/if}
{tabset name='tabs_adminusers'}

	{* ---------------------- tab with list -------------------- *}
	{tab name="{tr}Users{/tr}"}
		{if !$tsAjax}
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
							<a href="javascript:toggleBlock('search')" class="link">
								{icon name='add' alt="{tr}more{/tr}"}&nbsp;{tr}More Criteria{/tr}
							</a>
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
			<form class="form-horizontal" name="checkform" id="checkform" method="post">
				<div id="{$ts_tableid}-div" {if $tsOn}style="visibility:hidden;"{/if}>
					<div class="{if $js === 'y'}table-responsive{/if} user-table ts-wrapperdiv">
		{/if}
						{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
						<table id="{$ts_tableid}" class="table normal table-striped table-hover" data-count="{$cant|escape}">
							{* Note: th element ids here need to match those at /lib/core/Table/Settings/TikiAdminusers.php
							for tablesorter to work properly *}
							{if !$tsAjax}
								<thead>
									<tr>
										<th id="checkbox">
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
										<th id="actions"></th>
									</tr>
								</thead>
							{/if}
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
												<a
													class="link tips"
													href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}&amp;user={$users[user].userId}{if $prefs.feature_tabs ne 'y'}#2{/if}"
													title="{$username}:{tr}Edit account settings{/tr}">
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
																<a
																	class="link tips" {if isset($link_style)}{$link_style}{/if}
																	href="tiki-admingroups.php?group={$grs|escape:"url"}"
																	title="{$grs|escape}:{if $what eq 'included'}{tr}Edit this included group{/tr}"{else}{tr}Edit group{/tr}"{/if}>
																		{$grs|escape}
																</a>
															{else}
																{$grs|escape}
															{/if}
															{if $what eq 'included'}<span class="label label-info">{tr}Included{/tr}</span>{/if}
															{if $grs eq $users[user].default_group}<small>({tr}default{/tr})</small>{/if}
															{if $what ne 'included' and $grs != "Registered"}
																<a href="{bootstrap_modal controller=user action=manage_groups checked=$username groupremove=$grs offset=$offset sort_mode=$sort_mode numrows=$numrows}">
																	{icon name="remove"}
																</a>
															{/if}
															{if !$smarty.foreach.gr.last}<br>{/if}
														{/if}
													</div>
												{/foreach}
											</td>

											<td class="action">
												{capture name=user_actions}
													{strip}
														{$libeg}<a href="{bootstrap_modal controller=user action=manage_groups checked=$username all_groups=$all_groups offset=$offset sort_mode=$sort_mode numrows=$numrows}">
															{icon name="group" _menu_text='y' _menu_icon='y' alt="{tr}Add or remove from a group{/tr}"}
														</a>{$liend}
														{$libeg}
															<a class="link" href="tiki-assignuser.php?assign_user={$users[user].user|escape:url}" title="{tr}Edit group expiry{/tr}">
																{icon name='time'  _menu_text='y' _menu_icon='y' alt='{tr}Edit group expiry{/tr}'}
															</a>
														{$liend}
														{$libeg}<a href="{query _type='relative' user=$users[user].userId}">
															{icon name="edit" _menu_text='y' _menu_icon='y' alt="{tr}Edit account settings{/tr}"}
														</a>{$liend}
														{if $prefs.feature_userPreferences eq 'y' || $user eq 'admin'}
															{$libeg}<a href="tiki-user_preferences.php?userId={$users[user].userId}">
																{icon name="settings" _menu_text='y' _menu_icon='y' alt="{tr}Change user preferences{/tr}" }
															</a>{$liend}
														{/if}
														{if $users[user].user eq $user or $users[user].user_information neq 'private' or $tiki_p_admin eq 'y'}
															{$libeg}<a href="tiki-user_information.php?userId={$users[user].userId}"{if $users[user].user_information eq 'private'}
																style="opacity:0.5;"{/if}
															>
																{icon name="help" _menu_text='y' _menu_icon='y' alt="{tr}User information{/tr}"}
															</a>{$liend}
														{/if}

														{if $users[user].user ne 'admin'}
															{$libeg}<a href="{bootstrap_modal controller=user action=remove_users checked=$username offset=$offset sort_mode=$sort_mode numrows=$numrows}">
																{icon name="remove" _menu_text='y' _menu_icon='y' alt="{tr}Delete{/tr}"}
															</a>{$liend}
															{if $users[user].waiting eq 'a'}
																{$libeg}<a href="tiki-login_validate.php?user={$users[user].user|escape:url}&amp;pass={$users[user].valid|escape:url}">
																	{icon name="ok" _menu_text='y' _menu_icon='y' alt="{tr}Validate user{/tr}"}
																</a>{$liend}
															{/if}
															{if $users[user].waiting eq 'u'}
																{$libeg}<a href="tiki-confirm_user_email.php?user={$users[user].user|escape:url}&amp;pass={$users[user].provpass|md5|escape:url}" title="{tr _0=$users[user].user|username}Confirm user email: %0{/tr}">
																	{icon name="envelope" _menu_text='y' _menu_icon='y' alt="{tr}Confirm user email{/tr}"}
																</a>
															{/if}
															{if $prefs.email_due > 0 and $users[user].waiting ne 'u' and $users[user].waiting ne 'a'}
																{$libeg}<a href="tiki-adminusers.php?user={$users[user].user|escape:url}&amp;action=email_due">
																	{icon name="trash"  _menu_text='y' _menu_icon='y' alt="{tr}Invalidate email{/tr}"}
																</a>{$liend}
															{/if}
														{/if}
														{if !empty($users[user].openid_url)}
															{$libeg}{self_link _menu_text='y' _menu_icon='y' userId=$users[user].userId action='remove_openid' _icon_name="trash"}
																{tr}Remove link with OpenID account{/tr}
															{/self_link}{$liend}

															{$libeg}<a class="link" href="{self_link userId=$users[user].userId action='remove_openid' _tag="n"}{/self_link}" title="{tr _0=$users[user].user|username}User %0{/tr}:">
																{icon name="link" _menu_text='y' _menu_icon='y' alt="{tr}Remove link with OpenID account{/tr}"}
															</a>{$liend}
														{/if}
													{/strip}
												{/capture}
												{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
												<a
													class="tips"
													title="{tr}Actions{/tr}" href="#"
													{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.user_actions|escape:"javascript"|escape:"html"}{/if}
													style="padding:0; margin:0; border:0"
												>
													{icon name='wrench'}
												</a>
												{if $js === 'n'}
													<ul class="dropdown-menu" role="menu">{$smarty.capture.user_actions}</ul></li></ul>
												{/if}
											</td>
										</tr>
									{/if}
								{sectionelse}
									{if !$tsOn || ($tsOn && $tsAjax)}
										{norecords _colspan=8 _text="No user records found"}
									{else}
										{norecords _colspan=8 _text="Retrieving user records..."}
									{/if}
								{/section}
							</tbody>
						</table>
					{if !$tsAjax}
					</div>
					{if $users}
						<div class="input-group col-sm-6">
							<select class="form-control" name="action">
								<option value="no_action" selected="selected">
									{tr}Select action to perform with checked{/tr}...
								</option>
								<option value="remove_users">
									{tr}Remove users...{/tr}
								</option>
								{if $prefs.feature_banning == 'y'}
									<option value="ban_ips">
										{tr}Ban IPs{/tr}
									</option>
								{/if}
								<option value="manage_groups">
									{tr}Change group assignments{/tr}
								</option>
								<option value="default_groups">
									{tr}Set default groups{/tr}
								</option>
								{if $prefs.feature_wiki == 'y'}
									<option value="email_wikipage">
										{tr}Send wiki page content by email{/tr}
									</option>
								{/if}
							</select>
							<span class="input-group-btn">
								<button
									type="submit"
									form="checkform"
									formaction="{bootstrap_modal controller=user}"
									class="btn btn-primary confirm-submit"
								>
									{tr}OK{/tr}
								</button>
							</span>
						</div>
					{/if}
				</div>
				<input type="hidden" name="find" value="{$find|escape}">
				<input type="hidden" name="numrows" value="{$numrows|escape}">
				<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
			</form>
		{if !$tsOn}
			{pagination_links cant=$cant step=$numrows offset=$offset}{/pagination_links}
		{/if}
	{/if}
		{/tab}
	{if !$tsAjax}

		{* ---------------------- tab with form -------------------- *}
		<a id="tab2" ></a>
		{if isset($userinfo.userId) && $userinfo.userId}
			{capture assign=add_edit_user_tablabel}{tr}Edit user{/tr} <i>{$userinfo.login|escape}</i>{/capture}
		{else}
			{assign var=add_edit_user_tablabel value="{tr}Add a New User{/tr}"}
		{/if}

		{tab name="{$add_edit_user_tablabel}"}
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
								{elseif $prefs.login_autogenerate eq 'y'}
									<br>

									{icon name='warning' alt="{tr}Warning{/tr}" style="vertical-align:middle"}
									<em>{tr}The username will be an autogenerated number based on the user ID if no actual username is provided when the user is created. Do not change these numeric usernames.{/tr}</em>
								{/if}
								{if isset($userinfo.userId) && $userinfo.userId}
									<p>
										{icon name='warning' alt="{tr}Warning{/tr}" style="vertical-align:middle"}
										<em>{tr}Warning: changing the username could require the user to change his or her password (for user registered with an old Tiki&lt;=1.8){/tr}</em>
										{if $prefs.feature_intertiki_server eq 'y'}
											<br>
											<i>{tr}Warning: it will create a problem with Intertiki slave sites that use this one as master{/tr}</i>
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
						{include file='password_jq.tpl' ignorejq='y'}
						<div class="form-group">
							<label class="col-sm-3 col-md-2 control-label" for="pass1">{tr}New Password{/tr}</label>
							<div class="col-sm-7 col-md-6">
								<input type="password" class="form-control" placeholder="New Password" name="pass" id="pass1">
								<div style="margin-left:5px;">
									<div id="mypassword_text">{icon name='ok' istyle='display:none'}{icon name='error' istyle='display:none' } <span id="mypassword_text_inner"></span></div>
									<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div>
								</div>
								<div style="margin-top:5px">
									{include file='password_help.tpl'}
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 col-md-2 control-label" for="pass2">{tr}Repeat Password{/tr}</label>
							<div class="col-sm-7 col-md-6">
								<input type="password" class="form-control" name="passAgain" id="pass2" placeholder="Repeat Password">
								<div id="mypassword2_text">
									<div id="match" style="display:none">
										{icon name='ok' istyle='color:#0ca908'} {tr}Passwords match{/tr}
									</div>
									<div id="nomatch" style="display:none">
										{icon name='error' istyle='color:#ff0000'} {tr}Passwords do not match{/tr}
									</div>
								</div>
							</div>
						</div>
						{if $prefs.generate_password eq 'y' and not ( $prefs.auth_method eq 'ldap' and ( $prefs.ldap_create_user_tiki eq 'n' or $prefs.ldap_skip_admin eq 'y' ) and $prefs.ldap_create_user_ldap eq 'n')}
							<div class="form-group">
								<div class="col-sm-3 col-sm-offset-3 col-md-3 col-md-offset-2">
									<span id="genPass">{button href="#" _text="{tr}Generate a password{/tr}"}</span>
								</div>
								<div class="col-sm-3 col-md-3">
									<input id='genepass' class="form-control" name="genepass" type="text" tabindex="0" style="display:none">
								</div>
							</div>
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
										<input type="checkbox" name="need_email_validation" {if ($userinfo.login eq '' and ($prefs.validateUsers eq 'y' or $prefs.validateRegistration eq 'y')) or $userinfo.provpass neq '' or $userinfo.valid neq ''}checked="checked"{/if}>
										{tr}Send an email to the user to enable him or her to validate their account.{/tr}
									</label>
									{if empty($prefs.sender_email)}
										<div class="help-block"><span class="highlight">{tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a>{/tr}</span></div>
									{/if}
								</div>
							</div>
						</div>
					{/if}
					{if $prefs.userTracker eq 'y' and $userinfo.login eq ''}
						<div class="form-group">
							<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="insert_user_tracker_item">
										{tr}Add a user tracker item for this user{/tr}
									</label>
								</div>
							</div>
						</div>
					{/if}

					{if $prefs.userTracker eq 'y' and $userstrackerid}
						<div class="form-group">
							<label class="col-md-2 control-label">
								{tr}User tracker{/tr}
							</label>

							<div class="col-md-10">
								{if $usersitemid}
									<a href="{bootstrap_modal controller=tracker action=update_item trackerId=$userstrackerid itemId=$usersitemid}"
									   onclick="$('[data-toggle=popover]').popover('hide');" class="btn btn-default edit-usertracker">
										{tr}Edit Item{/tr}
									</a>
									<a href="{$usersitemid|sefurl:trackeritem}" class="btn btn-info">
										{tr}View item{/tr}
									<a>
								{else}
									<a href="{bootstrap_modal controller=tracker action=insert_item trackerId=$userstrackerid forced=$usersTrackerForced}"
									   onclick="$('[data-toggle=popover]').popover('hide');" class="btn btn-default insert-usertracker">
										{tr}Create Item{/tr}
									</a>
								{/if}
							</div>
						</div>
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
									{tr}The user must change his or her password the first time they log in{/tr}
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
								{tr}Send an email to the user to enable him or her to validate their account.{/tr}
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

		{tab name="{tr}Temporary Users{/tr}"}
			<h2>Invite new temporary user(s)</h2>
			{$temp_users_enabled = true}
			{if $prefs['auth_token_access'] != 'y'}
				{remarksbox type="warning" title="{tr}Token Access Feature Dependency{/tr}"}
					{tr}The token access feature is needed for Temporary Users to login.{/tr}
					<a href="tiki-admin.php?page=security">{tr}Turn it on here.{/tr}</a>
				{/remarksbox}
				{$temp_users_enabled = false}
			{/if}
			{if $prefs['login_is_email'] === 'y'}
				{remarksbox type="warning" title="{tr}Feature Conflict{/tr}"}
					{tr}This feature currently is incompatible with the "Use email as username" feature{/tr}
					<a href="tiki-admin.php?lm_criteria=login_is_email&exact">{tr}Turn it off here.{/tr}</a>
				{/remarksbox}
				{$temp_users_enabled = false}
			{/if}
			{if $prefs['user_unique_email'] === 'y'}
				{remarksbox type="warning" title="{tr}Feature Conflict{/tr}"}
					{tr}This feature currently is incompatible with the "User e-mails must be unique" feature{/tr}
					<a href="tiki-admin.php?lm_criteria=user_unique_email&exact">{tr}Turn it off here.{/tr}</a>
				{/remarksbox}
				{$temp_users_enabled = false}
			{/if}
			{if $temp_users_enabled}
				{remarksbox type="info" title="Temporary Users"}
					<p>{tr}Temporary users cannot login the usual way but instead do so via an autologin URL that is associated with a token.{/tr} {tr}An email will be sent out to invited users containing this URL. You will receive a copy of the email yourself.{/tr}</p>
					<p>{tr}These temporary users will be deleted (but can be set to be preserved in Admin Tokens) once the validity period is over. Normally, these users should have read-only access. Nevertheless, if you are allowing these users to submit information, e.g. fill in a tracker form, make sure to ask for their information again in those forms.{/tr}</p>
					<p>{tr}Please do not assign temporary users to Groups that can access any security sensitive information, since access to these accounts is relatively easy to obtain, for example by intercepting or otherwise getting access to these emails.{/tr}</p>
				{/remarksbox}
				{remarksbox type="info" title="Revoking Access"}
					{tr}To revoke access before validity expires or to review who has access, please see:{/tr} <a href="tiki-admin_tokens.php">{tr}Admin Tokens{/tr}</a>
				{/remarksbox}
				<form class="form-horizontal" name="tempuser" id="tempuser" method="post">
					<div class="form-group">
						<label class="col-sm-4 col-md-4 control-label" for="tempuser_emails">{tr}Email addresses (comma-separated){/tr}</label>
						<div class="col-sm-8 col-md-8">
							<input type="text" class="form-control" name="tempuser_emails" id="tempuser_emails" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 col-md-4 control-label" for="tempuser_groups">{tr}Groups (comma-separated){/tr}</label>
						<div class="col-sm-8 col-md-8">
							<input type="text" class="form-control" name="tempuser_groups" id="tempuser_groups" />
							{autocomplete element='#tempuser_groups' type='groupname'}
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 col-md-4 control-label" for="tempuser_expiry">{tr}Valid for days (use -1 for forever){/tr}</label>
						<div class="col-sm-8 col-md-8">
							<input type="text" class="form-control" name="tempuser_expiry" id="tempuser_expiry" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 col-md-4 control-label" for="tempuser_prefix">{tr}Username prefix{/tr}</label>
						<div class="col-sm-8 col-md-8">
							<input type="text" class="form-control" name="tempuser_prefix" id="tempuser_prefix" placeholder="guest"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 col-md-4 control-label" for="tempuser_path">{tr}Autologin (non-SEFURL) path{/tr}</label>
						<div class="col-sm-8 col-md-8">
							<input type="text" class="form-control" name="tempuser_path" id="tempuser_path" placeholder="index.php"/>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-4 col-md-10 col-md-offset-4">
							<input
								type="submit"
								class="btn btn-primary confirm-submit"
								form="tempuser"
								formaction="{bootstrap_modal controller=user action=invite_tempuser}"
								value="{tr}Invite{/tr}"
							>
						</div>
					</div>
				</form>
			{/if}
		{/tab}
	{/if}
{/tabset}
