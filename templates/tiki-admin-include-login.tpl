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
				<div class="adminoptionbox">
					<div class="adminoptionlabel"><label for="auth_method">{tr}Authentication method:{/tr}</label>
						<select name="auth_method" id="auth_method">
							<option value="tiki" {if $prefs.auth_method eq 'tiki'} selected="selected"{/if}>{tr}Tiki{/tr}</option>
							<!--option value="http" {if $prefs.auth_method eq 'http'} selected="selected"{/if}>{tr}Tiki and HTTP Auth{/tr}</option-->
							<option value="openid" {if $prefs.auth_method eq 'openid'} selected="selected"{/if}>{tr}Tiki and OpenID{/tr}</option>
							<option value="pam" {if $prefs.auth_method eq 'pam'} selected="selected"{/if}>{tr}Tiki and PAM{/tr}</option>
							<option value="ldap" {if $prefs.auth_method eq 'ldap'} selected="selected"{/if}>{tr}Tiki and LDAP{/tr}</option>
							<option value="cas" {if $prefs.auth_method eq 'cas'} selected="selected"{/if}>{tr}CAS (Central Authentication Service){/tr}</option>
							<option value="shib" {if $prefs.auth_method eq 'shib'} selected="selected"{/if}>{tr}Shibboleth{/tr}</option>
							<option value="ws" {if $prefs.auth_method eq 'ws'} selected="selected"{/if}>{tr}Web Server{/tr}</option>
							<option value="phpbb" {if $prefs.auth_method eq 'phpbb'} selected="selected"{/if}>{tr}phpBB{/tr}</option>
						</select> {if $prefs.feature_help eq 'y'} {help url="Login+Authentication+Methods"}{/if}
					</div>
				</div>	
			
				<fieldset><legend>{tr}Registration{/tr} &amp; {tr}Log in{/tr}</legend>
					<div class="adminoptionbox">
							{preference name=allowRegister}

						<div class="adminoptionboxchild" id="allowRegister_childcontainer">
							<div class="adminoptionbox">
								<div class="adminoption"><input type="checkbox" id="validateUsers" name="validateUsers" {if $prefs.validateUsers eq 'y'}checked="checked"{/if} /></div>
								<div class="adminoptionlabel"><label for="validateUsers">{tr}Validate by email{/tr}.</label>
									{if empty($prefs.sender_email)}<br /><span class="highlight">{tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a>{/tr}</span>{/if}
								</div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoption"><input type="checkbox" id="validateEmail" name="validateEmail" {if $prefs.validateEmail eq 'y'}checked="checked"{/if} /></div>
								<div class="adminoptionlabel"><label for="validateEmail">{tr}Validate user's email server{/tr}.</label></div>
							</div>
			
							<div class="adminoptionbox">
								<div class="adminoption"><input type="checkbox" id="validateRegistration" name="validateRegistration" {if $prefs.validateRegistration eq 'y'}checked="checked"{/if}  onclick="flip('validateRegistrationOptions');"/></div>
								<div class="adminoptionlabel"><label for="validateRegistration">{tr}Require validation by Admin{/tr}.</label>
									{if empty($prefs.sender_email)}<br /><span class="highlight">{tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a>{/tr}</span>{/if}
								</div>
								<div id="validateRegistrationOptions" style="clear:both;display:{if $prefs.validateRegistration eq 'y'}block{else}none{/if};margin-left:2.5em;">
									 <label>{tr}Validator emails (separated by comma) if different than the sender email:{/tr}<input type="text" name="validator_emails" value="{$prefs.validator_emails|escape}" id="validator_emails" /></label>
								</div>
							</div>
			
							<div class="adminoptionbox">
								<div class="adminoption"><input type="checkbox" id="useRegisterPasscode" name="useRegisterPasscode" {if $prefs.useRegisterPasscode eq 'y'}checked="checked"{/if} onclick="flip('usepasscode');" /></div>
									<div class="adminoptionlabel"><label for="useRegisterPasscode">{tr}Require passcode to register{/tr}.</label>
										<div id="usepasscode" style="display:{if $prefs.useRegisterPasscode eq 'y'}block{else}none{/if};" class="adminoptionboxchild">
											<div class="adminoptionlabel">{tr}Passcode:{/tr} <input type="text" name="registerPasscode" value="{$prefs.registerPasscode|escape}" size="20" /><br /><em>{tr}Users must enter this code to register{/tr}.</em></div>
										</div>
									</div>
							</div>
			
								<div class="adminoptionbox">
									<div class="adminoption"><input type="checkbox" id="rnd_num_reg" name="rnd_num_reg"{if $gd_lib_found neq 'y'} disabled="disabled"{/if}{if $prefs.rnd_num_reg eq 'y'} checked="checked"{/if} /></div>
									<div class="adminoptionlabel"><label for="rnd_num_reg">{tr}Use CAPTCHA to prevent automatic/robot registrations{/tr}.</label>
										{if $gd_lib_found neq 'y'}<br /><span class="highlight">{icon _id=information} {tr}Requires PHP GD library{/tr}.</span>{/if}{if $prefs.feature_help eq 'y'} {help url="Spam+Protection"}{/if}
									</div>
								</div>
			
								<div class="adminoptionbox">
									<div class="adminoption"><input type="checkbox" id="generate_password" name="generate_password" {if $prefs.generate_password eq 'y'}checked="checked"{/if}/></div>
									<div class="adminoptionlabel"><label for="generate_password">{tr}Include &quot;Generate Password&quot; option on registration form{/tr}.</label></div>
								</div>
			
								<div class="adminoptionbox">
									<div class="adminoptionlabel"><label for="registration_choices">{tr}Users can select a group to join at registration:{/tr}</label>
										<br /><em>{tr}By default, new users automatically join the Registered group{/tr}.</em>
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

									<div class="adminoptionbox">
										{preference name='url_after_validation'}
									</div>

								</div>
						</div>

			
							<div class="adminoptionbox">
								<div class="adminoption"><input id="userTracker" type="checkbox" name="userTracker" {if $prefs.userTracker eq 'y'}checked="checked"{/if} {if $prefs.feature_trackers ne 'y'}disabled="disabled" {/if}/></div>
								<div class="adminoptionlabel"><label for="userTracker">{tr}Use tracker to collect more user information{/tr}.</label> {if $prefs.feature_help eq 'y'} {help url="User+Tracker"}{/if} <br />
									{if $prefs.feature_trackers ne 'y'}<span>{icon _id=information} {tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}.</a></span>
									{else}<em>{tr}Use the <strong><a href="tiki-admingroups.php" title="Admin Groups">Admin: Groups</a></strong> page to select which tracker and fields to display{/tr}.</em>
									{/if}
								</div>
							</div>
			
							<div class="adminoptionbox">
								<div class="adminoption"><input type="checkbox" id="groupTracker" name="groupTracker" {if $prefs.groupTracker eq 'y'}checked="checked"{/if} {if $prefs.userTracker eq 'y'}checked="checked"{/if} {if $prefs.feature_trackers ne 'y'}disabled="disabled" {/if}/></div>
								<div class="adminoptionlabel"><label for="groupTracker">{tr}Use tracker to collect more group information{/tr}.</label> {if $prefs.feature_help eq 'y'} {help url="User+Tracker"}{/if} <br />
									{if $prefs.feature_trackers ne 'y'}<span>{icon _id=information} {tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}.</a></span>
									{else}<em>{tr}Use the <strong><a href="tiki-admingroups.php" title="Admin Groups">Admin: Groups</a></strong> page to select which tracker and fields to display{/tr}.</em>
									{/if}
								</div>
							</div>
			
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="email_due">{tr}Re-validate user by email after{/tr}</label> <input type="text" name="email_due" id="email_due" value="{$prefs.email_due|escape}" size="5" /> {tr}days{/tr}.
									<br /><em>{tr}Use <strong>-1</strong> for never{/tr}.</em></div>
								</div>
			
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="unsuccessful_logins">{tr}Re-validate user by email after{/tr}</label> <input id="unsuccessful_logins" type="text" name="unsuccessful_logins" size="5" value="{$prefs.unsuccessful_logins|escape}" /> {tr}unsuccessful login attempts{/tr}.
									<br /><em>{tr}Use <strong>-1</strong> for never{/tr}.</em>
								</div>
							</div>
			
							<div class="adminoptionbox">
								<div class="adminoption"><input type="checkbox" id="eponymousGroups" name="eponymousGroups" {if $prefs.eponymousGroups eq 'y'}checked="checked"{/if}/></div>
								<div class="adminoptionlabel"><label for="eponymousGroups">{tr}Create a new group for each user{/tr}.</label><br /><em>{tr}The group will be named identical to the user's username{/tr}.</em> {if $prefs.feature_help eq 'y'} {help url="Groups"}{/if} </div>
							</div>
			
							<div class="adminoptionbox">
								<div class="adminoption"><input type="checkbox" id="desactive_login_autocomplete" name="desactive_login_autocomplete" {if $prefs.desactive_login_autocomplete eq 'y'}checked="checked"{/if} /></div>
								<div class="adminoptionlabel"><label for="desactive_login_autocomplete">{tr}Disable browser's autocomplete feature for username and password fields{/tr}.</label> </div>
							</div>

							<div class="adminoptionbox">
								<div class="adminoption"><input type="checkbox" id="feature_challenge" name="feature_challenge" {if $prefs.feature_challenge eq 'y'}checked="checked" {/if}onclick="flip('challangeresponse');" /></div>
								<div class="adminoptionlabel"><label for="feature_challenge">{tr}Use challenge/response authentication{/tr}.</label></div>

								<div id="challangeresponse" class="adminoptinboxchild" style="display:{if $prefs.feature_challenge eq 'y'}block{else}none{/if};">
									{icon _id=information} <em>{tr}Confirm that the Admin account has a valid email address or you will not be permitted to login{/tr}.</em>
								</div>
							</div>

							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="https_login">{tr}Use HTTPS login:{/tr}</label> 
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
							<div class="adminoptionbox">
								<div class="adminoption"><input type="checkbox" id="feature_show_stay_in_ssl_mode" name="feature_show_stay_in_ssl_mode" {if $prefs.feature_show_stay_in_ssl_mode eq 'y'}checked="checked"{/if} /></div>
								<div class="adminoptionlabel"><label for="feature_show_stay_in_ssl_mode">{tr}Users can choose to stay in SSL mode after an HTTPS login{/tr}.</label></div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoption"><input id="feature_switch_ssl_mode" type="checkbox" name="feature_switch_ssl_mode" {if $prefs.feature_switch_ssl_mode eq 'y'}checked="checked"{/if }/></div>
								<div class="adminoptionlabel"><label for="feature_switch_ssl_mode">{tr}Users can switch between secured or standard mode at login{/tr}.</label></div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="http_port">{tr}HTTP port:{/tr}</label> <input id="http_port" type="text" name="http_port" size="5" value="{$prefs.http_port|escape}" /> </div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="https_port">{tr}HTTPS port:{/tr}</label> <input id="https_port" type="text" name="https_port" size="5" value="{$prefs.https_port|escape}" /> </div>
							</div>
							{preference name=https_external_links_for_users}
						</div>
	
						<div class="adminoptionbox">
							<div class="adminoptionlabel"><label for="rememberme">{tr}Remember me:{/tr}</label> 
								<select name="rememberme" id="rememberme" onchange="hidedisabled('remembermeoptions',this.value);">
									<option value="disabled" {if $prefs.rememberme eq 'disabled'}selected="selected"{/if}>{tr}Disabled{/tr}</option>
									<option value="all" {if $prefs.rememberme eq 'all'} selected="selected"{/if}>{tr}User's choice{/tr}</option>
									<option value="always" {if $prefs.rememberme eq 'always'} selected="selected"{/if}>{tr}Always{/tr}</option>
								</select>
						 	{if $prefs.feature_help eq 'y'}{help url="Login+Config#Remember_Me"}{/if}
							</div>
						</div>
	
						<div id="remembermeoptions" style="clear:both;margin-left:2.5em;display:{if $prefs.rememberme eq 'disabled'}none{else}block{/if}">
	
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="remembermethod">{tr}Method:{/tr}</label> 
									<select name="remembermethod" id="remembermethod">
										<option value="" {if $prefs.remembermethod eq ''}selected="selected"{/if}>{tr}Standard{/tr}</option>
										<option value="simple" {if $prefs.remembermethod eq 'simple'} selected="selected"{/if}>{tr}Simple{/tr}</option>
									</select>
									<br /><em>{tr}&quot;Standard&quot; uses the client's IP and is more secure. &quot;Simple&quot; uses a unique ID and is more reliable{/tr}.</em>
								</div>
							</div>

							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="remembertime">{tr}Duration:{/tr}</label> 
									<select name="remembertime" id="remembertime">
										<option value="300" {if $prefs.remembertime eq 300} selected="selected"{/if}>5 {tr}minutes{/tr}</option>
										<option value="900" {if $prefs.remembertime eq 900} selected="selected"{/if}>15 {tr}minutes{/tr}</option>
										<option value="1800" {if $prefs.remembertime eq 1800} selected="selected"{/if}>30 {tr}minutes{/tr}</option>
										<option value="3600" {if $prefs.remembertime eq 3600} selected="selected"{/if}>1 {tr}hour{/tr}</option>
										<option value="7200" {if $prefs.remembertime eq 7200} selected="selected"{/if}>2 {tr}hours{/tr}</option>
										<option value="36000" {if $prefs.remembertime eq 36000} selected="selected"{/if}>10 {tr}hours{/tr}</option>
										<option value="72000" {if $prefs.remembertime eq 72000} selected="selected"{/if}>20 {tr}hours{/tr}</option>
										<option value="86400" {if $prefs.remembertime eq 86400} selected="selected"{/if}>1 {tr}day{/tr}</option>
										<option value="604800" {if $prefs.remembertime eq 604800} selected="selected"{/if}>1 {tr}week{/tr}</option>
										<option value="2629743" {if $prefs.remembertime eq 2629743} selected="selected"{/if}>1 {tr}month{/tr}</option>
										<option value="31556926" {if $prefs.remembertime eq 31556926} selected="selected"{/if}>1 {tr}year{/tr}</option>
									</select>
								</div>
							</div>
						</div>
	
						<fieldset><legend>{tr}Cookie{/tr}</legend>
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="cookie_name">{tr}Cookie name:{/tr}</label> 
									<input type="text" id="cookie_name" name="cookie_name" value="{$prefs.cookie_name|escape}" size="50" />
								</div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="cookie_domain">{tr}Domain:{/tr}</label> 
									<input type="text" id="cookie_domain" name="cookie_domain" value="{$prefs.cookie_domain|escape}" size="50" />
								</div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="cookie_path">{tr}Path:{/tr}</label> 
									<input type="text" id="cookie_path" name="cookie_path" value="{$prefs.cookie_path|escape}" size="50" />
								</div>
							</div>
						</fieldset>
					</div>

					{preference name=feature_banning}

				</fieldset>
	
				<fieldset><legend>{tr}Username{/tr}</legend>
					<div class="adminoptionbox">
						<div class="adminoption"><input type="checkbox" id="login_is_email" name="login_is_email" {if $prefs.login_is_email eq 'y'}checked="checked"{/if} onclick="flip('useemailaslogin');" /></div>
						<div class="adminoptionlabel"><label for="login_is_email">{tr}Use email as username{/tr}.</label></div>
					</div>
					<div id="useemailaslogin" style="display:{if $prefs.login_is_email eq 'y'}none{else}block{/if};">
						<div class="adminoptionbox">
							<div class="adminoptionlabel"><label for="min_username_length">{tr}Minimum length:{/tr}</label> <input type="text" id="min_username_length" name="min_username_length" value="{$prefs.min_username_length|escape}" size="5" /></div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel"><label for="max_username_length">{tr}Maximum length:{/tr}</label> <input type="text" id="max_username_length" name="max_username_length" value="{$prefs.max_username_length|escape}" size="5" /></div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoption"><input type="checkbox" id="lowercase_username" name="lowercase_username" {if $prefs.lowercase_username eq 'y'}checked="checked"{/if}/></div>
							<div class="adminoptionlabel"><label for="lowercase_username">{tr}Force lowercase{/tr}.</label> {if $prefs.feature_help eq 'y'} {help url="Login+Config#Case_Sensitivity"}{/if}</div>
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="username_pattern">{tr}Username pattern:{/tr}</label> <input type="text" name="username_pattern" value="{$prefs.username_pattern|escape}" id="username_pattern" /></div>
					</div>
				</fieldset>
	
				<fieldset><legend>{tr}Password{/tr}</legend>
					{if $prefs.feature_clear_passwords eq 'y'} {* deprecated *}
						{preference name='feature_clear_passwords'}
						<div class="adminoptionboxchild" id='feature_clear_passwords_childcontainer'>
							{remarksbox type='warning' title='Security risk'}{tr}Store passwords in plain text is activated. You should never set this unless you know what you are doing.{/tr}{/remarksbox}
						</div>
					{/if}
	
					{preference name='forgotPass'}

					{preference name='feature_crypt_passwords'}
	
					<div class="adminoptionbox">
						<div class="adminoption"><input type="checkbox" id="change_password" name="change_password" {if $prefs.change_password eq 'y'}checked="checked"{/if} /></div>
						<div class="adminoptionlabel"><label for="change_password">{tr}Users can change their password{/tr}.</label> {if $prefs.feature_help eq 'y'} {help url="User+Preferences"}{/if}</div>
					</div>
	
					<div class="adminoptionbox">
						<div class="adminoption"><input type="checkbox" id="pass_chr_num" name="pass_chr_num" {if $prefs.pass_chr_num eq 'y'}checked="checked"{/if}/></div>
						<div class="adminoptionlabel"><label for="pass_chr_num">{tr}Require characters and numerals{/tr}.</label></div>
					</div>
	
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="min_pass_length">{tr}Minimum length:{/tr}</label> <input id="min_pass_length" type="text" name="min_pass_length" value="{$prefs.min_pass_length|escape}" size="5" /></div>
					</div>
	
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="pass_due">{tr}Password expires after{/tr}</label> <input id="pass_due" type="text" name="pass_due" value="{$prefs.pass_due|escape}" size="5"/> days.</div>
						<em>{tr}Use <strong>-1</strong> for never{/tr}.</em>
					</div>
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
