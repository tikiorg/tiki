<a href="tiki-galleries.php" class="pagetitle">{tr}Galleries{/tr}</a><br/><br/>
{if $tiki_p_create_galleries eq 'y'}
{if $edit_mode eq 'y'}
<h3>{tr}Create or edit a gallery using this form{/tr}</h3>
<div  align="center">
{if $individual eq 'y'}
<a class="gallink" href="tiki-objectpermissions.php?objectName=gallery%20{$name}&amp;objectType=image%20gallery&amp;permType=image%20galleries&amp;objectId={$galleryId}">{tr}There are inddividual permissions set for this gallery{/tr}</a>
{/if}
<form action="tiki-galleries.php" method="post">
<input type="hidden" name="galleryId" value="{$galleryId}" />
<table class="creategalform">
<tr><td class="galform">{tr}Name{/tr}:</td><td class="galform"><input type="text" name="name" value="{$name}"/></td></tr>
<tr><td class="galform">{tr}Description{/tr}:</td><td class="galform"><textarea rows="5" cols="40" name="description">{$description}</textarea></td></tr>
<!--<tr><td class="galform">{tr}Theme{/tr}:</td><td class="galform"><select name="theme">
       <option value="default" {if $theme eq 'default'}selected="selected"{/if}>default</option>
       <option value="dark" {if $theme eq 'dark'}selected="selected"{/if}>dark</option>
       </select></td></tr>-->
{if $tiki_p_admin_galleries eq 'y'}       
<tr><td class="galform">{tr}Gallery is visible to non-admin users?{/tr}</td><td class="galform"><input type="checkbox" name="visible" {if $visible eq 'y'}checked="checked"{/if} /></td></tr>       
{/if}
<tr><td class="galform">{tr}Max Rows per page{/tr}:</td><td class="galform"><input type="text" name="maxRows" value="{$maxRows}" /></td></tr>
<tr><td class="galform">{tr}Images per row{/tr}:</td><td class="galform"><input type="text" name="rowImages" value="{$rowImages}" /></td></tr>
<tr><td class="galform">{tr}Thumbnails size X{/tr}:</td><td class="galform"><input type="text" name="thumbSizeX" value="{$thumbSizeX}" /></td></tr>
<tr><td class="galform">{tr}Thumbnails size Y{/tr}:</td><td class="galform"><input type="text" name="thumbSizeY" value="{$thumbSizeY}" /></td></tr>      
{include file=categorize.tpl}
<tr><td class="galform">{tr}Other users can upload images to this gallery{/tr}:</td><td class="galform"><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="galform">&nbsp;</td><td class="galform"><input type="submit" value="{tr}edit/create{/tr}" name="edit" /></td></tr>
</table>
</form>
</div>
<br/>
{/if}
{/if}
{if $galleryId > 0}
{if $edited eq 'y'}
<div class="wikitext">
{tr}You can access the gallery using the following URL{/tr}: <a class="gallink" href="http://{$url}?galleryId={$galleryId}">http://{$url}?galleryId={$galleryId}</a>
</div>
{/if}
{/if}
<h2>{tr}Available Galleries{/tr}</h2>
<a class="gallink" href="tiki-galleries.php?edit_mode=1&amp;galleryId=0">create new gallery</a><br/><br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-galleries.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="gallerylisting">
<tr>
<td class="gallistheading"><a class="gallistheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="gallistheading"><a class="gallistheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="gallistheading"><a class="gallistheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="gallistheading"><a class="gallistheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr} modified{/tr}</a></td>
<!--<td class="gallistheading"><a class="gallistheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'theme_desc'}theme_asc{else}theme_desc{/if}">{tr}Theme{/tr}</a></td>-->
<td class="gallistheading"><a class="gallistheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="gallistheading"><a class="gallistheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'images_desc'}images_asc{else}images_desc{/if}">{tr}Imgs{/tr}</a></td>
<td class="gallistheading"><a class="gallistheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></td>
<td class="gallistheading">Actions</td>
</tr>
{section name=changes loop=$galleries}
{if $galleries[changes].visible eq 'y' or $tiki_p_admin_galleries eq 'y'}
<tr>
{if $smarty.section.changes.index % 2}
  <td class="gallistnameodd"><a class="galname" href="tiki-browse_gallery.php?galleryId={$galleries[changes].id}">{$galleries[changes].name}</a>&nbsp;</td>
  <td class="gallistdescriptionodd">{$galleries[changes].description}&nbsp;</td>
  <td class="gallistcreatedodd">{$galleries[changes].created|date_format:"%d of %b, %Y [%H:%M]"}&nbsp;</td>
  <td class="gallistlastModifodd">{$galleries[changes].lastModif|date_format:"%a %d of %b [%H:%M]"}&nbsp;</td>
  <!--<td class="gallistthemeodd">{$galleries[changes].theme}&nbsp;</td>-->
  <td class="gallistuserodd">{$galleries[changes].user}&nbsp;</td>
  <td class="gallistimagesodd">{$galleries[changes].images}&nbsp;</td>
  <td class="gallisthitsodd">{$galleries[changes].hits}&nbsp;</td>
  <td class="gallistactionsodd">
  {if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_galleries eq 'y' ) }
    <a class="gallink" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}">Edit</a>
    <a class="gallink" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}">Remove</a>
  {/if}
  {/if}
  {if $tiki_p_upload_images eq 'y'}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_upload_images eq 'y' ) }
  {if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
    <a class="gallink" href="tiki-upload_image.php?galleryId={$galleries[changes].id}">Upload</a>
  {/if}
  {/if}
  {/if}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_image_gallery eq 'y' ) }
  <a class="gallink" href="tiki-list_gallery.php?galleryId={$galleries[changes].id}">{tr}List{/tr}</a>
  {/if}
  {if $tiki_p_admin eq 'y'}
    {if $galleries[changes].individual eq 'y'}({/if}<a class="gallink" href="tiki-objectpermissions.php?objectName=gallery%20{$galleries[changes].name}&amp;objectType=image%20gallery&amp;permType=image%20galleries&amp;objectId={$galleries[changes].id}">{tr}perms{/tr}</a>{if $galleries[changes].individual eq 'y'}){/if}
  {/if}
  </td>
{else}
  <td class="gallistnameeven"><a class="galname" href="tiki-browse_gallery.php?galleryId={$galleries[changes].id}">{$galleries[changes].name}</a>&nbsp;</td>
  <td class="gallistdescriptioneven">{$galleries[changes].description}&nbsp;</td>
  <td class="gallistcreatedeven">{$galleries[changes].created|date_format:"%d of %b, %Y [%H:%M]"}&nbsp;</td>
  <td class="gallistlastModifeven">{$galleries[changes].lastModif|date_format:"%a %d of %b [%H:%M]"}&nbsp;</td>
  <!--<td class="gallistthemeeven">{$galleries[changes].theme}&nbsp;</td>-->
  <td class="gallistusereven">{$galleries[changes].user}&nbsp;</td>
  <td class="gallistimageseven">{$galleries[changes].images}&nbsp;</td>
  <td class="gallisthitseven">{$galleries[changes].hits}&nbsp;</td>
  <td class="gallistactionseven">
  {if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_galleries eq 'y' ) }
    <a class="gallink" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}">Edit</a>
    <a class="gallink" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}">Remove</a>
  {/if}
  {/if}
  {if $tiki_p_upload_images eq 'y'}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_upload_images eq 'y' ) }
  {if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
    <a class="gallink" href="tiki-upload_image.php?galleryId={$galleries[changes].id}">Upload</a>
  {/if}
  {/if}
  {/if}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_image_gallery eq 'y' ) }
  <a class="gallink" href="tiki-list_gallery.php?galleryId={$galleries[changes].id}">{tr}List{/tr}</a>
  {/if}
  {if $tiki_p_admin eq 'y'}
    {if $galleries[changes].individual eq 'y'}({/if}<a class="gallink" href="tiki-objectpermissions.php?objectName=gallery%20{$galleries[changes].name}&amp;objectType=image%20gallery&amp;permType=image%20galleries&amp;objectId={$galleries[changes].id}">{tr}perms{/tr}</a>{if $galleries[changes].individual eq 'y'}){/if}
  {/if}
  </td>
{/if}
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
[<a class="galprevnext" href="tiki-galleries.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="galprevnext" href="tiki-galleries.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
