<h2>{tr}Listing Gallery{/tr}: {$name}</h2>
  {if $tiki_p_admin_file_galleries eq 'y' or ($user and $user eq $owner)}
      <a  href="tiki-file_galleries.php?edit_mode=1&amp;galleryId={$galleryId}" class="gallink">{tr}edit gallery{/tr}</a> 
  {/if}
  {if $tiki_p_upload_files eq 'y'}
    {if $tiki_p_admin_file_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
        <a href="tiki-upload_file.php?galleryId={$galleryId}" class="gallink">{tr}upload file{/tr}</a><br/><br/>
    {/if}
  {/if}
  
  <div class="galdesc">
    {$description}
  </div>

  <h3>Gallery Files</h3>
<div align="center">
<table border="1" cellpadding="0" cellspacing="0" width="99%">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-list_file_gallery.php">
     <input type="hidden" name="galleryId" value="{$galleryId}" />
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table border="1" width="99%" cellpadding="0" cellspacing="0">
<tr>
<td width="4%" class="heading"><a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'fileId_desc'}fileId_asc{else}fileId_desc{/if}">{tr}ID{/tr}</a></td>
<!--<td width="15%" class="heading"><a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Title{/tr}</a></td>-->
<td class="heading"><a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Filesize{/tr}</a></td>
<td width="35%" class="heading"><a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td width="16%" class="heading"><a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td width="5%" class="heading"><a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'downloads_desc'}downloads_asc{else}downloads_desc{/if}">{tr}Dls{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">Actions</td>
</tr>
{section name=changes loop=$images}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">{$images[changes].fileId}&nbsp;</td>
<!--<td class="odd">{$images[changes].name}&nbsp;</td>-->
<td class="odd">{$images[changes].filename}&nbsp;</td>
<td class="odd">{$images[changes].filesize}&nbsp;</td>
<td class="odd">{$images[changes].description}&nbsp;</td>
<td class="odd">{$images[changes].created|date_format:"%d of %b, %Y"}&nbsp;</td>
<td class="odd">{$images[changes].downloads}&nbsp;</td>
<td class="odd">{$images[changes].user}&nbsp;</td>

<td class="odd">
{if $tiki_p_download_files eq 'y'}
<a class="link" href="tiki-download_file.php?fileId={$images[changes].fileId}">get</a>
{/if}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $user eq $owner)}
<a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$images[changes].fileId}">[x]</a>
{/if}
</td>
{else}
<td class="even">{$images[changes].fileId}&nbsp;</td>
<!--<td class="even">{$images[changes].name}&nbsp;</td>-->
<td class="even">{$images[changes].filename}&nbsp;</td>
<td class="even">{$images[changes].filesize}&nbsp;</td>
<td class="even">{$images[changes].description}&nbsp;</td>
<td class="even">{$images[changes].created|date_format:"%d of %b, %Y"}&nbsp;</td>
<td class="even">{$images[changes].downloads}&nbsp;</td>
<td class="even">{$images[changes].user}&nbsp;</td>

<td class="even">
{if $tiki_p_download_files eq 'y'}
<a class="link" href="tiki-download_file.php?fileId={$images[changes].fileId}">get</a>
{/if}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $user eq $owner)}
<a class="link" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$images[changes].fileId}">[x]</a>
{/if}
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>

  <div class="mini">
      {if $prev_offset >= 0}
        [<a class="prevnext" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;[<a class="prevnext" href="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
  </div>
</div>

