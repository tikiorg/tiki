<a class="pagetitle" href="tiki-map_upload.php">Layer Management</a><br/><br/>
<h3>{tr}{$dir}{/tr}</h3>
<table border="1" cellpadding="0" cellspacing="0" >
<tr>
<td class="heading" colspan="2">{tr}Directories{/tr}</a></td>
</tr>
{section name=user loop=$dirs}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">
<a class="link" href="tiki-map_upload.php?dir={$dir}/{$dirs[user]}">
{$dirs[user]}
</a>
</td>
<td class="odd">
{if $tiki_p_map_delete}
<a class="link" href="tiki-map_upload.php?dir={$dir}&directory={$dirs[user]}&action=deldir">(x)</a>
{/if}
</td>
</tr>
{else}
<tr>
<td class="even">
<a class="link" href="tiki-map_upload.php?dir={$dir}/{$dirs[user]}">
{$dirs[user]}
</a>
</td>
<td class="odd">
{if $tiki_p_map_delete}
<a class="link" href="tiki-map_upload.php?dir={$dir}&directory={$dirs[user]}&action=deldir">(x)</a>
{/if}
</td>
</tr>
{/if}
{/section}
<td class="heading" colspan="2">{tr}Files{/tr}</a></td>
{section name=user loop=$files}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">
{$files[user]}
</td>
<td class="odd">
{if $tiki_p_map_delete}
<a class="link" href="tiki-map_upload.php?dir={$dir}&file={$files[user]}&action=delete">(x)</a>
{/if}
</td>
</tr>
{else}
<tr>
<td class="even">
{$files[user]}
</td>
<td class="even">
{if $tiki_p_map_delete}
<a class="link" href="tiki-map_upload.php?dir={$dir}&file={$files[user]}&action=delete">(x)</a>
{/if}
</td>
</tr>
{/if}
{/section}
</table>
<br>
{if $tiki_p_map_create}
<form enctype="multipart/form-data" action="tiki-map_upload.php" method="post">
<input type="hidden" name="dir" value="{$dir}">
<input type="hidden" name="upload" value="true">
Send this file: <input name="userfile" type="file">
<input type="submit" value="Upload File"> ({$max_file_size}Bytes maximum)
</form>
<br>
<form action="tiki-map_upload.php" method="get">
<input type="hidden" name="dir" value="{$dir}">
<input type="hidden" name="action" value="createdir">
Create Directory: <input name="directory" type="text">
<input type="submit" value="Create">
</form>
{/if}