<div id="browsegallery">
  <div class="gallerytitle">{tr}Browsing Gallery{/tr}: {$name}</div>
  {if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner}
    <div class="adminoptions">
    <a href="tiki-galleries.php?editgal={$galleryId}" class="link">{tr}edit gallery{/tr}</a> 
    &nbsp;
    <a href="tiki-browse_gallery.php?galleryId={$galleryId}&rebuild={$galleryId}" class="link">{tr}rebuild thumbnails{/tr}</a> 
    </div>
  {/if}
  {if $tiki_p_upload_images}
    {if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner or $public eq 'y'}
    <div class="uploadimagelink">
    &nbsp;<a href="tiki-upload_image.php?galleryId={$galleryId}" class="link">{tr}upload image{/tr}</a><br/><br/>
    </div>
    {/if}
  {/if}

  <div class="galdesc">
    {$description}
  </div>
  
  <div class="sortoptions">
    <span class="sorttitle">{tr}Sort Images by{/tr}</span>
        <span class="sortoption"><a class="link" href="tiki-browse_gallery.php?galleryId={$galleryId}&offset={$offset}&sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></span>
        <span class="sortoption"><a class="link" href="tiki-browse_gallery.php?galleryId={$galleryId}&offset={$offset}&sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a></span>
        <span class="sortoption"><a class="link" href="tiki-browse_gallery.php?galleryId={$galleryId}&offset={$offset}&sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></span>
        <span class="sortoption"><a class="link" href="tiki-browse_gallery.php?galleryId={$galleryId}&offset={$offset}&sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></span>
        <span class="sortoption"><a class="link" href="tiki-browse_gallery.php?galleryId={$galleryId}&offset={$offset}&sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Size{/tr}</a></span>
  </div>

  <div class="thumbnails">
    <div class="spacer">&nbsp;</div>
    {section name=idx loop=$images}
    <div class="thumbnail">
    {* {if (($smarty.section.idx.index / $rowImages) % 2)}id="oddthumb"{else}id="eventhumb"{/if} *}
    {if (($smarty.section.idx.index / $rowImages) % 2)}<br/>{/if}
    <a href="tiki-browse_image.php?imageId={$images[idx].imageId}"><img id="athumb" width="{$thx}" height="{$thy}" src="show_image.php?id={$images[idx].imageId}&thumb=1" /></a>
    <p class="caption">{$images[idx].name}&nbsp;
      {if $user eq 'admin' or $tiki_p_admin eq 'y' or $user eq $owner}
        <a class="link" href="tiki-browse_gallery.php?galleryId={$galleryId}&remove={$images[idx].imageId}">[x]</a>
      {/if}
      <br/>
      ({$images[idx].xsize}x{$images[idx].ysize})[{$images[idx].hits} {tr}hits{/tr}]
    </p>
    </div>
    {sectionelse}
    <p class="norecords">{tr}No records found{/tr}</p>
    {/section}
    <div class="spacer">&nbsp;</div>
  </div>

  <div class="pagination">
    <div align="center" class="mini">
      {if $prev_offset >= 0}
        [<a href="tiki-browse_gallery.php?galleryId={$galleryId}&offset={$prev_offset}&sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
        &nbsp;[<a href="tiki-browse_gallery.php?galleryId={$galleryId}&offset={$next_offset}&sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
   </div>
  </div>
</div>

