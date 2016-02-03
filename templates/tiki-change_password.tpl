{* $Id$ *}
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
	{if $new_user_validation eq 'y'}
		<input type="hidden" name="new_user_validation" value="y">
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
	{if empty($smarty.request.actpass) and ($new_user_validation neq 'y' or empty($oldpass))}
	<div class="form-group">
		<label class="col-sm-3 col-md-2 control-label" for="oldpass">{tr}Old Password{/tr}</label>
		<div class="col-sm-7 col-md-6">
			<input type="password" class="form-control" name="oldpass" id="oldpass" placeholder="Old Password">
		</div>
	</div>
	{/if}
	{include file='password_jq.tpl'}
	<div class="form-group">
		<label class="col-sm-3 col-md-2 control-label" for="pass1">{tr}New Password{/tr}</label>
		<div class="col-sm-7 col-md-6">
			<input type="password" class="form-control" placeholder="New Password" name="pass" id="pass1">
			<div style="margin-left:5px;">
				<div id="mypassword_text">{icon name='ok' istyle='display:none'}{icon name='error' istyle='display:none' } <span id="mypassword_text_inner"></span></div>
				<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div>
			</div>
			<div style="margin-top:5px">
				{include file='password_help.tpl'}
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 col-md-2 control-label" for="pass2">{tr}Repeat Password{/tr}</label>
		<div class="col-sm-7 col-md-6">
			<input type="password" class="form-control" name="passAgain" id="pass2" placeholder="Repeat Password">
			<div id="mypassword2_text">
				<div id="match" style="display:none">
					{icon name='ok' istyle='color:#0ca908'} {tr}Passwords match{/tr}
				</div>
				<div id="nomatch" style="display:none">
					{icon name='error' istyle='color:#ff0000'} {tr}Passwords do not match{/tr}
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-3 col-sm-offset-3 col-md-2 col-md-offset-2">
			<span id="genPass">{button href="#" _text="{tr}Generate a password{/tr}"}</span>
		</div>
		<div class="col-sm-3 col-md-2">
			<input id='genepass' class="form-control" name="genepass" type="text" tabindex="0" style="display:none">
		</div>
	</div>
	{if empty($email)}
		<div class="form-group">
			<label class="col-sm-3 col-md-2 control-label" for="email">{tr}Email{/tr}</label>
			<div class="col-sm-7 col-md-6">
				<input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{if not empty($email)}{$email|escape}{/if}">
			</div>
		</div>
	{/if}

	<div class="form-group">
		<div class="col-sm-offset-3 col-md-offset-2 col-sm-10">
			<input type="submit" class="btn btn-default" name="change" onclick="return match_pass();" value="{tr}Change{/tr}"><span id="validate"></span>
		</div>
	</div>
</form>

