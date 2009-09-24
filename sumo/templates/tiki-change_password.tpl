{* test for caps lock*}
{literal}
	<script type="text/javascript">
	<!--
		function regCapsLock(e){
			kc = e.keyCode?e.keyCode:e.which;
			sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
			if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk))
				document.getElementById('divRegCapson').style.visibility = 'visible';
			else
				document.getElementById('divRegCapson').style.visibility = 'hidden';
		}
	// -->
	</script>
{/literal}
{if isset($new_user_validation) && $new_user_validation eq 'y'}
	{title}{tr}Your account has been validated.{/tr}<br />{tr}You have to choose a password to use this account.{/tr}{/title}
{else}
	{assign var='new_user_validation' value='n'}
	{title}{tr}Change password enforced{/tr}{/title}
{/if}

<form method="post" action="tiki-change_password.php" >
{if !empty($oldpass) and $new_user_validation eq 'y'}
	<input type="hidden" name="oldpass" value="{$oldpass|escape}" />
{elseif !empty($smarty.request.actpass)}
	<input type="hidden" name="actpass" value="{$smarty.request.actpass|escape}" />
{/if}
<fieldset>{if $new_user_validation neq 'y'}<legend>{tr}Change your password{/tr}</legend>{/if}
	<div class="simplebox highlight" id="divRegCapson" style="visibility:hidden">{icon _id=error style="vertical-align:middle"} {tr}CapsLock is on.{/tr}</div>
<table class="form">
<tr>
  <td class="formcolor">{tr}Username:{/tr}</td>
  <td class="formcolor">
	<input type="hidden" name="user" value="{$userlogin|escape}" />
	<strong>{$userlogin}</strong>
  </td>
</tr>
{if empty($smarty.request.actpass) and $new_user_validation neq 'y'}
<tr>
  <td class="formcolor"><label for="oldpass">{tr}Old password:{/tr}</label></td>
  <td class="formcolor"><input type="password" name="oldpass" id="oldpass" value="{$oldpass|escape}" /></td>
</tr>
{/if}     
<tr>
  <td class="formcolor"><label for="pass">{tr}New password:{/tr}</label></td>
  <td class="formcolor">
						<div style="float:right;width:150px;margin-left:5px;">
							<div id="mypassword_text"></div>
							<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div> 
						</div>
  <input type="password" name="pass" id="pass" onkeypress="regCapsLock(event)" onkeyup="runPassword(this.value, 'mypassword');{if $prefs.feature_ajax eq 'y'}check_pass();{/if}" />
	{if $prefs.feature_ajax ne 'y'}
		{if $prefs.min_pass_length > 1}
								<div class="highlight"><em>{tr}Minimum {$prefs.min_pass_length} characters long{/tr}</em></div>{/if}
		{if $prefs.pass_chr_num eq 'y'}
								<div class="highlight"><em>{tr}Password must contain both letters and numbers{/tr}</em></div>{/if}
	{/if}
  
  </td>
</tr>  
<tr>
  <td class="formcolor"><label for="pass2">{tr}Repeat password:{/tr}</label></td>
  <td class="formcolor"><input type="password" name="pass2" id="pass2" /></td>
</tr>  
<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="change" value="{tr}Change{/tr}" /></td>
</tr>  
</table>
</fieldset>
</form>
