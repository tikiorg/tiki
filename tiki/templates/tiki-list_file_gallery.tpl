<a class="pagetitle" href="tiki-list_file_gallery.php?galleryId={$galleryId}">{tr}Listing Gallery{/tr}: {$name}</a><br/><br/>
  [{if $tiki_p_admin_file_galleries eq 'y' or ($user and $user eq $owner)}
      <a  href="tiki-file_galleries.php?edit_mode=1&amp;galleryId={$galleryId}" class="gallink">{tr}edit gallery{/tr}</a> 
  {/if}
  {if $tiki_p_upload_files eq 'y'}
    {if $tiki_p_admin_file_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
        |<a href="tiki-upload_file.php?galleryId={$galleryId}" class="gallink">{tr}upload file{/tr}</a>
    {/if}
  {/if}
  {if $rss_file_gallery eq 'y'}
  |<a href="tiki-file_gallery_rss.php?galleryId={$galleryId}" class="gallink">RSS</a>
  {/if}]<br/><br/>
  
  <div class="fgaldesc">
    {$description}
  </div>

  <h3>Gallery Files</h3>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_file_gallery.php">
     <input type="hidden" name="galleryId" value="{$galleryId}" />
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="listfiles">
<tr>
<td width="4%" class="listfilesheading"><a class="llistfileslink" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'fileId_desc'}fileId_asc{else}fileId_desc{/if}">{tr}ID{/tr}</a></td>
<!--<td width="15%" class="listfilesheading"><a class="llistfileslink" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Title{/tr}</a></td>-->
<td width="25%" class="listfilesheading"><a class="llistfileslink" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}Name{/tr}</a></td>
<td class="listfilesheading"><a class="llistfileslink" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Filesize{/tr}</a></td>
<td width="35%" class="listfilesheading"><a class="llistfileslink" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td width="16%" class="listfilesheading"><a class="llistfileslink" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td width="5%" class="listfilesheading"><a class="llistfileslink" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'downloads_desc'}downloads_asc{else}downloads_desc{/if}">{tr}Dls{/tr}</a></td>
<!--<td class="listfilesheading"><a class="llistfileslink" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>-->
<!--<td class="listfilesheading">Actions</td>-->
</tr>
{section name=changes loop=$images}
<tr>
{if $smarty.section.changes.index % 2}
<td class="listfilesidodd">{$images[changes].fileId}&nbsp;</td>
<!--<td class="listfilesnameodd">{$images[changes].name}&nbsp;</td>-->
<td class="listfilesfilenameodd">
{if $tiki_p_download_files eq 'y'}
{$images[changes].filename|iconify}
<a class="fgalname" href="tiki-download_file.php?fileId={$images[changes].fileId}">
{/if}
{$images[changes].filename}
{if $tiki_p_download_files eq 'y'}
</a>
{/if}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $user eq $owner)}
<a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$images[changes].fileId}">[x]</a>
{/if}
&nbsp;</td>
<td class="listfilesfilesizeodd">{$images[changes].filesize}&nbsp;</td>
<td class="listfilesdescriptionodd">{$images[changes].description}&nbsp;</td>
<td class="listfilescreatedodd">{$images[changes].created|date_format:"%d of %b, %Y"}{if $images[changes].user} by {$images[changes].user}{/if}&nbsp;</td>
<td class="listfilesdlsodd">{$images[changes].downloads}&nbsp;</td>
<!--<td class="listfilesuserodd">{$images[changes].user}&nbsp;</td>-->
</td>
{else}
<td class="listfilesideven">{$images[changes].fileId}&nbsp;</td>
<!--<td class="listfilesnameodd">{$images[changes].name}&nbsp;</td>-->
<td class="listfilesfilenameeven">
{$images[changes].filename|iconify}
{if $tiki_p_download_files eq 'y'}
<a class="fgalname" href="tiki-download_file.php?fileId={$images[changes].fileId}">
{/if}
{$images[changes].filename}
{if $tiki_p_download_files eq 'y'}
</a>
{/if}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $user eq $owner)}
<a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$images[changes].fileId}">[x]</a>
{/if}
&nbsp;</td>
<td class="listfilesfilesizeeven">{$images[changes].filesize}&nbsp;</td>
<td class="listfilesdescriptioneven">{$images[changes].description}&nbsp;</td>
<td class="listfilescreatedeven">{$images[changes].created|date_format:"%d of %b, %Y"}{if $images[changes].user} by {$images[changes].user}{/if}&nbsp;</td>
<td class="listfilesdlseven">{$images[changes].downloads}&nbsp;</td>
<!--<td class="listfilesusereven">{$images[changes].user}&nbsp;</td>-->
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
  <div class="mini">
      {if $prev_offset >= 0}
        [<a class="fgalprevnext" href="tiki-list_file_gallery.php?find={$find}&amp;galleryId={$galleryId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;[<a class="fgalprevnext" href="tiki-list_file_gallery.php?find={$find}&amp;galleryId={$galleryId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
      {if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_file_gallery.php?find={$find}&amp;galleryId={$galleryId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

  </div>
</div>
{if $feature_file_galleries_comments eq 'y'}
{include file=comments.tpl}
{/if}
