<h1>{tr}I forgot my password{/tr}</h1>
{if $showmsg eq 'y'}
{$msg}<br/><br/>
<a href="{$tikiIndex}" class="link">{tr}Return to HomePage{/tr}</a>
{else}
<form action="tiki-remind_password.php" method="post">
<table class="normal">
<tr>
  <td class="formcolor">{tr}username{/tr}</td>
  <td class="formcolor"><input type="text" name="username" /></td>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="remind" value="{tr}send me my password{/tr}" /></td>
</tr>  
</table>
</form>
{/if}