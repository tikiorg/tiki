{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-pagehistory.tpl,v 1.2 2004-01-17 01:11:54 musus Exp $ *}

<a class="pagetitle" href="tiki-pagehistory?page={$page|escape:"url"}">{tr}History{/tr}</a> {tr}of{/tr}: <a class="pagetitle" href="tiki-index.php?page={$page|escape:"url"}">{$page}</a><br /><br />
{if $preview}
<h2>{tr}Version{/tr}: {$version}</h2>
<div  class="wikitext">{$preview.data}</div>
<br /> 
{/if}
{if $source}
<div  class="wikitext">{$sourcev}</div>
{/if}
{if $diff}
<h3>{tr}Comparing versions{/tr}</h3>
<table class="normalnoborder">
<tr>
  <td>{tr}Actual_version{/tr}</td>
  <td>{tr}Version{/tr}:{$version}</td>
</tr>
<tr>
  <td valign="top" ><div class="wikitext">{$parsed}</div></td>
  <td valign="top" ><div class="wikitext">{$diff}</div></td>
</tr>
</table>
{/if}
{if $diff2 eq 'y'}
<h3>{tr}Diff to version{/tr}: {$version}</h3>
{$diffdata}
{/if}
<br />
<form action="tiki-pagehistory.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />
<div align="center">
<table border="1" cellpadding="0" cellspacing="0">
<tr>
{if $tiki_p_remove eq 'y'}
<td class="heading"><input type="submit" name="delete" value="{tr}del{/tr}" /></td>
{/if}
<td class="heading">{tr}Date{/tr}</td>
<td class="heading">{tr}Ver{/tr}</td>
<td class="heading">{tr}User{/tr}</td>
<td class="heading">{tr}Ip{/tr}</td>
<td class="heading">{tr}Comment{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
<tr>
{if $tiki_p_remove eq 'y'}
<td class="odd">&nbsp;</td>
{/if}
<td class="odd">&nbsp;{$info.lastModif|tiki_short_datetime}&nbsp;</td>
<td class="odd" align="center">&nbsp;{$info.version}&nbsp;</td>
<td class="odd">&nbsp;{$info.user}&nbsp;</td>
<td class="odd">&nbsp;{$info.ip}&nbsp;</td>
<td class="odd">&nbsp;{$info.comment}&nbsp;</td>
<td class="odd">&nbsp;<a href="tiki-index.php?page={$page|escape:"url"}">{tr}current{/tr}</a>&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=hist loop=$history}
<tr>
{if $tiki_p_remove eq 'y'}
<td class="{cycle advance=false}" align=center><input type="checkbox" name="hist[{$history[hist].version}]" /></td>
{/if}
<td class="{cycle advance=false}">&nbsp;{$history[hist].lastModif|tiki_short_datetime}&nbsp;</td>
<td class="{cycle advance=false}" align="center">&nbsp;{$history[hist].version}&nbsp;</td>
<td class="{cycle advance=false}">&nbsp;{$history[hist].user}&nbsp;</td>
<td class="{cycle advance=false}">&nbsp;{$history[hist].ip}&nbsp;</td>
<td class="{cycle advance=false}">&nbsp;{$history[hist].comment}&nbsp;</td>
<td class="{cycle}">&nbsp;<a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$history[hist].version}" title="{tr}view{/tr}">v</a>&nbsp;
<!--{if $tiki_p_remove eq 'y'}
<a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version={$history[hist].version}" title="{tr}remove{/tr}">r</a>&nbsp;
{/if}
-->
{if $tiki_p_rollback eq 'y'}
<a href="tiki-rollback.php?page={$page|escape:"url"}&amp;version={$history[hist].version}" title="{tr}rollback{/tr}">b</a>&nbsp;
{/if}
<a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;diff={$history[hist].version}" title="{tr}compare{/tr}">c</a>&nbsp;
<a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;diff2={$history[hist].version}" title="{tr}diff{/tr}">d</a>&nbsp;
<a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;source={$history[hist].version}" title="{tr}source{/tr}">s</a>&nbsp;
</td></tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
</div>
</form>
