{* $Header: /cvsroot/tikiwiki/tiki/templates/map/tiki-map_upload.tpl,v 1.7 2003-11-23 22:27:35 gongo Exp $ *}

<a class="pagetitle" href="tiki-map_upload.php">Layer Management</a><br/><br/>
<h3>{tr}{$dir}{/tr}</h3>
<table class="normal">
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
{if $tiki_p_map_delete eq 'y'}
<a class="link" href="tiki-map_upload.php?dir={$dir}&amp;directory={$dirs[user]}&amp;action=deldir"
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this directory?{/tr}')">
<img src='img/icons2/delete.gif' border='0' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' />
</a>
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
<td class="even">
{if $tiki_p_map_delete eq 'y'}
<a class="link" href="tiki-map_upload.php?dir={$dir}&amp;directory={$dirs[user]}&amp;action=deldir"
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this directory?{/tr}')">
<img src='img/icons2/delete.gif' border='0' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' />
</a>
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
{if $tiki_p_map_delete eq 'y'}
<a class="link" href="tiki-map_upload.php?dir={$dir}&amp;file={$files[user]}&amp;action=delete"
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this file?{/tr}')">
<img src='img/icons2/delete.gif' border='0' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' />
</a>
{/if}
</td>
</tr>
{else}
<tr>
<td class="even">
{$files[user]}
</td>
<td class="even">
{if $tiki_p_map_delete eq 'y'}
<a class="link" href="tiki-map_upload.php?dir={$dir}&amp;file={$files[user]}&amp;action=delete"
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this file?{/tr}')">
<img src='img/icons2/delete.gif' border='0' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' />
</a>
{/if}
</td>
</tr>
{/if}
{/section}
</table>
<br/>
{if $tiki_p_map_create eq 'y'}
<form enctype="multipart/form-data" action="tiki-map_upload.php" method="post">
<input type="hidden" name="dir" value="{$dir}">
<input type="hidden" name="upload" value="true">
{tr}Upload From Disk:{/tr}<br/>
<input name="userfile1" type="file">
<input name="userfile2" type="file">
<br/>
<input name="userfile3" type="file">
<input name="userfile4" type="file">
<br/>
<input name="userfile5" type="file">
<input name="userfile6" type="file">
<br/>
<input type="submit" value="{tr}Upload Files{/tr}"> ({$max_file_size}{tr}Bytes maximum{/tr})
</form>
<br/>
<form action="tiki-map_upload.php" method="get">
<input type="hidden" name="dir" value="{$dir}">
<input type="hidden" name="action" value="createdir">
{tr}Create Directory:{/tr} <input name="directory" type="text">
<input type="submit" value="{tr}Create{/tr}">
</form>
<br/>
<form action="tiki-map_upload.php" method="get">
<input type="hidden" name="dir" value="{$dir}">
<input type="hidden" name="action" value="createindex">
{tr}index file (.shp):{/tr} <input name="indexfile" type="text">
{tr}files to index (regexp):{/tr} <input name="filestoindex" type="text">
<input type="submit" value="{tr}Create{/tr}">
</form>
{/if}