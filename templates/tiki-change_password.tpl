{title}{tr}Change password enforced{/tr}{/title}

<form method="post" action="tiki-change_password.php" >
{if !empty($smarty.request.actpass)}
<input type="hidden" name="actpass" value="{$smarty.request.actpass|escape}" />
{/if}
<fieldset><legend>{tr}Change your password{/tr}</legend>
<table class="form">
<tr>
  <td class="formcolor">{tr}Username{/tr}:</td>
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
  <td class="formcolor">
						<div style="float:right;width:150px;margin-left:5px;">
							<div id="mypassword_text"></div>
							<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div> 
						</div>
  <input type="password" name="pass" onkeyup="runPassword(this.value, 'mypassword');{if $prefs.feature_ajax eq 'y'}check_pass();{/if}" />
	{if $prefs.feature_ajax ne 'y'}
		{if $prefs.min_pass_length > 1}
								<div class="highlight"><em>{tr}Minimum {$prefs.min_pass_length} characters long{/tr}</em></div>{/if}
		{if $prefs.pass_chr_num eq 'y'}
								<div class="highlight"><em>{tr}Password must contain both letters and numbers{/tr}</em></div>{/if}
	{/if}
  
  </td>
</tr>  
<tr>
  <td class="formcolor">{tr}Repeat password{/tr}:</td>
  <td class="formcolor"><input type="password" name="pass2" /></td>
</tr>  
<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="change" value="{tr}Change{/tr}" /></td>
</tr>  
</table>
</form>
</fieldset>