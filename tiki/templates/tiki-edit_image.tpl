<a href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$imageId}" class="pagetitle">{tr}Edit Image{/tr}</a><br/><br/>
<div align="center">
{if $show eq 'y'}
<br/>
<hr>
<h2>{tr}Edit succesful!{/tr}</h2>
<h3>{tr}The following image was succesfully edited{/tr}:</h3>
<hr>
<br/>
{/if}
<img alt="image" src="show_image.php?id={$imageId}" /><br><br>
<form enctype="multipart/form-data" action="tiki-edit_image.php" method="post">
<input type="hidden" name="edit" value="{$imageId}">
<input type="hidden" name="galleryId" value="{$galleryId}">
<table class="normal">
<tr><td class="formcolor">{tr}Image Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name}" /></td></tr>
<tr><td class="formcolor">{tr}Image Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description">{$description}</textarea></tr></td>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="editimage" value="{tr}edit{/tr}" /></td></tr>
</table>
</form>
<br>
<div class="linksinfo">
{tr}You can view this image in your browser using{tr}: <a class="gallink" href="{$url_browse}?imageId={$imageId}">{$url_browse}?imageId={$imageId}</a><br/>
{tr}You can include the image in an HTML or Tiki page using{/tr} &lt;img src="{$url_show}?id={$imageId}" /&gt;
</div>
</div>
</div>
