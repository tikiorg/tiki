{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-pagehistory.tpl,v 1.37.2.4 2008-01-21 17:08:06 lphuberdeau Exp $ *}

<h1><a class="pagetitle" href="tiki-pagehistory.php?page={$page|escape:"url"}{if $preview}&amp;preview={$preview}{elseif $source}&amp;source={$source}{elseif $diff_style}&amp;compare=1&amp;oldver={$old.version}&amp;newver={$new.version}&amp;diff_style={$diff_style}{/if}" title="{tr}History{/tr}">{tr}History{/tr}: {$page}</a></h1>

<div class="navbar"><a href="tiki-index.php?page={$page|escape:url}" class="linkbut" title="{tr}View{/tr}">{tr}View page{/tr}</a></div>

{if $preview}
<h2>{tr}Preview of version{/tr}: {$preview}
{if $info.version eq $preview}<small><small>{tr}(current){/tr}</small></small>{/if}
</h2>
{if $info.version ne $preview and  $tiki_p_rollback eq 'y'}
<div class="navbar"><a class="linkbut" href="tiki-rollback.php?page={$page|escape:"url"}&amp;version={$preview}" title="{tr}rollback{/tr}">{tr}rollback to this version{/tr}</a></div>
{/if}
<div  class="wikitext">{$previewd}</div>
{/if}

{if $source}
<h2>{tr}Source of version{/tr}: {$source}
{if $info.version eq $source}<small><small>{tr}(current){/tr}</small></small>{/if}
</h2>
{if $info.version ne $source and $tiki_p_rollback eq 'y'}
<div class="navbar"><a class="linkbut" href="tiki-rollback.php?page={$page|escape:"url"}&amp;version={$source}" title="{tr}rollback{/tr}">{tr}rollback to this version{/tr}</a></div>
{/if}
<div  class="wikitext">{$sourced}</div>
{/if}

{include file=pagehistory.tpl}

<br />

{if (!isset($noHistory))}                                              
{if $preview || $source || $diff_style}<h2>{tr}History{/tr}</h2>{/if}
<form action="tiki-pagehistory.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />
<div style="text-align:center;">
<div class="simplebox"><b>{tr}Legend:{/tr}</b> {tr}v=view{/tr}{if $tiki_p_wiki_view_source eq "y" and $prefs.feature_source eq "y"}, {tr}s=source{/tr} {/if}{if $prefs.default_wiki_diff_style eq "old"}, {tr}c=compare{/tr}, {tr}d=diff{/tr}{/if}{if $tiki_p_rollback eq 'y'}, {tr}b=rollback{/tr}{/if}</div>
{if $prefs.default_wiki_diff_style ne "old"}
<div style=" text-align:right;"><select name="diff_style">
	<option value="htmldiff" {if $diff_style == "htmldiff"}selected="selected"{/if}>{tr}HTML diff{/tr}</option>
	<option value="sidediff" {if $diff_style == "sidediff"}selected="selected"{/if}>{tr}Side-by-side diff{/tr}</option>
	<option value="sidediff-char" {if $diff_style == "sidediff-char"}selected="selected"{/if}>{tr}Side-by-side diff by characters{/tr}</option>
	<option value="inlinediff" {if $diff_style == "inlinediff"}selected="selected"{/if}>{tr}Inline diff{/tr}</option>
	<option value="inlinediff-char" {if $diff_style == "inlinediff-char"}selected="selected"{/if}>{tr}Inline diff by characters{/tr}</option>
	<option value="sidediff-full" {if $diff_style == "sidediff-full"}selected="selected"{/if}>{tr}Full side-by-side diff{/tr}</option>
	<option value="sidediff-full-char" {if $diff_style == "sidediff-full-char"}selected="selected"{/if}>{tr}Full side-by-side diff by characters{/tr}</option>
	<option value="inlinediff-full" {if $diff_style == "inlinediff-full"}selected="selected"{/if}>{tr}Full inline diff{/tr}</option>
	<option value="inlinediff-full-char" {if $diff_style == "inlinediff-full-char"}selected="selected"{/if}>{tr}Full inline diff by characters{/tr}</option>
	<option value="unidiff" {if $diff_style == "unidiff"}selected="selected"{/if}>{tr}Unified diff{/tr}</option>
	<option value="sideview" {if $diff_style == "sideview"}selected="selected"{/if}>{tr}Side-by-side view{/tr}</option>
</select>
</div>
{/if}

<table border="1" cellpadding="2" cellspacing="0">
<tr>
{if $tiki_p_remove eq 'y'}<th class="heading"><input type="submit" name="delete" value="{tr}del{/tr}" /></th>{/if}
<th class="heading">{tr}Date{/tr}</th>
{if $tiki_p_wiki_view_author ne 'n'}<th class="heading">{tr}User{/tr}</th>{/if}
{if $prefs.feature_wiki_history_ip ne 'n'}<th class="heading">{tr}Ip{/tr}</th>{/if}
<th class="heading">{tr}Comment{/tr}</th>
{if $prefs.feature_contribution eq 'y'}<th class="heading">{tr}Contribution{/tr}</th>{/if}
{if $prefs.feature_contribution eq 'y' and $prefs.feature_contributor_wiki eq 'y'}<th class="heading">{tr}Contributors{/tr}</th>{/if}
<th class="heading">{tr}Version{/tr}</th>
<th class="heading">{tr}Action{/tr}</th>
{if $prefs.default_wiki_diff_style != "old" and $history}
<th class="heading" colspan="2">
<input type="submit" name="compare" value="{tr}compare{/tr}" /><br />
</th>
{/if}
</tr>
<tr>
{if $tiki_p_remove eq 'y'}
<td class="odd">&nbsp;</td>
{/if}
<td class="odd">{$info.lastModif|tiki_short_datetime}</td>
{if $tiki_p_wiki_view_author ne 'n'}<td class="odd">{$info.user}</td>{/if}
{if $prefs.feature_wiki_history_ip ne 'n'}<td class="odd">{$info.ip}</td>{/if}
<td class="odd">{if $info.comment}{$info.comment}{else}&nbsp;{/if}</td>
{if $prefs.feature_contribution eq 'y'}<td class="odd">{section name=ix loop=$contributions}{if !$smarty.section.ix.first},{/if}{$contributions[ix].name|escape}{/section}</td>{/if}
{if $prefs.feature_contribution eq 'y' and $prefs.feature_contributor_wiki eq 'y'}<td class="odd">{section name=ix loop=$contributors}{if !$smarty.section.ix.first},{/if}{$contributors[ix].login|username}{/section}</td>{/if}
<td class="odd button">{$info.version}<br />{tr}current{/tr}</td>
<td class="odd button">&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$info.version}" title="{tr}View{/tr}">v</a>
{if $tiki_p_wiki_view_source eq "y" and $prefs.feature_source eq "y"}
&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;source={$info.version}" title="{tr}Source{/tr}">s</a>
{/if}
</td>
{if $prefs.default_wiki_diff_style ne "old" and $history}
<td class="odd button"><input type="radio" name="oldver" value="0" title="{tr}compare{/tr}" {if $old.version == $info.version}checked="checked"{/if} /></td>
<td class="odd button"><input type="radio" name="newver" value="0" title="{tr}compare{/tr}" {if $new.version == $info.version or !$diff_style}checked="checked"{/if}  /></td>
{/if}
</tr>
{cycle values="odd,even" print=false}
{section name=hist loop=$history}
<tr>
{if $tiki_p_remove eq 'y'}
<td class="{cycle advance=false} button"><input type="checkbox" name="hist[{$history[hist].version}]" /></td>
{/if}
<td class="{cycle advance=false}">{$history[hist].lastModif|tiki_short_datetime}</td>
{if $tiki_p_wiki_view_author ne 'n'}<td class="{cycle advance=false}">{$history[hist].user}</td>{/if}
{if $prefs.feature_wiki_history_ip ne 'n'}<td class="{cycle advance=false}">{$history[hist].ip}</td>{/if}
<td class="{cycle advance=false}">{if $history[hist].comment}{$history[hist].comment}{else}&nbsp;{/if}</td>
{if $prefs.feature_contribution eq 'y'}<td class="{cycle advance=false}">{section name=ix loop=$history[hist].contributions}{if !$smarty.section.ix.first}&nbsp;{/if}{$history[hist].contributions[ix].name|escape}{/section}</td>{/if}
{if $prefs.feature_contribution eq 'y' and $prefs.feature_contributor_wiki eq 'y'}<td class="{cycle advance=false}">{section name=ix loop=$history[hist].contributors}{if !$smarty.section.ix.first},{/if}{$history[hist].contributors[ix].login|username}{/section}</td>{/if}
<td class="{cycle advance=false} button">{$history[hist].version}</td>
<td class="{cycle advance=false} button">
&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;preview={$history[hist].version}" title="{tr}View{/tr}">v</a>
{if $tiki_p_wiki_view_source eq "y" and $prefs.feature_source eq "y"}
&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;source={$history[hist].version}" title="{tr}Source{/tr}">s</a>
{/if}
{if $prefs.default_wiki_diff_style eq "old"}
&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;diff2={$history[hist].version}&amp;diff_style=sideview" title="{tr}compare{/tr}">c</a>
&nbsp;<a class="link" href="tiki-pagehistory.php?page={$page|escape:"url"}&amp;diff2={$history[hist].version}&amp;diff_style=unidiff" title="{tr}diff{/tr}">d</a>
{/if}
{if $tiki_p_rollback eq 'y' && $lock neq true}
&nbsp;<a class="link" href="tiki-rollback.php?page={$page|escape:"url"}&amp;version={$history[hist].version}" title="{tr}rollback{/tr}">b</a>
{/if}
&nbsp;
</td>
{if $prefs.default_wiki_diff_style ne "old"}
<td class="{cycle advance=false} button">
<input type="radio" name="oldver" value="{$history[hist].version}" title="{tr}older version{/tr}" {if $old.version == $history[hist].version or (!$diff_style and $smarty.section.hist.first)}checked="checked"{/if} />
</td>
<td class="{cycle} button">
{* if $smarty.section.hist.last &nbsp; *}
<input type="radio" name="newver" value="{$history[hist].version}" title="Select a newer version for comparison" {if $new.version == $history[hist].version}checked="checked"{/if} />
</td>
{/if}
</tr>
{/section}
{if $prefs.feature_multilingual eq 'y'}
<tr>
	<td colspan="9" class="right">
		<select name="tra_lang">
			{section name=ix loop=$languages}
			<option value="{$languages[ix].value|escape}"{if $lang eq $languages[ix].value} selected="selected"{/if}>{$languages[ix].name}</option>
			{/section}
		</select>
		<input type="submit" name="update_translation" value="{tr}Update Translation{/tr}"/>
	</td>
</tr>
{/if}
</table>
</div>
</form>
{/if}

{include file=tiki-page_bar.tpl}

