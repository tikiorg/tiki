<h1>{tr}I forgot my password{/tr}</h1>

{if $showmsg ne 'n'}
  {if $showmsg eq 'e'}<span class="warn">{/if}
  {$msg|escape:'html'}
  {if $showmsg eq 'e'}</span>{/if}
  <br /><br />
{/if}

{if $showfrm eq 'y'}
  <form action="tiki-remind_password.php" method="post">
  <table class="normal">
  {if $prefs.login_is_email ne 'y'}
  <tr>
    <td class="formcolor">{tr}Username{/tr}</td>
	<td class="formcolor"><input type="text" name="name" /></td>
  </tr>
  <tr><td class="formcolor" colspan="2">{tr}or{/tr}</td></tr>
  {/if}
  <tr>
    <td class="formcolor">{tr}Email{/tr}</td>
    <td class="formcolor">{if $prefs.login_is_email ne 'y'}<input type="text" name="email" />{else}<input type="text" name="name" />{/if}</td>
  </tr><tr>
    <td class="formcolor" colspan="2"><input type="submit" name="remind" value="{tr}Send me my Password{/tr}" /></td>
  </tr>  
  </table>
  </form>
{/if}

<br /><br />
<a href="{$prefs.tikiIndex}" class="link">{tr}Return to HomePage{/tr}</a>
