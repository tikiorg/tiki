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
{jq}
		$jq("#genPass span").click(function () {
			genPass('genepass','pass1','pass2');
			runPassword(document.RegForm.genepass.value, 'mypassword');
			checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');
			$jq('#pass1, #pass2').val('');
			$jq('#mypassword_text, #mypassword2_text').hide();
			$jq("#genepass").show();
		});

		$jq("#pass1, #pass2").change(function () {
			$jq('#mypassword_text, #mypassword2_text').show();
			document.RegForm.genepass.value='';
			$jq("#genepass").hide();
		});
{/jq}

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

{if $prefs.feature_ajax eq 'y'}
		<script src="lib/registration/register_ajax.js" type="text/javascript"></script>
{/if}

{if $showmsg eq 'y'}
		<div class="simplebox highlight">
	{$msg|nl2br}
		</div>

{elseif $userTrackerData}
	{$userTrackerData}

{elseif $email_valid eq 'n' and $allowRegister eq 'y'}
	<label for="email">{icon _id=error style="vertical-align:middle" align="left"} {tr}Your email could not be validated; make sure you email is correct and click register below.{/tr}</label>
 		<form action="tiki-register.php" method="post">
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

		<form action="tiki-register.php" method="post" name="RegForm">
		<fieldset><legend>{tr}Register as a new user{/tr}</legend>
			<table class="form">

				<tr>
					<td class="formcolor"><label for="name">{if $prefs.login_is_email eq 'y'}{tr}Email{/tr}{else}{tr}Username{/tr}{/if}:</label></td>
					<td class="formcolor">
						<input type="text" name="name" id="name" {if $prefs.feature_ajax eq 'y'} onkeyup="return check_name()" onblur="return check_name()"{/if} />
						{if $prefs.feature_ajax eq 'y'}<span id="ajax_msg_name" style="vertical-align: middle;"></span>{/if}
						{if $prefs.login_is_email eq 'y'}
						<em>{tr}Use your email as login{/tr}</em>.
						{else}
							{if $prefs.min_username_length > 1}<div class="highlight"><em>{tr}Minimum {$prefs.min_username_length} characters long{/tr}</em></div>{/if}
							{if $prefs.lowercase_username eq 'y'}<div class="highlight"><em>{tr}Lowercase only{/tr}</em></div>{/if}
						{/if}
					</td>
				</tr>

	{if $prefs.useRegisterPasscode eq 'y'}
				<tr>
					<td class="formcolor"><label for="passcode">{tr}Passcode to register:{/tr}</label></td>
					<td class="formcolor">
						<input type="password" name="passcode" id="passcode" onkeypress="regCapsLock(event)" />
						<em>{tr}Not your password.{/tr} {tr}To request a passcode, {if $prefs.feature_contact eq 'y'}<a href="tiki-contact.php">{/if}
						contact the system administrator{if $prefs.feature_contact eq 'y'}</a>{/if}{/tr}.</em>
					</td>
				</tr>
	{/if}
 
	{if $openid_associate eq 'n'}
				<tr>
					<td class="formcolor"><label for="pass1">{tr}Password:{/tr}</label></td>
					<td class="formcolor">
						<input id='pass1' type="password" name="pass" onkeypress="regCapsLock(event)" onkeyup="{if $prefs.feature_ajax neq 'y'}runPassword(this.value, 'mypassword');checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');{else}check_pass();{/if}" />
						<div style="float:right;margin-left:5px;">
							<div id="mypassword_text"></div>
							<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div> 
						</div>
						{if $prefs.feature_ajax ne 'y'}
							{if $prefs.min_pass_length > 1}<div class="highlight"><em>{tr}Minimum {$prefs.min_pass_length} characters long{/tr}</em></div>{/if}
							{if $prefs.pass_chr_num eq 'y'}<div class="highlight"><em>{tr}Password must contain both letters and numbers{/tr}</em></div>{/if}
						{/if}
					</td>
				</tr>

				<tr>
					<td class="formcolor" style="vertical-align:top"><label for="pass2">{tr}Repeat password:{/tr}</label></td>
					<td class="formcolor">
						<input id='pass2' type="password" name="passAgain" onkeypress="regCapsLock(event)" onkeyup="{if $prefs.feature_ajax neq 'y'}checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');{else}check_pass();{/if}" />
						<div style="float:right;margin-left:5px;">
							<div id="mypassword2_text"></div>
						</div>
						{if $prefs.feature_ajax eq'y'}<span id="checkpass"></span>{/if}
						{if $prefs.generate_password eq 'y'}
							<p>
							<input id='genepass' name="genepass" type="text" tabindex="0" style="display: none" />
							<span id="genPass">
							{if $prefs.feature_ajax eq 'y'}
								{button href="#" _onclick="check_pass();" _text="{tr}Generate a password{/tr}"}
							{else}
								{button href="#" _onclick="" _text="{tr}Generate a password{/tr}"}
							{/if}
							</span>
							</p>
						{/if}
					</td>
				</tr>
	{/if}

	{if $prefs.login_is_email ne 'y'}
				<tr>
					<td class="formcolor"><label for="email">{tr}Email:{/tr}</label></td>
					<td class="formcolor"><input type="text" id="email" name="email" {if $prefs.feature_ajax eq 'y'}onkeyup="return check_mail()" onblur="return check_mail()"{/if}/>
						{if $prefs.feature_ajax eq 'y'}<span id="ajax_msg_mail" style="vertical-align: middle;"></span>{/if}
						{if $prefs.validateUsers eq 'y' and $prefs.validateEmail ne 'y'}
						<div class="highlight"><em class='mandatory_note'>{tr}A valid email is mandatory to register{/tr}</em></div>
						{/if}
					</td>
				</tr>
	{/if}
	{* Custom fields *}
	{section name=ir loop=$customfields}
		{if $customfields[ir].show}
				<tr>
					<td class="form"><label for="{$customfields[ir].prefName}">{tr}{$customfields[ir].label}:{/tr}</label></td>
					<td class="form"><input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}" id="{$customfields[ir].prefName}" /></td>
				</tr>
		{/if}
	{/section}
      
    {* Groups *}
	{if isset($theChoiceGroup)}
				<input type="hidden" name="chosenGroup" value="{$theChoiceGroup|escape}" />
	{elseif isset($listgroups)}
				<tr>
					<td class="formcolor">{tr}Group{/tr}</td>
					<td class="formcolor">
		{foreach item=gr from=$listgroups}
			{if $gr.registrationChoice eq 'y'}
				<div class="registergroup">
					 <input type="radio" name="chosenGroup" id="gr_{$gr.groupName}" value="{$gr.groupName|escape}" /> 
					 <label for="gr_{$gr.groupName}">
					 	{if $gr.groupDesc}
					 		{tr}{$gr.groupDesc|escape}{/tr}
						{else}
							{$gr.groupName|escape}
						{/if}
					</label>
				</div>
			{/if}
		{/foreach}
					</td>
				</tr>
	{/if}

	{if $prefs.rnd_num_reg eq 'y'}{include file='antibot.tpl' td_style='formcolor'}{/if}

				<tr>
					<td class="formcolor">&nbsp;</td>
					<td class="formcolor"><input type="submit" name="register" value="{tr}Register{/tr}" /></td>
				</tr>
			</table>
			</fieldset>
		</form>
		{remarksbox type="note"  title="{tr}Note{/tr}"}
			{tr}Make sure to whitelist this domain to prevent registration emails being canned by your spam filter!{/tr}
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
