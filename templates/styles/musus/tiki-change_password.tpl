<h1>{tr}Change password enforced{/tr}</h1>
<form method="post" action="tiki-change_password.php" >
<table>
<tr>
  <td>{tr}User{/tr}:</td>
  <td><input type="text" name="user" value="{$user|escape}" /></td>
</tr>  
<tr>
  <td>{tr}Old password{/tr}:</td>
  <td><input type="password" name="oldpass" value="{$oldpass|escape}" /></td>
</tr>     
<tr>
  <td>{tr}New password{/tr}:</td>
  <td><input type="password" name="pass" /></td>
</tr>  
<tr>
  <td>{tr}Again please{/tr}:</td>
  <td><input type="password" name="pass2" /></td>
</tr>  
<tr>
  <td>&nbsp;</td>
  <td><input type="submit" name="change" value="{tr}change{/tr}" /></td>
</tr>  
</table>
</form>
