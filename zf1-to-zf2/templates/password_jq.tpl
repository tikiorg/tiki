{* $Id$ *}
{jq notonready=true}
	{* Test for caps lock - used below *}
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
{*
The below assumes:
	(1) first password input id='pass1',
	(1.1) divs underneath the first password input as follows (to show password strength icon, text and bar):
			<div id="mypassword_text">{icon name='ok' istyle='display:none'}{icon name='error' istyle='display:none' } <span id="mypassword_text_inner"></span></div>
			<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div>
	(2) repeat password input id='pass2',
	(2.1) divs underneath the repeat password input as follows to show whether passord matches:
		<div id="mypassword2_text">
			<div id="match" style="display:none">
				{icon name='ok' istyle='color:#0ca908'} {tr}Passwords match{/tr}
			</div>
			<div id="nomatch" style="display:none">
				{icon name='error' istyle='color:#ff0000'} {tr}Passwords do not match{/tr}
			</div>
		</div>

		This password match will only be run if jquery validator is not on
*}
{jq}
	{* Give warning if caps lock is on when user starts typing in characters for a password *}
	$('#pass1, #pass2').on('keypress', function () {
		regCapsLock(event);
	});
	{* Show strength of the password as it is being typed *}
	$('#pass1').on('keyup', function () {
		runPassword(this.value, 'mypassword');
	});
{/jq}
{if (isset($ignorejq) && $ignorejq === 'y') || $prefs.feature_jquery_validation neq 'y'}
{jq}
	{* Indicate whether repeat password matches as user types it in *}
	$('#pass1, #pass2').on('keyup', function () {
		checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text')
	});
{/jq}
{/if}

{if $prefs.generate_password eq 'y'}
{jq}
	{* Generate password and insert into an input element that will be shown and selected *}
	$('#genPass').click(function () {
		genPass('genepass');
		$('#genepass').show().select();
		return false;
	});
{/jq}
{/if}
