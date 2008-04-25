{* $Id$ *}
<div class="navbar">
<span class="button2"><a href="tiki-admingroups.php" class="linkbut">{tr}Admin Groups{/tr}</a></span>
<span class="button2"><a href="tiki-adminusers.php" class="linkbut">{tr}Admin Users{/tr}</a></span>
</div>

<div class="cbox">
<div class="cbox-title">
  {tr}User registration and login{/tr}
  {help url="Login+Config" desc="{tr}User registration and login{/tr}"}
</div>
<div class="cbox-data">
<form action="tiki-admin.php?page=login" method="post" name="login">
<table class="admin">
<tr><td class="form">{tr}Authentication method{/tr}</td><td>
<select name="auth_method">
<option value="tiki" {if $prefs.auth_method eq 'tiki'} selected="selected"{/if}>{tr}Just Tiki{/tr}</option>
<option value="ws" {if $prefs.auth_method eq 'ws'} selected="selected"{/if}>{tr}Web Server{/tr}</option>
<option value="auth" {if $prefs.auth_method eq 'auth'} selected="selected"{/if}>{tr}Tiki and PEAR::Auth{/tr}</option>
<option value="pam" {if $prefs.auth_method eq 'pam'} selected="selected"{/if}>{tr}Tiki and PAM{/tr}</option>
<option value="cas" {if $prefs.auth_method eq 'cas'} selected="selected"{/if}>{tr}CAS (Central Authentication Service){/tr}</option>
<option value="shib" {if $prefs.auth_method eq 'shib'} selected="selected"{/if}>{tr}Shibboleth{/tr}</option>
<option value="openid" {if $prefs.auth_method eq 'openid'} selected="selected"{/if}>{tr}OpenID and Tiki{/tr}</option>
<!--option value="http" {if $prefs.auth_method eq 'http'} selected="selected"{/if}>{tr}Tiki and HTTP Auth{/tr}</option-->
</select></td></tr>
<!--<tr><td class="form">{tr}Use WebServer authentication for Tiki{/tr}:</td><td><input type="checkbox" name="webserverauth" {if $prefs.webserverauth eq 'y'}checked="checked"{/if}/></td></tr>-->
<tr><td class="form">{tr}Users can register{/tr}:</td><td><input type="checkbox" name="allowRegister" {if $prefs.allowRegister eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}... but need admin validation{/tr}:</td><td><input type="checkbox" name="validateRegistration" {if $prefs.validateRegistration eq 'y'}checked="checked"{/if}/>
{if empty($prefs.sender_email)}
<div class="highlight">{tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a>{/tr}</div>
{/if} 
<tr><td class="form">{tr}Create a group for each user <br />(with the same name as the user){/tr}:</td><td><input type="checkbox"
name="eponymousGroups" {if $prefs.eponymousGroups eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use tracker for more user information{/tr}:</td><td><input type="checkbox" name="userTracker" {if $prefs.userTracker eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Use tracker for more group information{/tr}:</td><td><input type="checkbox" name="groupTracker" {if $prefs.groupTracker eq 'y'}checked="checked"{/if} /></td></tr>

<tr><td class="form">{tr}Request passcode to register{/tr}:</td><td><input type="checkbox" name="useRegisterPasscode" {if $prefs.useRegisterPasscode eq 'y'}checked="checked"{/if}/><input type="text" name="registerPasscode" value="{$prefs.registerPasscode|escape}"/></td></tr>
<tr><td class="form">{tr}Prevent automatic/robot registration{/tr}{php}if (!function_exists("gd_info")){ {/php} {tr} - Php GD library required{/tr}{php}}{/php}:</td><td><input type="checkbox" name="rnd_num_reg" {if $prefs.rnd_num_reg eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use login as email{/tr}:</td><td><input type="checkbox" name="login_is_email" {if $prefs.login_is_email eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Validate users by email{/tr}:</td><td><input type="checkbox" name="validateUsers" {if $prefs.validateUsers eq 'y'}checked="checked"{/if}/>
{if empty($prefs.sender_email)}
<div class="highlight">{tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a>{/tr}</div>
{/if} 
</td></tr>
<tr><td class="form">{tr}Validate user email server{/tr}:</td><td><input type="checkbox" name="validateEmail" {if $prefs.validateEmail eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Remind passwords by email (if "Store plaintext passwords" is activated.) Else, Reset passwords by email{/tr}:</td><td><input type="checkbox" name="forgotPass" {if $prefs.forgotPass ne 'n'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Store plaintext passwords{/tr}:</td><td><input type="checkbox" name="feature_clear_passwords" {if $prefs.feature_clear_passwords eq 'y'}checked="checked"{/if}/></td></tr>
<tr>
  <td class="form">{tr}Crypt passwords method{/tr}:</td>
  <td>
    <select name="feature_crypt_passwords">
      <option value='crypt-md5' {if $prefs.feature_crypt_passwords eq 'crypt-md5'}selected="selected"{/if}>crypt-md5</option>
      <option value='crypt-des' {if $prefs.feature_crypt_passwords eq 'crypt-des'}selected="selected"{/if}>crypt-des</option>
      <option value='tikihash' {if $prefs.feature_crypt_passwords eq 'tikihash'}selected="selected"{/if}>{tr}tikihash (old){/tr}</option>
    </select>
  </td>
</tr>
<tr>
  <td class="form">{tr}Reg users can change password{/tr}:</td>
  <td><input type="checkbox" name="change_password" {if $prefs.change_password eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr>
  <td class="form">{tr}Reg users can change theme{/tr}:</td>
  <td>
    <table><tr>
    <td style="width: 20px"><input type="checkbox" name="change_theme" {if $prefs.change_theme eq 'y'}checked="checked"{/if}/></td>
    <td>
      <div id="select_available_styles" {if count($prefs.available_styles) > 0 and $prefs.available_styles[0] ne ''}style="display:none;"{else}style="display:block;"{/if}>
        <a class="link" href="javascript:show('available_styles');hide('select_available_styles');">{tr}Restrict available themes{/tr}</a>
      </div>
      <div id="available_styles" {if count($prefs.available_styles) == 0 or $prefs.available_styles[0] eq ''}style="display:none;"{else}style="display:block;"{/if}>
        {tr}Available styles:{/tr}<br />
        <select name="available_styles[]" multiple="multiple" size="5">
		  <option value=''>{tr}All{/tr}</option>
          {section name=ix loop=$styles}
            <option value="{$styles[ix]|escape}"
              {if in_array($styles[ix], $prefs.available_styles)}selected="selected"{/if}>
              {$styles[ix]}
            </option>
          {/section}
        </select>
      </div>
    </td>
    </tr></table>
  </td>
</tr>
<tr>
  <td class="form">{tr}Reg users can change language{/tr}:</td>
  <td>
    <table><tr>
    <td style="width: 20px"><input type="checkbox" name="change_language" {if $prefs.change_language eq 'y'}checked="checked"{/if}/></td>
    <td>
      <div id="select_available_languages" {if count($prefs.available_languages) > 0}style="display:none;"{else}style="display:block;"{/if}>
        <a class="link" href="javascript:show('available_languages');hide('select_available_languages');">{tr}Restrict available languages{/tr}</a>
      </div>
      <div id="available_languages" {if count($prefs.available_languages) == 0}style="display:none;"{else}style="display:block;"{/if}>
        {tr}Available languages:{/tr}<br />
        <select name="available_languages[]" multiple="multiple" size="5">
          {section name=ix loop=$languages}
            <option value="{$languages[ix].value|escape}"
              {if in_array($languages[ix].value, $prefs.available_languages)}selected="selected"{/if}>
              {$languages[ix].name}
            </option>
          {/section}
        </select>
      </div>
    </td>
    </tr></table>
  </td>
</tr>

<tr><td class="form">{tr}Minimum username length{/tr}:</td><td><input type="text" name="min_username_length" value="{$prefs.min_username_length|escape}" /></td></tr>
<tr><td class="form">{tr}Maximum username length{/tr}:</td><td><input type="text" name="max_username_length" value="{$prefs.max_username_length|escape}" /></td></tr>
<tr><td class="form">{tr}Force lowercase username{/tr}:</td><td><input type="checkbox" name="lowercase_username" {if $prefs.lowercase_username eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use challenge/response authentication{/tr}:</td><td><input type="checkbox" name="feature_challenge" {if $prefs.feature_challenge eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Force to use chars and nums in passwords{/tr}:</td><td><input type="checkbox" name="pass_chr_num" {if $prefs.pass_chr_num eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Minimum password length{/tr}:</td><td><input type="text" name="min_pass_length" value="{$prefs.min_pass_length|escape}" /></td></tr>
<tr><td class="form">{tr}Password invalid after days{/tr}:</td><td><input type="text" name="pass_due" value="{$prefs.pass_due|escape}" /><i>{tr}-1 for never{/tr}</i></td></tr>
<tr><td class="form">{tr}Re-validate user by email after days{/tr}:</td><td><input type="text" name="email_due" value="{$prefs.email_due|escape}" /><i>{tr}-1 for never{/tr}</i></td></tr>
<tr><td class="form">{tr}Re-validate user by email after unsuccessful logins{/tr}:</td><td><input type="text" name="unsuccessful_logins" value="{$prefs.unsuccessful_logins|escape}" /><i>{tr}-1 for never{/tr}</i></td></tr>
<tr><td class="form">{tr}HTTPS login{/tr}:</td><td class="form">
<select name="https_login">
<option value="disabled"{if $prefs.https_login eq 'disabled'} selected="selected"{/if}>{tr}Disabled{/tr}</option>
<option value="allowed"{if $prefs.https_login eq 'allowed'} selected="selected"{/if}>{tr}Allow secure (https) login{/tr}</option>
<option value="encouraged"{if $prefs.https_login eq 'encouraged'} selected="selected"{/if}>{tr}Encourage secure (https) login{/tr}</option>
<option value="force_nocheck"{if $prefs.https_login eq 'force_nocheck'} selected="selected"{/if}>{tr}Consider we are in always in HTTPS, but do not check{/tr}</option>
<option value="required"{if $prefs.https_login eq 'required'} selected="selected"{/if}>{tr}Require secure (https) login{/tr}</option>
</select>
</td></tr>
<tr><td class="form">{tr}Users can choose to stay in SSL mode after an HTTPS login{/tr}:</td><td><input type="checkbox" name="feature_show_stay_in_ssl_mode" {if $prefs.feature_show_stay_in_ssl_mode eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Users can switch between secured or standard mode at login{/tr}:</td><td><input type="checkbox" name="feature_switch_ssl_mode" {if $prefs.feature_switch_ssl_mode eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}HTTP port{/tr}:</td><td><input type="text" name="http_port" size="5" value="{$prefs.http_port|escape}" /></td></tr>
<tr><td class="form">{tr}HTTPS port{/tr}:</td><td><input type="text" name="https_port" size="5" value="{$prefs.https_port|escape}" /></td></tr>
<tr><td class="form">{tr}Remember me feature{/tr}:</td><td class="form">
<select name="rememberme">
<option value="disabled" {if $prefs.rememberme eq 'disabled'}selected="selected"{/if}>{tr}Disabled{/tr}</option>
<option value="all" {if $prefs.rememberme eq 'all'} selected="selected"{/if}>{tr}User's choice{/tr}</option>
<option value="always" {if $prefs.rememberme eq 'always'} selected="selected"{/if}>{tr}Always{/tr}</option>
</select><br />
</select><br />
{tr}Duration:{/tr}
<select name="remembertime">
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
</td></tr>
<tr><td class="form">{tr}Remember me name{/tr}:</td><td><input type="text" name="cookie_name" value="{$prefs.cookie_name|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}Remember me domain{/tr}:</td><td><input type="text" name="cookie_domain" value="{$prefs.cookie_domain|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}Remember me path{/tr}:</td><td><input type="text" name="cookie_path" value="{$prefs.cookie_path|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}Protect against CSRF with a confirmation step{/tr}:</td>
<td><input type="checkbox" name="feature_ticketlib" {if $prefs.feature_ticketlib eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Protect against CSRF with a ticket{/tr}:</td>
<td><input type="checkbox" name="feature_ticketlib2" {if $prefs.feature_ticketlib2 eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Highlight Group{/tr}:</td><td>
<select name="highlight_group">
<option value="0">{tr}choose a group ...{/tr}</option>
{foreach key=g item=gr from=$listgroups}
<option value="{$gr.groupName|escape}" {if $gr.groupName eq $prefs.highlight_group} selected="selected"{/if}>{$gr.groupName|truncate:"52":" ..."}</option>
{/foreach}
</select>
</td></tr>
<tr><td class="form">{tr}User can choose beyond these groups at registration time{/tr}:</td>
<td><select name="registration_choices[]" multiple="multiple" size="5">
<option value="">&nbsp;</option>
{foreach key=g item=gr from=$listgroups}
{if $gr.groupName ne 'Anonymous'} 
<option value="{$gr.groupName|escape}" {if $gr.registrationChoice eq 'y'} selected="selected"{/if}>{$gr.groupName|truncate:"52":" ..."}</option>
{/if}
{/foreach}
</select><br /><i>{tr}Specify the fields that will be asked in admin->groups{/tr}</i>
</td></tr>
<tr><td class="form">{tr}Displays user's contribution in the user information page{/tr}:</td>
<td><input type="checkbox" name="feature_display_my_to_others" {if $prefs.feature_display_my_to_others eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Displays UserTracker information in user information page. Format: trackerId, fieldId1, fieldId2, …{/tr}:</td>
<td><input type="text" name="user_tracker_infos" value="{$prefs.user_tracker_infos|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}Deactivate login autocomplete (do not remember login and password){/tr}:</td>
<td><input type="checkbox" name="desactive_login_autocomplete" {if $prefs.desactive_login_autocomplete eq 'y'}checked="checked"{/if}/></td></tr>

<tr><td colspan="2" class="button"><input type="submit" name="loginprefs" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>

<div class="cbox">
<div class="cbox-title">
  {tr}PEAR::Auth{/tr}
  {help url="Login+Config" desc="{tr}LDAP{/tr}"}
</div>
<div class="cbox-data">
<form action="tiki-admin.php?page=login" method="post">
<table class="admin">
<tr><td class="form">{tr}Create user if not in Tiki?{/tr}</td><td><input type="checkbox" name="auth_create_user_tiki" {if $prefs.auth_create_user_tiki eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Create user if not in Auth?{/tr}</td><td><input type="checkbox" name="auth_create_user_auth" {if $prefs.auth_create_user_auth eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Just use Tiki auth for admin?{/tr}</td><td><input type="checkbox" name="auth_skip_admin" {if $prefs.auth_skip_admin eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}LDAP URL<br />(if set, this will override the Host and Port below){/tr}:</td><td><input type="text" name="auth_ldap_url" value="{$prefs.auth_ldap_url|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}LDAP Host{/tr}:</td><td><input type="text" name="auth_pear_host" value="{$prefs.auth_pear_host|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}LDAP Port{/tr}:</td><td><input type="text" name="auth_pear_port" value="{$prefs.auth_pear_port|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Scope{/tr}:</td><td>
<select name="auth_ldap_scope">
<option value="sub" {if $prefs.auth_ldap_scope eq "sub"} selected="selected"{/if}>sub</option>
<option value="one" {if $prefs.auth_ldap_scope eq "one"} selected="selected"{/if}>one</option>
<option value="base" {if $prefs.auth_ldap_scope eq "base"} selected="selected"{/if}>base</option>
</select>
</td></tr>
<tr><td class="form">{tr}LDAP Base DN{/tr}:</td><td><input type="text" name="auth_ldap_basedn" value="{$prefs.auth_ldap_basedn|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP User DN{/tr}:</td><td><input type="text" name="auth_ldap_userdn" value="{$prefs.auth_ldap_userdn|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP User Attribute{/tr}:</td><td><input type="text" name="auth_ldap_userattr" value="{$prefs.auth_ldap_userattr|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP User OC{/tr}:</td><td><input type="text" name="auth_ldap_useroc" value="{$prefs.auth_ldap_useroc|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Group DN{/tr}:</td><td><input type="text" name="auth_ldap_groupdn" value="{$prefs.auth_ldap_groupdn|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Group Attribute{/tr}:</td><td><input type="text" name="auth_ldap_groupattr" value="{$prefs.auth_ldap_groupattr|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Group OC{/tr}:</td><td><input type="text" name="auth_ldap_groupoc" value="{$prefs.auth_ldap_groupoc|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Member Attribute{/tr}:</td><td><input type="text" name="auth_ldap_memberattr" value="{$prefs.auth_ldap_memberattr|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Member Is DN{/tr}:</td><td><input type="text" name="auth_ldap_memberisdn" value="{$prefs.auth_ldap_memberisdn|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Admin User{/tr}:</td><td><input type="text" name="auth_ldap_adminuser" value="{$prefs.auth_ldap_adminuser|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Admin Pwd{/tr}:</td><td><input type="password" name="auth_ldap_adminpass" value="{$prefs.auth_ldap_adminpass|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Version{/tr}:</td><td><input type="text" name="auth_ldap_version" value="{$prefs.auth_ldap_version|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Realname Attribute{/tr}:</td><td><input type="text" name="auth_ldap_nameattr" value="{$prefs.auth_ldap_nameattr|escape}" /></td></tr>
<tr><td colspan="2" class="button"><input type="submit" name="auth_pear" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>

<div class="cbox">
<div class="cbox-title">
  {tr}PAM{/tr}
  {help url="AuthPAM" desc="{tr}PAM{/tr}"}
</div>
<div class="cbox-data">
<form action="tiki-admin.php?page=login" method="post">
<table class="admin">
<tr><td class="form">{tr}Create user if not in Tiki?{/tr}</td><td><input type="checkbox" name="pam_create_user_tiki" {if $prefs.pam_create_user_tiki eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Just use Tiki auth for admin?{/tr}</td><td><input type="checkbox" name="pam_skip_admin" {if $prefs.pam_skip_admin eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}PAM service{/tr} ({tr}Currently unused{/tr})</td><td><input type="text" name="pam_service" value="{$prefs.pam_service|escape}"/></td></tr>
<tr><td colspan="2" class="button"><input type="submit" name="auth_pam" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>

{if $phpcas_enabled eq 'y'}
<div class="cbox">
<div class="cbox-title">
  {tr}CAS (Central Authentication Service){/tr}
  {help url="AuthCAS" desc="{tr}CAS (Central Authentication Service){/tr}"}
</div>
<div class="cbox-data">

<div class="rbox" name="tip"></div>
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>
<div class="rbox-data" name="tip">{tr}You also need to upload the <a target="_blank" href="http://esup-phpcas.sourceforge.net/">phpCAS library</a> separately to lib/phpcas/.{/tr}</div>


<form action="tiki-admin.php?page=login" method="post">
<table class="admin">
<tr><td class="form">{tr}Create user if not in Tiki?{/tr}</td><td><input type="checkbox" name="cas_create_user_tiki" {if $prefs.cas_create_user_tiki eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Just use Tiki auth for admin?{/tr}</td><td><input type="checkbox" name="cas_skip_admin" {if $prefs.cas_skip_admin eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}CAS server version{/tr}:</td><td>
<select name="cas_version">
<option value="none" {if $prefs.cas_version neq "1" && $prefs.cas_version neq "2"} selected="selected"{/if}></option>
<option value="1.0" {if $prefs.cas_version eq "1.0"} selected="selected"{/if}>{tr}Version 1.0{/tr}</option>
<option value="2.0" {if $prefs.cas_version eq "2.0"} selected="selected"{/if}>{tr}Version 2.0{/tr}</option>
</select>
</td></tr>
<tr><td class="form">{tr}CAS server hostname{/tr}:</td><td><input type="text" name="cas_hostname" value="{$prefs.cas_hostname|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}CAS server port{/tr}:</td><td><input type="text" name="cas_port" size="5" value="{$prefs.cas_port|escape}" /></td></tr>
<tr><td class="form">{tr}CAS server path{/tr}:</td><td><input type="text" name="cas_path" value="{$prefs.cas_path|escape}" size="50" /></td></tr>
<tr><td colspan="2" class="button"><input type="submit" name="auth_cas" value="{tr}Change CAS preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
{/if}

<div class="cbox">
<div class="cbox-title">
  {tr}Shibboleth{/tr}
  {help url="AuthShib" desc="{tr}Shibboleth Authentication {/tr}"}
</div>
<div class="cbox-data">

<form action="tiki-admin.php?page=login" method="post">
<table class="admin">
<tr><td class="form">{tr}Create user if not in Tiki?{/tr}</td><td><input type="checkbox" name="shib_create_user_tiki" {if $prefs.shib_create_user_tiki eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Just use Tiki auth for admin?{/tr}</td><td><input type="checkbox" name="shib_skip_admin" {if $prefs.shib_skip_admin eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Valid Affiliations (separated by commas){/tr}:</td><td><input type="text" name="shib_affiliation" value="{$prefs.shib_affiliation}" size="50" /></td></tr>
<tr><td class="form">{tr}Create with default group?{/tr}</td><td><input type="checkbox" name="shib_usegroup" {if $prefs.shib_usegroup eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Default group: {/tr}</td><td><input type="text" name="shib_group" value="{$prefs.shib_group}" size="30"/></td></tr>
<tr><td colspan="2" class="button"><input type="submit" name="auth_shib" value="{tr}Change Shibboleth preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>

{* ************ Users Default Preferences *}
<div class="cbox">
<div class="cbox-title">
  {tr}Users Defaults{/tr}
  {help url="UsersDefaultPrefs" desc="{tr}Users Default Preferences{/tr}"}
</div>
<div class="cbox-data">

<form action="tiki-admin.php?page=login" method="post">

<table class="admin">

{* *** Preferences *** *}
<tr><td colspan="2" class="heading">
  {tr}Preferences{/tr}
</td></tr>

<tr><td class="form">{tr}Does your mail reader need a special charset{/tr}</td>
  <td class="form">
  <select name="users_prefs_mailCharset">
  <option value=''>{tr}default{/tr}</option>
   {section name=ix loop=$mailCharsets}
      <option value="{$mailCharsets[ix]|escape}" {if $users_prefs_mailCharset eq $mailCharsets[ix]}selected="selected"{/if}>{$mailCharsets[ix]}</option>
   {/section}
  </select>
</td></tr>

{if $prefs.change_theme eq 'y'}
<tr><td class="form">{tr}Theme{/tr}:</td><td class="form">
<select name="users_prefs_theme">
<option value='' >{tr}default{/tr}</option>
{section name=ix loop=$styles}
	{if count($prefs.available_styles) == 0 || in_array($styles[ix], $prefs.available_styles)}
        <option value="{$styles[ix]|escape}" {if $users_prefs_theme eq $styles[ix]}selected="selected"{/if}>{$styles[ix]}</option>
	{/if}
{/section}
</select>
</td></tr>
{/if}

{if $prefs.change_language eq 'y'}
<tr><td  class="form">{tr}Language{/tr}:</td><td class="form">
<select name="users_prefs_language">
	<option value=''>{tr}default{/tr}</option>
	{section name=ix loop=$languages}
	{if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
        <option value="{$languages[ix].value|escape}"
		{if $users_prefs_language eq $languages[ix].value}selected="selected"{/if}>
		{$languages[ix].name}
        </option>
	{/if}
	{/section}
</select></td></tr>
{/if}

<tr><td class="form">{tr}Number of visited pages to remember{/tr}:</td><td class="form">
<select name="users_prefs_userbreadCrumb">
<option value="1" {if $prefs.users_prefs_userbreadCrumb eq 1}selected="selected"{/if}>1</option>
<option value="2" {if $prefs.users_prefs_userbreadCrumb eq 2}selected="selected"{/if}>2</option>
<option value="3" {if $prefs.users_prefs_userbreadCrumb eq 3}selected="selected"{/if}>3</option>
<option value="4" {if $prefs.users_prefs_userbreadCrumb eq 4}selected="selected"{/if}>4</option>
<option value="5" {if $prefs.users_prefs_userbreadCrumb eq 5}selected="selected"{/if}>5</option>
<option value="10" {if $prefs.users_prefs_userbreadCrumb eq 10}selected="selected"{/if}>10</option>
</select>
</td></tr>
<tr><td class="form">{tr}Displayed time zone{/tr}:</td>
<td class="form">
<input type="radio" name="users_prefs_display_timezone" value="Site" {if $prefs.users_prefs_display_timezone eq 'Site'}checked="checked"{/if}/> {tr}Site default{/tr}<br />
<input type="radio" name="users_prefs_display_timezone" value="Local" {if $prefs.users_prefs_display_timezone ne 'Site'}checked="checked"{/if}/> {tr}Detect user timezone if browser allows, otherwise site default{/tr}
</td>
</tr>
<tr><td class="form">{tr}User information{/tr}:</td><td class="form">
<select name="users_prefs_user_information">
<option value='private' {if $prefs.users_prefs_user_information eq 'private'}selected="selected"{/if}>{tr}Private{/tr}</option>
<option value='public' {if $prefs.users_prefs_user_information eq 'public'}selected="selected"{/if}>{tr}public{/tr}</option>
</select>
</td></tr>
{if $prefs.feature_wiki eq 'y'}
<tr><td class="form">{tr}Use double-click to edit pages{/tr}:</td>
<td class="form">
<input type="checkbox" name="users_prefs_user_dbl" {if $prefs.users_prefs_user_dbl eq 'y'}checked="checked"{/if} />
</td>
</tr>
{* not used {if $prefs.feature_history eq 'y'}
<tr><td class="form">Use new diff any version interface:</td>
<td class="form">
<input type="checkbox" name="users_prefs_diff_versions" {if $prefs.users_prefs_diff_versions eq 'y'}checked="checked"{/if} />
</td>
</tr>
{/if} *}
{/if}
{if $prefs.feature_community_mouseover eq 'y'}
<tr><td class="form">{tr}Show user's info on mouseover{/tr}:</td>
<td class="form">
<input type="checkbox" name="users_prefs_show_mouseover_user_info" {if $prefs.users_prefs_show_mouseover_user_info eq 'y'}checked="checked"{/if} />
</td>
</tr>
{/if}

{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
{* *** User Messages *** *}

<tr><td colspan="2" class="heading">
  {tr}User Messages{/tr}
</td></tr>

<tr>
  <td class="form">{tr}Messages per page{/tr}</td>
  <td class="form">
    <select name="users_prefs_mess_maxRecords">
      <option value="2" {if $prefs.users_prefs_mess_maxRecords eq 2}selected="selected"{/if}>2</option>
      <option value="5" {if $prefs.users_prefs_mess_maxRecords eq 5}selected="selected"{/if}>5</option>
      <option value="10" {if $prefs.users_prefs_mess_maxRecords eq 10}selected="selected"{/if}>10</option>
      <option value="20" {if $prefs.users_prefs_mess_maxRecords eq 20}selected="selected"{/if}>20</option>
      <option value="30" {if $prefs.users_prefs_mess_maxRecords eq 30}selected="selected"{/if}>30</option>
      <option value="40" {if $prefs.users_prefs_mess_maxRecords eq 40}selected="selected"{/if}>40</option>
      <option value="50" {if $prefs.users_prefs_mess_maxRecords eq 50}selected="selected"{/if}>50</option>
    </select>
  </td>
</tr>
<tr>
  <td class="form">{tr}Allow messages from other users{/tr}</td>
  <td class="form"><input type="checkbox" name="users_prefs_allowMsgs" {if $prefs.users_prefs_allowMsgs eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr>
  <td class="form">{tr}Note author when reading his mail{/tr}</td>
  <td class="form"><input type="checkbox" name="users_prefs_mess_sendReadStatus" {if $prefs.users_prefs_mess_sendReadStatus eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr>
  <td class="form">{tr}Send me an email for messages with priority equal or greater than{/tr}:</td>
  <td class="form">
    <select name="users_prefs_minPrio">
      <option value="1" {if $prefs.users_prefs_minPrio eq 1}selected="selected"{/if}>1</option>
      <option value="2" {if $prefs.users_prefs_minPrio eq 2}selected="selected"{/if}>2</option>
      <option value="3" {if $prefs.users_prefs_minPrio eq 3}selected="selected"{/if}>3</option>
      <option value="4" {if $prefs.users_prefs_minPrio eq 4}selected="selected"{/if}>4</option>
      <option value="5" {if $prefs.users_prefs_minPrio eq 5}selected="selected"{/if}>5</option>
      <option value="6" {if $prefs.users_prefs_minPrio eq 6}selected="selected"{/if}>{tr}none{/tr}</option>
    </select>
  </td>
</tr>
<tr>
  <td class="form">{tr}Auto-archive read messages after x days{/tr}</td>
  <td class="form">
    <select name="users_prefs_mess_archiveAfter">
      <option value="0" {if $prefs.users_prefs_mess_archiveAfter eq 0}selected="selected"{/if}>{tr}never{/tr}</option>
      <option value="1" {if $prefs.users_prefs_mess_archiveAfter eq 1}selected="selected"{/if}>1</option>
      <option value="2" {if $prefs.users_prefs_mess_archiveAfter eq 2}selected="selected"{/if}>2</option>
      <option value="5" {if $prefs.users_prefs_mess_archiveAfter eq 5}selected="selected"{/if}>5</option>
      <option value="10" {if $prefs.users_prefs_mess_archiveAfter eq 10}selected="selected"{/if}>10</option>
      <option value="20" {if $prefs.users_prefs_mess_archiveAfter eq 20}selected="selected"{/if}>20</option>
      <option value="30" {if $prefs.users_prefs_mess_archiveAfter eq 30}selected="selected"{/if}>30</option>
      <option value="40" {if $prefs.users_prefs_mess_archiveAfter eq 40}selected="selected"{/if}>40</option>
      <option value="50" {if $prefs.users_prefs_mess_archiveAfter eq 50}selected="selected"{/if}>50</option>
      <option value="60" {if $prefs.users_prefs_mess_archiveAfter eq 60}selected="selected"{/if}>60</option>
    </select>
  </td>
</tr>

{/if}

{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
{* *** User Tasks *** *}
<tr><td colspan="2" class="heading">
  {tr}User Tasks{/tr}
</td></tr>

<tr>
  <td class="form">{tr}Tasks per page{/tr}</td>
  <td class="form">
    <select name="users_prefs_tasks_maxRecords">
      <option value="2" {if $prefs.users_prefs_tasks_maxRecords eq 2}selected="selected"{/if}>2</option>
      <option value="5" {if $prefs.users_prefs_tasks_maxRecords eq 5}selected="selected"{/if}>5</option>
      <option value="10" {if $prefs.users_prefs_tasks_maxRecords eq 10}selected="selected"{/if}>10</option>
      <option value="20" {if $prefs.users_prefs_tasks_maxRecords eq 20}selected="selected"{/if}>20</option>
      <option value="30" {if $prefs.users_prefs_tasks_maxRecords eq 30}selected="selected"{/if}>30</option>
      <option value="40" {if $prefs.users_prefs_tasks_maxRecords eq 40}selected="selected"{/if}>40</option>
      <option value="50" {if $prefs.users_prefs_tasks_maxRecords eq 50}selected="selected"{/if}>50</option>
    </select>
  </td>
</tr>

{/if}

{* *** My Tiki *** *}
<tr><td colspan="2" class="heading">
  {tr}My Tiki{/tr}
</td></tr>

{if $prefs.feature_wiki eq 'y'}
<tr><td class="form">{tr}My pages{/tr}</td><td class="form"><input type="checkbox" name="users_prefs_mytiki_pages" {if $prefs.users_prefs_mytiki_pages eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $prefs.feature_blogs eq 'y'}
<tr><td class="form">{tr}My blogs{/tr}</td><td class="form"><input type="checkbox" name="users_prefs_mytiki_blogs" {if $prefs.users_prefs_mytiki_blogs eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $prefs.feature_galleries eq 'y'}
<tr><td class="form">{tr}My galleries{/tr}</td><td class="form"><input type="checkbox" name="users_prefs_mytiki_gals" {if $prefs.users_prefs_mytiki_gals eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $prefs.feature_messages eq 'y'and $tiki_p_messages eq 'y'}
<tr><td class="form">{tr}My messages{/tr}</td><td class="form"><input type="checkbox" name="users_prefs_mytiki_msgs" {if $prefs.users_prefs_mytiki_msgs eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
<tr><td class="form">{tr}My tasks{/tr}</td><td class="form"><input type="checkbox" name="users_prefs_mytiki_tasks" {if $prefs.users_prefs_mytiki_tasks eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $prefs.feature_forums eq 'y'}
<tr><td class="form">{tr}My forum topics{/tr}</td><td class="form"><input type="checkbox" name="users_prefs_mytiki_forum_topics" {if $prefs.users_prefs_mytiki_forum_topics eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}My forum replies{/tr}</td><td class="form"><input type="checkbox" name="users_prefs_mytiki_forum_replies" {if $prefs.users_prefs_mytiki_forum_replies eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $prefs.feature_trackers eq 'y'}
<tr><td class="form">{tr}My items{/tr}</td><td class="form"><input type="checkbox" name="users_prefs_mytiki_items" {if $prefs.users_prefs_mytiki_items eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $prefs.feature_workflow eq 'y'}
  {if $tiki_p_use_workflow eq 'y'}
    <tr><td class="form">{tr}My workflow{/tr}</td><td class="form"><input type="checkbox" name="users_prefs_mytiki_workflow" {if $prefs.users_prefs_mytiki_workflow eq 'y'}checked="checked"{/if} /></td></tr>
  {/if}
{/if}

<tr><td colspan="2" class="button"><input type="submit" name="users_defaults" value="{tr}Change users defaults{/tr}" /></td></tr>

</table>
</form>
</div>
</div>
