{* $Id$ *}
<div class="navbar">
	{button href="tiki-admingroups.php" _text="{tr}Admin Groups{/tr}"}
	{button href="tiki-adminusers.php" _text="{tr}Admin Users{/tr}"}
</div>

<form action="tiki-admin.php?page=login" class="admin" method="post">
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
					</div>

					{preference name=rnd_num_reg}
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
				{preference name=groupTracker}
				{preference name=email_due}
				{preference name=unsuccessful_logins}
				{preference name=eponymousGroups}
				{preference name=desactive_login_autocomplete}
				{preference name=feature_challenge}

				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="https_login">{tr}Use HTTPS login:{/tr}</label>
						<select name="https_login" id="https_login" onchange="hidedisabled('httpsoptions',this.value);">
							<option value="disabled"{if $prefs.https_login eq 'disabled'} selected="selected"{/if}>{tr}Disabled{/tr}</option>
							<option value="allowed"{if $prefs.https_login eq 'allowed'} selected="selected"{/if}>{tr}Allow secure (https) login{/tr}</option>
							<option value="encouraged"{if $prefs.https_login eq 'encouraged'} selected="selected"{/if}>{tr}Encourage secure (https) login{/tr}</option>
							<option value="force_nocheck"{if $prefs.https_login eq 'force_nocheck'} selected="selected"{/if}>{tr}Consider we are always in HTTPS, but do not check{/tr}</option>
							<option value="required"{if $prefs.https_login eq 'required'} selected="selected"{/if}>{tr}Require secure (https) login{/tr}</option>
						</select>
					</div>
				</div>

				<div id="httpsoptions" style="clear:both;margin-left:2.5em;display:{if $prefs.https_login eq 'disabled'}none{else}block{/if}">
					{preference name=feature_show_stay_in_ssl_mode}
					{preference name=feature_switch_ssl_mode}
					{preference name=http_port}
					{preference name=https_port}
					{preference name=https_external_links_for_users}
				</div>
	
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="rememberme">{tr}Remember me:{/tr}</label>
						<select name="rememberme" id="rememberme" onchange="hidedisabled('remembermeoptions',this.value);">
							<option value="disabled" {if $prefs.rememberme eq 'disabled'}selected="selected"{/if}>{tr}Disabled{/tr}</option>
							<option value="all" {if $prefs.rememberme eq 'all'} selected="selected"{/if}>{tr}User's choice{/tr}</option>
							<option value="always" {if $prefs.rememberme eq 'always'} selected="selected"{/if}>{tr}Always{/tr}</option>
						</select>
				 	{if $prefs.feature_help eq 'y'}{help url="Login+Config#Remember_Me"}{/if}
					</div>
				</div>
	
				<div id="remembermeoptions" style="clear:both;margin-left:2.5em;display:{if $prefs.rememberme eq 'disabled'}none{else}block{/if}">
					{preference name=remembermethod}
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
				<div class="adminoptionbox">
					<div class="adminoption">
						<input type="checkbox" id="login_is_email" name="login_is_email" {if $prefs.login_is_email eq 'y'}checked="checked"{/if} onclick="flip('useemailaslogin');" />
					</div>
					<div class="adminoptionlabel">
						<label for="login_is_email">{tr}Use email as username{/tr}.</label>
					</div>
				</div>
				<div id="useemailaslogin" style="display:{if $prefs.login_is_email eq 'y'}none{else}block{/if};">
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
				{preference name=min_pass_length}
				{preference name=pass_due}
			</fieldset>
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
				<legend>{tr}LDAP Bind settings{/tr} {if $prefs.feature_help eq 'y'} {help url="LDAP+Authentication"}{/if}</legend>
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
				{preference name=auth_ldap_adminuser}
				{preference name=auth_ldap_adminpass}
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
				<legend>{tr}Shibboleth{/tr} {if $prefs.feature_help eq 'y'}{help url="AuthShib" desc="{tr}Shibboleth Authentication {/tr}"}{/if}</legend>
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
				<legend>{tr}CAS (Central Authentication Service){/tr} {if $prefs.feature_help eq 'y'} {help url="CAS+Authentication"}{/if}</legend>
				{if $prefs.auth_method ne 'cas'}
					<div style="padding:0.5em;clear:both" class="simplebox">
						<div>
							{icon _id=information} {tr}You must change the Authentication Method to CAS for these changes to take effect{/tr}.
						</div>
					</div>
				{/if}

				{preference name='cas_create_user_tiki'}
				{preference name='cas_create_user_tiki_ldap'}
				{preference name='cas_skip_admin'}
				{preference name='cas_show_alternate_login'}
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
				<legend>{tr}phpBB{/tr} {if $prefs.feature_help eq 'y'}{help url="AuthphpBB" desc="{tr}phpBB User Database Authentication {/tr}"}{/if}</legend>
				<input type="hidden" name="auth_phpbb" />
				{if $prefs.auth_method ne 'phpbb'}
					<div style="padding:0.5em;clear:both" class="simplebox">
						<div>{icon _id=information} {tr}You must change the Authentication Method to phpBB for these changes to take effect{/tr}.</div>
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
	{/tabset}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
