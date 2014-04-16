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
<form action="tiki-socialnetworks_firstlogin.php" method="post" name="RegForm">
<table class="formcolor">
{include file="register-login.tpl"}
{include file="register-email.tpl"}
{include file="register-groupchoice.tpl"}
{if $msg}<p>{$msg|escape}</p>{/if}
<tr>
<td>&nbsp;</td>
<td><input type="submit" class="btn btn-default btn-sm" name="localinfosubmit" value="{tr}Submit{/tr}"></td>
</tr>
</table>
</form>

<h4>{tr}Do you already have a local account for this site?{/tr}</h4>
<p>{tr}Login to link to it using the following form instead{/tr}</p>
<form action="tiki-socialnetworks_firstlogin.php" method="post" name="RegForm2">
<table class="formcolor">
<tr>
<td>{tr}Login:{/tr}</td>
<td><input type="text" name="userlogin"> </td>
</tr>
<tr>
<td>{tr}Password:{/tr}</td>
<td><input type="password" name="userpass"> </td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" class="btn btn-default btn-sm" name="linkaccount" value="{tr}Link to Existing Account{/tr}"></td>
</tr>
</table>
</form>

{/if}
