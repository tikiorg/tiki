<a class="pagetitle" href="tiki-browse_gallery.php?galleryId={$galleryId}">
{tr}Browsing Gallery{/tr}: {$name}
</a>
<br /><br />

{if $system eq 'n'}[
  {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
    <a href="tiki-galleries.php?edit_mode=1&amp;galleryId={$galleryId}" class="linkbut">{tr}Edit gallery{/tr}</a>
    <a href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;rebuild={$galleryId}" class="linkbut">{tr}|Rebuild thumbnails{/tr}</a>
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
      <a href="tiki-upload_image.php?galleryId={$galleryId}" class="linkbut">{tr}|Upload image{/tr}</a>
    {/if}
  {/if}
{/if}
<a href="tiki-list_gallery.php?galleryId={$galleryId}" class="linkbut">{tr}|List gallery{/tr}</a>
{if $rss_image_gallery eq 'y'}
  <a href="tiki-image_gallery_rss.php?galleryId={$galleryId}" class="linkbut">|RSS</a>
{/if}
]
{if strlen($description) > 0}
	<div class="imgaldescr">
	  {$description}
  </div>
{/if}
<br /><br />


	<span class="sorttitle">{tr}Sort Images by{/tr}</span>
    [ <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Size{/tr}</a></span> ]


  <div class="thumbnails">
    <table class="galtable"  cellpadding="0" cellspacing="0" align="center">
      <tr>
        {section name=idx loop=$images}
          <td align="center" {if (($smarty.section.idx.index / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
          &nbsp;&nbsp;<br />
          
          {if $nextx==0}
          <a href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;desp={$smarty.section.idx.index}&amp;offset={$offset}&amp;imageId={$images[idx].imageId}"><img alt="thumbnail" class="athumb" src="show_image.php?id={$images[idx].imageId}&amp;thumb=1" /></a>
	  {else}
          <a href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;desp={$smarty.section.idx.index}&amp;offset={$offset}&amp;imageId={$images[idx].imageId}&amp;scaled&amp;xsize={$nextx}&amp;ysize={$nexty}"><img alt="thumbnail" class="athumb" src="show_image.php?id={$images[idx].imageId}&amp;thumb=1" /></a>
	  {/if}
          <br />
          <small class="caption">{$images[idx].name}
          <br />
          {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
	    		{if $nextx!=0}
            		<a class="gallink" href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;desp={$smarty.section.idx.index}&amp;offset={$offset}&amp;imageId={$images[idx].imageId}" title="{tr}original size{/tr}"><img src='img/icons2/nav_dot.gif' border='0' alt='{tr}original size{/tr}' title='{tr}original size{/tr}' /></a>
	    		{/if}
            	{if $imagerotate}
            		<a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;rotateright={$images[idx].imageId}" title="{tr}rotate right{/tr}"><img src='img/icons2/admin_rotate.gif' border='0' alt='{tr}rotate{/tr}' title='{tr}rotate{/tr}' /></a>
            	{/if}
            	<a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;remove={$images[idx].imageId}" title="{tr}delete{/tr}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this image?{/tr}')"><img src='img/icons2/admin_delete.gif' border='0' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' /></a>
            	<a class="gallink" href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$images[idx].imageId}" title="{tr}edit{/tr}"><img src='img/icons2/admin_move.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
          {/if}
					{assign var=desp value=$smarty.section.idx.index}
                                        {assign var=THEimageId value=$images[idx].imageId}
          <a {jspopup href="tiki-browse_image.php?galleryId=$galleryId&amp;sort_mode=$sort_mode&amp;desp=$desp&amp;offset=$offset&amp;imageId=$THEimageId&amp;popup=1"} class="gallink"><img src='img/icons2/admin_unhide.gif' border='0' alt='{tr}popup{/tr}' title='{tr}popup{/tr}' /></a>
          <br />
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

<div align="center">
<div class="mini">
      {if $prev_offset >= 0}
        [<a  class="galprevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;&nbsp;[<a class="galprevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
      {if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxImages}
<a class="prevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

  </div>
</div>
{if $feature_image_galleries_comments eq 'y'}
{if $tiki_p_read_comments eq 'y'}
<div id="page-bar">
<table>
<tr><td>
<div class="button2">
<a href="javascript:flip('comzone{if $comments_show eq 'y'}open{/if}');" class="linkbut">{if $comments_cant eq 0}{tr}comment{/tr}{elseif $comments_cant eq 1}1 {tr}comment{/tr}{else}{$comments_cant} {tr}comments{/tr}{/if}</a>
</div>
</td></tr></table>
</div>
{include file=comments.tpl}
{/if}
{/if}
