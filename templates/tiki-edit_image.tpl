<a href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$imageId}" class="pagetitle">{tr}Edit Image{/tr}</a><br /><br />
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
<tr class="formcolor"><td>{tr}Image Name{/tr}:</td><td><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Image Description{/tr}:</td><td><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></tr></td>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="editimage" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
<br />
<div class="linksinfo">
{tr}You can view this image in your browser using{/tr}: <a class="gallink" href="{$url_browse}?imageId={$imageId}">{$url_browse}?imageId={$imageId}</a><br />
{tr}You can include the image in an HTML or Tiki page using{/tr} &lt;img src="{$url_show}?id={$imageId}" /&gt;
</div>
</div>
