<a href="tiki-galleries.php" class="pagetitle">{tr}Galleries{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=ImageGallery" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Image Gallery{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}


      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-galleries.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}galleries tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}


<br /><br />
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=gal"><img src='img/icons/config.gif' border='0'  alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" /></a>
<br /><br />
{/if}

{if $tiki_p_create_galleries eq 'y'}
{if $edit_mode eq 'y'}
{if $galleryId eq 0}
<h3>{tr}Create a Gallery{/tr}</h3>
{else}
<h3>{tr}Edit this Gallery:{/tr} {$name}</h3>
<a class="linkbut" href="tiki-galleries.php?edit_mode=1&amp;galleryId=0">[{tr}Create new gallery{/tr}]</a>
{/if}
<div align="center">
{if $individual eq 'y'}
<a class="gallink" href="tiki-objectpermissions.php?objectName=gallery%20{$name}&amp;objectType=image%20gallery&amp;permType=image%20galleries&amp;objectId={$galleryId}">{tr}There are individual permissions set for this gallery{/tr}</a>
{/if}
<form action="tiki-galleries.php" method="post">
<input type="hidden" name="galleryId" value="{$galleryId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}"/></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></td></tr>
<!--<tr><td class="formcolor">{tr}Theme{/tr}:</td><td class="formcolor"><select name="theme">
       <option value="default" {if $theme eq 'default'}selected="selected"{/if}>default</option>
       <option value="dark" {if $theme eq 'dark'}selected="selected"{/if}>dark</option>
       </select></td></tr>-->
{if $tiki_p_admin_galleries eq 'y'}       
<tr><td class="formcolor">{tr}Gallery is visible to non-admin users?{/tr}</td><td class="formcolor"><input type="checkbox" name="visible" {if $visible eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
<tr><td class="formcolor">{tr}Max Rows per page{/tr}:</td><td class="formcolor"><input type="text" name="maxRows" value="{$maxRows|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Images per row{/tr}:</td><td class="formcolor"><input type="text" name="rowImages" value="{$rowImages|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Thumbnails size X{/tr}:</td><td class="formcolor"><input type="text" name="thumbSizeX" value="{$thumbSizeX|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Thumbnails size Y{/tr}:</td><td class="formcolor"><input type="text" name="thumbSizeY" value="{$thumbSizeY|escape}" /></td></tr>      
<tr><td class="formcolor">{tr}Available scales{/tr}:</td><td class="formcolor">
{section  name=scales loop=$scaleinfo}
{tr}Remove{/tr}:<input type="checkbox" name="{$scaleinfo[scales].xsize|escape}x{$scaleinfo[scales].ysize}" />
{$scaleinfo[scales].xsize}x{$scaleinfo[scales].ysize}<br />
{sectionelse}
{tr}No scales available{/tr}
{/section}
</td></tr>
<tr><td class="formcolor">{tr}Add scaled images size X x Y{/tr}:</td><td class="formcolor"><input type="text" name="scaleSizeX" size=4 />Delete<input type="text" name="scaleSizeY" size=4 /></td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Other users can upload images to this gallery{/tr}:</td><td class="formcolor"><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" value="{tr}Save{/tr}" name="edit" /></td></tr>
</table>
</form>
</div>
<br />
{/if}
{/if}
{if $galleryId > 0}
{if $edited eq 'y'}
<div class="wikitext">
{tr}You can access the gallery using the following URL{/tr}: <a class="gallink" href="{$url}?galleryId={$galleryId}">{$url}?galleryId={$galleryId}</a>
</div>
{/if}
{/if}
<h2>{tr}Available Galleries{/tr}</h2>
{if $tiki_p_create_galleries eq 'y'}
<a class="linkbut" href="tiki-galleries.php?edit_mode=1&amp;galleryId=0">[{tr}Create new gallery{/tr}]</a><br /><br />
{/if}
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-galleries.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
{if $gal_list_name eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
{/if}
{if $gal_list_description eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
{/if}
{if $gal_list_created eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
{/if}
{if $gal_list_lastmodif eq 'y'} 
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a></td>
{/if}
<!--<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'theme_desc'}theme_asc{else}theme_desc{/if}">{tr}Theme{/tr}</a></td>-->
{if $gal_list_user eq 'y'} 
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
{/if}
{if $gal_list_imgs eq 'y'}   
<td style="text-align:right;"  class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'images_desc'}images_asc{else}images_desc{/if}">{tr}Images{/tr}</a></td>
{/if}
{if $gal_list_visits eq 'y'}   
<td style="text-align:right;"  class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></td>
{/if}
<td  class="heading">{tr}Actions{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$galleries}
{if $galleries[changes].visible eq 'y' or $tiki_p_admin_galleries eq 'y'}
<tr>
{if $gal_list_name eq 'y'}
  <td class="{cycle advance=false}"><a class="galname" href="tiki-browse_gallery.php?galleryId={$galleries[changes].id}">{$galleries[changes].name}</a></td>
{/if}
{if $gal_list_description eq 'y'}  
  <td class="{cycle advance=false}">{$galleries[changes].description}</td>
{/if}
{if $gal_list_created eq 'y'}  
  <td class="{cycle advance=false}">{$galleries[changes].created|tiki_short_datetime}</td>
{/if}
{if $gal_list_lastmodif eq 'y'} 
  <td class="{cycle advance=false}">{$galleries[changes].lastModif|tiki_short_datetime}</td>
{/if}  
  <!--<td class="{cycle advance=false}">{$galleries[changes].theme}</td>-->
{if $gal_list_user eq 'y'}   
  <td class="{cycle advance=false}">{$galleries[changes].user}</td>
{/if}  
{if $gal_list_imgs eq 'y'}   
  <td style="text-align:right;" class="{cycle advance=false}">{$galleries[changes].images}</td>
{/if}
{if $gal_list_visits eq 'y'}     
  <td style="text-align:right;" class="{cycle advance=false}">{$galleries[changes].hits}</td>
{/if}  
  <td class="{cycle}">
  {if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_galleries eq 'y' ) }
    <a class="gallink" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}"><img src='img/icons/edit.gif' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' border='0' /></a>
    <a class="gallink" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this image gallery?{/tr}')" ><img src='img/icons2/delete.gif' border='0' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' /></a>
  {/if}
  {/if}
  {if $tiki_p_upload_images eq 'y'}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_upload_images eq 'y' ) }
  {if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
    <a class="gallink" href="tiki-upload_image.php?galleryId={$galleries[changes].id}"><img src='img/icons2/upload.gif' border='0' alt='{tr}Upload{/tr}' title='{tr}Upload{/tr}' /></a>
  {/if}
  {/if}
  {/if}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_image_gallery eq 'y' ) }
  <a class="gallink" href="tiki-list_gallery.php?galleryId={$galleries[changes].id}"><img border='0' src='img/icons/ico_table.gif' title='{tr}List{/tr}' alt='{tr}List{/tr}' /></a>
  {/if}
  {if $tiki_p_admin eq 'y'}
    {if $galleries[changes].individual eq 'y'}({/if}<a class="gallink" href="tiki-objectpermissions.php?objectName=gallery%20{$galleries[changes].name}&amp;objectType=image%20gallery&amp;permType=image%20galleries&amp;objectId={$galleries[changes].id}"><img src='img/icons/key.gif' alt='{tr}perms{/tr}' title='{tr}perms{/tr}' border='0' /></a>{if $galleries[changes].individual eq 'y'}){/if}
  {/if}
  </td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="galprevnext" href="tiki-galleries.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="galprevnext" href="tiki-galleries.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-galleries.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
