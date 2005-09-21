<h1>{tr}I forgot my password{/tr}</h1>

{if $showmsg ne 'n'}
  {if $showmsg eq 'e'}<span class="warn">{/if}
  {$msg}
  {if $showmsg eq 'e'}</span>{/if}
  <br /><br />
{/if}

{tr}Please enter your Username and then click on the 'send me my password' button.<br />You will receive a new password shortly.  Use this new password to access the site.{/tr}

<br />

{if $showfrm eq 'y'}
  <form action="tiki-remind_password.php" method="post">
  <table class="normal">
  <tr>
    <td class="formcolor">{tr}Username:{/tr}</td>
    <td class="formcolor"><input type="text" name="username" /></td>
    <td class="formcolor"><input type="submit" class="button" name="remind"
                                 value="{tr}send me my password{/tr}" /></td>
  </tr>  
  </table>
<input type="hidden" name="option" value="{$option}" />
<input type="hidden" name="task" value="sendNewPass" /> 
  </form>
{/if}
{tr}Important: Username & password are CaSe SenSitiVe{/tr}

<br /><br />
<a href="{$tikiIndex}" class="link">{tr}Return to HomePage{/tr}</a>
