{* $Id$ *}
{* Used by smarty_function_filegal_uploader() when $prefs.file_galleries_use_jquery_upload is enabled *}
{* The fileinput-button span is used to style the file input field as button *}
<div class="form-group">
	<div class="well fileupload margin-bottom-none">
		<h3 class="text-center">{icon name="cloud-upload"} {tr}Drop files or {/tr}
			<div class="btn btn-primary fileinput-button">
				<span>{tr}Browse files{/tr}</span>
				{* The file input field used as target for the file upload widget *}
				<input id="fileupload" type="file" name="files[]" multiple>
			</div>
		</h3>
	</div>
</div>
<div class="form-group">
	<div id="files" class="files text-center"></div>
</div>
<div class="form-group">
	<label for="autoupload" class="col-md-8 col-md-offset-4">{* auto-upload user pref *}
		<input type="checkbox" id="autoupload" name="autoupload"{if $prefs.filegals_autoupload eq 'y'} checked="checked"{/if}>
		{tr}Automatic upload{/tr}
	</label>{* The container for the uploaded files *}
</div>
<div class="hidden">
	{icon name='file' id='file_icon'}
	{icon name='pdf' id='pdf_icon'}
	{icon name='video' id='video_icon'}
	{icon name='audio' id='audio_icon'}
	{icon name='zip' id='zip_icon'}
</div>
