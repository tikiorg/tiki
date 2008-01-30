{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-galleries.tpl,v 1.71.2.7 2008-01-30 15:33:51 nyloth Exp $ *}

<h1><a href="tiki-galleries.php" class="pagetitle">{tr}Galleries{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Image+Galleries" target="tikihelp" class="tikihelp" title="{tr}Image Gallery{/tr}" >
{icon _id='help'}</a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-galleries.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}galleries tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit Tpl{/tr}'}</a>
{/if}
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=gal" class="tikihelp">{icon _id='wrench' alt="{tr}Admin Feature{/tr}"}</a>
{/if}</h1>

{if $tiki_p_create_galleries eq 'y'}
{if $edit_mode ne 'y' or $galleryId ne 0}<div class="navbar"><a class="linkbut" href="tiki-galleries.php?edit_mode=1&amp;galleryId=0">{tr}Create New Gallery{/tr}</a></div>{/if}
{if $edit_mode eq 'y'}
{if $galleryId eq 0}
<h2>{tr}Create a gallery{/tr}</h2>
{else}
<h2>{tr}Edit this gallery:{/tr} {$name}</h2>
{/if}
{if $category_needed eq 'y'}
<div class="simplebox hoghlight">{tr}A category is mandatory{/tr}</div>
{/if}

<div style="text-align: center">
{if $individual eq 'y'}
<a class="gallink" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=image+gallery&amp;permType=image+galleries&amp;objectId={$galleryId}">{tr}There are individual permissions set for this gallery{/tr}</a>
{/if}
<form action="tiki-galleries.php" method="post" id="gal-edit-form">
<input type="hidden" name="galleryId" value="{$galleryId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}"/></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:<br />{include file="textareasize.tpl" area_name='gal-desc' formId='gal-edit-form'}</td><td class="formcolor"><textarea   rows="{$rows}" cols="{$cols}" name="description" id="gal-desc">{$description|escape}</textarea></td></tr>
{if $tiki_p_admin_galleries eq 'y'}
<tr><td class="formcolor">{tr}Gallery is visible to non-admin users?{/tr}</td><td class="formcolor"><input type="checkbox" name="visible" {if $visible eq 'y'}checked="checked"{/if} /></td></tr>
{* If a user can create a gallery, but doesn't have tiki_p_admin_galleries the new gallery needs to be visible. *}
{else}
<input type="hidden" name="visible" value="on" />
{/if}
{if $prefs.feature_maps eq 'y'}
<tr><td class="formcolor">{tr}Geographic{/tr}:</td><td class="formcolor"><input type="checkbox" name="geographic" {if $geographic eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
{if $prefs.preset_galleries_thumb ne 'y'}
<tr><td class="formcolor">{tr}Max Rows per page{/tr}:</td><td class="formcolor"><input type="text" name="maxRows" value="{$maxRows|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Images per row{/tr}:</td><td class="formcolor"><input type="text" name="rowImages" value="{$rowImages|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Thumbnails size X{/tr}:</td><td class="formcolor"><input type="text" name="thumbSizeX" value="{$thumbSizeX|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Thumbnails size Y{/tr}:</td><td class="formcolor"><input type="text" name="thumbSizeY" value="{$thumbSizeY|escape}" /></td></tr>
{/if}
<tr><td class="formcolor">{tr}Default sort order{/tr}:</td><td class="formcolor"><select name="sortorder">
{foreach from=$options_sortorder key=key item=item}
<option value="{$item}" {if $sortorder == $item} selected="selected"{/if}>{$key}</option>
{/foreach}
</select>
<input type="radio" name="sortdirection" value="desc" {if $sortdirection == 'desc'}checked="checked"{/if} />{tr}descending{/tr}
<input type="radio" name="sortdirection" value="asc" {if $sortdirection == 'asc'}checked="checked"{/if} />{tr}ascending{/tr}
</td></tr>
<tr><td class="formcolor">{tr}Fields to show during browsing the gallery{/tr}:</td>
<td class="formcolor">
	<input type="checkbox" name="showname" value="y" {if $showname=='y'}checked="checked"{/if} />{tr}Name{/tr}<br />
	<input type="checkbox" name="showimageid" value="y" {if $showimageid=='y'}checked="checked"{/if} />{tr}Image ID{/tr}<br />
	<input type="checkbox" name="showdescription" value="y" {if $showdescription=='y'}checked="checked"{/if} />{tr}Description{/tr}<br />
	<input type="checkbox" name="showcreated" value="y" {if $showcreated=='y'}checked="checked"{/if} />{tr}Creation Date{/tr}<br />
	<input type="checkbox" name="showuser" value="y" {if $showuser=='y'}checked="checked"{/if} />{tr}User{/tr}<br />
	<input type="checkbox" name="showhits" value="y" {if $showhits=='y'}checked="checked"{/if} />{tr}Hits{/tr}<br />
	<input type="checkbox" name="showxysize" value="y" {if $showxysize=='y'}checked="checked"{/if} />{tr}XY-Size{/tr}<br />
	<input type="checkbox" name="showfilesize" value="y" {if $showfilesize=='y'}checked="checked"{/if} />{tr}Filesize{/tr}<br />
	<input type="checkbox" name="showfilename" value="y" {if $showfilename=='y'}checked="checked"{/if} />{tr}Filename{/tr}<br />
</td></tr>
<tr><td class="formcolor">{tr}Gallery Image{/tr}:</td><td class="formcolor"><select name="galleryimage">
{foreach from=$options_galleryimage key=key item=item}
<option value="{$item}" {if $galleryimage == $item} selected="selected"{/if}>{$key}</option>
{/foreach}
</select>
</td></tr>
<tr><td class="formcolor">{tr}Parent gallery{/tr}:</td><td class="formcolor"><select name="parentgallery">
<option value="-1" {if $parentgallery == -1} selected="selected"{/if}>{tr}none{/tr}</option>
{foreach from=$galleries_list key=key item=item}
<option value="{$item.galleryId}" {if $parentgallery == $item.galleryId} selected="selected"{/if}>{$item.name}</option>
{/foreach}
</select>
</td></tr>
{if $prefs.preset_galleries_scale ne 'y'}
<tr><td class="formcolor">{tr}Available scales{/tr}:</td><td class="formcolor">

{tr}Global default{/tr} {$prefs.scaleSizeGalleries}x{$prefs.scaleSizeGalleries} ({tr}Bounding box{/tr}) <input type="radio" name="defaultscale" value="{$prefs.scaleSizeGalleries}" {if $defaultscale==$prefs.scaleSizeGalleries}checked="checked"{/if} />{tr}default scale{/tr}<br />

{section  name=scales loop=$scaleinfo}
{if $scaleinfo[scales].scale ne $prefs.scaleSizeGalleries}
{tr}Remove{/tr}:<input type="checkbox" name="removescale_{$scaleinfo[scales].scale|escape}" />
{$scaleinfo[scales].scale}x{$scaleinfo[scales].scale} ({tr}Bounding box{/tr}) <input type="radio" name="defaultscale" value="{$scaleinfo[scales].scale}" {if $defaultscale==$scaleinfo[scales].scale}checked="checked"{/if} />{tr}default scale{/tr}<br />
{/if}
{sectionelse}
{tr}No scales available{/tr}
{/section}<br />
{tr}Original image is default scale{/tr}<input type="radio" name="defaultscale" value="o" {if $defaultscale=='o'}checked="checked"{/if} />
</td></tr>
<tr><td class="formcolor">{tr}Add scaled images with bounding box of square size{/tr}:</td><td class="formcolor"><input type="text" name="scaleSize" />{tr}pixels{/tr}</td></tr>
{else}
{$defaultscale=$prefs.scaleSizeGalleries}
{/if}

<tr><td class="formcolor">{tr}Owner of the gallery{/tr}:</td><td class="formcolor"><input type="text" name="owner" value="{$owner|escape}"/></td></tr>
{include file=categorize.tpl}
{include file=freetag.tpl}
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
{if $tiki_p_create_galleries eq 'y' && $galleryId ne 0}
<div class="navbar"><a class="linkbut" href="tiki-galleries.php?edit_mode=1&amp;galleryId=0">{tr}Create New Gallery{/tr}</a></div>
{/if}
<h2>{tr}Available Galleries{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-galleries.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<div>
<form action="tiki-galleries.php" method="get">
<select name="filter">
<option value="">{tr}Choose a filter{/tr}</option>
<option value="topgal"{if $filter eq 'topgal'} selected="selected"{/if}>{tr}Top{/tr}</option>
<option value="parentgal"{if $filter eq 'parentgal'} selected="selected"{/if}>{tr}Parent gallery{/tr}</option>
{*foreach key=fid item=field from=$listfields}
{if $field.isSearchable eq 'y' and $field.type ne 'f' and $field.type ne 'j' and $field.type ne 'i'}
<option value="{$fid}"{if $fid eq $filterfield} selected="selected"{/if}>{$field.name|truncate:65:"..."}</option>
{/if}
{/foreach*}
</select>
<input type="submit" value="{tr}Filter{/tr}" />
</form>
</div>

<table class="normal">
<tr>
{if $prefs.gal_list_name eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading">{tr}Parent{/tr}</td>
{/if}
{if $prefs.gal_list_description eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
{/if}
{if $prefs.gal_list_created eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
{/if}
{if $prefs.gal_list_lastmodif eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last modified{/tr}</a></td>
{/if}
{if $prefs.gal_list_user eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
{/if}
{if $prefs.gal_list_imgs eq 'y'}
<td style="text-align:right;"  class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'images_desc'}images_asc{else}images_desc{/if}">{tr}Imgs{/tr}</a></td>
{/if}
{if $prefs.gal_list_visits eq 'y'}
<td style="text-align:right;"  class="heading"><a class="tableheading" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></td>
{/if}
<td  class="heading">{tr}Actions{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$galleries}
{if ($filter eq 'topgal' and $galleries[changes].parentgallery eq -1) or ($filter eq 'parentgal' and $galleries[changes].parentgal eq 'y') or ($filter eq '')}
{if $galleries[changes].visible eq 'y' or $tiki_p_admin_galleries eq 'y'}
<tr>
{if $prefs.gal_list_name eq 'y'}
  <td class="{cycle advance=false}"><a class="galname" href="tiki-browse_gallery.php?galleryId={$galleries[changes].galleryId}">{$galleries[changes].name}</a></td><td class="{cycle advance=false}">
  {if $galleries[changes].parentgallery ne -1 }<a class="galname" href="tiki-browse_gallery.php?galleryId={$galleries[changes].parentgallery}">{$galleries[changes].parentgalleryName}</a>{/if}
  {if $galleries[changes].parentgal eq 'y'}<i>{tr}Parent{/tr}</i>{/if}
  </td>
{/if}
{if $prefs.gal_list_description eq 'y'}
  <td class="{cycle advance=false}">{$galleries[changes].description}</td>
{/if}
{if $prefs.gal_list_created eq 'y'}
  <td class="{cycle advance=false}">{$galleries[changes].created|tiki_short_datetime}</td>
{/if}
{if $prefs.gal_list_lastmodif eq 'y'}
  <td class="{cycle advance=false}">{$galleries[changes].lastModif|tiki_short_datetime}</td>
{/if}
{if $prefs.gal_list_user eq 'y'}
  <td class="{cycle advance=false}">{$galleries[changes].user|userlink}</td>
{/if}
{if $prefs.gal_list_imgs eq 'y'}
  <td style="text-align:right;" class="{cycle advance=false}">{$galleries[changes].images}</td>
{/if}
{if $prefs.gal_list_visits eq 'y'}
  <td style="text-align:right;" class="{cycle advance=false}">{$galleries[changes].hits}</td>
{/if}
  <td class="{cycle}" nowrap="nowrap">
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_image_gallery eq 'y' ) }
  <a class="gallink" href="tiki-list_gallery.php?galleryId={$galleries[changes].galleryId}">{icon _id='table' alt='{tr}List{/tr}'}</a>
  {/if}
  {if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
    {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_galleries eq 'y' ) }
      <a class="gallink" title="{tr}Edit{/tr}" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].galleryId}">
        {icon _id='page_edit'}
      </a>
    {/if}
  {/if}
  {if $tiki_p_upload_images eq 'y'}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_upload_images eq 'y' ) }
  {if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
    <a class="gallink" href="tiki-upload_image.php?galleryId={$galleries[changes].galleryId}">{icon _id='upload'}</a>
  {if ($galleries[changes].geographic eq 'y')}
    <a class="gallink" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;make_map=1&amp;galleryId={$galleries[changes].galleryId}">{icon _id='wrench' alt='{tr}Make Map{/tr}'}</a>
  {/if}
  {/if}
  {/if}
  {/if}
  {if $tiki_p_admin eq 'y'}
    {if $galleries[changes].individual eq 'y'}
	<a class="gallink" href="tiki-objectpermissions.php?objectName={$galleries[changes].name|escape:"url"}&amp;objectType=image+gallery&amp;permType=image+galleries&amp;objectId={$galleries[changes].galleryId}">{icon _id='key_active' alt='{tr}Active Perms{/tr}'}</a>
    {else}
	<a class="gallink" href="tiki-objectpermissions.php?objectName={$galleries[changes].name|escape:"url"}&amp;objectType=image+gallery&amp;permType=image+galleries&amp;objectId={$galleries[changes].galleryId}">{icon _id='key' alt='{tr}Perms{/tr}'}</a>
    {/if}
  {/if}
{if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
  {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_galleries eq 'y' ) }
    &nbsp;&nbsp;<a class="gallink" title="{tr}Delete{/tr}" href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].galleryId}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
  {/if}
  {/if}

  </td>
</tr>
{/if}
{/if}
{sectionelse}
<tr><td class="odd" colspan="9">
{tr}No records found{/tr}
</td></tr>
{/section}
</table>
<br />
{if $prefs.feature_maps eq 'y'}{$map_error}{/if}
{if $cant_pages gt 0}<div class="mini">
{if $prev_offset >= 0}
[<a class="galprevnext" href="tiki-galleries.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="galprevnext" href="tiki-galleries.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-galleries.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>{/if}
</div>
