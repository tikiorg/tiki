{* $Id$ *}

{if $alldone}

{jq}
window.parent.location = 'tiki-index.php';
{/jq}
<p>{tr}You will be redirected to the home page shortly.{/tr} <a href="tiki-index.php" target="_parent">{tr}Click here{/tr}</a> {tr}to go to the home page immediately.{/tr}</p>

{else}
{$name = ($prefs.login_is_email eq 'y' and $userinfo.login neq 'admin') ? {$userinfo.email|escape} : {$userinfo.login|escape} }
{jq}
$("#name").val("{{$name}}")
	.rules("add", {
		remote: {
			url: "validate-ajax.php",
			type: "post",
			data: {
				validator: "username",
				input: function() { if ($("#name").val() !== "{{$name}}") { return $("#name").val();} else { return false; } }
			}
		}
	});
$("#email").val("{{$userinfo.email|escape}}");
{/jq}
{if $msg}<p><strong>{$msg|escape}</strong></p>{/if}

<h4>{tr}Please provide local account information{/tr}</h4>
<form action="tiki-socialnetworks_firstlogin.php" method="post" name="RegForm" class="form-horizontal">
{include file="register-login.tpl"}
{include file="register-email.tpl"}
{include file="register-groupchoice.tpl"}
{if $msg}<p>{$msg|escape}</p>{/if}
<div class="form-group">
	<label class="control-label col-sm-3"></label>
	<div class="col-sm-7">
		<input type="submit" class="btn btn-default" name="localinfosubmit" value="{tr}Submit{/tr}">
	</div>
</div>
</form>

<h4>{tr}Do you already have a local account for this site?{/tr}</h4>
<p>{tr}Login to link to it using the following form instead{/tr}</p>
<form action="tiki-socialnetworks_firstlogin.php" method="post" name="RegForm2" class="form-horizontal">
	<div class="form-group">
		<label class="control-label col-sm-3">{tr}Login:{/tr}</label>
		<div class="col-sm-7">
			<input type="text" name="userlogin" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">{tr}Password:{/tr}</label>
		<div class="col-sm-7">
			<input type="password" name="userpass" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3"></label>
		<div class="col-sm-7">
			<input type="submit" class="btn btn-default" name="linkaccount" value="{tr}Link to Existing Account{/tr}">
		</div>
	</div>
</form>

{/if}
