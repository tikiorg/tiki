<a href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$imageId}" class="pagetitle">{tr}Edit Image{/tr}</a><br /><br />
<a class="linkbut" href="tiki-browse_gallery.php?galleryId={$galleryId}">{tr}return to gallery{/tr}</a>
<a class="linkbut" href="tiki-browse_image.php?imageId={$imageId}">{tr}browse image{/tr}</a>
<br /><br />
<div align="center">
{if $show eq 'y'}
<br />
<hr/>
<h2>{tr}Edit successful!{/tr}</h2>
<h3>{tr}The following image was successfully edited{/tr}:</h3>
<hr/>
<br />
{/if}
<img alt="image" src="show_image.php?id={$imageId}" /><br /><br />
<form enctype="multipart/form-data" action="tiki-edit_image.php" method="post">
<input type="hidden" name="edit" value="{$imageId|escape}">
<input type="hidden" name="galleryId" value="{$galleryId|escape}">
<table class="normal">
<tr><td class="formcolor">{tr}Image Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Image Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></tr></td>
{include file=categorize.tpl}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="editimage" value="{tr}save{/tr}" />&nbsp;&nbsp;<a class="link" href="tiki-browse_image.php?imageId={$imageId}">{tr}cancel edit{/tr}</a></td></tr>
</table>
</form>
<br />
<table class="normal">
<tr>
   	<td class="even">
	<small>
	{tr}You can view this image in your browser using{/tr}: <a class="gallink" href="{$url_browse}?imageId={$imageId}">{$url_browse}?imageId={$imageId}</a><br/>
	</small>
	</td>
</tr>
<tr>
	<td class="even">
	<small>
	{tr}You can include the image in an HTML or Tiki page using{/tr} &lt;img src="{$url_show}?id={$imageId}" /&gt;
	</small>
	</td>
</tr>
</table>
</div>
