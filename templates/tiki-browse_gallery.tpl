<div class="browsegallery">
  <div class="gallerytitle">
    {tr}Browsing Gallery{/tr}: {$name}
  </div>
  {if $system eq 'n'}
  {if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner}
    <div class="adminoptions">
      <a href="tiki-galleries.php?editgal={$galleryId}" class="gallink">{tr}edit gallery{/tr}</a> 
      &nbsp;&nbsp;
      <a href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;rebuild={$galleryId}" class="gallink">{tr}rebuild thumbnails{/tr}</a> 
    </div>
  {/if}
  {if $tiki_p_upload_images}
    {if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner or $public eq 'y'}
      <div class="uploadimagelink">
        <a href="tiki-upload_image.php?galleryId={$galleryId}" class="gallink">{tr}upload image{/tr}</a><br/><br/>
      </div>
    {/if}
  {/if}
  {/if}
  <a href="tiki-list_gallery.php?galleryId={$galleryId}" class="gallink">{tr}list gallery{/tr}</a><br/><br/>  

  <div class="galdesc">
    {$description}
  </div>

  <div class="sortoptions">
    <span class="sorttitle">{tr}Sort Images by{/tr}</span>
    <span class="sortoption"><a class="sortlink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></span>
    <span class="sortoption"><a class="sortlink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a></span>
    <span class="sortoption"><a class="sortlink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></span>
    <span class="sortoption"><a class="sortlink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></span>
    <span class="sortoption"><a class="sortlink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Size{/tr}</a></span>
  </div>

  <div class="thumbnails">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        {section name=idx loop=$images}
          <td align="center" {if (($smarty.section.idx.index / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
          &nbsp;&nbsp;<br/>
          <a href="tiki-browse_image.php?imageId={$images[idx].imageId}"><img alt="thumbnail" class="athumb" width="{$thx}" height="{$thy}" src="show_image.php?id={$images[idx].imageId}&amp;thumb=1" /></a>
          <br/>
          <small class="caption">{$images[idx].name}&nbsp;&nbsp;
          {if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner}
            <a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;remove={$images[idx].imageId}">[x]</a>
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

  <div class="pagination">
      {if $prev_offset >= 0}
        [<a class="paglink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;&nbsp;[<a class="paglink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
  </div>
</div>

