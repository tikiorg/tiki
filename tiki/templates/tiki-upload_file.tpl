<a href="tiki-upload_file.php?galleryId={$galleryId}" class="pagetitle">{tr}Upload File{/tr}</a><br/><br/>
<a href="tiki-list_file_gallery.php?galleryId={$galleryId}" class="link">{tr}Browse gallery{/tr}</a><br/><br/>
<div align="center">
<form enctype="multipart/form-data" action="tiki-upload_file.php" method="post">
<table class="normal">
<tr><td class="formcolor">{tr}File Title{/tr}:</td><td class="formcolor"><input type="text" name="name" /></td></tr>
<tr><td class="formcolor">{tr}File Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description"></textarea></tr></td>
<tr><td class="formcolor">{tr}File Gallery{/tr}:</td><td class="formcolor"> 
<select name="galleryId">
{section name=idx loop=$galleries}
{if ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_upload_files eq 'y')}
<option  value="{$galleries[idx].id}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
{/if}
{/section}
</select></td></tr>
<!--<tr><td colspan="2"><b>{tr}Now enter the file URL{/tr}{tr} or upload a local file from your disk{/tr}
<tr><td class="formcolor">URL:</td><td><input size="50" type="text" name="url" /></td></tr>-->
<tr><td class="formcolor">{tr}Upload from disk:{/tr}</td><td class="formcolor">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
<input name="userfile1" type="file"></td></tr>
<tr><td class="formcolor">{tr}Batch upload{/tr}</td><td class="formcolor">
<input type="checkbox" name="isbatch" /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>
</div>
{if $show eq 'y'}
<br/>
<hr>
<h2>{tr}Upload succesful!{/tr}</h2>
<h3>{tr}The following file was succesfully uploaded{/tr}:</h3><br/>
<div align="center">
{$upload_name} ({$upload_size} bytes)<br/>
<div class="wikitext">
{tr}You can download this file using{/tr}: <a class="link" href="{$url_browse}?fileId={$fileId}">{$url_browse}?fileId={$fileId}</a><br/><br/>
{tr}You can include the file in an HTML/Tiki page using{/tr}: <textarea cols="60" rows="2">&lt;a href="{$url_browse}?fileId={$fileId}"&gt;{$upload_name} ({$upload_size} bytes)&lt;/a&gt;</textarea>
</div>
</div>
{/if}



