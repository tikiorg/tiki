<a name="login"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a> |
<a href="#directory" class="link">{tr}directory{/tr}</a> |
<a href="#userfiles" class="link">{tr}userfiles{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}User registration and login{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php#login" method="post">
<table width="100%">
<tr><td class="form">{tr}Authentication method{/tr}</td><td>
<select name="auth_method">
<option value="tiki" {if $auth_method eq 'tiki'} selected="selected"{/if}>{tr}Just Tiki{/tr}</option>
<option value="auth" {if $auth_method eq 'auth'} selected="selected"{/if}>{tr}Tiki and PEAR::Auth{/tr}</option>
<!--option value="http" {if $auth_method eq 'http'} selected="selected"{/if}>{tr}Tiki and HTTP Auth{/tr}</option-->
</select></td></tr>
<tr><td class="form">{tr}Users can register{/tr}:</td><td><input type="checkbox" name="allowRegister" {if $allowRegister eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Request passcode to register{/tr}:</td><td><input type="checkbox" name="useRegisterPasscode" {if $useRegisterPasscode eq 'y'}checked="checked"{/if}/><input type="text" name="registerPasscode" value="{$registerPasscode}"/></td></tr>
<tr><td class="form">{tr}Validate users by email{/tr}:</td><td><input type="checkbox" name="validateUsers" {if $validateUsers eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Remind passwords by email{/tr}:</td><td><input type="checkbox" name="forgotPass" {if $forgotPass eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Reg users can change theme{/tr}:</td><td><input type="checkbox" name="change_theme" {if $change_theme eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Reg users can change language{/tr}:</td><td><input type="checkbox" name="change_language" {if $change_language eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Store plaintext passwords{/tr}:</td><td><input type="checkbox" name="feature_clear_passwords" {if $feature_clear_passwords eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use challenge/response authentication{/tr}:</td><td><input type="checkbox" name="feature_challenge" {if $feature_challenge eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Force to use chars and nums in passwords{/tr}:</td><td><input type="checkbox" name="pass_chr_num" {if $pass_chr_num eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Minimum password length{/tr}:</td><td><input type="text" name="min_pass_length" value="{$min_pass_length}" /></td></tr>
<tr><td class="form">{tr}Password invalid after days{/tr}:</td><td><input type="text" name="pass_due" value="{$pass_due}" /></td></tr>
<!-- # not implemented
<tr><td class="form">{tr}Require HTTP Basic authentication{/tr}:</td><td><input type="checkbox" name="http_basic_auth" {if $http_basic_auth eq 'y'}checked="checked"{/if}/></td></tr>
-->
<tr><td class="form">{tr}Allow secure (https) login{/tr}:</td><td><input type="checkbox" name="https_login" {if $https_login eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Require secure (https) login{/tr}:</td><td><input type="checkbox" name="https_login_required" {if $https_login_required eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}HTTP server name{/tr}:</td><td><input type="text" name="http_domain" value="{$http_domain}" /></td></tr>
<tr><td class="form">{tr}HTTP port{/tr}:</td><td><input type="text" name="http_port" size="5" value="{$http_port}" /></td></tr>
<tr><td class="form">{tr}HTTP URL prefix{/tr}:</td><td><input type="text" name="http_prefix" value="{$http_prefix}" /></td></tr>
<tr><td class="form">{tr}HTTPS server name{/tr}:</td><td><input type="text" name="https_domain" value="{$https_domain}" /></td></tr>
<tr><td class="form">{tr}HTTPS port{/tr}:</td><td><input type="text" name="https_port" size="5" value="{$https_port}" /></td></tr>
<tr><td class="form">{tr}HTTPS URL prefix{/tr}:</td><td><input type="text" name="https_prefix" value="{$https_prefix}" /></td></tr>
<tr><td class="form">{tr}Remember me feature{/tr}:</td><td>
<select name="rememberme">
<option value="disabled" {if $rememberme eq 'disabled'}selected="selected"{/if}>{tr}Disabled{/tr}</option>
<!--<option value="noadmin" {if $rememberme eq 'noadmin'}selected="selected"{/if}>{tr}Only for users{/tr}</option>-->
<option value="all" {if $rememberme eq 'all'} selected="selected"{/if}>{tr}Users and admins{/tr}</option>
</select>
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
</tr></tr>

<tr><td>&nbsp;</td><td><input type="submit" name="loginprefs" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
</div>

<a name="auth_pear"></a>
<div class="cbox">
<div class="cbox-title">{tr}PEAR::Auth{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td class="form">{tr}Create user if not in Tiki?{/tr}</td><td><input type="checkbox" name="auth_create_user_tiki" {if $auth_create_user_tiki eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Create user if not in Auth?{/tr}</td><td><input type="checkbox" name="auth_create_user_auth" {if $auth_create_user_auth eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Just use Tiki auth for admin?{/tr}</td><td><input type="checkbox" name="auth_skip_admin" {if $auth_skip_admin eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}LDAP Host{/tr}:</td><td><input type="text" name="auth_ldap_host" value="{$auth_ldap_host}" /></td></tr>
<tr><td class="form">{tr}LDAP Port{/tr}:</td><td><input type="text" name="auth_ldap_port" value="{$auth_ldap_port}" /></td></tr>
<tr><td class="form">{tr}LDAP Scope{/tr}:</td><td><input type="text" name="auth_ldap_scope" value="{$auth_ldap_scope}" /></td></tr>
<tr><td class="form">{tr}LDAP Base DN{/tr}:</td><td><input type="text" name="auth_ldap_basedn" value="{$auth_ldap_basedn}" /></td></tr>
<tr><td class="form">{tr}LDAP User DN{/tr}:</td><td><input type="text" name="auth_ldap_userdn" value="{$auth_ldap_userdn}" /></td></tr>
<tr><td class="form">{tr}LDAP User Attribute{/tr}:</td><td><input type="text" name="auth_ldap_userattr" value="{$auth_ldap_userattr}" /></td></tr>
<tr><td class="form">{tr}LDAP User OC{/tr}:</td><td><input type="text" name="auth_ldap_useroc" value="{$auth_ldap_useroc}" /></td></tr>
<tr><td class="form">{tr}LDAP Group DN{/tr}:</td><td><input type="text" name="auth_ldap_groupdn" value="{$auth_ldap_groupdn}" /></td></tr>
<tr><td class="form">{tr}LDAP Group Atribute{/tr}:</td><td><input type="text" name="auth_ldap_groupattr" value="{$auth_ldap_groupattr}" /></td></tr>
<tr><td class="form">{tr}LDAP Group OC{/tr}:</td><td><input type="text" name="auth_ldap_groupoc" value="{$auth_ldap_groupoc}" /></td></tr>
<tr><td class="form">{tr}LDAP Member Attribute{/tr}:</td><td><input type="text" name="auth_ldap_memberattr" value="{$auth_ldap_memberattr}" /></td></tr>
<tr><td class="form">{tr}LDAP Member Is DN{/tr}:</td><td><input type="text" name="auth_ldap_memberisdn" value="{$auth_ldap_memberisdn}" /></td></tr>
<tr><td class="form">{tr}LDAP Admin User{/tr}:</td><td><input type="text" name="auth_ldap_adminuser" value="{$auth_ldap_adminuser}" /></td></tr>
<tr><td class="form">{tr}LDAP Admin Pwd{/tr}:</td><td><input type="password" name="auth_ldap_adminpass" value="{$auth_ldap_adminpass}" /></td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="auth_pear" value="{tr}Set prefs{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
</div>

