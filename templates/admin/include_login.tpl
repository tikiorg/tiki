{* $Id$ *}
{jq}
	$("#genPass").click(function () {
		var passcodeId = $("input[name=registerPasscode]").attr('id');
		genPass(passcodeId);
		return false
	});
{/jq}
{if !empty($feedback)}
	{remarksbox title="{tr}Feedback{/tr}" type=note}
		{$feedback}
	{/remarksbox}
{/if}
<form action="tiki-admin.php?page=login" class="admin form-horizontal" method="post" name="LogForm">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<input type="hidden" name="loginprefs" />
	<div class="t_navbar margin-bottom-md">
		{button href="tiki-admingroups.php" _type="text" _class="btn btn-link tips" _icon_name="group" _text="{tr}Groups{/tr}" _title=":{tr}Group Administration{/tr}"}
		{button href="tiki-adminusers.php" _type="text" _class="btn btn-link tips" _icon_name="user" _text="{tr}Users{/tr}" _title=":{tr}User Administration{/tr}"}
		{permission_link mode=text label="{tr}Permissions{/tr}"}
		<div class="pull-right">
			<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
		</div>
	</div>
	{tabset name="admin_login"}
		{tab name="{tr}General Preferences{/tr}"}
			<h2>{tr}General Preferences{/tr}</h2>
			{preference name=auth_method}
			{preference name=feature_intertiki}
			<fieldset>
				<legend>{tr}Registration{/tr} &amp; {tr}Log in{/tr}</legend>
				{preference name=allowRegister}
				<div class="adminoptionboxchild" id="allowRegister_childcontainer">
					<div class="col-sm-8 col-sm-offset-4">
						{remarksbox type="note" title="{tr}Note{/tr}" close="n"}
							{tr}By default anonymous must enter anti-bot code (CAPTCHA).{/tr}
							{tr}You can change this setting in the Admin, <a href="tiki-admin.php?page=security#content_admin1-2">Security section</a>{/tr}
						{/remarksbox}
					</div>
					{preference name=validateUsers}
					{preference name=validateEmail}
					{preference name=validateRegistration}
					<div class="adminoptionboxchild" id="validateRegistration_childcontainer">
						{preference name=validator_emails size="80"}
					</div>
					{preference name=useRegisterPasscode}
					<div class="adminoptionboxchild" id="useRegisterPasscode_childcontainer">
						{preference name=registerPasscode}
						<div class="col-sm-8 col-sm-offset-4">
							<span id="genPass">
								{button href="#" _onclick="" _text="{tr}Generate a passcode{/tr}"}
							</span>
						</div>
						{preference name=showRegisterPasscode}
					</div>
					{preference name=registerKey}
					{if $gd_lib_found neq 'y'}
						{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
							{tr}Requires PHP GD library{/tr}.
						{/remarksbox}
					{/if}
					{preference name=generate_password}
					{preference name=http_referer_registration_check}
					<div class="adminoptionbox form-group">
						<label for="registration_choices" class="col-sm-4 control-label">{tr}Users can select a group to join at registration:{/tr}</label>
						<div class="col-sm-8 adminoptionlabel">
							<select id="registration_choices" name="registration_choices[]" multiple="multiple" size="5" class="form-control">
								{foreach key=g item=gr from=$listgroups}
									{if $gr.groupName ne 'Anonymous'}
										<option value="{$gr.groupName|escape}" {if $gr.registrationChoice eq 'y'} selected="selected"{/if}>{$gr.groupName|truncate:"52"|escape}</option>
									{/if}
								{/foreach}
							</select>
							<div class="help-block">{tr}By default, new users automatically join the Registered group{/tr}.</div>
						</div>
					</div>
					{preference name=user_must_choose_group}
					{preference name=url_after_validation}
				</div>
				{preference name=userTracker}
				<div class="adminoptionboxchild" id="userTracker_childcontainer">
					{preference name=feature_userWizardDifferentUsersFieldIds}
					<div class="adminoptionboxchild" id="feature_userWizardDifferentUsersFieldIds_childcontainer">
						{preference name=feature_userWizardUsersFieldIds}
					</div>
					{preference name=user_register_prettytracker}
					<div class="adminoptionboxchild" id="user_register_prettytracker_childcontainer">
						{preference name=user_register_prettytracker_tpl}
					</div>
					{preference name=user_register_prettytracker_output}
					<div class="adminoptionboxchild" id="user_register_prettytracker_output_childcontainer">
						{preference name=user_register_prettytracker_outputwiki}
						{preference name=user_register_prettytracker_outputtowiki}
					</div>
					{preference name=user_trackersync_trackers}
					{preference name=user_trackersync_realname}
					{preference name=user_trackersync_geo}
					{preference name=user_trackersync_lang}
					{preference name=user_tracker_auto_assign_item_field}
				</div>
				{preference name=groupTracker}
				{preference name=email_due}
				{preference name=unsuccessful_logins}
				{preference name=unsuccessful_logins_invalid}
				{preference name=eponymousGroups}
				{preference name=desactive_login_autocomplete}
				{preference name=feature_challenge}
				{preference name=login_multiple_forbidden}
				{preference name=login_grab_session}
				{preference name=session_protected}
				{preference name=https_login}
				{preference name=login_http_basic}
				<div class="adminoptionboxchild https_login_childcontainer allowed encouraged force_nocheck required">
					{preference name=feature_show_stay_in_ssl_mode}
					{preference name=feature_switch_ssl_mode}
					{preference name=http_port}
					{preference name=https_port}
					{preference name=https_external_links_for_users}
				</div>
				<fieldset>
					<legend>{tr}Cookies{/tr}</legend>
					{preference name=rememberme}
					<div class="adminoptionboxchild rememberme_childcontainer all always">
						{preference name=remembertime}
						{preference name=cookie_refresh_rememberme}
					</div>
					{preference name=cookie_name}
					{preference name=cookie_domain}
					{preference name=cookie_path}
					<hr>
					<strong>{tr}Cookie Consent{/tr}</strong>
					{preference name=cookie_consent_feature}
					<div class="adminoptionboxchild" id="cookie_consent_feature_childcontainer">
						{preference name=cookie_consent_name}
						{preference name=cookie_consent_expires}
						{preference name=cookie_consent_description}
						{preference name=cookie_consent_question}
						{preference name=cookie_consent_alert}
						{preference name=cookie_consent_button}
						{preference name=cookie_consent_mode}
						{preference name=cookie_consent_dom_id}
					</div>
				</fieldset>
				{preference name=feature_banning}
			</fieldset>
			<fieldset>
				<legend>{tr}Username{/tr}</legend>
				{preference name=login_is_email mode=invert}
				{preference name=login_is_email_obscure}
				{preference name=user_unique_email}
				{preference name=login_allow_email}
				<div class="adminoptionboxchild" id="login_is_email_childcontainer">
					{preference name=min_username_length}
					{preference name=max_username_length}
					{preference name=lowercase_username}
				</div>
				{preference name=username_pattern}
				{preference name=login_autogenerate}
			</fieldset>
			<fieldset>
				<legend>{tr}Password{/tr}</legend>
				{if $prefs.feature_clear_passwords eq 'y'} {* deprecated *}
					{preference name='feature_clear_passwords'}
					<div class="adminoptionboxchild" id='feature_clear_passwords_childcontainer'>
						{remarksbox type='warning' title="{tr}Security risk{/tr}" close="n"}
							{tr}Store passwords in plain text is activated. You should never set this unless you know what you are doing.{/tr}
						{/remarksbox}
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
		{tab name="{tr}Remote Tiki Autologin{/tr}"}
		  <h2>{tr}Remote Tiki Autologin{/tr}</h2>
			<fieldset>
			  {preference name=login_autologin}
			  {preference name=login_autologin_user}
			  {preference name=login_autologin_group}
			  {preference name=login_autologin_createnew}
			  {preference name=login_autologin_allowedgroups}
			  {preference name=login_autologin_syncgroups}
			  {preference name=login_autologin_logoutremote}
			  {preference name=login_autologin_redirectlogin}
			  {preference name=login_autologin_redirectlogin_url}
			</fieldset>
		{/tab}
		{tab name="{tr}LDAP{/tr}"}
			<h2>{tr}LDAP{/tr}</h2>
			<input type="hidden" name="auth_ldap" />
			<fieldset>
				<legend>LDAP {help url="Login+Authentication+Methods"}</legend>
				{if $prefs.auth_method ne 'ldap'}
					{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
						{tr}You must change the Authentication Method to LDAP for these changes to take effect{/tr}
					{/remarksbox}
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
				<input type="password" style="display:none" name="auth_ldap_adminuser_autocomplete_off"> {* This is now required so the browser don't store the user's login here *}
				{preference name=auth_ldap_adminuser}
				<input type="password" style="display:none" name="auth_ldap_adminpass_autocomplete_off"> {* This is now required so the browser don't store the user's password here *}
				{preference name=auth_ldap_adminpass}
			</fieldset>
		{/tab}
		{tab name="{tr}LDAP external groups{/tr}"}
			<h2>{tr}LDAP external groups{/tr}</h2>
			<fieldset>
				<legend>{tr}LDAP external groups{/tr}</legend>
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
				{preference name=syncGroupsWithDirectory}
			</fieldset>
			<fieldset>
				<legend>{tr}LDAP Group{/tr}</legend>
				{preference name=auth_ldap_groupdn}
				{preference name=auth_ldap_groupattr}
				{preference name=auth_ldap_groupdescattr}
				{preference name=auth_ldap_groupoc}
				{preference name=syncUsersWithDirectory}
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
			<h2>{tr}PAM{/tr}</h2>
			<input type="hidden" name="auth_pam" />
			<fieldset>
				<legend>{tr}PAM{/tr} {help url="AuthPAM" desc="{tr}PAM{/tr}"}</legend>
				{if $prefs.auth_method ne 'pam'}
					{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
						{tr}You must change the Authentication Method to PAM for these changes to take effect{/tr}
					{/remarksbox}
				{/if}
				{preference name=pam_create_user_tiki}
				{preference name=pam_skip_admin}
				{preference name=pam_service}
			</fieldset>
		{/tab}
		{tab name="{tr}Shibboleth{/tr}"}
			<h2>{tr}Shibboleth{/tr}</h2>
			<fieldset>
				<legend>{tr}Shibboleth{/tr}{help url="AuthShib" desc="{tr}Shibboleth Authentication {/tr}"}</legend>
				<input type="hidden" name="auth_shib" />
				{if $prefs.auth_method ne 'shib'}
					{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
						{tr}You must change the Authentication Method to Shibboleth for these changes to take effect{/tr}
					{/remarksbox}
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
			<h2>{tr}CAS{/tr}</h2>
			<input type="hidden" name="auth_cas" />
			<fieldset>
				<legend>{tr}CAS (Central Authentication Service){/tr}{help url="CAS+Authentication"}</legend>
				{if $prefs.auth_method ne 'cas'}
					{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
						{tr}You must change the Authentication Method to CAS for these changes to take effect{/tr}
					{/remarksbox}
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
			<h2>{tr}phpBB{/tr}</h2>
			<fieldset>
				<legend>{tr}phpBB{/tr}{help url="phpBB+Authentication" desc="{tr}phpBB User Database Authentication {/tr}"}</legend>
				<input type="hidden" name="auth_phpbb" />
				{if $prefs.auth_method ne 'phpbb'}
					{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
						{tr}You must change the Authentication Method to phpBB for these changes to take effect{/tr}
					{/remarksbox}
				{/if}
				{if $prefs.allowRegister ne 'n'}
					{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
						{tr}You must turn Users can register off for phpBB Authentication to function properly{/tr}
					{/remarksbox}
				{/if}
				{preference name=auth_phpbb_create_tiki}
				{preference name=auth_phpbb_skip_admin}
				{preference name=auth_phpbb_disable_tikionly}
				{preference name=auth_phpbb_version}
				{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
					{tr}MySql only (for now){/tr}
				{/remarksbox}
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
					{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
						{tr}You must change the Authentication Method to Web Server for these changes to take effect{/tr}
					{/remarksbox}
				{/if}
				{preference name='auth_ws_create_tiki'}
			</fieldset>
		{/tab}
	{/tabset}
	<div class="t_navbar margin-bottom-md text-center">
		<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
	</div>
</form>
