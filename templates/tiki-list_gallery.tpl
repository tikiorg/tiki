<h2>{tr}Browsing Gallery{/tr}: {$name}</h2>
  {if $system eq 'n'}
  {if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner}
      <a  href="tiki-galleries.php?editgal={$galleryId}" class="gallink">{tr}edit gallery{/tr}</a> 
      &nbsp;
      <a href="tiki-list_gallery.php?galleryId={$galleryId}&amp;rebuild={$galleryId}" class="gallink">{tr}rebuild thumbnails{/tr}</a> 
  {/if}
  {if $tiki_p_upload_images}
    {if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner or $public eq 'y'}
        <a href="tiki-upload_image.php?galleryId={$galleryId}" class="gallink">{tr}upload image{/tr}</a><br/><br/>
    {/if}
  {/if}
  {/if}
  <a href="tiki-browse_gallery.php?galleryId={$galleryId}" class="gallink">{tr}browse gallery{/tr}</a><br/><br/> 
  <div class="galdesc">
    {$description}
  </div>

  <h3>Gallery Images</h3>
<div align="center">
<table border="1" width="94%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'imageId_desc'}imageId_asc{else}imageId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Filesize{/tr}</a></td>
<td class="heading">Actions</td>
</tr>
{section name=changes loop=$images}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">{$images[changes].imageId}&nbsp;</td>
<td class="odd">{$images[changes].name}&nbsp;</td>
<td class="odd">{$images[changes].created|date_format:"%A %d of %B, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">{$images[changes].hits}&nbsp;</td>
<td class="odd">{$images[changes].user}&nbsp;</td>
<td class="odd">{$images[changes].filesize}&nbsp;</td>
<td class="odd">
<a class="link" href="tiki-browse_image.php?imageId={$images[changes].imageId}">browse</a>
{if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner}
<a class="link" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$images[changes].imageId}">[x]</a>
{/if}
</td>
{else}
<td class="even">{$images[changes].imageId}&nbsp;</td>
<td class="even">{$images[changes].name}&nbsp;</td>
<td class="even">{$images[changes].created|date_format:"%A %d of %B, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">{$images[changes].hits}&nbsp;</td>
<td class="even">{$images[changes].user}&nbsp;</td>
<td class="even">{$images[changes].filesize}&nbsp;</td>
<td class="even">
<a class="link" href="tiki-browse_image.php?imageId={$images[changes].imageId}">browse</a>
{if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner}
<a class="link" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$images[changes].imageId}">[x]</a>
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
        [<a class="prevnext" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;[<a class="prevnext" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
  </div>
</div>

