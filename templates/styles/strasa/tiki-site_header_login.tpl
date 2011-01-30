{* $Id$ *}
{strip}
{if $prefs.feature_site_login eq 'y'}
{if $user}
<div id="siteloginbar" class="logged-in">
	{$user|userlink} | <a href="tiki-logout.php" title="{tr}Logout{/tr}">{tr}Logout{/tr}</a>
{else}
<div id="siteloginbar">
	{if $prefs.auth_method eq 'cas' && $showloginboxes neq 'y'}
		<b><a href="tiki-login.php?cas=y">{tr}Login through CAS{/tr}</a></b>
		{if $prefs.cas_skip_admin eq 'y' && $prefs.cas_show_alternate_login eq 'y'}
			&nbsp;|&nbsp;{self_link _template='tiki-site_header_login.tpl' _title="{tr}Log in as admin{/tr}" _icon='user_red' _htmlelement='siteloginbar' user='admin'}{tr}Log in as admin{/tr}{/self_link}
		{/if}
	{elseif $prefs.auth_method eq 'shib' && $showloginboxes neq 'y'}
		<b><a href="tiki-login.php">{tr}Login through Shibboleth{/tr}</a></b>
		{if $prefs.shib_skip_admin eq 'y'}
			&nbsp;|&nbsp;{self_link _template='tiki-site_header_login.tpl' _title="{tr}Log in as admin{/tr}" _icon='user_red' _htmlelement='siteloginbar' user='admin'}{tr}Log in as admin{/tr}{/self_link}
		{/if}
	{else}
		<form class="forms" name="loginbox" action="{if $prefs.https_login eq 'encouraged' || $prefs.https_login eq 'required' || $prefs.https_login eq 'force_nocheck'}{$base_url_https}{/if}{$prefs.login_url}" method="post" {if $prefs.feature_challenge eq 'y'}onsubmit="doChallengeResponse()"{/if}{if $prefs.desactive_login_autocomplete eq 'y'} autocomplete="off"{/if}>
					{if $prefs.allowRegister eq 'y'}
				<div class="register">
					<a href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}Register{/tr}</a>
				</div>
			{/if}	
			<label for="sl-login-user">{if $prefs.login_is_email eq 'y'}{tr}Email{/tr}{else}{tr}Username{/tr}{/if}:</label>
			<input type="text" name="user" id="sl-login-user" />
			<label for="sl-login-pass">{tr}Password:{/tr}</label>
			<input type="password" name="pass" id="sl-login-pass" size="10" />
			<input class="wikiaction" type="submit" name="login" value="{tr}Login{/tr}" />
			{*<div>*}
			{if $prefs.rememberme eq 'always'}<input type="hidden" name="rme" value="on" />
			{elseif $prefs.rememberme eq 'all'}
				<div class="rme">
					<label for="login-remember">{tr}Remember me{/tr}</label><input type="checkbox" name="rme" id="login-remember" value="on" checked="checked" />
				</div>
			{/if}
			{if $prefs.change_password eq 'y' and $prefs.forgotPass eq 'y'}
				<div class="pass">
					 <a href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}">{tr}I forgot my password{/tr}</a>
				</div>
			{/if}
	
		{*	</div>*}
		</form>
	{/if}
{/if}
</div>
{/if}
{/strip}
