{* $Id$ *}
{if isset($new_user_validation) && $new_user_validation eq 'y'}
	{title}{tr}Your account has been validated.{/tr}{/title}
	{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}{tr}You have to choose a password to use this account.{/tr}{/remarksbox}
{else}
	{assign var='new_user_validation' value='n'}
{/if}
<div class="row">
<div class="col-md-10 col-md-push-1 clearfix">
	<form role="form" class="form-horizontal" method="post" action="tiki-change_password.php">
		<div class="panel panel-default">
			{if !empty($oldpass) and $new_user_validation eq 'y'}
				<input type="hidden" name="oldpass" value="{$oldpass|escape}">
			{elseif !empty($smarty.request.actpass)}
				<input type="hidden" name="actpass" value="{$smarty.request.actpass|escape}">
			{/if}
			{if $new_user_validation eq 'y'}
				<input type="hidden" name="new_user_validation" value="y">
			{/if}
			<div class="panel-heading text-center">
				{if $new_user_validation neq 'y'}
					<h3 class="panel-title">{tr}Change password{/tr}</h3>
				{else}
					<h3 class="panel-title">{tr}Set password{/tr}</h3>
				{/if}
			</div>
			<div class="panel-body">
				<div class="clearfix">
					{include file='password_jq.tpl'}
					<div class="text-center" id="divRegCapson" style="display:none;">
						{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}{tr}CapsLock is on.{/tr}{/remarksbox}
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label" for="user">{tr}Username{/tr}</label>
					<div class="col-md-8">
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
						<label class="col-md-4 control-label" for="oldpass">{tr}Old Password{/tr}</label>
						<div class="col-md-8">
							<input type="password" class="form-control" name="oldpass" id="oldpass" placeholder="Old Password">
						</div>
					</div>
				{/if}
				<div class="form-group">
					<label class="col-md-4 control-label" for="pass1">{tr}New Password{/tr}</label>
					<div class="col-md-8">
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
					<label class="col-md-4 control-label" for="pass2">{tr}Repeat Password{/tr}</label>
					<div class="col-md-8">
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
				{if $prefs.generate_password eq 'y'}
					<div class="form-group">
						<div class="col-md-4 col-md-offset-4">
							<span id="genPass">{button href="#" _text="{tr}Generate a password{/tr}"}</span>
						</div>
						<div class="col-md-4">
							<input id='genepass' class="form-control" name="genepass" type="text" tabindex="0" style="display:none">
						</div>
					</div>
				{/if}
				{if empty($email)}
					<div class="form-group">
						<label class="col-md-4 control-label" for="email">{tr}Email{/tr}</label>
						<div class="col-md-8">
							<input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{if not empty($email)}{$email|escape}{/if}">
						</div>
					</div>
				{/if}
			</div>
			<div class="panel-footer text-center">
				<input type="submit" class="btn btn-primary" name="change" onclick="return checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');" value="{tr}Apply{/tr}"><span id="validate"></span>
			</div>
		</div>
	</form>
</div>
</div>
