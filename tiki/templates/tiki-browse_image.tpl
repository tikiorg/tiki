<div  class="browseimage">
{if ($popup != "") }
  <script language=javascript>
	window.resizeTo({$winx},{$winy});
  </script>
{/if}

{if ($popup == "")  }
  <div class="imagetitle">
    {tr}Browsing Image{/tr}: {$name}
  </div>
  <div class="gallerylink">
    <a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}">{tr}return to gallery{/tr}</a>
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
    | <a class="gallink" href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$imageId}">{tr}edit image{/tr}</a>
    {/if}
  </div>
{/if}  


{if ($popup != "") }
  <div valign=middle height={$winy} align="center" >
	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$firstId}{if $itype=='s'}&amp;scaled&amp;xsize={$sxsize}&amp;ysize={$sysize}{/if}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_first.gif' border='0' alt='{tr}first image{/tr}' title='{tr}first image{/tr}' /></a>    
    {if $prevx != 0}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;scaled&amp;xsize={$prevx}&amp;ysize={$prevy}&amp;popup={$popup}" class="gallink"><img src='img/icons2/up.gif' border='0' alt='{tr}smaller{/tr}' title='{tr}smaller{/tr}' /></a>
    {/if}
    {if $itype !='o'}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_dot.gif' border='0' alt='{tr}original size{/tr}' title='{tr}original size{/tr}' /></a>
    {/if}
    {if $nextx != 0}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;scaled&amp;xsize={$nextx}&amp;ysize={$nexty}&amp;popup={$popup}" class="gallink"><img src='img/icons2/down.gif' border='0' alt='{tr}bigger{/tr}' title='{tr}bigger{/tr}' /></a>
    {/if}

    {if $previmg}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$previmg}{if $itype=='s'}&amp;scaled&amp;xsize={$sxsize}&amp;ysize={$sysize}{/if}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}prev image{/tr}' title='{tr}prev image{/tr}' /></a>
    {/if}
    {if $nextimg}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$nextdesp}&amp;galleryId={$galleryId}&amp;imageId={$nextimg}{if $itype=='s'}&amp;scaled&amp;xsize={$sxsize}&amp;ysize={$sysize}{/if}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}next image{/tr}' title='{tr}next image{/tr}' /></a>
    {/if}
    <a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$lastId}{if $itype=='s'}&amp;scaled&amp;xsize={$sxsize}&amp;ysize={$sysize}{/if}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_last.gif' border='0' alt='{tr}last image{/tr}' title='{tr}last image{/tr}' /></a>    
  </div>
  <br>
{/if}   

  <div class="showimage" {if ($popup) }height=400{/if}>
    {if $itype=='o'}
    	<img alt="image" src="show_image.php?id={$imageId}" />
    {else}
	    <a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$desp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;xsize={$nextx}&amp;ysize={$nexty}&amp;scaled" title="{tr}Klick to enlarge{/tr}">
	    <img alt="image" src="show_image.php?id={$imageId}&amp;scaled&amp;xsize={$sxsize}&amp;ysize={$sysize}" /></a>
    {/if}
  </div>
  
{if ($popup == "")}
	<div align="center" >
		<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$firstId}{if $itype=='s'}&amp;scaled&amp;xsize={$sxsize}&amp;ysize={$sysize}{/if}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_first.gif' border='0' alt='{tr}first image{/tr}' title='{tr}first image{/tr}' /></a>    
	    {if $prevx != 0}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;scaled&amp;xsize={$prevx}&amp;ysize={$prevy}&amp;popup={$popup}" class="gallink"><img src='img/icons2/up.gif' border='0' alt='{tr}smaller{/tr}' title='{tr}smaller{/tr}' /></a>
	    {/if}
	    {if $itype !='o'}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_dot.gif' border='0' alt='{tr}original size{/tr}' title='{tr}original size{/tr}' /></a>
	    {/if}
	    {if $nextx != 0}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;scaled&amp;xsize={$nextx}&amp;ysize={$nexty}&amp;popup={$popup}" class="gallink"><img src='img/icons2/down.gif' border='0' alt='{tr}bigger{/tr}' title='{tr}bigger{/tr}' /></a>
	    {/if}
	    {if $previmg}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$previmg}{if $itype=='s'}&amp;scaled&amp;xsize={$sxsize}&amp;ysize={$sysize}{/if}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}prev image{/tr}' title='{tr}prev image{/tr}' /></a>
	    {/if}
	    {if $nextimg}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$nextdesp}&amp;galleryId={$galleryId}&amp;imageId={$nextimg}{if $itype=='s'}&amp;scaled&amp;xsize={$sxsize}&amp;ysize={$sysize}{/if}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}next image{/tr}' title='{tr}next image{/tr}' /></a>
	    {/if}
	   	<a {jspopup height="$winy" width="$winx" href="tiki-browse_image.php?offset=$offset&amp;sort_mode=$sort_mode&amp;desp=$nextdesp&amp;galleryId=$galleryId&amp;imageId=$imageId&amp;popup=1"} class="gallink"><img src='img/icons2/admin_unhide' border='0' alt='{tr}Popup window{/tr}' title='{tr}popup window{/tr}' /></a>
		<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$lastId}{if $itype=='s'}&amp;scaled&amp;xsize={$sxsize}&amp;ysize={$sysize}{/if}&amp;popup={$popup}" class="gallink"><img src='img/icons2/nav_last.gif' border='0' alt='{tr}last image{/tr}' title='{tr}last image{/tr}' /></a>    
	</div>
{/if}

  
{if ($popup == "")  }
  <div class="imageinfo">
      <table class="imageinfo">
      <tr><td class="imageinfo">{tr}Image Name{/tr}:</td><td class="imageinfo">{$name}</td></tr>
      <tr><td class="imageinfo">{tr}Created{/tr}:</td><td class="imageinfo">{$created|tiki_long_datetime}</td></tr>
      <tr><td class="imageinfo">{tr}Hits{/tr}:</td><td class="imageinfo">{$hits}</td></tr>
      <tr><td class="imageinfo">{tr}Description{/tr}:</td><td class="imageinfo">{$description}</td></tr>
      {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
        <tr><td class="imageinfo">{tr}Move image{/tr}:</td><td class="imageinfo">
        <form action="tiki-browse_image.php" method="post">
        <input type="hidden" name="imageId" value="{$imageId}"/>
        <input type="hidden" name="galleryId" value="{$galleryId}"/>
        <select name="newgalleryId">
          {section name=idx loop=$galleries}
            <option value="{$galleries[idx].id}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
          {/section}
        </select>
        <input type="submit" name="move_image" value="{tr}move{/tr}" />
        </form>
        </td></tr>
      {/if}
    </table>
  </div>
  <div class="linksinfo">
    {tr}You can view this image in your browser using{/tr}: <a class="gallink" href="{$url_browse}?imageId={$imageId}">{$url_browse}?imageId={$imageId}</a><br/>
    {tr}You can include the image in an HTML or Tiki page using{/tr} &lt;img src="{$url_show}?id={$imageId}" /&gt;
  </div>
{/if}  
</div>
