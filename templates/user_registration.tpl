{if empty($user)}

	{jq notonready=true} {* test for caps lock*}
	var divRegCapson = $('#divRegCapson');
	function regCapsLock(e){
		kc = e.keyCode?e.keyCode:e.which;
		sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
		if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk)) {
			divRegCapson.show();
		} else {
			divRegCapson.hide();
		}
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
					setuppass").hide();
				});
			{/jq}
		{/if}
	{/if}

	{if $openid_associate neq 'n'}
		<h1>{tr}Your OpenID identity is valid{/tr}</h1>
		<p>{tr}However, no account is associated to the OpenID identifier.{/tr}</p>
	{/if}
	<div class="simplebox highlight" id="divRegCapson" style="display: none;">{icon _id=error style="vertical-align:middle"} {tr}CapsLock is on.{/tr}</div>
	{if $allowRegister eq 'y'}
		<div>
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

		{remarksbox type="note"  title="{tr}Note{/tr}"}
			{tr _0=$prefs.sender_email|default:"this domain"|escape}If you use an email filter, be sure to add %0 to your accepted list{/tr}
		{/remarksbox}
		</div>
	{/if}

	{if $openid_associate eq 'y'}
		<p>
			{tr}Associate OpenID with an existing Tiki account{/tr}
		</p>
		{include file="modules/mod-login_box.tpl"}
	{/if}
{else}
	{include file='modules/mod-login_box.tpl' nobox='y'}
{/if}

