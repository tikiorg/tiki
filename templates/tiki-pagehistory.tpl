{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-pagehistory.tpl,v 1.18 2004-08-12 20:43:31 sylvieg Exp $ *}

<a class="pagetitle" href="tiki-pagehistory?page={$page|escape:"url"}">{tr}History{/tr}</a> {tr}of{/tr}: <a class="pagetitle" href="tiki-index.php?page={$page|escape:"url"}">{$page}</a><br /><br />
{if $preview}
<h2>{tr}Version{/tr}: {$oldver}</h2>
<div  class="wikitext">{$preview.data}</div>
<br /> 
{/if}
{if $source}
<h2>{tr}Version{/tr}: {$oldver}</h2>
<div  class="wikitext">{$sourcev}</div>
{/if}
{if $diff_style eq "sideview"}
<h2>{tr}Comparing the version {$oldver} with the last version{/tr}</h2>
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
{if $diff_style eq 'sidediff' || $diff_style eq 'unidiff' || $diff_style eq 'minsidediff'}
<h2>{if $newver == 0}{tr}Comparing the version {$oldver} with the last version{/tr}{else}{tr}Comparing the version {$oldver} with the version {$newver}{/tr}{/if}</h2>
{$diffdata}
{/if}
<br />
<form action="tiki-pagehistory.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />
<div align="center">
<div class="simplebox"><b>{tr}Legend{/tr}:</b> {tr}v=view, b=rollback, s=source{/tr}</div>
<table border="1" cellpadding="2" cellspacing="0">
<tr>
{if $tiki_p_remove eq 'y'}
<td class="heading"><input type="submit" name="delete" value="{tr}del{/tr}" /></td>
{/if}
<td class="heading">{tr}Date{/tr}</td>
<td class="heading">{tr}Ver{/tr}</td>
<td class="heading">{tr}User{/tr}</td>
{if $feature_wiki_history_ip ne 'n'}<td class="heading">{tr}Ip{/tr}</td>{/if}
<td class="heading">{tr}Comment{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
<td class="heading" colspan="2"><input type="submit" name="compare" value="{tr}compare{/tr}" /><br />
<select name="diff_style">
	<option value="sidediff" {if $diff_style == "sidediff"}selected="selected"{/if}>{tr}Side-by-side diff{/tr}</option>
	<option value="unidiff" {if $diff_style == "unidiff"}selected="selected"{/if}>{tr}Unified diff{/tr}</option>
	<option value="sideview" {if $diff_style == "sideview"}selected="selected"{/if}>{tr}Side-by-side view{/tr}</option>
	<option value="minsidediff" {if $diff_style == "minsidediff"}selected="selected"{/if}>{tr}Compact side-by-side diff{/tr}</option>
</select><br />
{tr}old ver - new ver{/tr}</td>
</tr>
<tr>
{if $tiki_p_remove eq 'y'}
<td class="odd">&nbsp;</td>
{/if}
<td class="odd">{$info.lastModif|tiki_short_datetime}</td>
<td class="odd" align="center">{$info.version}</td>
<td class="odd">{$info.user}</td>
{if $feature_wiki_history_ip ne 'n'}<td class="odd">{$info.ip}</td>{/if}
<td class="odd">{if $info.comment}{$info.comment}{else}&nbsp;{/if}</td>
<td class="odd" align="center"><a class="link" href="tiki-index.php?page={$page|escape:"url"}">{tr}current{/tr}</a></td>
<td class="odd">&nbsp;</td>
<td class="odd" align="center"><input type="radio" name="newver" value="0" title="Select a newer version for comparison" checked="checked" /></td>
</tr>
{cycle values="odd,even" print=false}
{section name=hist loop=$history}
<tr>
{if $tiki_p_remove eq 'y'}
<td class="{cycle advance=false}" align="center"><input type="checkbox" name="hist[{$history[hist].version}]" /></td>
{/if}
<td class="{cycle advance=false}">{$history[hist].lastModif|tiki_short_datetime}</td>
<td class="{cycle advance=false}" align="center">{$history[hist].version}</td>
<td class="{cycle advance=false}">{$history[hist].user}</td>
{if $feature_wiki_history_ip ne 'n'}<td class="{cycle advance=false}">{$history[hist].ip}</td>{/if}
<td class="{cycle advance=false}">{if $history[hist].comment}{$history[hist].comment}{else}&nbsp;{/if}</td>
<td class="{cycle}" align="center"><a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$history[hist].version}" title="{tr}view{/tr}">v</a>&nbsp;
{if $tiki_p_rollback eq 'y'}
<a class="link" href="tiki-rollback.php?page={$page|escape:"url"}&amp;version={$history[hist].version}" title="{tr}rollback{/tr}">b</a>&nbsp;
{/if}
<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;source={$history[hist].version}" title="{tr}source{/tr}">s</a>
</td>
<td class="{cycle advance=false}" align="center">
<input type="radio" name="oldver" value="{$history[hist].version}" title="Select an older version for comparison" {if $oldver == $history[hist].version || $smarty.section.hist.first}checked="checked"{/if} />
</td>
<td class="{cycle advance=false}" align="center">
{if $smarty.section.hist.last}&nbsp;{else}<input type="radio" name="newver" value="{$history[hist].version}" title="Select a newer version for comparison" {if $newver == $history[hist].version}checked="checked"{/if} />{/if}
</td>
</tr>
{sectionelse}
<tr><td colspan="{if $feature_wiki_history_ip ne 'n'}9{else}8{/if}">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
</div>
</form>
