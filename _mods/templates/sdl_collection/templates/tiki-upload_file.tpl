<a href="tiki-upload_file.php?galleryId={$galleryId}" class="pagetitle">{tr}Upload File{/tr}</a><br /><br />
{if count($galleries) > 0}
	<a href="tiki-list_file_gallery.php?galleryId={$galleryId}" class="linkbut">[{tr}Browse Gallery{/tr}]</a><br /><br />
	<div align="center">
	<form enctype="multipart/form-data" action="tiki-upload_file.php" method="post">
	<table class="normal">
	<tr><td class="formcolor">{tr}File Title{/tr}:</td><td class="formcolor"><input type="text" name="name" /></td></tr>
	<tr><td class="formcolor">{tr}File Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description"></textarea></td></tr>
	<tr><td class="formcolor">{tr}File Gallery{/tr}:</td><td class="formcolor"> 
	<select name="galleryId">
	{section name=idx loop=$galleries}
	{if ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_upload_files eq 'y')}
	<option  value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
	{/if}
	{/section}
	</select></td></tr>
	<!--<tr><td colspan="2"><b>{tr}Now enter the file URL{/tr}{tr} or upload a local file from your disk{/tr}
	<tr><td class="formcolor">URL:</td><td><input size="50" type="text" name="url" /></td></tr>-->
	<tr><td class="formcolor">	{tr}Upload from disk:{/tr}</td>
	<td class="formcolor">
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
		<input name="userfile1" type="file" />
		<input name="userfile2" type="file" />
		<br />
		<input name="userfile3" type="file" />
		<input name="userfile4" type="file" />
		<br />
		<input name="userfile5" type="file" />
		<input name="userfile6" type="file" />
	</td></tr>
	<tr><td class="formcolor">{tr}Batch Upload{/tr}</td><td class="formcolor">
	<input type="checkbox" name="isbatch" /></td></tr>
	<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="upload" value="{tr}Upload{/tr}" /></td></tr>
	</table>
	</form>
	</div>
	<br />
	<hr/>
	{if count($errors) > 0}
		<h3>{tr}Errors detected{/tr}</h3>
		{section name=ix loop=$errors}
			{$errors[ix]}<br />
		{/section}
	{/if}
	
	{if count($uploads) > 0}
		<h3>{tr}The following file was successfully uploaded{/tr}:</h3><br />
		{section name=ix loop=$uploads}
			<div align="center">
				{$uploads[ix].name} ({$uploads[ix].size|kbsize})<br />
				<div class="wikitext">
					{tr}You can download this file using{/tr}: <a class="link" href="{$url_browse}?fileId={$uploads[ix].fileId}">{$url_browse}?fileId={$uploads[ix].fileId}</a><br /><br />
					{tr}You can include the file in an HTML/Tiki page using{/tr}: <textarea cols="60" rows="2">&lt;a href="{$url_browse}?fileId={$uploads[ix].fileId}"&gt;{$uploads[ix].name} ({$uploads[ix].size|kbsize})&lt;/a&gt;</textarea>
				</div>
			</div>
		{/section}
	{/if}
{else}
	{tr}You have to create a gallery first!{/tr}
{/if}