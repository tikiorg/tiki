{* test for caps lock*}
{literal}
<script language="Javascript">
<!--
function capLock(e){
 kc = e.keyCode?e.keyCode:e.which;
 sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
 if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk))
  document.getElementById('divCapson').style.visibility = 'visible';
 else
  document.getElementById('divCapson').style.visibility = 'hidden';
}
// -->
</script>
{/literal}
<h2>{tr}Register as a new user{/tr}</h2>
<div id="divCapson" style="visibility:hidden">{icon _id=error style="vertical-align:middle"} {tr}CapsLock is on.{/tr}</div>
<br />
{if $prefs.feature_ajax eq 'y'}
  <script src="lib/registration/register_ajax.js" type="text/javascript"></script>
{/if}

{if $showmsg eq 'y'}
<div class="simplebox highlight">
{$msg}
</div>

{elseif $userTrackerData}
{$userTrackerData}

{elseif $email_valid eq 'n'}
{icon _id=error style="vertical-align:middle" align="left"} {tr}Your email could not be validated; make sure you email is correct and click register below.{/tr}<br />
  <form action="tiki-register.php" method="post">
    <input type="text" name="email" value="{$smarty.post.email}"/>
    <input type="hidden" name="name" value="{$smarty.post.name}"/>
    <input type="hidden" name="pass" value="{$smarty.post.pass}"/>
    <input type="hidden" name="regcode" value="{$smarty.post.regcode}"/>
    <input type="hidden" name="novalidation" value="yes"/>
    {if isset($smarty.post.antibotcode)}<input type="hidden" name="antibotcode" value="{$smarty.post.antibotcode}"/>{/if}
    {if $smarty.post.chosenGroup}<input type="hidden" name="chosenGroup" value="{$smarty.post.chosenGroup}" />{/if}
    <input type="submit" name="register" value="{tr}Register{/tr}" />
  </form>

{else}

  <form action="tiki-register.php" method="post" name="RegForm"> <br />
    <table class="normal">

      <tr><td class="formcolor">{if $prefs.login_is_email eq 'y'}{tr}Email{/tr}{else}{tr}Username{/tr}{/if}:</td>
      <td class="formcolor">
        <input type="text" name="name" id="name"{if $prefs.feature_ajax eq 'y'} onKeyUp="return check_name()"{/if} /><br />
          {if $prefs.feature_ajax eq'y'}<div id="checkfield" style="float:left"></div>{/if}
		{if $prefs.login_is_email eq 'y'} 
		<em>{tr}Use your email as login{/tr}</em>. 
		{else}
	  {if $prefs.lowercase_username eq 'y'} <em>{tr}Lowercase only{/tr}</em>.{/if}</td>
		{/if}
      </tr>

{if $prefs.useRegisterPasscode eq 'y'}
        <tr><td class="formcolor">{tr}Passcode to register{/tr}:</td>
	<td class="formcolor"><input type="password" name="passcode" onkeypress="capLock(event)" /><br /><em>{tr}Not your password.{/tr} {tr}To request a passcode, {if $prefs.feature_contact eq 'y'}<a href="tiki-contact.php">{/if}
	contact the sytem administrator{if $prefs.feature_contact eq 'y'}</a>{/if}{/tr}.</em> </td></tr>
      {/if}
 
      <tr><td class="formcolor">{tr}Password{/tr}:</td>
      <td class="formcolor">
			<input style="float:left"  id='pass1' type="password" name="pass" onkeypress="capLock(event)" onKeyUp="runPassword(this.value, 'mypassword');{if $prefs.feature_ajax eq 'y'}check_pass();{/if}" />
				<div style="float:left;width:150px;margin-left:5px;">
				<div id="mypassword_text"></div>
				<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div> 
				</div>			
{if $prefs.feature_ajax ne 'y'}<p><div>{/if}
	  {if $prefs.feature_ajax ne 'y' and $prefs.min_pass_length > 1}<em>{tr}Minimum {$prefs.min_pass_length} characters long{/tr}</em>. {/if}
	  {if $prefs.feature_ajax ne 'y' and $prefs.pass_chr_num eq 'y'}<em>{tr}Password must contain both letters and numbers{/tr}</em>.{/if}
{if $prefs.feature_ajax ne 'y'}</div></p>{/if}


{if $prefs.generate_password eq 'y'}
	<p><div><span class="button2"><a href="#" onClick="genPass('genepass','pass1','pass2');runPassword(document.RegForm.genpass.value, 'mypassword');{if $prefs.feature_ajax eq 'y'}check_pass();{/if}">{tr}Generate a password{/tr}:</a></span><input id='genepass' name="genpass" type="text" /></div></p>
{/if}


	  </td>
      </tr>

      <tr><td class="formcolor">{tr}Repeat password{/tr}:</td>
      <td class="formcolor"><input style="float:left" id='pass2' type="password" name="passAgain" onkeypress="capLock(event)" 
        {if $prefs.feature_ajax eq'y'}onKeyUp="check_pass()"{/if}/>{if $prefs.feature_ajax eq'y'}<div style="float:left;margin-left:5px;" id="checkpass"></div>{/if}</td>
      </tr>

{if $prefs.login_is_email ne 'y'}
      <tr><td class="formcolor">{tr}Email{/tr}:</td>
      <td class="formcolor"><input style="float:left" type="text" id="email" name="email"
        {if $prefs.validateUsers eq 'y' and $prefs.feature_ajax eq 'y'}onKeyUp="return check_mail()"{/if}/>{if $prefs.feature_ajax eq'y'}<div id="checkmail" style="float:left"></div>{/if}&nbsp;
        {if $prefs.validateUsers eq 'y' and $prefs.validateEmail ne 'y'}
        <em>{tr}A valid email is mandatory to register{/tr}</em>.{/if}</td>
      </tr>
{/if}
      {* Custom fields *}
      {section name=ir loop=$customfields}
        {if $customfields[ir].show}
          <tr><td class="form">{tr}{$customfields[ir].label}{/tr}:</td>
            <td class="form"><input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}" /></td>
          </tr>
        {/if}
      {/section}
      
      {* Groups *}
{if isset($theChoiceGroup)}
        <input type="hidden" name="chosenGroup" value="{$theChoiceGroup|escape}" />
{elseif $listgroups}
		<tr>
			<td class="formcolor">{tr}Select your group{/tr}:</td>
			<td class="formcolor">
{foreach item=gr from=$listgroups}
{if $gr.registrationChoice eq 'y'}			<input type="radio" name="chosenGroup" id="gr_{$gr.groupName}" value="{$gr.groupName|escape}" /> <label for="gr_{$gr.groupName}">{if $gr.groupDesc}{tr}{$gr.groupDesc}{/tr}{else}{$gr.groupName}{/if}</label><br />{/if}
{/foreach}
			</td>
		</tr>
{/if}

{if $prefs.rnd_num_reg eq 'y'}{include file='antibot.tpl'}{/if}

      <tr><td class="formcolor">&nbsp;</td>
      <td class="formcolor"><input type="submit" name="register" value="{tr}Register{/tr}" /></td>
      </tr>
    </table>
  </form>
<br /><div class="simplebox">
{icon _id=information style="vertical-align:middle" align="left"}{tr}NOTE: Make sure to whitelist this domain to prevent registration emails being canned by your spam filter!{/tr}
</div>
  <br />
{/if}
