<div  class="browseimage">
  
  <div class="imagetitle">
    {tr}Browsing Image{/tr}: {$name}
  </div>
  
  <div class="gallerylink">
    <a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}">{tr}return to gallery{/tr}</a>
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
    | <a class="gallink" href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$imageId}">{tr}edit image{/tr}</a>
    {/if}
  </div>
  
  <div class="showimage">
    <img alt="image" src="show_image.php?id={$imageId}" />
  </div>
  
  <div align="center">
    [{if $previmg}
    <a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$previmg}" class="gallink"> {tr}prev image{/tr}</a>{/if}{if $previmg and $nextimg} | {/if}
    {if $nextimg}
    <a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$nextdesp}&amp;galleryId={$galleryId}&amp;imageId={$nextimg}" class="gallink">{tr}next image{/tr}</a> {/if}]
  </div>

  
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
        <select name="galleryId">
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
</div>
