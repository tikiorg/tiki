<a class="pagetitle" href="tiki-map_edit.php?mode=listing">{tr}Edit mapfiles{/tr}</a><br/><br/>
{if $mapfile}<h2>{tr}Mapfile{/tr}: {$mapfile}</h2>{/if}
{if $mode eq 'listing'}
<h3>{tr}Available mapfiles{/tr}:</h3>
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr>
<td class="heading">{tr}Mapfile{/tr}</a></td>
</tr>
{section name=user loop=$files}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd"><a class="link" href="tiki-map_edit.php?mapfile={$files[user]}">{$files[user]}</a></td>
</tr>
{else}
<tr>
<td class="even"><a class="link" href="tiki-map_edit.php?mapfile={$files[user]}">{$files[user]}</a></td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="2" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
{if $tiki_p_map_create eq 'y'}
<h3>{tr}Create a new mapfile{/tr}</h3>
<form action="tiki-map_edit.php" method="post">
<input type="text" name="newmapfile" size="20">
<input type="submit" name="create" value="{tr}create{/tr}" />
{/if}
{/if}
{if $mode eq 'editing'}
<a class="link" href="tiki-map_edit.php">{tr}Mapfile listing{/tr}</a><br/>
<form action="tiki-map_edit.php" method="post">
<textarea name="data" rows="25" cols="80">{$data|escape}</textarea>
<input type="hidden" name="mapfile" value="{$mapfile}" />
<div align="center">
<input type="submit" name="save" value="{tr}save{/tr}" /> &nbsp&nbsp&nbsp
<input type="submit" name="delete" value="{tr}delete{/tr}" />
</div>
</form>
{/if}
