<b><a href="stats.php" class="pagetitle">User prefs stats for tw.o</a></b>
<br /><br />

<span class="button2"><a href="stats.php?countries">Countries</a></span>
<span class="button2"><a href="stats.php?languages">Languages</a></span>
<span class="button2"><a href="stats.php?timezones">Timezones</a></span>
<span class="button2"><a href="stats.php?themes">Themes</a></span>
<br /><br />

<div class="wikitext">
<table>
{section name=c loop=$show}
{if $smarty.section.c.first}
<tr>{foreach key=k item=i from=$show[c]}<th>{$k}</th>{/foreach}</tr>
{/if}
<tr>{foreach key=k item=i from=$show[c]}<td>{$i}</td>{/foreach}</tr>
{sectionelse}
<tr><td><i>choose an item to report</i></td></tr>
{/section}
</table>
</div>
