{* $Id: tiki-site_header.tpl 12554 2008-04-15 23:03:57Z luciash $ *}
{* site header login form *}
{strip}
<div id="login_search">
	<div class="wrapper">
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
		<form class="forms" name="loginbox" action="tiki-login.php" method="post">
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
		<div id="login_language"> | language</div>
		</div>	
		{*if $filegals_manager eq '' and $print_page ne 'y'*}
			{if $prefs.feature_sitesearch eq 'y' and $prefs.feature_search eq 'y' and $tiki_p_search eq 'y'}
				<div id="sitesearchbar"{if $prefs.feature_sitemycode neq 'y' and $prefs.feature_banners eq 'y' && $prefs.feature_sitelogo neq 'y' and ($prefs.feature_banners neq 'y' or $prefs.feature_sitead neq 'y') and $prefs.feature_fullscreen eq 'y' and $filegals_manager eq '' and $print_page ne 'y'}{if $smarty.session.fullscreen neq 'y'}style="margin-right: 80px"{/if}{/if}>
					<div class="wrapper">
						{if $prefs.feature_search_fulltext eq 'y'}
							{include file="tiki-searchresults.tpl"
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}
						{else}
							{include file="tiki-searchindex.tpl"
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}
						{/if}
					</div>
				</div>{* search the site *}
			{/if}
		{*/if*}
	</div>
{/strip}
