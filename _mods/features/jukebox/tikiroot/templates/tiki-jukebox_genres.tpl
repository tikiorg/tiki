<a class="pagetitle" href="tiki-jukebox_genres.php">{tr}Jukebox Genres{/tr}</a><br /><br />
{if $tiki_p_jukebox_genres eq 'y'}
<a class="linkbut" href="tiki-jukebox_genres.php?edit_mode=1&amp;genreId=0">{tr}create new genre{/tr}</a>
{/if}
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=jukebox" title="{tr}configure listing{/tr}"><img src='img/icons/config.gif' border='0'  alt="{tr}configure listing{/tr}" /></a>
{/if}
<br /><br />
{if $tiki_p_jukebox_genres eq 'y'}
{if $edit_mode eq 'y'}
{if $genreId eq 0}
<h3>{tr}Create a new genre{/tr}</h3>
{else}
<h3>{tr}Edit this genre:{/tr} {$name}</h3>
<a class="linkbut" href="tiki-jukebox_genres.php?edit_mode=1&amp;genreId=0">{tr}create new genre{/tr}</a>
{/if}
<div  align="center">
<form action="tiki-jukebox_genres.php" method="post">
<input type="hidden" name="genreId" value="{$genreId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Title{/tr}:</td><td class="formcolor"><input type="text" name="title" value="{$title|escape}"/></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" value="{tr}save{/tr}" name="edit" /></td></tr>
</table>
</form>
</div>
<br />

{/if}
{/if}

<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-jukebox_genres.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="jukeboxlist">
<tr>
	<td class="jukeboxlistheading"><a class="jukeboxlistheading" href="tiki-jukebox_genres.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'genreName_desc'}genreName_asc{else}genreName_desc{/if}">{tr}Title{/tr} <img border="0" src="img/icons2/{if $sort_mode eq 'genreName_desc'}down{else}up{/if}_active.gif" alt="{tr}sort order{/tr}" /></a></td>
	<td class="jukeboxlistheading">{tr}Description{/tr}</td>
	<td class="jukeboxlistheading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listgenres}
<tr>
	<td class="jukeboxlisttitle{cycle advance=false}">{$listgenres[changes].genreName|escape}</td>
	<td class="jukeboxlistdescription{cycle advance=false}">{$listgenres[changes].genreDescription|escape}</td>
	<td class="jukeboxlistaction{cycle advance=false}">
	<a title="{tr}edit{/tr}" class="link" href="tiki-jukebox_genres.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;genreId={$listgenres[changes].genreId}"><img border="0" alt="{tr}edit{/tr}" src="img/icons/edit.gif" /></a>&nbsp;&nbsp;<a title="{tr}delete{/tr}" class="link" href="tiki-jukebox_genres.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listgenres[changes].genreId}"><img border="0" alt="{tr}delete{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;
	</td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="3">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="jukeboxprevnext" href="tiki-jukebox_genres.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="jukeboxprevnext" href="tiki-jukebox_genres.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-jukebox_genres.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

