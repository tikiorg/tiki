{* $Id: tiki-site_header.tpl 12554 2008-04-15 23:03:57Z luciash $ *}
{* site header login form *}
{strip}
{if $filegals_manager eq '' and $print_page ne 'y'}
	{if $prefs.feature_site_login eq 'y'}
		{if $user}
<div id="siteloginbar" class="logged-in">
	{$user|userlink} | <a href="tiki-logout.php" title="{tr}Logout{/tr}">{tr}Logout{/tr}</a>
{else}
<div id="siteloginbar">
	{if $user}
		{$user|userlink} | <a href="tiki-logout.php" title="{tr}Logout{/tr}">{tr}Logout{/tr}</a>
	{else}
		<form class="forms" name="loginbox" action="{if $prefs.https_login eq 'encouraged' || $prefs.https_login eq 'required' || $prefs.https_login eq 'force_nocheck'}{$base_url_https}{/if}{$prefs.login_url}" method="post" {if $prefs.feature_challenge eq 'y'}onsubmit="doChallengeResponse()"{/if}{if $prefs.desactive_login_autocomplete eq 'y'} autocomplete="off"{/if}>
			<label for="sl-login-user">{if $prefs.login_is_email eq 'y'}{tr}Email{/tr}{else}{tr}User{/tr}{/if}:</label>
			<input type="text" name="user" id="sl-login-user" />
			<label for="sl-login-pass">{tr}Password{/tr}:</label>
			<input type="password" name="pass" id="sl-login-pass" size="10" />
			<input class="wikiaction" type="submit" name="login" value="{tr}Login{/tr}" />
			<div>
			{if $prefs.rememberme eq 'always'}<input type="hidden" name="rme" value="on" />
			{elseif $prefs.rememberme eq 'all'}
				<span class="rme">
					<label for="login-remember">{tr}Remember me{/tr}</label><input type="checkbox" name="rme" id="login-remember" value="on" checked="checked" />
				</span>
			{/if}
			{if $prefs.change_password eq 'y' and $prefs.forgotPass eq 'y'}
				<span class="pass">
					 <a href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}">{tr}I forgot my password{/tr}</a>
				</span>
			{/if}
			{if $prefs.allowRegister eq 'y'}
				<span class="register">
					&nbsp;<a href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}Register{/tr}</a>
				</span>
			{/if}		
			</div>
		</form>
	{/if}
{/if}
</div>
	{/if}
{/if}
{/strip}
