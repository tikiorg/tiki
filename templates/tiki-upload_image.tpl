{* $Id$ *}

{title help="Image Galleries"}{tr}Upload Image{/tr}{/title}

<div class="navbar btn-group">
	{if $galleryId ne ''}
		{button href="tiki-browse_gallery.php" _auto_args="galleryId" class="btn btn-default" _text="{tr}Browse Gallery{/tr}"}
	{else}
		{button href="tiki-galleries.php" class="btn btn-default" _text="{tr}Browse Gallery{/tr}"}
	{/if}

	{if $prefs.feature_gal_batch eq "y" and $tiki_p_batch_upload_image_dir eq 'y'}
		{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
			{button href="tiki-batch_upload.php" _auto_args="galleryId" class="btn btn-default" _text="{tr}Directory Batch{/tr}"}
		{/if}
	{/if}
</div>

{if $batchRes}
	<h2>{tr}Batch Upload Results{/tr}</h2>
	<div class="table-responsive">
		<table class="table">
			<tr>
				<th>{tr}Filename{/tr}</th>
				<th>{tr}Status{/tr}</th>
				<th>{tr}ID{/tr}</th>
				<th>{tr}Image{/tr}</th>
			</tr>

			{section name=ix loop=$batchRes}
				<tr>
					<td>{$batchRes[ix].filename}</td>
					{if $batchRes[ix].msg}
						<td colspan="3">
							{icon name='error' alt="{tr}Errors detected{/tr}" style="vertical-align:middle"} {$batchRes[ix].msg}
						</td>
					{else}
						<td>
							{icon name='ok' alt="{tr}Upload successful!{/tr}" style="vertical-align:middle"}{tr}Upload successful!{/tr}</td><td>{$batchRes[ix].imageId}</td><td><img src="{$url_show}?id={$batchRes[ix].imageId}&amp;thumb=1" alt="{$batchRes[ix].filename}">
						</td>
					{/if}
				</tr>
			{/section}
		</table>
	</div>
{/if}

{if $show eq 'y'}
	<h2>{tr}Upload successful!{/tr}</h2>
	<h3>{tr}The following image was successfully uploaded:{/tr}</h3>
	<div align="center">
		<img src="show_image.php?id={$imageId}" alt="{tr}Image ID{/tr}">
		<br>
		<b>{tr}Thumbnail:{/tr}</b>
		<br>
		<img src="show_image.php?id={$imageId}&amp;thumb=1" alt="{tr}Image ID thumb{/tr}">
		<br><br>
		<div class="wikitext">
			{tr}You can view this image in your browser using:{/tr}&nbsp;
			<a class="link" href="{$url_browse}?imageId={$imageId}">{$url_browse}?imageId={$imageId}</a>
			<br><br>
			{tr}You can include the image in an Wiki page using:{/tr}&nbsp;
			<code>{literal}{{/literal}img id={$imageId}{literal}}{/literal}</code>
		</div>
	</div>
{/if}

{if count($galleries) > 0}
	<div align="center">
		{if $batchRes or $show eq 'y'}
			<h2>Upload File</h2>
		{/if}
		<form enctype="multipart/form-data" action="tiki-upload_image.php" method="post">
			<table class="formcolor">
				<tr>
					<td>{tr}Image Name:{/tr}</td>
					<td>
						<input type="text" size ="50" name="name">
						<br>
						{tr}or use filename:{/tr} <input type="checkbox" name="use_filename">
					</td>
				</tr>
				<tr>
					<td>{tr}Image Description:{/tr}</td>
					<td>
						<textarea rows="5" cols="50" name="description"></textarea>
					</td>
				</tr>
				{if $tiki_p_list_image_galleries eq 'y'}
					<tr>
						<td>{tr}Gallery:{/tr}</td>
						<td>
							<select name="galleryId">
								{section name=idx loop=$galleries}
									{if ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_upload_images eq 'y')}
										<option value="{$galleries[idx].galleryId|escape}" {if $galleries[idx].galleryId eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
									{/if}
								{/section}
							</select>
						</td>
					</tr>
				{else}
					<input type="hidden" name="galleryId" value="{$galleryId}">
				{/if}
				{include file='categorize.tpl'}
				<tr>
					<td colspan="2">
						<b>{tr}Now enter the image URL{/tr} {tr}or upload a local image from your disk{/tr}</b>
					</td>
				</tr>
				<tr>
					<td>URL:</td>
					<td>
						<input size="50" type="text" name="url">
					</td>
				</tr>
				<tr>
					<td>{tr}Upload From Disk{/tr} / {tr}Batch Upload:{/tr}</td>
					<td>
						<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
						<input size="50" name="userfile1" type="file">
					</td>
				</tr>
				<tr>
					<td>{tr}Thumbnail (optional, overrides automatic thumbnail generation):{/tr}</td>
					<td>
						<input name="userfile2" size ="50" type="file">
					</td>
				</tr>
				<tr>
					<td>{tr}Upload From Disk:{/tr}</td>
					<td>
						<input name="userfile3" type="file">
						<input name="userfile4" type="file">
						<br>
						<input name="userfile5" type="file">
						<input name="userfile6" type="file">
						<br>
						<input name="userfile7" type="file">
						<input name="userfile8" type="file">
					</td>
				</tr>
				{if $prefs.feature_antibot eq 'y' && $user eq ''}
					{include file='antibot.tpl' td_style="formcolor"}
				{/if}
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" class="btn btn-default btn-sm" name="upload" value="{tr}Upload{/tr}">
					</td>
				</tr>
			</table>
			{remarksbox type="note"}{tr}Maximum file size is around:{/tr} {if $tiki_p_admin eq 'y'}<a title="{$max_upload_size_comment}">{/if}{$max_upload_size|kbsize:true:0}{if $tiki_p_admin eq 'y'}</a>{/if}{/remarksbox}
		</form>
	</div>
{else}
	{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle;"} {tr}No gallery available.{/tr} {tr}You have to create a gallery first!{/tr}
	<p><a href="tiki-galleries.php?edit_mode=1&galleryId=0">{tr}Create New Gallery{/tr}</a></p>
{/if}


