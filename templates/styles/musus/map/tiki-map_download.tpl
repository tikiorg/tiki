{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/map/tiki-map_download.tpl,v 1.3 2004-01-17 01:23:36 musus Exp $ *}

<h3>{tr}Download Layer{/tr}</h3>
<b>{$userwatch}</b>, {tr}you have requested to download the layer:{/tr}<b>{$layer}</b> {tr}from
the mapfile:{/tr}<b>{$mapfile}</b><br /><br />
{tr}Here are the files to download, do not forget to rename them:{/tr}<br />
<table >
{section name=j loop=$files}
{if $smarty.section.j.index % 2}
<tr>
<td class="odd">
<a href="files/{$dfiles[j]}">{$files[j]}</a>
</td>
</tr>
{else}
<tr>
<td class="even">
<a href="files/{$dfiles[j]}">{$files[j]}</a>
</td>
</tr>
{/if}
{/section}
</table>
