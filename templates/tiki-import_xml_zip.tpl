{* $Id$ *}

{title}{tr}XML Zip Import{/tr}{/title}

<div class="navbar">
	 {button href='tiki-admin_structures.php' _text='{tr}Structures{/tr}'}
</div>

{if $error}
	<div class="simplebox highlight">
		 {$error}
	</div>
{/if}
{if $msg}
	<div class="simplebox highlight">
		 {$msg}
	</div>
{/if}
<form enctype='multipart/form-data' method="post" action="{$smarty.server.PHP_SELF}">
	  <div><input type="file" name="zip" /></div>
	  <div>{tr}Or{/tr}</div>
	  <div><label>{tr}Name of the zip file on the server{/tr}<input type="text" name="local" /></label></div>
	  <input type="submit" name="import" value="{tr}Import{/tr}"/>
</form>