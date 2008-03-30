{* $Id$ *}
  {tikimodule title="{tr}Login{/tr}" name="login-box"}
    {if $user}
      {tr}logged as{/tr}: {$user}<br />
      <a class="linkmodule" href="tiki-logout.php">{tr}Logout{/tr}</a><br />
      {if $tiki_p_admin eq 'y'}
        <form action="{$prefs.login_url}" method="post">
        {tr}User{/tr}:
        <input type="text" name="username" size="8" />
        <input type="submit" name="su" value="{tr}Set{/tr}" />
        </form>
      {/if}
    {else}
     <form name="loginbox" action="{$prefs.login_url}" method="post" {if $prefs.feature_challenge eq 'y'}onsubmit="doChallengeResponse()"{/if}> 
     {if $prefs.feature_challenge eq 'y'}
     <script type='text/javascript' src="lib/md5.js"></script>   
     {literal}
     <script type='text/javascript'>
     <!--
     function doChallengeResponse() {
       document.loginbox.zoofoo.value=MD5(document.loginbox.pass.value);
       str = document.loginbox.user.value + 
       MD5(document.loginbox.pass.value) +
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
     <input type="hidden" name="zoofoo" value="" />
     <input type="hidden" name="response" value="" />
     {/if}
          <div class="module"><input type="text" name="user"  size="8" /> {tr}User{/tr}</div>
          <div class="module"><input type="password" name="pass" size="8" /> {tr}pass{/tr}</div>
          <div class="module"><input type="submit" name="login" value="{tr}Login{/tr}" style="width:100%;"/></div>
          {if $prefs.rememberme ne 'disabled'}
          <div class="module"><input type="checkbox" name="rme" value="on"/> {tr}Remember me{/tr}</div>
          {/if}
          {if $prefs.allowRegister eq 'y' and $prefs.forgotPass eq 'y'}
            <div class="module"><a class="linkmodule" href="tiki-register.php">{tr}Register{/tr}</a></div>
						<div class="module"><a class="linkmodule" href="tiki-remind_password.php">{tr}I forgot my pass{/tr}</a></div>
          {/if}
          {if $prefs.forgotPass eq 'y' and $prefs.allowRegister ne 'y'}
            <div class="module"><a class="linkmodule" href="tiki-remind_password.php">{tr}I forgot my pass{/tr}</a></div>
          {/if}
          {if $prefs.forgotPass ne 'y' and $prefs.allowRegister eq 'y'}
            <div class="bottom"><a class="linkmodule" href="tiki-register.php">{tr}Register{/tr}</a></div>
          {/if}
          {if $http_login_url ne '' or $prefs.https_login_url ne ''}
            <div class="module"><a class="linkmodule" href="{$http_login_url}">{tr}Standard{/tr}</a> |
            <a class="linkmodule" href="{$prefs.https_login_url}">{tr}Secure{/tr}</a></div>
          {/if}
          {if $show_stay_in_ssl_mode eq 'y'}
             <div class="module">{tr}Stay in ssl Mode{/tr}:&nbsp;
                <input type="checkbox" name="stay_in_ssl_mode" {if $stay_in_ssl_mode eq 'y'}checked="checked"{/if} /></div>
          {/if}
      {if $show_stay_in_ssl_mode ne 'y'}
        <input type="hidden" name="stay_in_ssl_mode" value="{$stay_in_ssl_mode|escape}" />
      {/if}
      </form>
    {/if}
{/tikimodule}
