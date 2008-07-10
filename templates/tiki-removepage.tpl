<h1>{tr}Remove page{/tr}: {$page} ({if $version == 'last'}{tr}Last Version{/tr}{else}{tr}Version{/tr}: {$version}{/if})</h1>
<br />
<span class="button2"><a class="linkbut" href="tiki-index.php?page={$page|escape:'url'}">{tr}return to wikipage{/tr} <b>{$page}</b></a></span>
<br />
<form action="tiki-removepage.php" method="post">
<p>{tr}You are about to remove the page{/tr} {$page} {tr}permanently{/tr}.</p>
<p><label for="all">{tr}Remove all versions of this page{/tr}:</label> <input type="checkbox" id="all" name="all" /></p>
<input type="hidden" name="page" value="{$page|escape}" />
<input type="hidden" name="version" value="{$version|escape}" />
<input type="hidden" name="historyId" value="{$historyId|escape}" />
<input type="submit" name="remove" value="{tr}Remove{/tr}" />
</form>

