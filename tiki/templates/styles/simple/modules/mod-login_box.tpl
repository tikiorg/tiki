{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/modules/mod-login_box.tpl,v 1.2 2006-02-17 15:10:48 sylvieg Exp $ *}
{tikimodule title="{tr}Login{/tr}" name="login_box" flip=$module_params.flip decorations=$module_params.decorations}

	{if $user}
		{tr}logged as{/tr}: {$user}<br />
<div><a class="linkmodule" href="tiki-logout.php">{tr}Logout{/tr}</a></div>

		{if $tiki_p_admin eq 'y'}
<form action="{$login_url}" method="post">
	<label for="login-switchuser">{tr}user{/tr}:</label>
	<input type="hidden" name="su" value="1" />
	<input type="text" name="username" id="login-switchuser" size="8" />
	<button type="submit" name="actsu" value="{tr}set{/tr}">{tr}set{/tr}</button>
</form>
		{/if}

	{elseif $auth_method eq 'cas' && $showloginboxes neq 'y'}
<div><strong><a class="linkmodule" href="tiki-login.php">{tr}Login through CAS{/tr}</a></strong></div>

		{if $cas_skip_admin eq 'y'}
<div><a class="linkmodule" href="tiki-login_scr.php?user=admin">{tr}Login as admin{/tr}</a></div>
		{/if}

	{else}
<form name="loginbox" action="{$login_url}" method="post"{if $feature_challenge eq 'y'} onsubmit="doChallengeResponse()"{/if}>

		{if $feature_challenge eq 'y'}
	<script type='text/javascript' src="lib/md5.js"></script>
			{literal}
	<script type='text/javascript'>
		<!--
			function doChallengeResponse() {
			hashstr = document.loginbox.user.value +
			document.loginbox.pass.value +
			document.loginbox.email.value;
			str = document.loginbox.user.value + 
			MD5(hashstr) +
			document.loginbox.challenge.value;
			document.loginbox.response.value = MD5(str);
			document.loginbox.pass.value='';
			document.loginbox.submit();
			return false;
			}
		// -->
	</script>
			{/literal}

	<input type="hidden" name="challenge" value="{$challenge|escape}" />
	<input type="hidden" name="response" value="" />
		{/if}

		{if $http_login_url ne '' or $https_login_url ne ''}
	<div class="module">
		<a class="linkbut{if $show_stay_in_ssl_mode ne 'y'} highlight{/if}" href="{$http_login_url}" title="{tr}Click here to login using the default security protocol{/tr}">{tr}standard{/tr}</a>
		<a class="linkbut{if $show_stay_in_ssl_mode eq 'y'} highlight{/if}" href="{$https_login_url}" title="{tr}Click here to login using a secure protocol{/tr}">{tr}secure{/tr}</a>
	</div>
		{/if}

	<div class="module"><label for="login-user">{tr}user{/tr}:</label>

		{if $loginuser eq ''}
	<input type="text" name="user" id="login-user" size="20" />
		{else}
	<input type="hidden" name="user" id="login-user" value="{$loginuser}" /><b>{$loginuser}</b>
		{/if}

	</div>

		{if $feature_challenge eq 'y'} <!-- quick hack to make challenge/response work until 1.8 tiki auth overhaul -->
	<div class="module"><label for="login-email">{tr}email{/tr}:</label><input type="text" name="email" id="login-email" size="20" /></div>
		{/if}

	<div class="module"><label for="login-pass">{tr}pass{/tr}:</label>
	<input type="password" name="pass" id="login-pass" size="20" /></div>
	<div><button type="submit" name="login" value="{tr}login{/tr}">{tr}login{/tr}</button></div>

		{if $rememberme ne 'disabled'}
	<div class="module">
		<input type="checkbox" name="rme" id="login-remember" value="on" />
		<label for="login-remember">{tr}Remember me{/tr}</label>
	</div>
		{/if}

		{if $forgotPass eq 'y' and $allowRegister ne 'y' and $change_password eq 'y'}
	<div class="module"><a class="linkmodule" href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}"><em>{tr}I forgot my pass{/tr}</em></a></div>
		{/if}

		{if ($forgotPass ne 'y' or $change_password ne 'y') and $allowRegister eq 'y'}
	<div class="module" valign="bottom"><a class="linkmodule" href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}register{/tr}</a></div>
		{/if}

		{if $show_stay_in_ssl_mode eq 'y'}
	<div class="module">
		<input type="checkbox" name="stay_in_ssl_mode" id="login-stayssl"{if $stay_in_ssl_mode eq 'y'} checked="checked"{/if} />
		<label for="login-stayssl">{tr}stay in ssl mode{/tr}</label>
	</div>
		{/if}

	<div class="module">
		<ul>
			<li><a class="linkmodule" href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}register{/tr}</a></li>
			<li><a class="linkmodule" href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}">{tr}I forgot my pass{/tr}</a></li>
		</ul>
	</div>

		{if $show_stay_in_ssl_mode ne 'y'}
	<input type="hidden" name="stay_in_ssl_mode" value="{$stay_in_ssl_mode|escape}" />
		{/if}

</form>
	{/if}

{/tikimodule}