<a class="pagetitle" href="tiki-map_history.php?mapfile={$mapfile}">{tr}Mapfile History{/tr}</a> {tr}of{/tr}<a class="pagetitle" href="tiki-map_edit.php?mapfile={$mapfile}&mode=editing"> {$mapfile}</a><br />
{if $preview}
<h2>{tr}Version{/tr}: {$preview}</h2>
<div  class="wikitext">{$previewd}</div>
{/if}

{if $diff_style}
<h2><a href="tiki-map_history.php?mapfile={$mapfile}" title="{tr}Compare{/tr}">{tr}Comparing version {$old.version} with version {$new.version}{/tr}</a></h2>
<table class="normal diff">
<tr>
  <th colspan="2"><b>{tr}Version:{/tr} {$old.version}{if $old.version == $info.version} ({tr}Current{/tr}){/if}</b></th>
  <th colspan="2"><b>{tr}Version:{/tr} {$new.version}{if $new.version == $info.version} ({tr}Current{/tr}){/if}</b></th>
</tr>
<tr>
  <td colspan="2">{$old.user|userlink} - {$old.lastModif|tiki_short_datetime}</td>
  <td colspan="2">{$new.user|userlink} - {$new.lastModif|tiki_short_datetime}</td>
</tr>
{if $old.comment || $new.comment}
<tr>
  <td colspan="2" class="editdate">{if $old.comment}{$old.comment}{else}&nbsp;{/if}</td>
  <td colspan="2" class="editdate">{if $new.comment}{$new.comment}{else}&nbsp;{/if}</td>
</tr>
{/if}
{if $old.description != $new.description}
<tr>
  <td colspan="2" class="diffdeleted">{if $old.description}{$old.description}{else}&nbsp;{/if}</td>
  <td colspan="2" class="diffadded">{if $new.description}{$new.description}{else}&nbsp;{/if}</td>
</tr>
{/if}
{/if}

{if $diff_style eq "sideview"}
<tr>
  <td colspan="2" valign="top" ><div class="wikitext">{$old.data}</div></td>
  <td colspan="2" valign="top" ><div class="wikitext">{$new.data}</div></td>
</tr>
</table>
{/if}

{if $diff_style eq 'unidiff'}
 <tr><td colspan="4">
 {if $diffdata}
   {section name=ix loop=$diffdata}
      {if $diffdata[ix].type == "diffheader"}
		{assign var="oldd" value=$diffdata[ix].old}
		{assign var="newd" value=$diffdata[ix].new}
           <br /><div class="diffheader">@@ {tr}-Lines: {$oldd} changed to +Lines: {$newd}{/tr} @@</div>
      {elseif $diffdata[ix].type == "diffdeleted"}
		<div class="diffdeleted">
			{section name=iy loop=$diffdata[ix].data}
				{if not $smarty.section.iy.first}<br />{/if}
				- {$diffdata[ix].data[iy]}
			{/section}
            </div>
      {elseif $diffdata[ix].type == "diffadded"}
            <div class="diffadded">
			{section name=iy loop=$diffdata[ix].data}
				{if not $smarty.section.iy.first}<br />{/if}
				+ {$diffdata[ix].data[iy]}
			{/section}
		</div>
      {elseif $diffdata[ix].type == "diffbody"}
            <div class="diffbody">
			{section name=iy loop=$diffdata[ix].data}
				{if not $smarty.section.iy.first}<br />{/if}
				{$diffdata[ix].data[iy]}
			{/section}
		</div>
      {/if}
   {/section}
 {else}
 <div class="diffheader">{tr}Versions are identical{/tr}</div>
 {/if}
</td></tr>
</table>
{/if}

{if $diff_style eq 'sidediff' || $diff_style eq 'minsidediff'}
  {if $diffdata}{$diffdata}{else}{tr}Versions are identical{/tr}</td></tr></table>{/if}
{/if}
<br />

{if $preview || $diff_style}<h2>{tr}History{/tr}</h2>{/if}
<form action="tiki-map_history.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />
<div style="text-align:center;">
<div class="simplebox"><b>{tr}Legend:{/tr}</b> {tr}v=view{/tr} {if $prefs.default_wiki_diff_style eq "old"}, {tr}c=compare{/tr}, {tr}d=diff{/tr}{/if}</div>
{if $prefs.default_wiki_diff_style ne "old" and $history}
<div style=" text-align:right;"><select name="diff_style">
	<option value="minsidediff" {if $diff_style == "minsidediff"}selected="selected"{/if}>{tr}Side-by-side diff{/tr}</option>
	<option value="sidediff" {if $diff_style == "sidediff"}selected="selected"{/if}>{tr}Full side-by-side diff{/tr}</option>
	<option value="unidiff" {if $diff_style == "unidiff"}selected="selected"{/if}>{tr}Unified diff{/tr}</option>
	<option value="sideview" {if $diff_style == "sideview"}selected="selected"{/if}>{tr}Side-by-side view{/tr}</option>
</select>
</div>
{/if}

<div>
<table border="1" cellpadding="2" cellspacing="0">
<tr>
<th class="heading">{tr}Date{/tr}</th>
<th class="heading">{tr}User{/tr}</th>
<th class="heading">{tr}Ip{/tr}</th>
<th class="heading">{tr}Version{/tr}</th>
<th class="heading">{tr}Action{/tr}</th>
{if $prefs.default_wiki_diff_style != "old" and $history}
<th class="heading" colspan="2">
<input type="submit" name="compare" value="{tr}Compare{/tr}" /><br />
</th>
{/if}
</tr>
{cycle values="odd,even" print=false}
{section name=hist loop=$history step=-1}
<tr>
<td class="{cycle advance=false}">{$history[hist].lastModif|tiki_short_datetime}</td>
<td class="{cycle advance=false}">{$history[hist].user}</td>
<td class="{cycle advance=false}">{$history[hist].ip}</td>
<td class="{cycle advance=false} button">{$history[hist].version}</td>
<td class="{cycle advance=false} button">
&nbsp;<a class="link" href="tiki-map_history.php?mapfile={$mapfile}&amp;preview={$history[hist].version}" title="{tr}View{/tr}">v</a>
{if $prefs.default_wiki_diff_style eq "old"}
&nbsp;<a class="link" href="tiki-map_history.php?mapfile={$mapfile}&amp;diff2={$history[hist].version}&amp;diff_style=sideview" title="{tr}Compare{/tr}">c</a>
&nbsp;<a class="link" href="tiki-map_history.php?mapfile={$mapfile}&amp;diff2={$history[hist].version}&amp;diff_style=unidiff" title="{tr}Diff{/tr}">d</a>
{/if}
&nbsp;
</td>
{if $prefs.default_wiki_diff_style ne "old"}
<td class="{cycle advance=false} button">
<input type="radio" name="oldver" value="{$history[hist].version}" title="{tr}Older Version{/tr}" {if $old.version == $history[hist].version or (!$diff_style and $smarty.section.hist.first)}checked="checked"{/if} />
</td>
<td class="{cycle} button">
{* if $smarty.section.hist.last &nbsp; *}
<input type="radio" name="newver" value="{$history[hist].version}" title="Select a newer version for comparison" {if $new.version == $history[hist].version}checked="checked"{/if} />
</td>
{/if}
</tr>
{/section}
</table>
</div>