<h1>{tr}Remove page{/tr}: {$page} ({if $version == 'last'}{tr}last version{/tr}{else}{tr}version{/tr}: {$version}{/if})</h1>
<form action="tiki-removepage.php" method="post">
<p>{tr}You are about to remove the page{/tr} {$page} {tr}permanently{/tr}.</p>
<p><label for="all">{tr}Remove all versions of this page{/tr}:</label> <input type="checkbox" id="all" name="all" /></p>
<input type="hidden" name="page" value="{$page|escape}" />
<input type="hidden" name="version" value="{$version|escape}" />
<input type="submit" name="remove" value="{tr}remove{/tr}" />
</form>
