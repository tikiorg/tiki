{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-upload_image.tpl,v 1.2 2004-01-17 01:20:31 musus Exp $ *}
<a href="tiki-upload_image.php?galleryId={$galleryId}" class="pagetitle">{tr}Upload Image{/tr}</a><br /><br />

{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=ImageGallery" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Image Gallery{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-upload_image.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Image Gallery tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>
{/if}

<br /><br />

{if $galleryId ne ''}
	<a href="tiki-browse_gallery.php?galleryId={$galleryId}" class="linkbut">
{else}
	<a href="tiki-galleries.php" class="linkbut">
{/if}
{tr}Browse gallery{/tr}</a><br /><br />
{if count($galleries) > 0}
	<div align="center">
	<form enctype="multipart/form-data" action="tiki-upload_image.php" method="post">
	<table>
	<tr><td>{tr}Image Name{/tr}:</td><td><input type="text" name="name" /> {tr}use filename{/tr}:<input type="checkbox" name="use_filename" /></td></tr>
	<tr><td>{tr}Image Description{/tr}:</td><td><textarea rows="5" cols="40" name="description"></textarea></td></tr>
	<tr><td>{tr}Gallery{/tr}:</td><td> 
	<select name="galleryId">
	{section name=idx loop=$galleries}
	{if ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_upload_images eq 'y')}
	<option  value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
	{/if}
	{/section}
	</select></td></tr>
	<tr><td  colspan="2"><b>{tr}Now enter the image URL{/tr}{tr} or upload a local image from your disk{/tr}
	<tr><td>URL:</td><td><input size="50" type="text" name="url" /></td></tr>
	<tr><td>{tr}Upload from disk{/tr}:</td><td>
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
	<input name="userfile1" type="file" />
	</td></tr>
	<tr><td>{tr}Thumbnail (optional, overrides automatic thumbnail generation){/tr}:</td><td>
	<input name="userfile2" type="file" />
	</td></tr>
	<tr><td>&nbsp;</td><td><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
	</table>
	</form>
	</div>
	{if $show eq 'y'}
	<br />
	<hr/>
	<h2>{tr}Upload successful!{/tr}</h2>
	<h3>{tr}The following image was successfully uploaded{/tr}:</h3>
	<div align="center">
	<img src="{$url_show}?id={$imageId}" alt='{tr}Image ID{/tr}'/><br />
	<b>{tr}Thumbnail{/tr}:</b><br />
	<img src="{$url_show}?id={$imageId}&amp;thumb=1"
		alt='{tr}Image ID thumb{/tr}'/><br /><br />
	<div class="wikitext">
	{tr}You can view this image in your browser using{/tr}: <a href="{$url_browse}?imageId={$imageId}">{$url_browse}?imageId={$imageId}</a><br /><br />
	{tr}You can include the image in an Wiki page using{/tr}:  <textarea rows="3" cols="60">{literal}{{/literal}img src="{$url_show}?id={$imageId}"{literal}}{/literal}</textarea>
	</div>
	</div>
	{/if}
{else}
	{tr}You have to create a gallery first!{/tr}
{/if}


