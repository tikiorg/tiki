<h1>{tr}Change password enforced{/tr}</h1>
<form method="post" action="tiki-change_password.php" >
{if !empty($smarty.request.actpass)}
<input type="hidden" name="actpass" value="{$smarty.request.actpass|escape}" />
{/if}
<table class="normal">
<tr>
  <td class="formcolor">{tr}User{/tr}:</td>
  <td class="formcolor"><input type="text" name="user" value="{$userlogin|escape}" /></td>
</tr>
{if empty($smarty.request.actpass)}
<tr>
  <td class="formcolor">{tr}Old password{/tr}:</td>
  <td class="formcolor"><input type="password" name="oldpass" value="{$oldpass|escape}" /></td>
</tr>	
{/if}     
<tr>
  <td class="formcolor">{tr}New password{/tr}:</td>
  <td class="formcolor"><input type="password" name="pass" /></td>
</tr>  
<tr>
  <td class="formcolor">{tr}Again please{/tr}:</td>
  <td class="formcolor"><input type="password" name="pass2" /></td>
</tr>  
<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="change" value="{tr}Change{/tr}" /></td>
</tr>  
</table>
</form>
