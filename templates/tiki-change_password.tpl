{* test for caps lock*}
{jq notonready=true}
{literal}
	function regCapsLock(e){
		var kc = e.keyCode?e.keyCode:e.which;
		var sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
		if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk))
			document.getElementById('divRegCapson').style.visibility = 'visible';
		else
			document.getElementById('divRegCapson').style.visibility = 'hidden';
	}

	var submit_counter = 0;
	function match_pass() {
		submit_counter += 1;
		var ret_msg = document.getElementById('validate');
		var pass0 = document.getElementById('oldpass') ? document.getElementById('oldpass').value : "dummy";
		var pass1 = document.getElementById('pass1').value;
		var pass2 = document.getElementById('pass2').value;
		if (submit_counter > 10) {
			ret_msg.innerHTML = "<img src='img/icons/exclamation.png' style='vertical-align:middle' alt='Overflow'> {tr}Too many tries{/tr}";
			return false;
		} else if ((pass0 == '') || (pass1 == '') || (pass2 == '')) {
			ret_msg.innerHTML = "<img src='img/icons/exclamation.png' style='vertical-align:middle' alt='Missing'> {tr}Passwords missing{/tr}";
			return false;
		} else if ( pass1 != pass2 ) {
			ret_msg.innerHTML = "<img src='img/icons/exclamation.png' style='vertical-align:middle' alt='Do not match'> {tr}Passwords don\'t match{/tr}";
			return false;
		}
		return true;
	}
{/literal}
{/jq}

{if isset($new_user_validation) && $new_user_validation eq 'y'}
	{title}{tr}Your account has been validated.{/tr} {tr}You have to choose a password to use this account.{/tr}{/title}
{else}
	{assign var='new_user_validation' value='n'}
	{title}{tr}Change password enforced{/tr}{/title}
{/if}

<form class="form-horizontal col-md-12" method="post" action="tiki-change_password.php">
	{if !empty($oldpass) and $new_user_validation eq 'y'}
		<input type="hidden" name="oldpass" value="{$oldpass|escape}">
	{elseif !empty($smarty.request.actpass)}
		<input type="hidden" name="actpass" value="{$smarty.request.actpass|escape}">
	{/if}
	<fieldset>{if $new_user_validation neq 'y'}<legend>{tr}Change your password{/tr}</legend>{/if}
		<div class="alert alert-warning" id="divRegCapson" style="visibility:hidden">{icon name='error' style="vertical-align:middle"} {tr}CapsLock is on.{/tr}</div>
	</fieldset>
	<div class="form-group">
		<label class="col-sm-3 col-md-2 control-label" for="user">{tr}Username{/tr}</label>
		<div class="col-sm-7 col-md-6">
			{if empty($userlogin)}
				<input type="text" class="form-control" id="user" name="user">
			{else}
				<input type="hidden" id="user" name="user" value="{$userlogin|escape}">
				<div class="form-control-static"><strong>{$userlogin|escape}</strong></div>
			{/if}
		</div>
	</div>
	{if empty($smarty.request.actpass) and $new_user_validation neq 'y'}
	<div class="form-group">
		<label class="col-sm-3 col-md-2 control-label" for="oldpass">{tr}Old Password{/tr}</label>
		<div class="col-sm-7 col-md-6">
			<input type="password" class="form-control" name="oldpass" id="oldpass" placeholder="Old Password">
		</div>
	</div>
	{/if}
	<div class="form-group">
		<label class="col-sm-3 col-md-2 control-label" for="pass1">{tr}New Password{/tr}</label>
		<div class="col-sm-7 col-md-6">
			<input type="password" class="form-control" placeholder="New Password" name="pass" id="pass1"
				onkeypress="regCapsLock(event)" onkeyup="runPassword(this.value, 'mypassword');{if 0 and $prefs.feature_ajax eq 'y'}check_pass();{/if}">
		</div>
		<div class="col-md-4">
			<div id="mypassword_text"></div>
			<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div>
		</div>
		<p class="col-md-4 col-sm-10 help-block">{tr}Minimum 5 characters long.{/tr}</p>
	</div>
	<div class="form-group">
		<label class="col-sm-3 col-md-2 control-label" for="pass2">{tr}Repeat Password{/tr}</label>
		<div class="col-sm-7 col-md-6">
			<input type="password" class="form-control" name="pass2" id="pass2" placeholder="Repeat Password">
		</div>
	</div>
	{if empty($email)}
		<div class="form-group">
			<label class="col-sm-3 col-md-2 control-label" for="email">{tr}E-mail{/tr}</label>
			<div class="col-sm-7 col-md-6">
				<input type="email" class="form-control" name="email" id="email" placeholder="E-mail">
			</div>
		</div>
	{/if}

	<div class="form-group">
		<div class="col-sm-offset-3 col-md-offset-2 col-sm-10">
			<input type="submit" class="btn btn-default" name="change" onclick="return match_pass();" value="{tr}Change{/tr}"><span id="validate"></span>
		</div>
	</div>
</form>

