{* $Header: /cvsroot/tikiwiki/tiki/templates/map/tiki-map_download.tpl,v 1.9 2005-04-02 17:43:38 michael_davey Exp $ *}

<h3>{tr}Download Layer{/tr}</h3>
{if $nodownload}
{tr}<b>This layer has been set by the maps administrator to not be downloadable</b>{/tr}
{else}
<b>{$userwatch}</b>, {tr}you have requested to download the layer:{/tr}<b>{$layer}</b> {tr}from
the mapfile:{/tr}<b>{$mapfile}</b><br /><br />
{tr}Here are the files to download, do not forget to rename them:{/tr}<br />
<table class="normal" >
{section name=j loop=$files}
{if $smarty.section.j.index % 2}
<tr>
<td class="odd">
<a class="link" href="files/{$dfiles[j]}">{$files[j]}</a>
</td>
</tr>
{else}
<tr>
<td class="even">
<a class="link" href="files/{$dfiles[j]}">{$files[j]}</a>
</td>
</tr>
{/if}
{/section}
</table>
{/if}