<div class="cbox">
<div class="cbox-title">{tr}User Registration and Login{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php?page=login" method="post">
<table class="admin">
<tr><td class="form">{tr}Authentication method{/tr}</td><td>
<select name="auth_method">
<option value="tiki" {if $auth_method eq 'tiki'} selected="selected"{/if}>{tr}Just Tiki{/tr}</option>
<option value="ws" {if $auth_method eq 'ws'} selected="selected"{/if}>{tr}Web Server{/tr}</option>
<option value="auth" {if $auth_method eq 'auth'} selected="selected"{/if}>{tr}Tiki and PEAR::Auth{/tr}</option>
<!--option value="http" {if $auth_method eq 'http'} selected="selected"{/if}>{tr}Tiki and HTTP Auth{/tr}</option-->
</select></td></tr>
<!--<tr><td class="form">{tr}Use WebServer authentication for Tiki{/tr}:</td><td><input type="checkbox" name="webserverauth" {if $webserverauth eq 'y'}checked="checked"{/if}/></td></tr>-->
<tr><td class="form">{tr}Users can register{/tr}:</td><td><input type="checkbox" name="allowRegister" {if $allowRegister eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Create a group for each user <br />(with the same
name as the user){/tr}:</td><td><input type="checkbox"
name="eponymousGroups" {if $eponymousGroups eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Request passcode to register{/tr}:</td><td><input type="checkbox" name="useRegisterPasscode" {if $useRegisterPasscode eq 'y'}checked="checked"{/if}/><input type="text" name="registerPasscode" value="{$registerPasscode|escape}"/></td></tr>
<tr><td class="form">{tr}Prevent automatic/robot registration{/tr}{php}if (!function_exists("gd_info")) print(tra(" - Php GD library required")); {/php}:</td><td><input type="checkbox" name="rnd_num_reg" {if $rnd_num_reg eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Validate users by email{/tr}:</td><td><input type="checkbox" name="validateUsers" {if $validateUsers eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Validate email address (may not work){/tr}:</td><td><input type="checkbox" name="validateEmail" {if $validateEmail eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Remind passwords by email{/tr}:</td><td><input type="checkbox" name="forgotPass" {if $forgotPass eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Reg users can change theme{/tr}:</td><td><input type="checkbox" name="change_theme" {if $change_theme eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Reg users can change language{/tr}:</td><td><input type="checkbox" name="change_language" {if $change_language eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Store plaintext passwords{/tr}:</td><td><input type="checkbox" name="feature_clear_passwords" {if $feature_clear_passwords eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use challenge/response authentication{/tr}:</td><td><input type="checkbox" name="feature_challenge" {if $feature_challenge eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Force to use chars and nums in passwords{/tr}:</td><td><input type="checkbox" name="pass_chr_num" {if $pass_chr_num eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Minimum password length{/tr}:</td><td><input type="text" name="min_pass_length" value="{$min_pass_length|escape}" /></td></tr>
<tr><td class="form">{tr}Password invalid after days{/tr}:</td><td><input type="text" name="pass_due" value="{$pass_due|escape}" /></td></tr>
<!-- # not implemented
<tr><td class="form">{tr}Require HTTP Basic authentication{/tr}:</td><td><input type="checkbox" name="http_basic_auth" {if $http_basic_auth eq 'y'}checked="checked"{/if}/></td></tr>
-->
<tr><td class="form">{tr}Allow secure (https) login{/tr}:</td><td><input type="checkbox" name="https_login" {if $https_login eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Require secure (https) login{/tr}:</td><td><input type="checkbox" name="https_login_required" {if $https_login_required eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}HTTP server name{/tr}:</td><td><input type="text" name="http_domain" value="{$http_domain|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}HTTP port{/tr}:</td><td><input type="text" name="http_port" size="5" value="{$http_port|escape}" /></td></tr>
<tr><td class="form">{tr}HTTP URL prefix{/tr}:</td><td><input type="text" name="http_prefix" value="{$http_prefix|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}HTTPS server name{/tr}:</td><td><input type="text" name="https_domain" value="{$https_domain|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}HTTPS port{/tr}:</td><td><input type="text" name="https_port" size="5" value="{$https_port|escape}" /></td></tr>
<tr><td class="form">{tr}HTTPS URL prefix{/tr}:</td><td><input type="text" name="https_prefix" value="{$https_prefix|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}Remember me feature{/tr}:</td><td>
<select name="rememberme">
<option value="disabled" {if $rememberme eq 'disabled'}selected="selected"{/if}>{tr}Disabled{/tr}</option>
<!--<option value="noadmin" {if $rememberme eq 'noadmin'}selected="selected"{/if}>{tr}Only for users{/tr}</option>-->
<option value="all" {if $rememberme eq 'all'} selected="selected"{/if}>{tr}Users and admins{/tr}</option>
</select><br />
{tr}Duration:{/tr}
<select name="remembertime">
<option value="300" {if $remembertime eq 300} selected="selected"{/if}>5 {tr}minutes{/tr}</option>
<option value="900" {if $remembertime eq 900} selected="selected"{/if}>15 {tr}minutes{/tr}</option>
<option value="1800" {if $remembertime eq 1800} selected="selected"{/if}>30 {tr}minutes{/tr}</option>
<option value="3600" {if $remembertime eq 3600} selected="selected"{/if}>1 {tr}hour{/tr}</option>
<option value="7200" {if $remembertime eq 7200} selected="selected"{/if}>2 {tr}hours{/tr}</option>
<option value="36000" {if $remembertime eq 36000} selected="selected"{/if}>10 {tr}hours{/tr}</option>
<option value="72000" {if $remembertime eq 72000} selected="selected"{/if}>1 {tr}day{/tr}</option>
<option value="720000" {if $remembertime eq 720000} selected="selected"{/if}>1 {tr}week{/tr}</option>
</select>
</td></tr>
<tr><td class="form">{tr}Remember me domain{/tr}:</td><td><input type="text" name="cookie_domain" value="{$cookie_domain|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}Remember me path{/tr}:</td><td><input type="text" name="cookie_path" value="{$cookie_path|escape}" size="50" /></td></tr>

<tr><td colspan="2" class="button"><input type="submit" name="loginprefs" value="{tr}Change Preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
</div>

<div class="cbox">
<div class="cbox-title">{tr}PEAR::Auth{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php?page=login" method="post">
<table class="admin">
<tr><td class="form">{tr}Create user if not in Tiki?{/tr}</td><td><input type="checkbox" name="auth_create_user_tiki" {if $auth_create_user_tiki eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Create user if not in Auth?{/tr}</td><td><input type="checkbox" name="auth_create_user_auth" {if $auth_create_user_auth eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Just use Tiki auth for admin?{/tr}</td><td><input type="checkbox" name="auth_skip_admin" {if $auth_skip_admin eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}LDAP Host{/tr}:</td><td><input type="text" name="auth_ldap_host" value="{$auth_ldap_host|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}LDAP Port{/tr}:</td><td><input type="text" name="auth_ldap_port" value="{$auth_ldap_port|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Scope{/tr}:</td><td>
<select name="auth_ldap_scope">
<option value="sub" {if $auth_ldap_scope eq "sub"} selected="selected"{/if}>sub</option>
<option value="one" {if $auth_ldap_scope eq "one"} selected="selected"{/if}>one</option>
<option value="base" {if $auth_ldap_scope eq "base"} selected="selected"{/if}>base</option>
</select>
</td></tr>
<tr><td class="form">{tr}LDAP Base DN{/tr}:</td><td><input type="text" name="auth_ldap_basedn" value="{$auth_ldap_basedn|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP User DN{/tr}:</td><td><input type="text" name="auth_ldap_userdn" value="{$auth_ldap_userdn|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP User Attribute{/tr}:</td><td><input type="text" name="auth_ldap_userattr" value="{$auth_ldap_userattr|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP User OC{/tr}:</td><td><input type="text" name="auth_ldap_useroc" value="{$auth_ldap_useroc|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Group DN{/tr}:</td><td><input type="text" name="auth_ldap_groupdn" value="{$auth_ldap_groupdn|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Group Atribute{/tr}:</td><td><input type="text" name="auth_ldap_groupattr" value="{$auth_ldap_groupattr|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Group OC{/tr}:</td><td><input type="text" name="auth_ldap_groupoc" value="{$auth_ldap_groupoc|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Member Attribute{/tr}:</td><td><input type="text" name="auth_ldap_memberattr" value="{$auth_ldap_memberattr|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Member Is DN{/tr}:</td><td><input type="text" name="auth_ldap_memberisdn" value="{$auth_ldap_memberisdn|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Admin User{/tr}:</td><td><input type="text" name="auth_ldap_adminuser" value="{$auth_ldap_adminuser|escape}" /></td></tr>
<tr><td class="form">{tr}LDAP Admin Pwd{/tr}:</td><td><input type="password" name="auth_ldap_adminpass" value="{$auth_ldap_adminpass|escape}" /></td></tr>
<tr><td colspan="2" class="button"><input type="submit" name="auth_pear" value="{tr}Change Preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
</div>

