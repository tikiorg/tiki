{* $Id$ *}
{jq notonready=true} {* test for caps lock*}
		function regCapsLock(e){
			kc = e.keyCode?e.keyCode:e.which;
			sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
			if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk))
				document.getElementById('divRegCapson').style.visibility = 'visible';
			else
				document.getElementById('divRegCapson').style.visibility = 'hidden';
		}
{/jq}
{if $prefs.generate_password eq 'y'}
{if $userTrackerData}
{jq}		
		$("#genPass span").click(function () {
			genPass('genepass','pass1','pass2');
			runPassword(document.editItemForm{{$trackerEditFormId}}.genepass.value, 'mypassword');
			checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');
			$('#pass1, #pass2').val('');
			$('#mypassword_text, #mypassword2_text').hide();
			$("#genepass").show();
		});

		$("#pass1, #pass2").change(function () {
			$('#mypassword_text, #mypassword2_text').show();
			document.editItemForm{{$trackerEditFormId}}.genepass.value='';
			$("#genepass").hide();
		});
{/jq}
{else}
{jq}		
		$("#genPass span").click(function () {
			genPass('genepass','pass1','pass2');
			runPassword(document.RegForm.genepass.value, 'mypassword');
			checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');
			$('#pass1, #pass2').val('');
			$('#mypassword_text, #mypassword2_text').hide();
			$("#genepass").show();
		});

		$("#pass1, #pass2").change(function () {
			$('#mypassword_text, #mypassword2_text').show();
			document.RegForm.genepass.value='';
			$("#genepass").hide();
		});
{/jq}
{/if}
{/if}

{if $openid_associate eq 'n'}
	{title admpage='login'}{tr}Registration{/tr}{/title}
{else}
	<h1>{tr}Your OpenID identity is valid{/tr}</h1>
	<p>{tr}However, no account is associated to the OpenID identifier.{/tr}</p>
	{if $allowRegister eq 'y'}
	<table width="100%">
		<col width="50%"/>
		<col width="50%"/>
		<tr>
			<td>
	{/if}
{/if}	
<div class="simplebox highlight" id="divRegCapson" style="visibility:hidden">{icon _id=error style="vertical-align:middle"} {tr}CapsLock is on.{/tr}</div>

{if $prefs.ajax_xajax eq 'y'}
		<script src="lib/registration/register_ajax.js" type="text/javascript"></script>
{/if}

{if $showmsg eq 'y'}
		<div class="simplebox highlight">
	{$msg|nl2br}
		</div>

{elseif $email_valid eq 'n' and $allowRegister eq 'y'}
	<label for="email">{icon _id=error style="vertical-align:middle" align="left"} {tr}Your email could not be validated; make sure you email is correct and click register below.{/tr}</label>
 		<form action="tiki-register.php" method="post">
			{if $smarty.request.invite}<input type='hidden' name='invite' value='{$smarty.request.invite|escape}'/>{/if}
			<input type="text" name="email" id="email" value="{$smarty.post.email}"/>
			<input type="hidden" name="name" value="{$smarty.post.name}"/>
			<input type="hidden" name="pass" value="{$smarty.post.pass}"/>
			<input type="hidden" name="passcode" value="{$smarty.post.passcode}"/>
			<input type="hidden" name="novalidation" value="yes"/>
	{if isset($smarty.post.antibotcode)}		<input type="hidden" name="antibotcode" value="{$smarty.post.antibotcode}"/>{/if}
	{if $smarty.post.chosenGroup}		<input type="hidden" name="chosenGroup" value="{$smarty.post.chosenGroup}" />{/if}
			<input type="submit" name="register" value="{tr}Register{/tr}" />
		</form>

{else}
	{if $allowRegister eq 'y'}

		<fieldset>{if !isset($userTrackerHasDescription)}<legend>{tr}Register as a new user{/tr}</legend>{/if}
		
		{if $userTrackerData}
			{$userTrackerData}
		{else}
			<form action="tiki-register.php" method="post" name="RegForm">
                        {if $smarty.request.invite}<input type='hidden' name='invite' value='{$smarty.request.invite|escape}'/>{/if}
			<table class="formcolor">
			{include file="register-form.tpl"}
			{if $merged_prefs.feature_antibot eq 'y'}{include file='antibot.tpl' td_style='formcolor'}{/if}
			<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="register" value="{tr}Register{/tr}" /></td>
			</tr>
			</table>
			</form>
		{/if}
		
		</fieldset>
		
		{remarksbox type="note"  title="{tr}Note{/tr}"}
			{tr 0=$prefs.sender_email|default:"this domain"|escape}If you use an email filter, be sure to add %0 to your accepted list{/tr}
		{/remarksbox}
	{/if}
	
	{if $openid_associate eq 'y'}
		{if $allowRegister eq 'y'}
			</td>
			<td>
		{/if}
				<p>
					{tr}Associate OpenID with an existing Tikiwiki account{/tr}
				</p>
				{include file="modules/mod-login_box.tpl"} 
		{if $allowRegister eq 'y'}
			</td>
		</tr>
		</table>
		{/if}
	{/if}
		
{/if}
