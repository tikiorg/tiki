{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/map/tiki-map_edit.tpl,v 1.3 2004-01-17 01:23:36 musus Exp $ *}

<a class="pagetitle" href="tiki-map_edit.php?mode=listing">{tr}Mapfiles{/tr}</a><br /><br />
{if $mapfile}<h2>{tr}Mapfile{/tr}: {$mapfile}</h2>{/if}
{if $mode eq 'listing'}
<h3>{tr}Available mapfiles{/tr}:</h3>
<table>
<tr>
<td class="heading">{tr}Mapfile{/tr}</td>
<td class="heading">{tr}Actions{/tr}</td>
</tr>
{section name=user loop=$files}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">
<a href="tiki-map.phtml?mapfile={$files[user]}">{$files[user]}</a>
</td>
<td class="odd">
{if $tiki_p_map_edit eq 'y'}
<a href="tiki-map_edit.php?mapfile={$files[user]}&amp;mode=editing">
<img src="img/icons/config.gif" border="0"  alt="{tr}edit{/tr}" title="{tr}edit{/tr}" />
</a>
{/if}
{if $user and $feature_user_watches eq 'y'}
 {if $user_watching_map[user] eq 'n'}
  	<a href="tiki-map_edit.php?watch_event=map_changed&amp;watch_object={$files[user]}&amp;watch_action=add"><img border='0' alt='{tr}monitor this map{/tr}' title='{tr}monitor this map{/tr}' src='img/icons/icon_watch.png' /></a>
	{else}
		<a href="tiki-map_edit.php?watch_event=map_changed&amp;watch_object={$files[user]}&amp;watch_action=remove"><img border='0' alt='{tr}stop monitoring this map{/tr}' title='{tr}stop monitoring this map{/tr}' src='img/icons/icon_unwatch.png' /></a>
	{/if}
{/if}
</td>
</tr>
{else}
<tr>
<td class="even">
<a href="tiki-map.phtml?mapfile={$files[user]}">{$files[user]}</a>
</td>
<td class="even">
{if $tiki_p_map_edit eq 'y'}
<a href="tiki-map_edit.php?mapfile={$files[user]}&amp;mode=editing">
<img src="img/icons/config.gif" border="0" alt="{tr}edit{/tr}" title="{tr}edit{/tr}" />
</a>
{/if}
{if $user and $feature_user_watches eq 'y'}
 {if $user_watching_map[user] eq 'n'}
  	<a href="tiki-map_edit.php?watch_event=map_changed&amp;watch_object={$files[user]}&amp;watch_action=add"><img border='0' alt='{tr}monitor this map{/tr}' title='{tr}monitor this map{/tr}' src='img/icons/icon_watch.png' /></a>
	{else}
		<a href="tiki-map_edit.php?watch_event=map_changed&amp;watch_object={$files[user]}&amp;watch_action=remove"><img border='0' alt='{tr}stop monitoring this map{/tr}' title='{tr}stop monitoring this map{/tr}' src='img/icons/icon_unwatch.png' /></a>
	{/if}
{/if}	
</td>
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
<a href="tiki-map_edit.php">{tr}Mapfile listing{/tr}</a><br />
<form action="tiki-map_edit.php" method="post">
<textarea name="data" rows="25" cols="80">{$data|escape}</textarea>
<input type="hidden" name="mapfile" value="{$mapfile}" />
<input type="hidden" name="mode" value="{$mode}" />
<div align="center">
<input type="submit" name="save" value="{tr}save{/tr}" /> 
{if $tiki_p_map_delete eq 'y'}
&nbsp&nbsp&nbsp
<input type="submit" name="delete" value="{tr}delete{/tr}" />
{/if}
  <br />
  <table>
  <tr>
  	<td class="even">
  	<small>
    {tr}You can view this map in your browser using{/tr}: <a class="maplink" href="{$url_browse}?mapfile={$mapfile}">{$url_browse}?mapfile={$mapfile}</a><br />
    </small>
    </td>
  </tr>
  </table>
</div>
</form>
{/if}

