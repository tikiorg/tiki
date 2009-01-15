{* $Id: tiki-listpages.tpl 14254 2008-08-14 15:31:06Z pkdille $ *}

{title}{tr}XML Zip{/tr}{/title}

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
	  <input type="file" name="zip" />
	  <input type="submit" name="import" value="{tr}Import{/tr}"/>	  
</form>