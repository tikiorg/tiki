{* $Header: /cvsroot/tikiwiki/_mods/themes/classicblue/theme/templates/styles/classicblue/modules/mod-login_box.tpl,v 1.1 2004-09-22 13:14:46 damosoft Exp $ *}

{tikimodule title="{tr}Login{/tr}" name="login_box"}

    {if $user}
      {tr}logged as{/tr}: {$user}<br />
      <a class="linkmodule" href="tiki-logout.php">{tr}Logout{/tr}</a><br />
      {if $tiki_p_admin eq 'y'}
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
          <tr>
            <td>
		{if $loginuser eq ''}
             <input type="text" name="user" value="USER" size=8 onblur="if(this.value=='')this.value='USER';" onfocus="if(this.value=='USER')this.value='';"/>
              {else}
              <input type="hidden" name="user" value="{$loginuser}" /><b>{$loginuser}</b>
		{/if}
			 {if $feature_challenge eq 'y'}<!-- quick hack to make challenge/response work until 1.8 tiki auth overhaul -->
              <input name="email" type="text" value="EMAIL" size="8" onblur="if(this.value=='')this.value='EMAIL';" onfocus="if(this.value=='EMAIL')this.value='';"/>
             {/if}
              <INPUT type="text" name="pass" value="PASS" size=8 onblur="if(this.value=='')this.value='PASS';" onfocus="if(this.value=='PASS')this.value='';"/>
              <INPUT type=submit value="GO!" name=login>
                                                        </td>
          </tr>
          <tr>
          </tr>
          {if $rememberme ne 'disabled'}
          <tr>
            <td class="module"><label for="login-remember">{tr}remember me{/tr}</label> <input type="checkbox" name="rme" id="login-remember" value="on"/></td>
          </tr>
          {/if}
          <tr>
          {if $forgotPass eq 'y' and $allowRegister eq 'y'}
            <td  class="module" valign="bottom"><a class="linkmodule" href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}register{/tr}</a> • <a class="linkmodule" href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}">{tr}forgot my pass{/tr}</a></td>
            {/if}
          {if $forgotPass eq 'y' and $allowRegister ne 'y'}
            <td width="55" valign="bottom"  class="module"><a class="linkmodule" href="tiki-remind_password.php" title="{tr}Click here if you've forgotten your password{/tr}">{tr}forgot my pass{/tr}</a></td>
            {/if}
          {if $forgotPass ne 'y' and $allowRegister eq 'y'}
            <td width="96" valign="bottom"  class="module"><a class="linkmodule" href="tiki-register.php" title="{tr}Click here to register{/tr}">{tr}register{/tr}</a></td>
            {/if}
          {if $forgotPass ne 'y' and $allowRegister ne 'y'}
            <td width="2" valign="bottom">&nbsp;</td>
            {/if}
          </tr>
          {if $http_login_url ne '' or $https_login_url ne ''}
          <tr>
            <td  class="module" valign="bottom">
            <a class="linkmodule" href="{$http_login_url}" title="{tr}Click here to login using the default security protocol{/tr}">{tr}standard{/tr}</a> • <a class="linkmodule" href="{$https_login_url}" title="{tr}Click here to login using a secure protocol{/tr}">{tr}secure{/tr}</a>
          </td>
          </tr>
          {/if}
          {if $show_stay_in_ssl_mode eq 'y'}
            
          <tr>
              
            <td class="module">
                <label for="login-stayssl">{tr}stay in ssl mode{/tr}:</label>
              ?
                
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
{/tikimodule}
