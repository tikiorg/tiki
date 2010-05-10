{* $Id$ *}

<!-- FileGallery.upload.insertImage('{$file}',document.getElementById('fg-insert-link-x1').checked,$('#fg-insert-size-width').val(),$('fg-insert-size-height').val()) -->

<form {if $prefs.javascript_enabled eq 'y' and !$editFileId}onsubmit='return false' target='upload_progress_0'{/if} {if $filegals_manager neq '' and $editFileId neq ''} onsubmit='FileGallery.open(this.action,this.id);FileGallery.upload.close();return false;'{/if} id='file_0' name='file_0' action='tiki-upload_file.php' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
<input type="hidden" name="formId" value="0"/>
{if $filegals_manager neq ''}
	<input type="hidden" name="filegals_manager" value="{$filegals_manager}"/>
{/if}
		{if $editFileId}
			<input type="hidden" name="galleryId" value="{$galleryId}"/>
			<input type="hidden" name="fileId" value="{$editFileId}"/>
			<input type="hidden" name="lockedby" value="{$fileInfo.lockedby|escape}" />
		{/if}
			{if count($galleries) eq 0}
				<input type="hidden" name="galleryId" value="{$prefs.fgal_root_id}"/>
			{elseif !empty($groupforalert)}
				<input type="hidden" name="galleryId" value="{$galleryId}"/>
			{/if}
{if $prefs.javascript_enabled eq 'y'}
<input type="hidden" name="upload" {if $editFileId neq ''} value="1"{/if}/>
{/if}

<script src="lib/filegals/file_gallery.js" language="JavaScript"></script>
<link rel="stylesheet" type="text/css" href="css/file_gallery.css"/>
<div class="fg-upload{if $extra eq '1'} fg-upload-extra{/if}{if $fgspecial eq ''}{if $filegals_manager eq ''} fg-standalone{/if}{/if}">
	<h2>Upload file</h2>
	<a class="fg-upload-close" onclick="FileGallery.upload.close()"><img src="images/file_gallery/close.gif" border="0"/></a>

	{if count($errors) > 0}
		<div class="simplebox highlight">
		<h2>{tr}Errors detected{/tr}</h2>
		{section name=ix loop=$errors}
			{$errors[ix]}<br />
		{/section}
		</div>
	{/if}
	
	{if $prefs.javascript_enabled eq 'y'}
	{remarksbox}{/remarksbox}
	<div id="upload_progress">
	<iframe id="upload_progress_0" name="upload_progress_0" height="1" width="1" style="border:0px none"></iframe>
	</div>
	<div id='progress'>
	<div id='progress_0'></div>
	</div>
	{/if}

	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><label for="fg-upload-title">{tr}File title:{/tr}</label></td>
		<td colspan="2">
			<input id="fg-upload-title" style="width:100%" type="text" name="name[]" {if isset($fileInfo) and $fileInfo.name}value="{$fileInfo.name}"{/if} size="40" />
			{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}<br/>({tr}required field for podcasts{/tr}){/if}
		</td>
	</tr>
	<tr>
		<td><label for="fg-upload-description">{tr}File description:{/tr}</label></td>
		<td colspan="2">
			<textarea id="fg-upload-description" style="width:100%" rows="2" cols="40" name="description[]">{if isset($fileInfo) and $fileInfo.description}{$fileInfo.description}{/if}</textarea>
			{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}<br /><em>{tr}Required for podcasts{/tr}.</em>{/if}
		</td>
	</tr>
	{if $prefs.javascript_enabled neq 'y' || !$editFileId}
	<tr>
		<td><label for="fg-upload-file">{tr}Upload from disk:{/tr}</label></td>
		<td>
			{if $editFileId}{$fileInfo.filename|escape}{/if}
			<input id="fg-upload-file" name="userfile[]" type="file" size="15"/>
		</td>
		{if !$editFileId and $tiki_p_batch_upload_files eq 'y'}
		<td class="fg-upload-unzip">
			<input type="checkbox" name="isbatch[]" /> Unzip files
		</td>
		{/if}
	</tr>
	{/if}
	{if empty($groupforalert)}
	<tr>
		<td><label for="fg-upload-gallery">{tr}File gallery:{/tr}</label></td>
		<td>
			<select id="galleryId" name="galleryId[]" id="fg-upload-gallery">
			<option value="{$prefs.fgal_root_id}" {if $prefs.fgal_root_id eq $galleryId}selected="selected"{/if} style="font-style:italic; border-bottom:1px dashed #666;">{tr}File Galleries{/tr}</option>
			{section name=idx loop=$galleries}
				{if $galleries[idx].id neq $prefs.fgal_root_id and( ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_upload_files eq 'y') )}
				<option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name|escape}</option>
				{/if}
			{/section}
			</select>
		</td>
	</tr>
	{/if}
	{if $tiki_p_admin_file_galleries eq 'y'}
	<tr>
		<td><label for="fg-upload-creator">{tr}Creator:{/tr}</label></td>
		<td>
			<select id="user" name="user[]" id="fg-upload-creator">
			{section name=ix loop=$users}
			<option value="{$users[ix].login|escape}"{if (isset($fileInfo) and $fileInfo.user eq $users[ix].login) or (!isset($fileInfo) and $user == $users[ix].login)}  selected="selected"{/if}>{$users[ix].login|username}</option>
			{/section}
			</select>
		</td>
	</tr>
	{/if}
	{if $prefs.feature_file_galleries_author eq 'y'}
	<tr>
		<td><label for="fr-upload-author">{tr}Author, if different from the Creator:{/tr}</label></td>
		<td><input type="text" id="fg-upload-author" name="author[]" value="{$fileInfo.author|escape}" /></td>
	</tr>
	{/if}
	{if !empty($groupforalert)}
		{if $showeachuser eq 'y' }
		<tr>
			<td>{tr}Choose users to alert{/tr}</td>
			<td>
		{/if}
		{section name=idx loop=$listusertoalert}
			{if $showeachuser eq 'n' }
				<input type="hidden"  name="listtoalert[]" value="{$listusertoalert[idx].user}">
			{else}
				<input type="checkbox" name="listtoalert[]" value="{$listusertoalert[idx].user}"> {$listusertoalert[idx].user}
			{/if}
		{/section}
		{if $showeachuser eq 'y' }
			</td>
		</tr>
		{/if}
	{/if}
	{if $prefs.fgal_limit_hits_per_file eq 'y'}
	<tr>
		<td><label for="hit_limit">{tr}Maximum number of downloads:{/tr}</label></td>
		<td><input type="text" id="hit_limit" name="hit_limit[]" value="{$hit_limit|default:0}"/>
			<br /><em>{tr}Use{/tr} {tr}0 for no limit{/tr}.</em>
		</td>
	</tr>
	{/if}
	{* We want comments only on updated files *}
	{if $prefs.javascript_enabled neq 'y' && $editFileId}
	<tr>
		<td><label for="comment">{tr}Comment:{/tr}</label></td>
		<td><input type="text" id="comment" name="comment[]" value="" size="40" /></td>
	</tr>
	{/if}
	</table>
	
	{if $extra eq '1'}
	<div class="fg-insert-choose">
		<a id="fg-insert-mode-image" onclick="FileGallery.upload.switchto('image')"{if $as='image'} class="fg-insert-active"{/if}>{tr}Insert as an image{/tr}</a>
		<a id="fg-insert-mode-link" onclick="FileGallery.upload.switchto('link')"{if $as<>'image'} class="fg-insert-active"{/if}>{tr}Insert as a link{/tr}</a>
	</div>
	<div class="fg-insert-form">
		<div id="fg-insert-as-image">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><input type="radio" name="x" id="fg-insert-link-x1"/></td>
				<td><label for="fg-insert-link-x1">{tr}Original size{/tr}</label></td>
			</tr>
			<tr>
				<td><input type="radio" name="x" id="fg-insert-link-x2"/></td>
				<td><label for="fg-insert-link-x2">{tr}Thumbnail{/tr}</label></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="text" class="fg-insert-size" id="fg-insert-size-width"/> x <input type="text" class="fg-insert-size" id="fg-insert-size-height"/></td>
			</tr>
			</table>
		</div>
		<div id="fg-insert-as-link" style="display:none">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><label for="fg-insert-title">{tr}Link title{/tr}</label></td>
				<td><input type="text" id="fg-insert-title"/></td>
			</tr>
			</table>
		</div>
	</div>
	{/if}

	{if $prefs.javascript_enabled neq 'y' and !$editFileId}
		<input id="fg-upload-submit" type="submit" value="{tr}Upload{/tr}"/>
	{/if}
	{if $prefs.javascript_enabled eq 'y' and !$editFileId}
		<input id="fg-upload-submit" type="button" onclick="FileGallery.upload.upload('0', 'loader_0')" value="{tr}Upload{/tr}"/>
		<!--input type="button" onclick="javascript:add_upload_file('multiple_upload')" value="{tr}Add File{/tr}"/-->
	{/if}
	{if $editFileId}
		<input id="fg-upload-submit" type="submit" value="{tr}Save{/tr}"/>
	{/if}
	
	<div class="fg-upload-hint">
		{tr}Maximum file size is around:{/tr}
		{if $tiki_p_admin eq 'y'}<a title="{$max_upload_size_comment}">{/if}{$max_upload_size|kbsize:true:0}{if $tiki_p_admin eq 'y'}</a>{/if}
	</div>
</div>

</form>
