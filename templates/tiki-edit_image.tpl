<h1><a href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$imageId}" class="pagetitle">{tr}Edit Image{/tr}</a></h1>
<a class="linkbut" href="tiki-browse_gallery.php?galleryId={$galleryId}">{tr}Return to Gallery{/tr}</a>
<a class="linkbut" href="tiki-browse_image.php?imageId={$imageId}">{tr}Browse Images{/tr}</a>
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
<img src="show_image.php?id={$imageId}" alt="{tr}Image{/tr}" /><br /><br />
<form enctype="multipart/form-data" action="tiki-edit_image.php" method="post">
<input type="hidden" name="edit" value="{$imageId|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="galleryId" value="{$galleryId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Image Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Image Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></td></tr>
{if $prefs.feature_maps eq 'y' and $gal_info.geographic eq 'y'}
<tr><td class="formcolor">{tr}Latitude (WGS84/decimal degrees){/tr}:</td><td class="formcolor"><input type="text" name="lat" value="{$lat|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Longitude (WGS84/decimal degrees){/tr}:</td><td class="formcolor"><input type="text" name="lon" value="{$lon|escape}" /></td></tr>
{/if}
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Upload from disk to change the image:{/tr}</td><td class="formcolor">{$filename}<br /><input name="userfile" type="file" />
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="editimage" value="{tr}Save{/tr}" />&nbsp;&nbsp;<input type="submit" name="editimage_andgonext" value="{tr}Save and Go Next{/tr}" />&nbsp;&nbsp;<a class="link" href="tiki-browse_image.php?imageId={$imageId}">{tr}Cancel Edit{/tr}</a></td></tr>
</table>
</form>
<br />
<br /><br />    
  <table class="normal">
  <tr>
  	<td class="even">
  	<small>
    {tr}You can view this image in your browser using{/tr}:<br /><br />
    <a class="gallink" href="{$url_browse}?imageId={$imageId}">{$url_browse}?imageId={$imageId}</a><br />
    </small>
    </td>
  </tr>
  <tr>
    <td class="even">
    <small>
    {tr}You can include the image in an HTML page using one of these lines{/tr}:<br /><br />
    &lt;img src="{$url_show}?id={$imageId}" /&gt;<br />
    &lt;img src="{$url_show}?name={$name|escape}" /&gt;<br />
    </small>
    </td>
  </tr>
  <tr>
    <td class="even">
    <small>
    {tr}You can include the image in a tiki page using one of these lines{/tr}:<br /><br />
    {literal}{{/literal}img src={$url_show}?id={$imageId} {literal}}{/literal}<br />
    {literal}{{/literal}img src={$url_show}?name={$name|escape} {literal}}{/literal}<br />
    </small>
    </td>
  </tr>
  </table>
</div>
