{* $Id$ *}
{jq}		
	$("#genPass span").click(function () {
		var passcodeId = $("input[name=registerPasscode]").attr('id');
		genPass(passcodeId);
		return false
	});

	$("input[name=useRegisterPasscode]").change(function () {
		document.LogForm.registerPasscode.value='';
		return false
	});
{/jq}

<div class="navbar">
	{button href="tiki-admingroups.php" _text="{tr}Admin Groups{/tr}"}
	{button href="tiki-adminusers.php" _text="{tr}Admin Users{/tr}"}
</div>
{if !empty($feedback)}
	{remarksbox title="{tr}Feedback{/tr}" type=note}
		{$feedback}
	{/remarksbox}
{/if}

<form action="tiki-admin.php?page=login" class="admin" method="post" name="LogForm">
	<input type="hidden" name="loginprefs" />
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_login"}
		{tab name="{tr}General Preferences{/tr}"}
			{preference name=auth_method}
			
			<fieldset>
				<legend>{tr}Registration{/tr} &amp; {tr}Log in{/tr}</legend>
				{preference name=allowRegister}
				<div class="adminoptionboxchild" id="allowRegister_childcontainer">
					{preference name=validateUsers}
					{preference name=validateEmail}
					{preference name=validateRegistration}
					<div class="adminoptionboxchild" id="validateRegistration_childcontainer">
						{preference name=validator_emails}
					</div>

					{preference name=useRegisterPasscode}
					<div class="adminoptionboxchild" id="useRegisterPasscode_childcontainer">
						{preference name=registerPasscode}
						<span id="genPass">
								{button href="#" _onclick="" _text="{tr}Generate a passcode{/tr}"}
						</span>
					</div>

					{if $gd_lib_found neq 'y'}
						<div class="highlight">
							{icon _id=information} {tr}Requires PHP GD library{/tr}.
						</div>
					{/if}
					{preference name=generate_password}
					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<label for="registration_choices">{tr}Users can select a group to join at registration:{/tr}</label>
							<br />
							<em>{tr}By default, new users automatically join the Registered group{/tr}.</em>
						</div>
						<div class="adminoptionlabel">
							<select id="registration_choices" name="registration_choices[]" multiple="multiple" size="5" style="width:95%;">
								{foreach key=g item=gr from=$listgroups}
									{if $gr.groupName ne 'Anonymous'}
										<option value="{$gr.groupName|escape}" {if $gr.registrationChoice eq 'y'} selected="selected"{/if}>{$gr.groupName|truncate:"52"|escape}</option>
									{/if}
								{/foreach}
							</select>
						</div>
						{preference name=url_after_validation}
					</div>
				</div>
				{preference name=userTracker}
				<div class="adminoptionboxchild" id="userTracker_childcontainer">
				{preference name=user_register_prettytracker}
					<div class="adminoptionboxchild" id="user_register_prettytracker_childcontainer">
					{preference name=user_register_prettytracker_tpl}
					</div>
				{preference name=user_trackersync_trackers}
				{preference name=user_trackersync_realname}
				{preference name=user_trackersync_geo}
				{preference name=user_trackersync_groups}
				{preference name=user_trackersync_parentgroup}
				</div>
				{preference name=groupTracker}
				{preference name=email_due}
				{preference name=unsuccessful_logins}
				{preference name=unsuccessful_logins_invalid}
				{preference name=eponymousGroups}
				{preference name=syncGroupsWithDirectory}
				{preference name=syncUsersWithDirectory}
				{preference name=desactive_login_autocomplete}
				{preference name=feature_challenge}

				{preference name=https_login}
				{preference name=login_http_basic}

				<div class="adminoptionboxchild https_login_childcontainer allowed encouraged force_nocheck required">
					{preference name=feature_show_stay_in_ssl_mode}
					{preference name=feature_switch_ssl_mode}
					{preference name=http_port}
					{preference name=https_port}
					{preference name=https_external_links_for_users}
				</div>

				{preference name=rememberme}
				<div class="adminoptionboxchild rememberme_childcontainer all always">
					{preference name=remembertime}
				</div>
	
				<fieldset>
					<legend>{tr}Cookie{/tr}</legend>
					{preference name=cookie_name}
					{preference name=cookie_domain}
					{preference name=cookie_path}
				</fieldset>
				{preference name=feature_banning}
			</fieldset>
	
			<fieldset>
				<legend>{tr}Username{/tr}</legend>
				{preference name=login_is_email mode=invert}
				{preference name=login_is_email_obscure}
				<div class="adminoptionboxchild" id="login_is_email_childcontainer">
					{preference name=min_username_length}
					{preference name=max_username_length}
					{preference name=lowercase_username}
				</div>
				{preference name=username_pattern}
			</fieldset>
	
			<fieldset>
				<legend>{tr}Password{/tr}</legend>
				{if $prefs.feature_clear_passwords eq 'y'} {* deprecated *}
					{preference name='feature_clear_passwords'}
					<div class="adminoptionboxchild" id='feature_clear_passwords_childcontainer'>
						{remarksbox type='warning' title='Security risk'}{tr}Store passwords in plain text is activated. You should never set this unless you know what you are doing.{/tr}{/remarksbox}
					</div>
				{/if}
	
				{preference name=forgotPass}
				{preference name=feature_crypt_passwords}
				{preference name=change_password}
				{preference name=pass_chr_num}
				{preference name=pass_chr_case}
				{preference name=pass_chr_special}
				{preference name=pass_repetition}
				{preference name=pass_diff_username}
				{preference name=min_pass_length}
				{preference name=pass_due}
			</fieldset>
			{button href="?page=login&amp;refresh_email_group=y" _text="{tr}Assign users to group function of email pattern{/tr}"}
		{/tab}

		{tab name="{tr}LDAP{/tr}"}
			<input type="hidden" name="auth_ldap" />
			<fieldset>
				<legend>LDAP {help url="Login+Authentication+Methods"}</legend>
				{if $prefs.auth_method ne 'ldap'}
					<div style="padding:0.5em;clear:both" class="simplebox">
						<div>{icon _id=information} {tr}You must change the Authentication Method to LDAP for these changes to take effect{/tr}.</div>
					</div>
				{/if}
					
				{preference name=ldap_create_user_tiki}
				{preference name=ldap_create_user_ldap}
				{preference name=ldap_skip_admin}
				{preference name=auth_ldap_permit_tiki_users}
			</fieldset>

			<fieldset>
				<legend>{tr}LDAP Bind settings{/tr}{help url="LDAP+Authentication"}</legend>
				{preference name=auth_ldap_host}
				{preference name=auth_ldap_port}
				{preference name=auth_ldap_debug}
				{preference name=auth_ldap_ssl}
				{preference name=auth_ldap_starttls}
				{preference name=auth_ldap_type}
				{preference name=auth_ldap_scope}
				{preference name=auth_ldap_version}
				{preference name=auth_ldap_basedn}
			</fieldset>

			<fieldset>
				<legend>{tr}LDAP User{/tr}</legend>
				{preference name=auth_ldap_userdn}
				{preference name=auth_ldap_userattr}
				{preference name=auth_ldap_useroc}
				{preference name=auth_ldap_nameattr}
				{preference name=auth_ldap_countryattr}
				{preference name=auth_ldap_emailattr}
			</fieldset>

			<fieldset>
				<legend>{tr}LDAP Admin{/tr}</legend>
				{preference name=auth_ldap_adminuser}
				{preference name=auth_ldap_adminpass}
			</fieldset>
		{/tab}

		{tab name="{tr}LDAP external groups{/tr}"}
			<fieldset>
				<legend>LDAP external groups</legend>

				{preference name=auth_ldap_group_external}
			</fieldset>

			<fieldset>
				<legend>{tr}LDAP Bind settings{/tr}{help url="LDAP+Authentication"}</legend>
				{preference name=auth_ldap_group_host}
				{preference name=auth_ldap_group_port}
				{preference name=auth_ldap_group_debug}
				{preference name=auth_ldap_group_ssl}
				{preference name=auth_ldap_group_starttls}
				{preference name=auth_ldap_group_type}
				{preference name=auth_ldap_group_scope}
				{preference name=auth_ldap_group_version}
				{preference name=auth_ldap_group_basedn}
			</fieldset>

			<fieldset>
				<legend>{tr}LDAP User{/tr}</legend>
				{preference name=auth_ldap_group_userdn}
				{preference name=auth_ldap_group_userattr}
				{preference name=auth_ldap_group_corr_userattr}
				{preference name=auth_ldap_group_useroc}
			</fieldset>

			<fieldset>
				<legend>{tr}LDAP Group{/tr}</legend>
				{preference name=auth_ldap_groupdn}
				{preference name=auth_ldap_groupattr}
				{preference name=auth_ldap_groupdescattr}
				{preference name=auth_ldap_groupoc}
			</fieldset>

			<fieldset>
				<legend>{tr}LDAP Group Member - if group membership can be found in group attributes{/tr}</legend>
				{preference name=auth_ldap_memberattr}
				{preference name=auth_ldap_memberisdn}
			</fieldset>

			<fieldset>
				<legend>{tr}LDAP User Group - if group membership can be found in user attributes{/tr}</legend>
				{preference name=auth_ldap_usergroupattr}
				{preference name=auth_ldap_groupgroupattr}
			</fieldset>

			<fieldset>
				<legend>{tr}LDAP Admin{/tr}</legend>
				{preference name=auth_ldap_group_adminuser}
				{preference name=auth_ldap_group_adminpass}
			</fieldset>
		{/tab}

		{tab name="{tr}PAM{/tr}"}
			<input type="hidden" name="auth_pam" />
			<fieldset>
				<legend>{tr}PAM{/tr} {help url="AuthPAM" desc="{tr}PAM{/tr}"}</legend>
	
				{if $prefs.auth_method ne 'pam'}
					<div style="padding:0.5em;clear:both" class="simplebox">
						<div>
							{icon _id=information} {tr}You must change the Authentication Method to PAM for these changes to take effect{/tr}.
						</div>
					</div>
				{/if}
					
				{preference name=pam_create_user_tiki}
				{preference name=pam_skip_admin}
				{preference name=pam_service}
			</fieldset>
		{/tab}

		{tab name="{tr}Shibboleth{/tr}"}
			<fieldset>
				<legend>{tr}Shibboleth{/tr}{help url="AuthShib" desc="{tr}Shibboleth Authentication {/tr}"}</legend>
				<input type="hidden" name="auth_shib" />
				{if $prefs.auth_method ne 'shib'}
					<div style="padding:0.5em;clear:both" class="simplebox">
						<div>{icon _id=information} {tr}You must change the Authentication Method to Shibboleth for these changes to take effect{/tr}.</div>
					</div>
				{/if}

				{preference name=shib_create_user_tiki}
				{preference name=shib_skip_admin}
				{preference name=shib_affiliation}

				{preference name=shib_usegroup}
				<div class="adminoptionboxchild" id="shib_usegroup_childcontainer">
					{preference name=shib_group}
				</div>
			</fieldset>
		{/tab}

		{tab name="{tr}CAS{/tr}"}
			<input type="hidden" name="auth_cas" />
			<fieldset>
				<legend>{tr}CAS (Central Authentication Service){/tr}{help url="CAS+Authentication"}</legend>
				{if $prefs.auth_method ne 'cas'}
					<div style="padding:0.5em;clear:both" class="simplebox">
						<div>
							{icon _id=information} {tr}You must change the Authentication Method to CAS for these changes to take effect{/tr}.
						</div>
					</div>
				{/if}

				{preference name='cas_create_user_tiki'}
				{preference name='cas_autologin'}
				{preference name='cas_skip_admin'}
				{preference name='cas_show_alternate_login'}
				{preference name='cas_force_logout'}
				{preference name='cas_version'}

				<fieldset>
					<legend>{tr}CAS Server{/tr}</legend>
					{preference name='cas_hostname' label="{tr}CAS Server Name{/tr}"}
					{preference name='cas_port' label="{tr}CAS Server Port{/tr}"}
					{preference name='cas_path' label="{tr}CAS Server Path{/tr}"}
					{preference name='cas_extra_param' label="{tr}CAS Extra Parameter{/tr}"}
					{preference name='cas_authentication_timeout'}
				</fieldset>
			</fieldset>
		{/tab}
		{tab name="{tr}phpBB{/tr}"}
			<fieldset>
				<legend>{tr}phpBB{/tr}{help url="phpBB+Authentication" desc="{tr}phpBB User Database Authentication {/tr}"}</legend>
				<input type="hidden" name="auth_phpbb" />
				{if $prefs.auth_method ne 'phpbb'}
					<div style="padding:0.5em;clear:both" class="simplebox">
						<div>{icon _id=information} {tr}You must change the Authentication Method to phpBB for these changes to take effect{/tr}.</div>
					</div>
				{/if}
				{if $prefs.allowRegister ne 'n'}
					<div style="padding:0.5em;clear:both" class="simplebox">
						<div>{icon _id=information} {tr}You must turn Users can register off for phpBB Authentication to function properly{/tr}.</div>
					</div>
				{/if}
				{preference name=auth_phpbb_create_tiki}
				{preference name=auth_phpbb_skip_admin}
				{preference name=auth_phpbb_disable_tikionly}
				{preference name=auth_phpbb_version}

				<div style="padding:0.5em;clear:both" class="simplebox">
					<div>{icon _id=information} {tr}MySql only (for now){/tr}.</div>
				</div>
				{preference name=auth_phpbb_dbhost}
				{preference name=auth_phpbb_dbuser}
				{preference name=auth_phpbb_dbpasswd}
				{preference name=auth_phpbb_dbname}
				{preference name=auth_phpbb_table_prefix}
			</fieldset>
		{/tab}

		{tab name="{tr}Web Server{/tr}"}
			<fieldset>
				<legend>{tr}Web Server{/tr}{help url="External+Authentication#Web_Server_HTTP_" desc="{tr}Web Server Authentication {/tr}"}</legend>
				<input type="hidden" name="auth_ws" />
				{if $prefs.auth_method ne 'ws'}
					<div style="padding:0.5em;clear:both" class="simplebox">
						<div>
							{icon _id=information} {tr}You must change the Authentication Method to Web Server for these changes to take effect{/tr}.
						</div>
					</div>
				{/if}
				{preference name='auth_ws_create_tiki'}
			</fieldset>
		{/tab}
	{/tabset}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
