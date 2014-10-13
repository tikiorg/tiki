{* $Id$ *}

<a class="pagetitle" href="tiki-map_edit.php?mode=listing">{tr}Mapfiles{/tr}</a><br>
<a href="http://www.mapserver.org/mapfile/reference.html">http://www.mapserver.org/mapfile/reference.html</a><br><br>
{if $mapfile}<h2>{tr}Mapfile:{/tr} {$mapfile}</h2>{/if}
{if $mode eq 'listing'}
<h3>{tr}Available mapfiles:{/tr}</h3>
<div class="table-responsive">
<table class="table normal">
<tr>
<th>{tr}Mapfile{/tr}</th>
<th style="width:20%">{tr}Actions{/tr}</th>
<th style="width:10%">{tr}Hits{/tr}</th>
<th style="width:10%">{tr}hits last 7 days{/tr}</th>
</tr>
{section name=user loop=$files}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">
<a class="link" href="tiki-map.php?mapfile={$files[user]}">{$files[user]}</a>
</td>
<td class="odd">
{if $tiki_p_map_edit eq 'y'}
<a class="link" href="tiki-map_edit.php?mapfile={$files[user]}&amp;mode=editing">
<img src="img/icons/wrench.png" alt="{tr}Edit{/tr}" title="{tr}Edit{/tr}" width='16' height='16'>
</a>
{/if}
{if $user and $prefs.feature_user_watches eq 'y'}
 {if $user_watching_map[user] eq 'n'}
  	<a href="tiki-map_edit.php?watch_event=map_changed&amp;watch_object={$files[user]}&amp;watch_action=add" class="icon" title="{tr}monitor this map{/tr}">{icon name="watch"}</a>
	{else}
		<a class="icon" href="tiki-map_edit.php?watch_event=map_changed&amp;watch_object={$files[user]}&amp;watch_action=remove" title="{tr}Stop Monitoring this Map{/tr}">{icon name="stop-watching"}</a>
	{/if}
{/if}
</td>
<td class="odd">
{$mapstats[user]}
</td>
<td class="odd">
{$mapstats7days[user]}
</td>
</tr>
{else}
<tr>
<td class="even">
<a class="link" href="tiki-map.php?mapfile={$files[user]}">{$files[user]}</a>
</td>
<td class="even">
{if $tiki_p_map_edit eq 'y'}
<a class="link" href="tiki-map_edit.php?mapfile={$files[user]}&amp;mode=editing">
<img src="img/icons/wrench.png" alt="{tr}Edit{/tr}" title="{tr}Edit{/tr}" width='16' height='16'>
</a>
{/if}
{if $user and $prefs.feature_user_watches eq 'y'}
 {if $user_watching_map[user] eq 'n'}
  	<a class="icon" href="tiki-map_edit.php?watch_event=map_changed&amp;watch_object={$files[user]}&amp;watch_action=add" title="{tr}Monitor this map{/tr}">{icon name="watch"}</a>
	{else}
		<a class="icon" href="tiki-map_edit.php?watch_event=map_changed&amp;watch_object={$files[user]}&amp;watch_action=remove" title="{tr}Stop monitoring this map{/tr}">{icon name="stop-watching"}</a>
	{/if}
{/if}	
</td>
<td class="even">
{$mapstats[user]}
</td>
<td class="even">
{$mapstats7days[user]}
</td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="2" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
</div>
{if $tiki_p_map_create eq 'y'}
<h3>{tr}Create a new mapfile{/tr}</h3>
<form action="tiki-map_edit.php" method="post">
<input type="text" name="newmapfile" size="20">
<input type="submit" name="create" class="btn btn-default" value="{tr}Create{/tr}">
{/if}
{/if}
{if $mode eq 'editing'}
<a class="link" href="tiki-map_edit.php">{tr}Mapfile listing{/tr}</a><br><br>
<form enctype="multipart/form-data" action="tiki-map_edit.php" method="post" id='editpageform'>
<a class="link" href="tiki-map_edit.php?mapfile={$mapfile}&mode=editing">{tr}Reload{/tr}</a>&nbsp;&nbsp;
<a class="link" href="tiki-map_history.php?mapfile={$mapfile}">{tr}History{/tr}</a><br>

<div class="table-responsive">
<table class="table normal">
<tr class="formcolor">
<td>
<div id='edit-zone'>
	<div id='textarea-toolbar' style='padding:3px; font-size:10px;'>
		{toolbars area_id='mapdata'}
	</div>
	<textarea id='mapdata' class='wikiedit' name='pagedata' rows='20' wrap='virtual' style='width:99%'>{$pagedata|escape}</textarea>
	<input type="hidden" name="mapfile" value="{$mapfile}">
	<input type="hidden" name="mode" value="{$mode}">
</div>
</td>
</tr>
</table>
</div>

<div align="center">
<input type="submit" class="wikiaction btn btn-default" name="save" value="{tr}Save{/tr}">
</div>
</form>



{if $tiki_p_map_delete eq 'y'}
<hr>
<div align="center">
<form class="wikiaction" action="tiki-map_edit.php" method="get" id='editpageform'>
<input type="hidden" name="mapfile" value="{$mapfile}">
<input type="submit" name="delete" class="btn btn-default" value="{tr}Delete{/tr}">
</form>
</div>
{/if}
  <br>
  <div align="center table-responsive">
  <table class="table normal">
  <tr>
  	<td class="even">
  	<small>
    {tr}You can view this map in your browser using:{/tr} <a class="maplink" href="{$url_browse}?mapfile={$mapfile}">{$url_browse}?mapfile={$mapfile}</a><br>
    </small>
    </td>
  </tr>
  </table>
</div>
{/if}

