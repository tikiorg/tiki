{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-login_box.tpl,v 1.45 2007-06-01 13:56:01 nyloth Exp $ *}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Login{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="login_box" flip=$module_params.flip decorations=$module_params.decorations}

    {if $user}
      {tr}logged as{/tr}: {$user|userlink}<br />
      <a class="linkmodule" href="tiki-logout.php">{tr}Logout{/tr}</a><br />
      {if $tiki_p_admin eq 'y'}
        <form action="{if $https_login eq 'encouraged' || $https_login eq 'required'}{$base_url_https}{/if}{$login_url}" method="post"{if $desactive_login_autocomplete eq 'y'} autocomplete="off"{/if}>
        <label for="login-switchuser">{tr}user{/tr}:</label>
        <input type="hidden" name="su" value="1" />
        <input type="text" name="username" id="login-switchuser" size="{if empty($module_params.input_size)}20{else}{$module_params.input_size}{/if}" />
        <input type="submit" name="actsu" value="{tr}set{/tr}" />
        </form>
      {/if}
      {elseif $auth_method eq 'cas' && $showloginboxes neq 'y'}
		<b><a class="linkmodule" href="tiki-login.php?user">{tr}Login through CAS{/tr}</a></b>
		{if $cas_skip_admin eq 'y'}
		<br /><a class="linkmodule" href="tiki-login_scr.php?user=admin">{tr}Login as admin{/tr}</a>
      {/if}
      {elseif $auth_method eq 'shib' && $showloginboxes neq 'y'}
		<b><a class="linkmodule" href="tiki-login.php">{tr}Login through Shibboleth{/tr}</a></b>
		{if $shib_skip_admin eq 'y'}
		<br /><a class="linkmodule" href="tiki-login_scr.php?user=admin">{tr}Login as admin{/tr}</a>
      {/if}
    {else}
     <form name="loginbox" action="{if $https_login eq 'encouraged' || $https_login eq 'required'}{$base_url_https}{/if}{$login_url}" method="post" {if $feature_challenge eq 'y'}onsubmit="doChallengeResponse()"{/if}{if $desactive_login_autocomplete eq 'y'} autocomplete="off"{/if}> 
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
       /*
       document.login.password.value = "";
       document.logintrue.username.value = document.login.username.value;
       document.logintrue.response.value = MD5(str);
       document.logintrue.submit();
       */
       document.loginbox.submit();
       return false;
     }
     // -->
    </script>
    {/literal}
     <input type="hidden" name="challenge" value="{$challenge|escape}" />
     <input type="hidden" name="response" value="" />
     {/if}
      <table border="0">
      <tr>
        <td>
          <table>
          <tr><td class="module"><label for="login-user">{tr}user{/tr}:</label></td></tr>
		{if $loginuser eq ''}
          <tr><td><input type="text" name="user" id="login-user" size="{if empty($module_params.input_size)}20{else}{$module_params.input_size}{/if}" /></td></tr>
	  <script type="text/javascript">document.getElementById('login-user').focus();</script>
		{else}
		  <tr><td><input type="hidden" name="user" id="login-user" value="{$loginuser}" /><b>{$loginuser}</b></td></tr>
		{/if}
<script type="text/javascript">document.getElementById('login-user').focus();</script>
          {if $feature_challenge eq 'y'} <!-- quick hack to make challenge/response work until 1.8 tiki auth overhaul -->
          <tr><td class="module"><label for="login-email">{tr}email{/tr}:</label></td></tr>
          <tr><td><input type="text" name="email" id="login-email" size="{if empty($module_params.input_size)}20{else}{$module_params.input_size}{/if}" /></td></tr>
          {/if}
          <tr><td class="module"><label for="login-pass">{tr}pass{/tr}:</label></td></tr>
          <tr><td><input type="password" name="pass" id="login-pass" size="{if empty($module_params.input_size)}20{else}{$module_params.input_size}{/if}" /></td></tr>
          <tr><td><input type="submit" name="login" value="{tr}login{/tr}" /></td></tr>
          {if $rememberme ne 'disabled'}
            {if $rememberme eq 'always'}
              <input type="hidden" name="rme" id="login-remember" value="on"/>
            {else}
              <tr><td class="module"><label for="login-remember">{tr}Remember me{/tr}</label> <input type="checkbox" name="rme" id="login-remember" value="on"/></td></tr>
            {/if}
          {/if}
          <tr>
          {if $forgotPass eq 'y' and $allowRegister eq 'y' and $change_password eq 'y'}
            <td  class="module" valign="bottom">[ <a class="linkmodule" href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}register{/tr}</a> | <a class="linkmodule" href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}">{tr}I forgot my pass{/tr}</a> ]</td>
          {/if}
          {if $forgotPass eq 'y' and $allowRegister ne 'y' and $change_password eq 'y'}
            <td  class="module" valign="bottom"><a class="linkmodule" href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}">{tr}I forgot my pass{/tr}</a></td>
          {/if}
          {if ($forgotPass ne 'y' or $change_password ne 'y') and $allowRegister eq 'y'}
            <td  class="module" valign="bottom"><a class="linkmodule" href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}register{/tr}</a></td>
          {/if}
          {if ($forgotPass ne 'y' or $change_password ne 'y') and $allowRegister ne 'y'}
          <td valign="bottom">&nbsp;</td>
          {/if}
          </tr>
          {if $feature_switch_ssl_mode eq 'y' && ($https_login eq 'allowed' || $https_login eq 'encouraged')}
          <tr>
          <td class="module" valign="bottom">
            <a class="linkmodule" href="{$base_url_http}{$login_url}" title="{tr}Click here to login using the default security protocol{/tr}">{tr}standard{/tr}</a> |
            <a class="linkmodule" href="{$base_url_https}{$login_url}" title="{tr}Click here to login using a secure protocol{/tr}">{tr}secure{/tr}</a>
          </td>
          </tr>
          {/if}
          {if $feature_show_stay_in_ssl_mode eq 'y' && $show_stay_in_ssl_mode eq 'y'}
            <tr>
              <td class="module">
                <label for="login-stayssl">{tr}stay in ssl mode{/tr}:</label>?
                <input type="checkbox" name="stay_in_ssl_mode" id="login-stayssl" {if $stay_in_ssl_mode eq 'y'}checked="checked"{/if} />
              </td>
            </tr>
          {/if}
          </table>
        </td>
      </tr>
      </table>

      {if $feature_show_stay_in_ssl_mode neq 'y' || $show_stay_in_ssl_mode neq 'y'}
        <input type="hidden" name="stay_in_ssl_mode" value="{$stay_in_ssl_mode|escape}" />
      {/if}
			{if $use_intertiki_auth eq 'y'}
				<select name='intertiki'>
					<option value="">{tr}local account{/tr}</option>
					<option value="">-----------</option>
					{foreach key=k item=i from=$intertiki}
					<option value="{$k}">{$k}</option>
					{/foreach}
				</select>
			{/if}
      </form>
    {/if}
{/tikimodule}
