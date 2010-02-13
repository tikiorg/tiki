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
						</select> {if $prefs.feature_help eq 'y'} {help url="Login+Authentication+Methods"}{/if}
					</div>
				</div>	
			
				<fieldset><legend>{tr}Registration{/tr} &amp; {tr}Login{/tr}</legend>
					<div class="adminoptionbox">
						<div class="adminoption">
							<input type="checkbox" id="allowRegister" name="allowRegister" {if $prefs.allowRegister eq 'y'}checked="checked"{/if} onclick="flip('userscanregister');" />
						</div>
						<div class="adminoptionlabel"><label for="allowRegister">{tr}Users can register{/tr}.</label></div>
						<div id="userscanregister" style="clear:both;display:{if $prefs.allowRegister eq 'y'}block{else}none{/if};margin-left:2.5em;">
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
										<div class="adminoptionlabel"><label for="url_after_validation">{tr}Url a user is redirected after account validation:{/tr}</label> <input type="text" id="url_after_validation" name="url_after_validation" value="{$prefs.url_after_validation|escape}" /><br /><em>{tr}Default:{/tr} tiki-information.php?msg={tr}Account validated successfully.{/tr}</em></div>
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
				<fieldset><legend>LDAP {help url="Login+Authentication+Methods"}</legend>
					{if $prefs.auth_method ne 'ldap'}
						<div style="padding:0.5em;clear:both" class="simplebox">
							<div>{icon _id=information} {tr}You must change the Authentication Method to LDAP for these changes to take effect{/tr}.</div>
						</div>
					{/if}

					<div class="adminoptionbox">
						<div class="adminoption"><input type="checkbox" id="ldap_create_user_tiki" name="ldap_create_user_tiki" {if $prefs.ldap_create_user_tiki eq 'y'}checked="checked"{/if} /></div>
						<div class="adminoptionlabel"><label for="ldap_create_user_tiki">{tr}Create user if not in Tiki{/tr}.</label></div>
					</div>

					<div class="adminoptionbox">
						<div class="adminoption"><input type="checkbox" id="ldap_create_user_ldap" name="ldap_create_user_ldap" {if $prefs.ldap_create_user_ldap eq 'y'}checked="checked"{/if} /></div>
						<div class="adminoptionlabel"><label for="ldap_create_user_ldap">{tr}Create user if not in LDAP{/tr}.</label></div>
					</div>

					<div class="adminoptionbox">
						<div class="adminoption"><input type="checkbox" id="ldap_skip_admin" name="ldap_skip_admin" {if $prefs.ldap_skip_admin eq 'y'}checked="checked"{/if} /></div>
						<div class="adminoptionlabel"><label for="ldap_skip_admin">{tr}Use Tiki authentication for Admin login{/tr}.</label></div>
					</div>
                                        <div class="adminoptionbox">
                                                <div class="adminoption"><input type="checkbox" id="auth_ldap_permit_tiki_users" name="auth_ldap_permit_tiki_users" {if $prefs.auth_ldap_permit_tiki_users eq 'y'}checked="checked"{/if} /></div>
                                                <div class="adminoptionlabel"><label for="auth_ldap_permit_tiki_users">{tr}Use Tiki authentication for users created in tiki{/tr}.</label></div>
                                        </div>
				</fieldset>

				<fieldset><legend>{tr}LDAP Bind settings{/tr} {if $prefs.feature_help eq 'y'} {help url="LDAP+Authentication"}{/if}</legend>
					<!--
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_url">{tr}URL:{/tr}</label>
							<input type="text" id="auth_ldap_url" name="auth_ldap_url" value="{$prefs.auth_ldap_url|escape}" size="50" />
							<br /><em>{tr}Will override the Host and Port settings{/tr}.</em>
						</div>
					</div>
					-->
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_host">{tr}Host:{/tr}</label>
							<input type="text" id="auth_ldap_host" name="auth_ldap_host" value="{$prefs.auth_ldap_host|escape}" />
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_port">{tr}Port:{/tr}</label>
							<input type="text" name="auth_ldap_port" id="auth_ldap_port" value="{$prefs.auth_ldap_port|escape}" size="5" />
						</div>
					</div>
                                        <div class="adminoptionbox">
                                                <div class="adminoptionlabel"><label for="auth_ldap_debug">{tr}Write LDAP debug Information in Tiki Logs:{/tr}</label>
                                                        <input id="auth_ldap_debug" type="checkbox" name="auth_ldap_debug" {if $prefs.auth_ldap_debug eq 'y'}checked="checked"{/if} />
                                                </div>
                                        </div>
                                        <div class="adminoptionbox">
                                                <div class="adminoptionlabel"><label for="auth_ldap_ssl">{tr}Use SSL (ldaps):{/tr}</label>
							<input id="auth_ldap_ssl" type="checkbox" name="auth_ldap_ssl" {if $prefs.auth_ldap_ssl eq 'y'}checked="checked"{/if} />
						</div>
                                        </div>
					<div class="adminoptionbox">
                                                <div class="adminoptionlabel"><label for="auth_ldap_starttls">{tr}Use TLS:{/tr}</label>
                                                	<input id="auth_ldap_starttls" type="checkbox" name="auth_ldap_starttls" {if $prefs.auth_ldap_starttls eq 'y'}checked="checked"{/if} />
						</div>
                                        </div>
                                        <div class="adminoptionbox">
                                                <div class="adminoptionlabel"><label for="auth_ldap_type">{tr}LDAP Bind Type:{/tr}</label>
                                                        <select name="auth_ldap_type" id="auth_ldap_type">
                                                                <option value="full" {if $prefs.auth_ldap_type eq "full"} selected="selected"{/if}>{tr}Default: userattr=username,UserDN,BaseDN{/tr}</option>
                                                                <option value="ol" {if $prefs.auth_ldap_type eq "ol"} selected="selected"{/if}>{tr}userattr=username,BaseDN{/tr}</option>
                                                                <option value="ad" {if $prefs.auth_ldap_type eq "ad"} selected="selected"{/if}>{tr}Active Directory (username@domain){/tr}</option>
                                                                <option value="plain" {if $prefs.auth_ldap_type eq "plain"} selected="selected"{/if}>{tr}Plain Username{/tr}</option>
                                                        </select>
                                                </div>
                                        </div>



					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_scope">{tr}Search scope:{/tr}</label>
							<select name="auth_ldap_scope" id="auth_ldap_scope">
								<option value="sub" {if $prefs.auth_ldap_scope eq "sub"} selected="selected"{/if}>{tr}Subtree{/tr}</option>
								<option value="one" {if $prefs.auth_ldap_scope eq "one"} selected="selected"{/if}>{tr}One level{/tr}</option>
								<option value="base" {if $prefs.auth_ldap_scope eq "base"} selected="selected"{/if}>{tr}Base object{/tr}</option>
							</select>
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_version">{tr}LDAP version:{/tr}</label>
							<input type="text" id="auth_ldap_version" name="auth_ldap_version" value="{$prefs.auth_ldap_version|escape}" />
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_basedn">{tr}Base DN:{/tr}</label>
							<input type="text" name="auth_ldap_basedn" id="auth_ldap_basedn" value="{$prefs.auth_ldap_basedn|escape}" />
						</div>
					</div>
					</fieldset>

					<fieldset><legend>{tr}LDAP User{/tr}</legend>
						<div class="adminoptionbox">
							<div class="adminoptionlabel"><label for="auth_ldap_userdn">{tr}User DN:{/tr}</label>
								<input type="text" id="auth_ldap_userdn" name="auth_ldap_userdn" value="{$prefs.auth_ldap_userdn|escape}" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel"><label for="auth_ldap_userattr">{tr}User attribute:{/tr}</label>
								<input type="text" name="auth_ldap_userattr" id="auth_ldap_userattr" value="{$prefs.auth_ldap_userattr|escape}" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel"><label for="auth_ldap_useroc">{tr}User OC:{/tr}</label>
								<input type="text" name="auth_ldap_useroc" id="auth_ldap_useroc" value="{$prefs.auth_ldap_useroc|escape}" />
							</div>
						</div>
	                                        <div class="adminoptionbox">
        	                                        <div class="adminoptionlabel"><label for="auth_ldap_nameattr">{tr}Realname attribute:{/tr}</label>
                	                                        <input type="text" id="auth_ldap_nameattr" name="auth_ldap_nameattr" value="{$prefs.auth_ldap_nameattr|escape}" />
                        	                        </div>
                                	        </div>
                                        	<div class="adminoptionbox">
                                                	<div class="adminoptionlabel"><label for="auth_ldap_countryattr">{tr}Country attribute:{/tr}</label>
                                                        	<input type="text" id="auth_ldap_countryattr" name="auth_ldap_countryattr" value="{$prefs.auth_ldap_countryattr|escape}" />
                      	                          </div>
                        	                </div>
                                	        <div class="adminoptionbox">
                                        	        <div class="adminoptionlabel"><label for="auth_ldap_emailattr">{tr}E-mail attribute:{/tr}</label>
                                                	        <input type="text" id="auth_ldap_emailattr" name="auth_ldap_emailattr" value="{$prefs.auth_ldap_emailattr|escape}" />
                                      	          </div>
                                       		 </div>
<!--
	                                        <div class="adminoptionbox">
       	                                         <div class="adminoptionlabel"><label for="auth_ldap_syncuserattr">{tr}Synchronize user attributes to tiki everytime the user logs in:{/tr}</label>
                                                        <input type="checkbox" id="auth_ldap_syncuserattr" name="auth_ldap_syncuserattr" {if $prefs.auth_ldap_syncuserattr eq 'y'}checked="checked"{/if} />
       	                                         </div>
        	                                </div>
-->

					</fieldset>

				<fieldset><legend>{tr}LDAP Group{/tr}</legend>
<!--
                                                <div class="adminoptionbox">
                                                 <div class="adminoptionlabel"><label for="auth_ldap_syncgroupattr">{tr}Synchronize group attributes to tiki everytime the user logs in:{/tr}</label>
                                                        <input type="checkbox" id="auth_ldap_syncgroupattr" name="auth_ldap_syncgroupattr" {if $prefs.auth_ldap_syncgroupattr eq 'y'}checked="checked"{/if} />
							{tr}Note: this enables the usage of LDAP groups{/tr}
                                                 </div>
                                                </div>
-->
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_groupdn">{tr}Group DN:{/tr}</label>
							<input type="text" name="auth_ldap_groupdn" id="auth_ldap_groupdn" value="{$prefs.auth_ldap_groupdn|escape}" />
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_groupattr">{tr}Group attribute:{/tr}</label>
							<input type="text" name="auth_ldap_groupattr" id="auth_ldap_groupattr" value="{$prefs.auth_ldap_groupattr|escape}" />
						</div>
					</div>
               <div class="adminoptionbox">
                  <div class="adminoptionlabel"><label for="auth_ldap_groupdescattr">{tr}Group description attribute:{/tr}</label>
                     <input type="text" name="auth_ldap_groupdescattr" id="auth_ldap_groupdescattr" value="{$prefs.auth_ldap_groupdescattr|escape}" />
                  </div>                                
               </div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_groupoc">{tr}Group OC:{/tr}</label>
							<input id="auth_ldap_groupoc" type="text" name="auth_ldap_groupoc" value="{$prefs.auth_ldap_groupoc|escape}" />
						</div>
					</div>
				</fieldset>

				<fieldset><legend>{tr}LDAP Group Member - if group membership can be found in group attributes{/tr}</legend>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_memberattr">{tr}Member attribute:{/tr}</label>
							<input type="text" id="auth_ldap_memberattr" name="auth_ldap_memberattr" value="{$prefs.auth_ldap_memberattr|escape}" />
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_memberisdn">{tr}Member is DN:{/tr}</label>
							<input type="checkbox" id="auth_ldap_memberisdn" name="auth_ldap_memberisdn" {if $prefs.auth_ldap_memberisdn eq 'y'}checked="checked"{/if} />
						</div>
					</div>
				</fieldset>
				<fieldset><legend>{tr}LDAP User Group - if group membership can be found in user attributes{/tr}</legend>
                                        <div class="adminoptionbox">
                                                <div class="adminoptionlabel"><label for="auth_ldap_usergroupattr">{tr}Group attribute:{/tr}</label>
                                                        <input type="text" id="auth_ldap_usergroupattr" name="auth_ldap_usergroupattr" value="{$prefs.auth_ldap_usergroupattr|escape}" />
                                                </div>
                                        </div>
                                        <div class="adminoptionbox">
                                                <div class="adminoptionlabel"><label for="auth_ldap_groupgroupattr">{tr}Group attribute in group entry:{/tr}</label>
                                                        <input type="text" id="auth_ldap_groupgroupattr" name="auth_ldap_groupgroupattr" value="{$prefs.auth_ldap_groupgroupattr|escape}" />
							(Leave this empty if the group name is already given in the user attribute)
                                                </div>
                                        </div>
                                </fieldset>
				<fieldset><legend>{tr}LDAP Admin{/tr}</legend>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_adminuser">{tr}Admin user:{/tr}</label>
							<input type="text" id="auth_ldap_adminuser" name="auth_ldap_adminuser" value="{$prefs.auth_ldap_adminuser|escape}" />
						</div>
					</div>
					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="auth_ldap_adminpass">{tr}Admin password:{/tr}</label>
							<input type="password" id="auth_ldap_adminpass" name="auth_ldap_adminpass" value="{$prefs.auth_ldap_adminpass|escape}" />
						</div>
					</div>
				</fieldset>
			{/tab}

			{tab name="{tr}PAM{/tr}"}
				<input type="hidden" name="auth_pam" />
				<fieldset><legend>{tr}PAM{/tr} {help url="AuthPAM" desc="{tr}PAM{/tr}"}</legend>
	
					{if $prefs.auth_method ne 'pam'}
						<div style="padding:0.5em;clear:both" class="simplebox">
							<div>{icon _id=information} {tr}You must change the Authentication Method to PAM for these changes to take effect{/tr}.</div>
						</div>
					{/if}

					<div class="adminoptionbox">
						<div class="adminoption"><input type="checkbox" name="pam_create_user_tiki" {if $prefs.pam_create_user_tiki eq 'y'}checked="checked"{/if} id="pam_create_user_tiki" /></div>
						<div class="adminoptionlabel"><label for="pam_create_user_tiki">{tr}Create user if not in Tiki{/tr}.</label></div>
					</div>

					<div class="adminoptionbox">
						<div class="adminoption"><input type="checkbox" id="pam_skip_admin" name="pam_skip_admin" {if $prefs.pam_skip_admin eq 'y'}checked="checked"{/if} /></div>
						<div class="adminoptionlabel"><label for="pam_skip_admin">{tr}Use Tiki authentication for Admin login{/tr}.</label></div>
					</div>

					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="pam_service">{tr}PAM service:{/tr}</label>
							<input type="text" id="pam_service" name="pam_service" value="{$prefs.pam_service|escape}" />
							<br /><em>{tr}Currently unused{/tr}.</em>
						</div>
					</div>
				</fieldset>
			{/tab}

			{tab name="{tr}Shibboleth{/tr}"}
				<fieldset><legend>{tr}Shibboleth{/tr} {if $prefs.feature_help eq 'y'}{help url="AuthShib" desc="{tr}Shibboleth Authentication {/tr}"}{/if}</legend>
					<input type="hidden" name="auth_shib" />	  
					{if $prefs.auth_method ne 'shib'}
						<div style="padding:0.5em;clear:both" class="simplebox">
							<div>{icon _id=information} {tr}You must change the Authentication Method to Shibboleth for these changes to take effect{/tr}.</div>
						</div>
					{/if}

					<div class="adminoptionbox">
						<div class="adminoption"><input id="shib_create_user_tiki" type="checkbox" name="shib_create_user_tiki" {if $prefs.shib_create_user_tiki eq 'y'}checked="checked"{/if} /></div>
						<div class="adminoptionlabel"><label for="shib_create_user_tiki">{tr}Create user if not in Tiki{/tr}.</label></div>
					</div>

					<div class="adminoptionbox">
						<div class="adminoption"><input id="shib_skip_admin" type="checkbox" name="shib_skip_admin" {if $prefs.shib_skip_admin eq 'y'}checked="checked"{/if} /></div>
						<div class="adminoptionlabel"><label for="shib_skip_admin">{tr}Use Tiki authentication for Admin login{/tr}.</label></div>
					</div>

					<div class="adminoptionbox">
						<div class="adminoptionlabel"><label for="shib_affiliation">{tr}Valid affiliations:{/tr}</label>
							<input type="text" id="shib_affiliation" name="shib_affiliation" value="{$prefs.shib_affiliation}" size="50" />
							<br /><em>{tr}Separate multiple affiliations with commas{/tr}.</em>
						</div>
					</div>

					<div class="adminoptionbox">
						<div class="adminoption"><input type="checkbox" id='shib_usegroup' name="shib_usegroup" {if $prefs.shib_usegroup eq 'y'}checked="checked"{/if} onclick="flip('defaultgroup');" /></div>
						<div class="adminoptionlabel"><label for="shib_usegroup">{tr}Create with default group{/tr}.</label></div>

						<div id="defaultgroup" style="margin-left:2.5em;display:{if $prefs.shib_usegroup eq 'y'}block{else}none{/if};">
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="shib_group">{tr}Default group:{/tr}</label>
									<input type="text" id="shib_group" name="shib_group" value="{$prefs.shib_group}" size="50"/>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			{/tab}

			{tab name="{tr}CAS{/tr}"}
				<input type="hidden" name="auth_cas" />
				<fieldset><legend>{tr}CAS (Central Authentication Service){/tr} {if $prefs.feature_help eq 'y'} {help url="CAS+Authentication"}{/if}</legend>
					{if $prefs.auth_method ne 'cas'}
						<div style="padding:0.5em;clear:both" class="simplebox">
							<div>{icon _id=information} {tr}You must change the Authentication Method to CAS for these changes to take effect{/tr}.</div>
						</div>
					{/if}

					{if $phpcas_enabled eq 'y'}
						{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}You also need to upload the <a target="_blank" href="http://esup-phpcas.sourceforge.net/">phpCAS library</a> separately to lib/phpcas/.{/tr}{/remarksbox}

						<div class="adminoptionbox">
							<div class="adminoption"><input id="cas_create_user_tiki" type="checkbox" name="cas_create_user_tiki" {if $prefs.cas_create_user_tiki eq 'y'}checked="checked"{/if} /></div>
							<div class="adminoptionlabel"><label for="cas_create_user_tiki">{tr}Create user if not in Tiki{/tr}.</label></div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoption"><input id="cas_skip_admin" type="checkbox" name="cas_skip_admin" {if $prefs.cas_skip_admin eq 'y'}checked="checked"{/if} /></div>
							<div class="adminoptionlabel"><label for="cas_skip_admin">{tr}Use Tiki authentication for Admin login{/tr}.</label></div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel"><label for="cas_version">{tr}CAS server version:{/tr}</label>
								<select name="cas_version" id="cas_version">
									<option value="none" {if $prefs.cas_version neq "1" && $prefs.cas_version neq "2"} selected="selected"{/if}></option>
									<option value="1.0" {if $prefs.cas_version eq "1.0"} selected="selected"{/if}>{tr}Version 1.0{/tr}</option>
									<option value="2.0" {if $prefs.cas_version eq "2.0"} selected="selected"{/if}>{tr}Version 2.0{/tr}</option>
								</select>
							</div>
						</div>

						<fieldset><legend>{tr}CAS Server{/tr}</legend>
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="cas_hostname">{tr}Hostname:{/tr}</label> <input type="text" name="cas_hostname" id="cas_hostname" value="{$prefs.cas_hostname|escape}" size="50" /></div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="cas_port">{tr}Port:{/tr}</label> <input type="text" name="cas_port" id="cas_port" size="5" value="{$prefs.cas_port|escape}" /></div>
							</div>
							<div class="adminoptionbox">
								<div class="adminoptionlabel"><label for="cas_path">{tr}Path:{/tr}</label> <input id="cas_path" type="text" name="cas_path" value="{$prefs.cas_path|escape}" size="50" /></div>
							</div>
						</fieldset>
					{else}
						<p>{icon _id=delete} {tr}You must enable PHP CAS first{/tr}. {help url="Mod+phpcas"}</p>
					{/if}
				</fieldset>	 
			{/tab}
		{/tabset}
		<div class="heading input_submit_container" style="text-align: center">
			<input type="submit" value="{tr}Change preferences{/tr}" />
		</div>
	</form>


