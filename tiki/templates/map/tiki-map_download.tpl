<div align="center">
<br>
{$userwatch}, you have requested to download the layer:{$layer} from
the mapfile:{$mapfile}<br>
Here are the files to download, do not forget to rename them<br>
{section name=j loop=$files}
<a href="files/{$dfiles[j]}">{$files[j]}</a><br>
{/section}
</div>