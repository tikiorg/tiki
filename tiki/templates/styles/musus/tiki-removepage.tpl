<h2>{tr}Remove page{/tr}: {$page} ({tr}version{/tr}: {$version})</h2>
<form action="tiki-removepage.php" method="post">
{tr}You are about to remove the page{/tr}: {$page} {tr}permanently{/tr}.<br />
{tr}Remove all versions of this page{/tr}: <input type="checkbox" name="all" /><br />
<input type="hidden"  name="page" value="{$page|escape}" />
<input type="hidden" name="version" value="{$version|escape}" />
<input type="submit" name="remove" value="{tr}remove{/tr}" />
</form>
