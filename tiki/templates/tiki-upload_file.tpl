<h1>{tr}Upload File{/tr}</h1>
<div align="center">
<form enctype="multipart/form-data" action="tiki-upload_file.php" method="post">
<table>
<tr><td class="form">{tr}File Title{/tr}:</td><td class="form"><input type="text" name="name" /></td></tr>
<tr><td class="form">{tr}File Description{/tr}:</td><td class="form"><textarea rows="5" cols="40" name="description"></textarea></tr></td>
<tr><td class="form">{tr}File Gallery{/tr}:</td><td> 
<select name="galleryId">
{section name=idx loop=$galleries}
<option  value="{$galleries[idx].id}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
{/section}
</select></td></tr>
<!--<tr><td colspan="2"><b>{tr}Now enter the file URL{/tr}{tr} or upload a local file from your disk{/tr}
<tr><td class="form">URL:</td><td><input size="50" type="text" name="url" /></td></tr>-->
<tr><td class="form">{tr}Upload from disk:{/tr}</td><td class="form">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
<input name="userfile1" type="file"></td></tr>
<tr><td>&nbsp;</td><td class="form"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>
</div>
{if $show eq 'y'}
<br/>
<hr>
<h2>{tr}Upload succesful!{/tr}</h2>
<h3>{tr}The following file was succesfully uploaded{/tr}:</h3>
<div align="center">
{$upload_name} ({$upload_size} bytes)<br/>
<div class="wikitext">
{tr}You can download this file using{/tr}: <a href="http://{$url_browse}?fileId={$fileId}">http://{$url_browse}?fileId={$fileId}</a><br/>
{tr}You can include the file in an HTML/Tiki page using{/tr} &lt;a href="http://{$url_browse}?id={$fileId}"&gt;{$upload_name} ({$upload_size} bytes)&lt;/a&gt;
</div>
</div>
{/if}



