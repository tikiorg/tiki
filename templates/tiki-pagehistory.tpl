{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-pagehistory.tpl,v 1.16 2004-08-11 20:33:04 sylvieg Exp $ *}

<a class="pagetitle" href="tiki-pagehistory?page={$page|escape:"url"}">{tr}History{/tr}</a> {tr}of{/tr}: <a class="pagetitle" href="tiki-index.php?page={$page|escape:"url"}">{$page}</a><br /><br />
{if $preview}
<h2>{tr}Version{/tr}: {$oldver}</h2>
<div  class="wikitext">{$preview.data}</div>
<br /> 
{/if}
{if $source}
<div  class="wikitext">{$sourcev}</div>
{/if}
{if $diff}
<h3>{tr}Comparing the version {$oldver} with the last version{/tr}</h3>
<table class="normalnoborder">
<tr>
  <td>{tr}Version{/tr}:{$oldver}</td>
  <td>{tr}Last version{/tr}</td>
</tr>
<tr>
  <td valign="top" ><div class="wikitext">{$diff}</div></td>
  <td valign="top" ><div class="wikitext">{$parsed}</div></td>
</tr>
</table>
{/if}
{if $diff2 eq 'y'}
<h3>{if $newver == 0}{tr}Comparing the version {$oldver} with the last version{/tr}{else}{tr}Comparing the version {$oldver} with the version {$newver}{/tr}{/if}</h3>
{$diffdata}
{/if}
<br />
<form action="tiki-pagehistory.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />
<div align="center">
<div class="simplebox"><b>{tr}Legend{/tr}:</b> {tr}v=view, b=rollback, c=compare with last, d=diff to last, s=source{/tr}</div>
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
<td class="heading" colspan="2"><input type="submit" name="compare" value="{tr}compare{/tr}" /><br />{tr}old ver - new ver{/tr}</td>
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
<td class="odd">&nbsp;<a class="link" href="tiki-index.php?page={$page|escape:"url"}">{tr}current{/tr}</a>&nbsp;</td>
<td class="odd">&nbsp;</td>
<td class="odd"><input type="radio" name="newver" value="0" title="Select a newer version for comparison" checked="checked" /></td>
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
<td class="{cycle}">&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$history[hist].version}" title="{tr}view{/tr}">v</a>&nbsp;
{if $tiki_p_rollback eq 'y'}
<a class="link" href="tiki-rollback.php?page={$page|escape:"url"}&amp;version={$history[hist].version}" title="{tr}rollback{/tr}">b</a>&nbsp;
{/if}
<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;diff={$history[hist].version}" title="{tr}compare with last{/tr}">c</a>&nbsp;
<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;diff2={$history[hist].version}" title="{tr}diff to last{/tr}">d</a>&nbsp;
<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;source={$history[hist].version}" title="{tr}source{/tr}">s</a>&nbsp;
</td>
<td class="{cycle advance=false}">
<input type="radio" name="oldver" value="{$history[hist].version}" title="Select an older version for comparison" {if $oldver == $history[hist].version || $smarty.section.hist.first}checked="checked"{/if} />
</td>
<td class="{cycle advance=false}">
{if $smarty.section.hist.last}&nbsp;{else}<input type="radio" name="newver" value="{$history[hist].version}" title="Select a newer version for comparison" {if $newver == $history[hist].version}checked="checked"{/if} />{/if}
</td>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
</div>
</form>
