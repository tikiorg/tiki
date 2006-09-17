{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-gal.tpl,v 1.27 2006-09-17 09:27:47 ohertel Exp $ *}

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To add/remove image galleries, go to "Image Galleries" on the application menu, or{/tr} <a class="rbox-link" href="tiki-galleries.php">{tr}click here{/tr}</a>.</div>
<div class="rbox-data" name="tip">{tr}You can upload images of a size of {/tr}{$max_img_upload_size|kbsize}. {tr}Alter the php.ini variables file_uploads, upload_max_filesize, post_max_size and database variables (max_allowed_packet for mysql) to change this value{/tr}.</div>
</div>
<br />

<div class="cbox">
<div class="cbox-title">{tr}Home Gallery{/tr}</div>
<div class="cbox-data">
<form action="tiki-admin.php?page=gal" method="post">
<table class="admin">
<tr class="form"><td><label>{tr}Home Gallery (main gallery){/tr}</label></td><td>
<select name="home_gallery">
{section name=ix loop=$galleries}
<option value="{$galleries[ix].galleryId|escape}" {if $galleries[ix].galleryId eq $home_gallery}selected="selected"{/if}>{$galleries[ix].name|truncate:20:"...":true}</option>
{/section}
</select></td>
<td><input type="submit" name="galset" value="{tr}ok{/tr}" /></td></tr>
</table>
</form>
</div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Galleries features{/tr}</div>
  <div class="cbox-data">
    <form action="tiki-admin.php?page=gal" method="post">
    <table class="admin">
    <tr class="form"><td><label>{tr}Rankings{/tr}:</label></td><td><input type="checkbox" name="feature_gal_rankings" {if $feature_gal_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr class="form"><td><label>{tr}Comments{/tr}:</label></td><td><input type="checkbox" name="feature_image_galleries_comments" {if $feature_image_galleries_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr class="form"><td><label>{tr}Uses Slideshow{/tr}:</label></td><td><input type="checkbox" name="feature_gal_slideshow" {if $feature_gal_slideshow eq 'y'}checked="checked"{/if}/></td></tr>
    <tr class="form"><td><label>{tr}Use database to store images{/tr}:</label></td><td><input type="radio" name="gal_use_db" value="y" {if $gal_use_db eq 'y'}checked="checked"{/if}/></td></tr>
    <tr class="form"><td><label>{tr}Use a directory to store images{/tr}:</label></td><td><input type="radio" name="gal_use_db" value="n" {if $gal_use_db eq 'n'}checked="checked"{/if}/> <label>{tr}Directory path{/tr}:</label><br /><input type="text" name="gal_use_dir" value="{$gal_use_dir|escape}" size="50" /><br />
       ({tr}Note: if you change this directory, you have to move the contents to the new directory. You can also use the 'Mover' below.{/tr})</td></tr>
    <tr class="form"><td><label>{tr}Library to use for processing images{/tr}:</label></td><td><input type="radio" name="gal_use_lib" value="gd" {if $gal_use_lib ne 'imagick'}checked="checked"{/if}/>GD: {$gdlib}</td></tr>
    <tr class="form"><td></td><td><input type="radio" name="gal_use_lib" value="imagick" {if $gal_use_lib eq 'imagick'}checked="checked"{/if}/>Imagick: {$imagicklib}</td></tr>
    <tr class="form"><td><label>{tr}Uploaded image names must match regex{/tr}:</label></td><td><input type="text" name="gal_match_regex" value="{$gal_match_regex|escape}"/></td></tr>
    <tr class="form"><td><label>{tr}Uploaded image names cannot match regex{/tr}:</label></td><td><input type="text" name="gal_nmatch_regex" value="{$gal_nmatch_regex|escape}"/></td></tr>
		<tr><td colspan="2"><b>{tr}Directory Batch Loading{/tr}</b><br />
		{tr}If you enable Directory Batch Loading, you need to setup a web-readable directory (outside of your web space is better). Then setup a way to upload images in that dir, either by scp, ftp, or other protocols{/tr}</td></tr>
    <tr class="form"><td><label>{tr}Enable directory batch loading{/tr}:</label></td><td><input type="checkbox" name="feature_gal_batch" {if $feature_gal_batch eq 'y'}checked="checked"{/if}/></td></tr>
    <tr class="form"><td><label>{tr}Batch loading directory{/tr}:</label></td><td><input type="text" name="gal_batch_dir" value="{$gal_batch_dir|escape}" size="50" /></td></tr>
    <tr class="form"><td><label>{tr}Enable cache images to all galleries{/tr}:</label></td><td><input type="checkbox" name="feature_gal_imgcache" {if $feature_gal_imgcache eq 'y'}checked="checked"{/if}/></td></tr>
     <tr class="form"><td><label>{tr}Images cache directory{/tr}:</label></td><td><input type="text" name="gal_imgcache_dir" value="{$gal_imgcache_dir|escape}" size="50" /></td></tr>
{if $feature_categories eq 'y'}
    <tr><td class="form">{tr}Mandatory category in the category tree{/tr}</td>
    <td class="form"><select name="feature_image_gallery_mandatory_category">
	<option value="-1" {if $feature_image_gallery_mandatory_category eq -1 or $feature_image_gallery_mandatory_category eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
	<option value="0" {if $feature_image_gallery_mandatory_category eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
	{section name=ix loop=$catree}
	<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $feature_image_gallery_mandatory_category}selected="selected"{/if}>{$catree[ix].categpath}</option>
	{/section}
	</select>
</td></tr>
{/if}
    <tr><td colspan="2" class="button"><input type="submit" name="galfeatures" value="{tr}Set features{/tr}" /></td></tr>
    </table>
    </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Exterminator{/tr}</div>
  <div class="cbox-data">
<form action="tiki-admin.php?page=gal" method="post">
<table class="admin">
<tr class="form"><td><label>
{tr}Remove images in the system gallery not being used in Wiki pages, articles or blog posts{/tr}
</label>
<input type="hidden" name="rmvorphimg" value="1" /></td><!--/tr>
<tr><td colspan="2" class="button"--><td><input type="submit" name="button" value="{tr}Remove{/tr}" /></td></tr>
</table>                 
</form>
</div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Mover{/tr}</div>
    <div class="cbox-data">
    <form action="tiki-admin.php?page=gal" method="post">
    <table class="admin">
    <tr class="form"><td><label>
{if $gal_use_db eq 'n'}
{tr}Move images from database storage to filesystem storage{/tr}
</label>
<input type="hidden" name="mvimg" value="to_fs"></td>
{else}
{tr}Move images from filesystem storage to database storage{/tr}
</label>
<input type="hidden" name="mvimg" value="to_db"></td>
{/if}
<td>
<select name="move_gallery">
<option value="-1">{tr}All galleries{/tr}</option>
<option value="0">{tr}System gallery{/tr}</option>
{section name=ix loop=$galleries}
<option value="{$galleries[ix].galleryId|escape}">{$galleries[ix].name|truncate:20:"...":true}</option>
{/section}
</select></td>
<td><input type="submit" name="button" value="{tr}Move{/tr}" /></td></tr>
{if $gal_use_db eq 'n'}
<tr class="form">
<td><label>{tr}Move images from old filesystem store to new directory{/tr}
<input type="hidden" name="newdir" value="to_newdir"></label>
<td>
<input type="text" name="gal_use_dir" value="{$gal_use_dir|escape}" size="50" />
<td><input type="submit" name="button" value="{tr}Move{/tr}" /></td></tr>
{/if}
{if $movedimgs}
<tr class="form">
<td colspan="3">{tr}Moved{/tr} {$movedimgs} {tr}images{/tr}</td>
</tr>
{/if}
</table>
</form>
</div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Gallery listing configuration{/tr}</div>
  <div class="cbox-data">
	<form method="post" action="tiki-admin.php?page=gal">
	<table class="admin">
	<tr class="form">
		<td><label>{tr}Name{/tr}</label></td>
		<td><input type="checkbox" name="gal_list_name" {if $gal_list_name eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr class="form">
		<td><label>{tr}Description{/tr}</label></td>
		<td><input type="checkbox" name="gal_list_description" {if $gal_list_description eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr class="form">
		<td><label>{tr}Created{/tr}</label></td>
		<td><input type="checkbox" name="gal_list_created" {if $gal_list_created eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr class="form">
		<td><label>{tr}Last modified{/tr}</label></td>
		<td><input type="checkbox" name="gal_list_lastmodif" {if $gal_list_lastmodif eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr class="form">
		<td><label>{tr}User{/tr}</label></td>
		<td><input type="checkbox" name="gal_list_user" {if $gal_list_user eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr class="form">
		<td><label>{tr}Images{/tr}</label></td>
		<td><input type="checkbox" name="gal_list_imgs" {if $gal_list_imgs eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr class="form">
		<td><label>{tr}Visits{/tr}</label></td>
		<td><input type="checkbox" name="gal_list_visits" {if $gal_list_visits eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr><td colspan="2" class="button"><input type="submit" name="imagegallistprefs" value="{tr}Change configuration{/tr}" /></td></tr>
	</table>	
	</form>	
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Image galleries comments settings{/tr}</div>
  <div class="cbox-data">
    <form method="post" action="tiki-admin.php?page=gal">
    <table class="admin">
    <tr class="form"><td><label>{tr}Default number of comments per page{/tr}: </label></td><td><input size="5" type="text" name="image_galleries_comments_per_page" value="{$image_galleries_comments_per_page|escape}" /></td></tr>
    <tr class="form"><td><label>{tr}Comments default ordering{/tr}</label>
    </td><td>
    <select name="image_galleries_comments_default_order">
    <option value="commentDate_desc" {if $image_galleries_comments_default_order eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
		<option value="commentDate_asc" {if $image_galleries_comments_default_order eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
    <option value="points_desc" {if $image_galleries_comments_default_order eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="imagegalcomprefs" value="{tr}Change settings{/tr}" /></td></tr>
    </table>
    </form>
  </div>
</div>
