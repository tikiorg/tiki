{* $Id: mod-login_box.tpl,v 1.3 2005-10-11 10:00:20 michael_davey Exp $ *}

{tikimodule title="{tr}Login{/tr}" name="login_box" flip=$module_params.flip decorations=$module_params.decorations}

    <div align="left" style="margin: 6px;">
    {if $user}
      <p>{tr}You are currently logged in to the private area of this site{/tr}</p>
      {tr}logged as{/tr}: {$user}<br />
      <a class="linkmodule" href="tiki-logout.php">{tr}Logout{/tr}</a><br />
      {if $tiki_p_admin eq 'y'}
        <br />
        <form action="{$login_url}" method="post">
        <label for="login-switchuser">{tr}user{/tr}:</label>
        <input type="hidden" name="su" value="1" />
        <input type="text" name="username" id="login-switchuser" size="8" />
        <input type="submit" name="actsu" value="{tr}set{/tr}" />
        </form>
      {/if}
    {elseif $auth_method eq 'cas' && $showloginboxes neq 'y'}
      <b><a class="linkmodule" href="tiki-login.php">{tr}Login through CAS{/tr}</a></b>
      {if $cas_skip_admin eq 'y'}
        <br /><a class="linkmodule" href="tiki-login_scr.php?user=admin">{tr}Login as admin{/tr}</a>
      {/if}
    {else}
     <form name="loginbox" action="{$login_url}" method="post" {if $feature_challenge eq 'y'}onsubmit="doChallengeResponse()"{/if}> 
     {if $feature_challenge eq 'y'}
     <script language="javascript" type='text/javascript' src="lib/md5.js"></script>   
     {literal}
     <script language='Javascript' type='text/javascript'>
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
          <tr><td><input type="text" name="user" id="login-user" size="20" /></td></tr>
		{else}
		  <tr><td><input type="hidden" name="user" id="login-user" value="{$loginuser}" /><b>{$loginuser}</b></td></tr>
		{/if}
          {if $feature_challenge eq 'y'} <!-- quick hack to make challenge/response work until 1.8 tiki auth overhaul -->
          <tr><td class="module"><label for="login-email">{tr}email{/tr}:</label></td></tr>
          <tr><td><input type="text" name="email" id="login-email" size="20" /></td></tr>
          {/if}
          <tr><td class="module"><label for="login-pass">{tr}pass{/tr}:</label></td></tr>
          <tr><td><input type="password" name="pass" id="login-pass" size="20" /></td></tr>
          <tr><td><input type="submit" name="login" value="{tr}login{/tr}" /></td></tr>
          {if $rememberme ne 'disabled'}
          <tr><td class="module"><label for="login-remember">{tr}Remember me{/tr}</label> <input type="checkbox" name="rme" id="login-remember" value="on"/></td></tr>
          {/if}
          <tr>
          {if $forgotPass eq 'y' and $allowRegister eq 'y' and $change_password eq 'y'}
            <td  class="module" valign="bottom"><a class="linkmodule" href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}register{/tr}</a><a class="linkmodule" href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}">{tr}I forgot my pass{/tr}</a></td>
          {/if}
          {if $forgotPass eq 'y' and $allowRegister ne 'y' and $change_password eq 'y'}
            <td  class="module" valign="bottom"><a class="linkmodule" href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}">{tr}I forgot my pass{/tr}</a></td>
          {/if}
          {if ($forgotPass ne 'y' or $change_password ne 'y') and $allowRegister eq 'y'}
            <td  class="module" valign="bottom"><a class="linkmodule" href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}register{/tr}</a></td>
          {/if}
          {if ($forgotPass ne 'y' or $change_password ne 'y')and $allowRegister ne 'y'}
          <td valign="bottom">&nbsp;</td>
          {/if}
          </tr>
          {if $http_login_url ne '' or $https_login_url ne ''}
          <tr>
          <td  class="module" valign="bottom">
            <a class="linkmodule" href="{$http_login_url}" title="{tr}Click here to login using the default security protocol{/tr}">{tr}standard{/tr}</a> |
            <a class="linkmodule" href="{$https_login_url}" title="{tr}Click here to login using a secure protocol{/tr}">{tr}secure{/tr}</a>
          </td>
          </tr>
          {/if}
          {if $show_stay_in_ssl_mode eq 'y'}
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

      {if $show_stay_in_ssl_mode ne 'y'}
        <input type="hidden" name="stay_in_ssl_mode" value="{$stay_in_ssl_mode|escape}" />
      {/if}
      </form>
    {/if}
    </div>
{/tikimodule}
