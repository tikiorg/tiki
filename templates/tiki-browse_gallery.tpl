<div class="browsegallery">
  <div  class="gallerytitle">
    {tr}Browsing Gallery{/tr}: {$name}
  </div>
  <div class="adminoptions">[
  {if $system eq 'n'}
  {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
      <a href="tiki-galleries.php?edit_mode=1&amp;galleryId={$galleryId}" class="gallink">{tr}edit gallery{/tr}</a>|
      <a href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;rebuild={$galleryId}" class="gallink">{tr}rebuild thumbnails{/tr}</a> 
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
        |<a href="tiki-upload_image.php?galleryId={$galleryId}" class="gallink">{tr}upload image{/tr}</a>
    {/if}
  {/if}
  {/if}
  |<a href="tiki-list_gallery.php?galleryId={$galleryId}" class="gallink">{tr}list gallery{/tr}</a> {if $rss_image_gallery eq 'y'}| <a href="tiki-image_gallery_rss.php?galleryId={$galleryId}" class="gallink">RSS</a>{/if}  
  ]</div>

  <div class="galdesc">
    {$description}
  </div>

  <div class="sortoptions">
    <span class="sorttitle">{tr}Sort Images by{/tr}</span>
    [<span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></span>
    |<span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a></span>
    |<span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></span>
    |<span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></span>
    |<span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Size{/tr}</a></span>]
  </div>

  <div class="thumbnails">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        {section name=idx loop=$images}
          <td align="center" {if (($smarty.section.idx.index / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
          &nbsp;&nbsp;<br/>
          
          
          <a href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;desp={$smarty.section.idx.index}&amp;offset={$offset}&amp;imageId={$images[idx].imageId}"><img alt="thumbnail" class="athumb" src="show_image.php?id={$images[idx].imageId}&amp;thumb=1" /></a>
          <br/>
          <small class="caption">{$images[idx].name}
          {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
            <br>
            [
            <a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;remove={$images[idx].imageId}">x</a>
            |
            <a class="gallink" href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$images[idx].imageId}">edit</a>
            ]
          {/if}
          <br/>
         ({$images[idx].xsize}x{$images[idx].ysize})[{$images[idx].hits} {tr}hits{/tr}]</small>
         </td>
         {if $smarty.section.idx.index % $rowImages eq $rowImages2}
           </tr><tr>
         {/if}
        {sectionelse}
          <tr><td colspan="6">
            <p class="norecords">{tr}No records found{/tr}</p>
          </td></tr>
        {/section}
      </tr>
    </table>
  </div>

  <div class="mini">
      {if $prev_offset >= 0}
        [<a  class="galprevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;&nbsp;[<a class="galprevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
      {if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

  </div>
</div>
{if $feature_image_galleries_comments eq 'y'}
{include file=comments.tpl}
{/if}
