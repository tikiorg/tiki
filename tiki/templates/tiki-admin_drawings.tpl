<a class="pagetitle" href="tiki-admin_drawings.php">Edit drawings & pictures</a><br/><br/>

<h3>{tr}Available drawings{/tr}:</h3>
<table class="normal">
<tr>
<td class="heading">{tr}Thumbnail{/tr}</a></td>
<td class="heading">{tr}Name{/tr}</a></td>
</tr>
{section name=user loop=$files}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">
<a href='#' onClick="javascript:window.open('tiki-editdrawing.php?path={$path}&amp;drawing={$files[user]}','','menubar=no,width=252,height=25');"><img width='154' height='98' border='0' src='img/wiki/{$files[user]}.gif' alt='click to edit' /></a>
</td>
<td class="odd">{$files[user]}
[<a href="tiki-admin_drawings.php?remove={$files[user]}" class="link">{tr}x{/tr}</a>]
</td>
</tr>
{else}
<tr>
<td class="even">
<a href='#' onClick="javascript:window.open('tiki-editdrawing.php?path={$path}&amp;drawing={$files[user]}','','menubar=no,width=252,height=25');"><img width='154' height='98' border='0' src='img/wiki/{$files[user]}.gif' alt='click to edit' /></a>
</td>
<td class="even">{$files[user]}
[<a href="tiki-admin_drawings.php?remove={$files[user]}" class="link">{tr}x{/tr}</a>]
</td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="2" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>

