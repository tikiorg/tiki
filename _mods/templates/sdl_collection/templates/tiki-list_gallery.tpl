<a href="tiki-list_gallery.php?galleryId={$galleryId}" class="pagetitle">{tr}Browsing Gallery{/tr}: {$name}</a><br /><br />

[{if $system eq 'n'}
  {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
  <a  href="tiki-galleries.php?edit_mode=1&amp;galleryId={$galleryId}" class="linkbut">{tr}Edit Gallery|{/tr}</a>
    <a href="tiki-list_gallery.php?galleryId={$galleryId}&amp;rebuild={$galleryId}" class="linkbut">{tr}Rebuild Thumbnails|{/tr}</a>
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
        <a href="tiki-upload_image.php?galleryId={$galleryId}" class="linkbut">{tr}Upload Image|{/tr}</a>
    {/if}
  {/if}
{/if}
<a href="tiki-browse_gallery.php?galleryId={$galleryId}" class="linkbut">{tr}Browse Gallery{/tr}]</a>
<br /><br /> 
<div class="galdesc">
  {$description}
</div>

  <h3>{tr}Gallery Images{/tr}</h3>
<div align="center">
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'imageId_desc'}imageId_asc{else}imageId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}File Size{/tr}</a></td>
</tr>
{cycle print=false values="even,odd"}
{section name=changes loop=$images}
<tr>
<td class="{cycle advance=false}">{$images[changes].imageId}&nbsp;</td>
<td class="{cycle advance=false}"><a class="imagename" href="tiki-browse_image.php?galleryId={$images[changes].galleryId}&amp;imageId={$images[changes].imageId}">{$images[changes].name}</a>&nbsp;
{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
<a class="gallink" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$images[changes].imageId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this file gallery?{/tr}')">[Delete]</a>
{/if}
</td>
<td class="{cycle advance=false}">{$images[changes].created|tiki_short_datetime}&nbsp;</td>
<td class="{cycle advance=false}">{$images[changes].hits}&nbsp;</td>
<td class="{cycle advance=false}">{$images[changes].user}&nbsp;</td>
<td class="{cycle}">{$images[changes].filesize}&nbsp;</td>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>

  <div class="mini">
      {if $prev_offset >= 0}
        [<a class="galprevnext" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;[<a class="galprevnext" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
      {if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
  </div>
</div>