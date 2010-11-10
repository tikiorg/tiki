{* test for caps lock*}
{jq notonready=true}
{literal}
		function regCapsLock(e){
			kc = e.keyCode?e.keyCode:e.which;
			sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
			if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk))
				document.getElementById('divRegCapson').style.visibility = 'visible';
			else
				document.getElementById('divRegCapson').style.visibility = 'hidden';
		}

		var submit_counter = 0;
		function match_pass() {
			submit_counter += 1;
			ret_msg = document.getElementById('validate');
			pass0 = document.getElementById('oldpass').value;
			pass1 = document.getElementById('pass1').value;
			pass2 = document.getElementById('pass2').value;
			if (submit_counter > 10) {
				ret_msg.innerHTML = "<img src='pics/icons/exclamation.png' style='vertical-align:middle' alt='Overflow' /> Too many tries";
				return false;
			} else if ((pass0 == '') || (pass1 == '') || (pass2 == '')) {
				ret_msg.innerHTML = "<img src='pics/icons/exclamation.png' style='vertical-align:middle' alt='Missing' /> Passwords missing";
				return false;
			} else if ( pass1 != pass2 ) {
				ret_msg.innerHTML = "<img src='pics/icons/exclamation.png' style='vertical-align:middle' alt='Do not match' /> Passwords don\'t match";
				return false;
			}
			return true;
		}
{/literal}
{/jq}
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
<table class="formcolor">
<tr>
  <td><label for="user">{tr}Username:{/tr}</label></td>
  <td>
  	{if empty($userlogin)}
		<input type="text" id="user" name="user"/ >
	{else}
		<input type="hidden" id="user" name="user" value="{$userlogin|escape}" />
		<strong>{$userlogin}</strong>
	{/if}
  </td>
</tr>
{if empty($smarty.request.actpass) and $new_user_validation neq 'y'}
<tr>
  <td><label for="oldpass">{tr}Old password:{/tr}</label></td>
  <td><input type="password" name="oldpass" id="oldpass" value="{$oldpass|escape}" /></td>
</tr>
{/if}     
<tr>
  <td><label for="pass1">{tr}New password:{/tr}</label></td>
  <td>
						<div style="float:right;width:175px;margin-left:5px;">
							<div id="mypassword_text"></div>
							<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div> 
						</div>
  <input type="password" name="pass" id="pass1" onkeypress="regCapsLock(event)" onkeyup="runPassword(this.value, 'mypassword');{if $prefs.ajax_xajax eq 'y'}check_pass();{/if}" />
	{if $prefs.ajax_xajax ne 'y'}
		{include file='password_help.tpl'}
	{/if}
  
  </td>
</tr>
<tr>
  <td><label for="pass2">{tr}Repeat password:{/tr}</label></td>
  <td><input type="password" name="pass2" id="pass2" /></td>
</tr>
{if empty($email)}
<tr>
  <td><label for="email">{tr}Email:{/tr}</label></td>
  <td><input type="text" name="email" id="email" /></td>
</tr>
{/if}
<tr>
  <td>&nbsp;</td>
  <td><input type="submit" name="change" value="{tr}Change{/tr}" onclick="return match_pass();"/><span id="validate"></span></td>
</tr>
</table>
</fieldset>
</form>
