{* $Id$ *}

{title}{tr}XML Zip Import{/tr}{/title}

<div class="t_navbar">
	{button href='tiki-admin_structures.php' class="btn btn-default" _text="{tr}Structures{/tr}"}
</div>

{if $error}
	{remarksbox type='errors' title="{tr}Errors{/tr}"}
		{$error}
	{/remarksbox}
{/if}
{if $msg}
	{remarksbox type='feedback' title="{tr}Feedback{/tr}"}
		{$msg}
	{/remarksbox}
{/if}
<form enctype='multipart/form-data' method="post" action="{$smarty.server.PHP_SELF}">
	<div><input type="file" name="zip"></div>
	<div>{tr}Or{/tr}</div>
	<div><label>{tr}Name of the zip file on the server{/tr}<input type="text" name="local"></label></div>
	<input type="submit" class="btn btn-default btn-sm" name="import" value="{tr}Import{/tr}">
</form>
