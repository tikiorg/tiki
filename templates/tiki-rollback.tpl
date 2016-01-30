{title}{tr}Roll back page{/tr} {$page} {tr}to version{/tr} {$version}{/title}

<form action="tiki-rollback.php?page={$page|escape:url}&amp;version={$version|escape}&amp;rollback=y" method="post">
	<input type="submit" class="btn btn-default btn-sm" name="rollback" value="{tr}Roll back{/tr}">
	<input type="hidden" name="page" value="{$page|escape}">
	<input type="hidden" name="version" value="{$version|escape}">
</form>
<div class="wikitext">{$preview.data}</div>
<form action="tiki-rollback.php?page={$page|escape:url}&amp;version={$version|escape}&amp;rollback=y" method="post">
	<div align="center">
		<input type="hidden" name="page" value="{$page|escape}">
		<input type="hidden" name="version" value="{$version|escape}">
		<input type="submit" class="btn btn-default btn-sm" name="rollback" value="{tr}Roll back{/tr}">
	</div>
</form>

{include file='tiki-page_bar.tpl'}

