<h2>{tr}Rollback page{/tr}: {$page} {tr}to_version{/tr}: {$version}</h2>
<div class="wikitext">{$preview.data}</div>
<div align="center">
<form action="tiki-rollback.php" method="post">
<input type="hidden"  name="page" value="{$page|escape}" />
<input type="hidden" name="version" value="{$version|escape}" />
<input type="submit" name="rollback" value="{tr}rollback{/tr}" />
</form>
</div>
