{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-pagehistory.tpl,v 1.21 2004-08-20 11:26:20 sylvieg Exp $ *}

<a class="pagetitle" href="tiki-pagehistory?page={$page|escape:"url"}" title="{tr}history{/tr}">{tr}History{/tr}</a> {tr}of{/tr}: <a class="pagetitle" href="tiki-index.php?page={$page|escape:"url"}" title="{tr}view{/tr}">{$page}</a><br /><br />
{if $preview}
<h2>{tr}Version{/tr}: {$preview}</h2>
<div  class="wikitext">{$previewd}</div>
{/if}

{if $source}
<h2>{tr}Version{/tr}: {$source}</h2>
<div  class="wikitext">{$sourced}</div>
{/if}

{if $diff_style}
<h2><a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;compare&amp;oldver={$old.version}&amp;newver={$new.version}" title="{tr}compare{/tr}">{tr}Comparing the version {$old.version} with the version {$new.version}{/tr}{if $new.version == $info.version} ({tr}current{/tr}){/if}</a></h2>
<table class="normal diff">
<tr>
  <th colspan="2" align="center"><b>{tr}Version:{/tr} <a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$old.version}" title="{tr}view{/tr}">{$old.version}</a></b></th>
  <th colspan="2" align="center"><b>{tr}Version:{/tr} <a href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$new.version}" title="{tr}view{/tr}">{$new.version}{if $new.version == $info.version} ({tr}current{/tr})</a>{/if}</b></th>
</tr>
<tr>
  <td colspan="2" align="center">{if $tiki_p_wiki_view_author ne 'n'}{$old.user|userlink} - {/if}{$old.lastModif|tiki_short_datetime}</td>
  <td colspan="2" align="center">{if $tiki_p_wiki_view_author ne 'n'}{$new.user|userlink} - {/if}{$new.lastModif|tiki_short_datetime}</td>
</tr>
{if $old.comment || $new.comment}
<tr>
  <td colspan="2" class="editdate" align="center">{if $old.comment}{$old.comment}{else}&nbsp;{/if}</td>
  <td colspan="2" class="editdate" align="center">{if $new.comment}{$new.comment}{else}&nbsp;{/if}</td>
</tr>
{/if}
{if $old.description != $new.description}
<tr>
  <td colspan="2" class="diffdeleted">{if $old.description}{$old.description}{else}&nbsp;{/if}</td>
  <td colspan="2" class="diffadded">{if $new.description}{$new.description}{else}&nbsp;{/if}</td>
</tr>
{/if}
{if $diff_style == "sideview" || $diff_style == "unidiff"}
</table>
{/if}
{/if}

{if $diff_style eq "sideview"}
<table class="normalnoborder">
<tr>
  <td valign="top" ><div class="wikitext">{$old.data}</div></td>
  <td valign="top" ><div class="wikitext">{$new.data}</div></td>
</tr>
</table>
{/if}

{if $diff_style eq 'sidediff' || $diff_style eq 'unidiff' || $diff_style eq 'minsidediff'}
  {if $diffdata}{$diffdata}{else}<tr><td colspan="4" align="center">{tr}Versions are identical{/tr}</td></tr></table>{/if}
{/if}
<br />

{if $preview || $source || $diff_style}<h2>{tr}History{/tr}</h2>{/if}
<form action="tiki-pagehistory.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />
<div align="center">
<div class="simplebox"><b>{tr}Legend:{/tr}</b> {tr}v=view{/tr}, {tr}s=source{/tr}{if $tiki_p_rollback eq 'y'}, {tr}b=rollback{/tr}{/if}</div>
<table border="1" cellpadding="2" cellspacing="0">
<tr>
{if $tiki_p_remove eq 'y'}<th class="heading" rowspan="2" valign="middle"><input type="submit" name="delete" value="{tr}del{/tr}" /></th>{/if}
<th class="heading" rowspan="2" valign="middle">{tr}Date{/tr}</th>
<th class="heading" rowspan="2" valign="middle">{tr}User{/tr}</th>
{if $feature_wiki_history_ip ne 'n'}<th class="heading" rowspan="2" valign="middle">{tr}Ip{/tr}</th>{/if}
<th class="heading" rowspan="2" valign="middle">{tr}Comment{/tr}</th>
<th class="heading" rowspan="2" valign="middle">{tr}Version{/tr}</th>
<th class="heading" rowspan="2" valign="middle">{tr}Action{/tr}</th>
<th class="heading" colspan="2">
<select name="diff_style">
	<option value="minsidediff" {if $diff_style == "minsidediff"}selected="selected"{/if}>{tr}Side-by-side diff{/tr}</option>
	<option value="sidediff" {if $diff_style == "sidediff"}selected="selected"{/if}>{tr}Full side-by-side diff{/tr}</option>
	<option value="unidiff" {if $diff_style == "unidiff"}selected="selected"{/if}>{tr}Unified diff{/tr}</option>
	<option value="sideview" {if $diff_style == "sideview"}selected="selected"{/if}>{tr}Side-by-side view{/tr}</option>
</select><br /><input type="submit" name="compare" value="{tr}compare{/tr}" /><br />
</th>
</tr>
<tr>
<th class="heading">{tr}Old{/tr}</th><th class="heading">{tr}New{/tr}</th>
</tr>
<tr>
{if $tiki_p_remove eq 'y'}
<td class="odd">&nbsp;</td>
{/if}
<td class="odd">{$info.lastModif|tiki_short_datetime}</td>
{if $tiki_p_wiki_view_author ne 'n'}<td class="odd">{$info.user}</td>{/if}
{if $feature_wiki_history_ip ne 'n'}<td class="odd">{$info.ip}</td>{/if}
<td class="odd">{if $info.comment}{$info.comment}{else}&nbsp;{/if}</td>
<td class="odd" align="center">{$info.version}<br />{tr}current{/tr}</td>
<td class="odd" align="center"><a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$info.version}" title="{tr}view{/tr}">v</a>
&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;source={$info.version}" title="{tr}source{/tr}">s</a>
</td>
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
{if $tiki_p_wiki_view_author ne 'n'}<td class="{cycle advance=false}">{$history[hist].user}</td>{/if}
{if $feature_wiki_history_ip ne 'n'}<td class="{cycle advance=false}">{$history[hist].ip}</td>{/if}
<td class="{cycle advance=false}">{if $history[hist].comment}{$history[hist].comment}{else}&nbsp;{/if}</td>
<td class="{cycle advance=false}" align="center">{$history[hist].version}</td>
<td class="{cycle advance=false}" align="center"><a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$history[hist].version}" title="{tr}view{/tr}">v</a>&nbsp;
<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;source={$history[hist].version}" title="{tr}source{/tr}">s</a>
{if $tiki_p_rollback eq 'y'}
&nbsp;<a class="link" href="tiki-rollback.php?page={$page|escape:"url"}&amp;version={$history[hist].version}" title="{tr}rollback{/tr}">b</a>&nbsp;
{/if}
</td>
<td class="{cycle advance=false}" align="center">
<input type="radio" name="oldver" value="{$history[hist].version}" title="Select an older version for comparison" {if $old.version == $history[hist].version || $smarty.section.hist.first}checked="checked"{/if} />
</td>
<td class="{cycle}" align="center">
{if $smarty.section.hist.last}&nbsp;{else}<input type="radio" name="newver" value="{$history[hist].version}" title="Select a newer version for comparison" {if $new.version == $history[hist].version}checked="checked"{/if} />{/if}
</td>
</tr>
{sectionelse}
<tr><td colspan="{if $feature_wiki_history_ip ne 'n'}{if $tiki_p_wiki_view_author ne 'n'}9{else}\{/if}{else}{if $tiki_p_wiki_view_author ne 'n'}8{else}7{/if}{/if}">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
</div>
</form>
