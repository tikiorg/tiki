{if $playlistId ne ''}
{if $playlistId eq "0"}
{title}Create Kaltura Playlist{/title}
<form action="tiki-list_kaltura_playlists.php?playlistId=0" method="get">
<table class="normal">
<tr>
	<td>Playlist Name</td><td><input name="playlist_name" /></td>
</tr>
<tr>
	<td>Playlist Type</td><td><select name="playlist_type" ><option>Manual</option><option>Rule Based</option></select></td>
</tr>
<tr>
	<td>Tags</td><td><input name="playlist_tags"/></td>
</tr>
<tr>
	<td>Description</td><td><input name="playlist_description" /></td>
</tr>
</table>
<br>
<input type="hidden" name="playlistId" value="0">
<input type="submit" name="action" value="Create">
</form>
{else}
{if $action eq 'add_entries'}
{if $type eq "mix"}
{title}Create Kaltura Playlist: Add Mix Entries{/title}
{button _text="{tr}Media Entries{/tr}" href="tiki-list_kaltura_playlists.php?type=media&action=add_entries&playlistId=$playlistId" }
{assign var=klist value=$kmixlist->objects}
{assign var=cant value=$kmixlist->totalCount}

<form name="list" action="tiki-list_kaltura_playlists.php?type=mix&action=add_entries&playlistId={$playlistId}" method="post">
<br>
{include file="tiki-list_kaltura_mix_entries.tpl"}
<br>
<input type="hidden" name="playlistId" value="{$playlistId}">
<input type="hidden" name="action" value="add_entries">
<input type="hidden" name="type" value="mix">
<input type="hidden" name="add" value="true">
<input type="submit" value="Add">
</form>

{else}
{title}Create Kaltura Playlist: Add Media Entries{/title}
{button _text="{tr}Mix Entries{/tr}" href="tiki-list_kaltura_playlists.php?type=mix&action=add_entries&playlistId=$playlistId" }
<br><br>
{assign var=klist value=$kmedialist->objects}
{assign var=cant value=$kmedialist->totalCount}
<form action="tiki-list_kaltura_playlists.php?type=media&action=add_entries&playlistId={$playlistId}" method="post" >
{include file="tiki-list_kaltura_media_entries.tpl"}
<br>
<input type="hidden" name="playlistId" value="{$playlistId}">
<input type="hidden" name="action" value="add_entries">
<input type="hidden" name="type" value="media">
<input type="hidden" name="add" value="true">
<input type="submit" name="add" value="Add">
</form>
{/if}
{else}
{title}Kaltura Playlist{/title}
<table class="noborder">
<tr><td>
<object height="350" width="800" type="application/x-shockwave-flash" data="{$prefs.kServiceUrl}kwidget/cache_st/1255548761/wid/_23929/ui_conf_id/48306" id="kaltura_playlist">		
<param name="allowscriptaccess" value="always"/>
<param name="allownetworking" value="all"/>
<param name="bgcolor" value="#000000"/>
<param name="wmode" value="opaque"/>
<param name="allowfullscreen" value="true"/>
<param name="movie" value="{$prefs.kServiceUrl}kwidget/cache_st/1255548761/wid/_23929/ui_conf_id/48304"/>
<param name="flashvars" value="layoutId=playlistLight&uid=0&partner_id=23929&subp_id=2392900&k_pl_autoContinue=true&k_pl_autoInsertMedia=true&k_pl_0_name={$kplaylist->name}&k_pl_0_url=http%3A%2F%2Fwww.kaltura.com%2Findex.php%2Fpartnerservices2%2Fexecuteplaylist%3Fuid%3D%26partner_id%3D23929%26subp_id%3D2392900%26format%3D8%26ks%3D%7Bks%7D%26playlist_id%3D{$kplaylist->id}"/></object>
</td></tr>
<tr><td>
<br>
<table class="normal">
<tr><td width="200" class="even">Name </td><td class="even">{$kplaylist->name}</td></tr>
<tr><td width="200" class="odd">Description </td><td class="odd">{$kplaylist->description}</td></tr>
<tr><td width="200" class="even">Tags </td><td class="even">{$kplaylist->tags}</td></tr>
<tr><td width="200" class="odd">Duration </td><td class="odd">{$kplaylist->duration}</td></tr>
<tr><td width="200" class="even">Views </td><td class="even">{$kplaylist->views}</td></tr>
<tr><td width="200" class="odd">Plays </td><td class="odd">{$kplaylist->plays}</td></tr>
</table>
</td></tr>
</table>
{/if}
{/if}
{else}
{if $count > 0}
{title}Kaltura Playlists{/title}
<form action="tiki-list_kaltura_playlists.php?list=media" method="get" class="normal">
{button _text="{tr}Mix Entries{/tr}" href="tiki-list_kaltura_entries2.php?list=mix" }
{button _text="{tr}Media Entries{/tr}" href="tiki-list_kaltura_entries2.php?list=media&view=browse" }
<input type="submit" name="action" value="Create Playlist"/> 
<input type="submit" name="action" value="Delete"/>
<br>
<br>
<table class="normal">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="150"><a href="tiki-list_kaltura_playlists.php?list={$entryType}offset={$offset}&amp;sort_mode={if $sort_mode eq '-name'}asc_name{else}desc_name{/if}">{tr}Name{/tr}</a></th>
			<th width="100"><a href="tiki-list_kaltura_playlists.php?list={$entryType}offset={$offset}&amp;sort_mode={if $sort_mode eq '-media_type'}asc_media_type{else}desc_media_type{/if}">{tr}Playlist Type{/tr}</a></th>
			<th width="100"><a href="tiki-list_kaltura_playlists.php?list={$entryType}offset={$offset}&amp;sort_mode={if $sort_mode eq '-created_at'}asc_created_at{else}desc_created_at{/if}">{tr}Created{/tr}</a></th>
			<th><a>{tr}Tags{/tr}</a></th>
			<th width="100"><a>{tr}Version{/tr}</a></th>
			<th width="30"><a href='#'{popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" text=$smarty.capture.other_sorts|escape:"javascript"|escape:"html"} title="{tr}Other Sorts{/tr}">{icon _id='timeline_marker' alt="{tr}Other Sorts{/tr}" title=''}</a></th>
		</tr>		
		{foreach from=$klist key=key item=item}
		{if $item->id ne ''}
		<tr {if ($key % 2)}class="odd"{else}class="even"{/if}>	
			{include file=tiki-list_kaltura_entries_actions.tpl}
			<td><input type="checkbox" name="playlistId[]" value="{$item->id}" /></td>
			<td><a href="tiki-list_kaltura_playlists.php?playlistId={$item->id}">{$item->name}</a></td>
			<td>{$item->playlistType}</td>
			<td>{$item->createdAt}<br/><br/>Created By: {$item->userId}</td>
			<td height="100">{$item->tags}</td>
			<td>{$item->version}</td>
			{include file=tiki-list_kaltura_entries_add_info.tpl}	
			<td><a href="#" {popup trigger="onmouseover" fullhtml="1" text=$smarty.capture.add_info|escape:"javascript"|escape:"html" left=true}>{icon _id='information' class='' title=''}</a></td>
		</tr>
		{/if}
		{/foreach}   
</table>
</form>
{else}
{remarksbox type="info" title="{tr}No entries{/tr}" }
{tr}No playlists found. {/tr}<a href="tiki-kaltura_playlists.php?playlistId=0">{tr}Click here {/tr}</a>{tr}to create a playlist.{/tr}{/remarksbox}
{/if}
{/if}
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
