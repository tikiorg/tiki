<h3>{tr}Download Layer{/tr}</h3>
{$userwatch}, {tr}you have requested to download the layer:{/tr}{$layer} {tr}from
the mapfile:{/tr}{$mapfile}<br><br>
{tr}Here are the files to download, do not forget to rename them:{/tr}<br>
<table border="1" cellpadding="0" cellspacing="0" >
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
