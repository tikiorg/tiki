<a class="pagetitle" href="tiki-jukebox_albums.php">{tr}Jukebox Albums{/tr}</a><br /><br />
{if $tiki_p_jukebox_tracks eq 'y'}
<a class="linkbut" href="tiki-jukebox_albums.php?edit_mode=1&amp;albumId=0">{tr}create new album{/tr}</a>
{/if}
{if $tiki_p_jukebox_genres eq 'y'}
 <a class="linkbut" href="tiki-jukebox_genres.php">{tr}Admin Genres{/tr}</a>
{/if}
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=jukebox"><img src='img/icons/config.gif' border='0'  alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" /></a>
{/if}
<br /><br />
{if $tiki_p_create_file_galleries eq 'y'}
{if $edit_mode eq 'y'}
{if $galleryId eq 0}
<h3>{tr}Create a new album{/tr}</h3>
{else}
<h3>{tr}Edit this album:{/tr} {$name}</h3>
<a class="linkbut" href="tiki-jukebox_albums.php?edit_mode=1&amp;galleryId=0">{tr}create new album{/tr}</a>
{/if}
<div  align="center">
<form action="tiki-jukebox_albums.php" method="post">
<input type="hidden" name="albumId" value="{$albumId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Title{/tr}:</td><td class="formcolor"><input type="text" name="title" value="{$title|escape}"/></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Max tracks per page{/tr}:</td><td class="formcolor"><input type="text" name="maxRows" value="{$maxRows|escape}" /></td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Other users can upload tracks to this album{/tr}:</td><td class="formcolor"><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" value="{tr}save{/tr}" name="edit" /></td></tr>
</table>
</form>
</div>
<br />
{if $albumId>0}
{if $edited eq 'y'}
<div class="wikitext">
{tr}You can access this album using the following URL{/tr}: <a class="jukeboxlink" href="{$url}?albumId={$albumId}">{$url}?albumId={$albumId}</a>
</div>
{/if}
{/if}

{/if}
{/if}

<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-jukebox_albums.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="jukeboxlist">
<tr>
{if $jukebox_album_list_title eq 'y'}
	<td class="jukeboxlistheading"><a class="jukeboxlistheading" href="tiki-jukebox_albums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
{/if}
{if $jukebox_album_list_description eq 'y'}
	<td class="jukeboxlistheading">{tr}Description{/tr}</td>
{/if}
{if $jukebox_album_list_created eq 'y'}
	<td class="jukeboxlistheading"><a class="jukeboxlistheading" href="tiki-jukebox_albums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
{/if}
{if $jukebox_album_list_lastmodif eq 'y'}
	<td class="jukeboxlistheading"><a class="jukeboxlistheading" href="tiki-jukebox_albums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a></td>
{/if}
{if $jukebox_album_list_user eq 'y'}
	<td class="jukeboxlistheading"><a class="jukeboxlistheading" href="tiki-jukebox_albums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
{/if}
{if $jukebox_album_list_tracks eq 'y'}
	<td style="text-align:right;"  class="jukeboxlistheading"><a class="jukeboxlistheading" href="tiki-jukebox_albums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'tracks_desc'}tracks_asc{else}tracks_desc{/if}">{tr}Tracks{/tr}</a></td>
{/if}
{if $jukebox_album_list_visits eq 'y'}
	<td style="text-align:right;"  class="jukeboxlistheading"><a class="jukeboxlistheading" href="tiki-jukebox_albums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></td>
{/if}
<td class="jukeboxlistheading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="jukeboxprevnext" href="tiki-jukebox_albums.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="jukeboxprevnext" href="tiki-jukebox_albums.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-jukebox_albums.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

