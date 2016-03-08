{* $Id$ *}

{if !empty($filegals_manager) and !isset($smarty.request.simpleMode)}
	{assign var=simpleMode value='y'}
{else}
	{assign var=simpleMode value='n'}
{/if}

{title help="File+Galleries" admpage="fgal"}{if $editFileId}{tr}Edit File:{/tr} {$fileInfo.filename}{else}{tr}Upload File{/tr}{/if}{/title}

{if !empty($galleryId) or (isset($galleries) and count($galleries) > 0 and $tiki_p_list_file_galleries eq 'y') or (isset($uploads) and count($uploads) > 0)}
	<div class="t_navbar margin-bottom-md">
		{if !empty($galleryId)}
			{button galleryId="$galleryId" href="tiki-list_file_gallery.php" class="btn btn-default" _text="{tr}Browse Gallery{/tr}"}
		{/if}

		{if isset($galleries) and count($galleries) > 0 and $tiki_p_list_file_galleries eq 'y'}
			{if !empty($filegals_manager)}
				{assign var=fgmanager value=$filegals_manager|escape}
				{button href="tiki-list_file_gallery.php?filegals_manager=$fgmanager" class="btn btn-default" _text="{tr}List Galleries{/tr}"}
			{else}
				{button href="tiki-list_file_gallery.php" class="btn btn-default" _text="{tr}List Galleries{/tr}"}
			{/if}
		{/if}
		{if isset($uploads) and count($uploads) > 0}
			{button href="#upload" class="btn btn-default" _text="{tr}Upload File{/tr}"}
		{/if}
		{if !empty($filegals_manager)}
			{if $simpleMode eq 'y'}{button simpleMode='n' galleryId=$galleryId href="" class="btn btn-default" _text="{tr}Advanced mode{/tr}" _ajax="n"}{else}{button galleryId=$galleryId href="" _text="{tr}Simple mode{/tr}" _ajax="n"}{/if}
			<span{if $simpleMode eq 'y'} style="display:none;"{/if}>
				<label for="keepOpenCbx">{tr}Keep gallery window open{/tr}</label>
				<input type="checkbox" id="keepOpenCbx" checked="checked">
			</span>
		{/if}
	</div>
{/if}

{if isset($errors) and count($errors) > 0}
	<div class="alert alert-danger">
		<h2>{tr}Errors detected{/tr}</h2>
		{section name=ix loop=$errors}
			{$errors[ix]}<br>
		{/section}
		{button href="#upload" _text="{tr}Retry{/tr}"}
	</div>
{/if}


{if $prefs.javascript_enabled eq 'y'}
	<div id='progress'>
		<div id='progress_0'></div>
	</div>
{/if}

{if isset($uploads) and count($uploads) > 0}
	<h2>
	{if count($uploads) eq 1}
		{tr}The following file was successfully uploaded:{/tr}
	{else}
		{tr}The following files have been successfully uploaded:{/tr}
	{/if}
	</h2>

	<table border="0" cellspacing="4" cellpadding="4">
		{section name=ix loop=$uploads}
			<tr class="{cycle values="odd,even"}">
				<td style="text-align: center">
					<img src="{$uploads[ix].fileId|sefurl:thumbnail}">
				</td>
				<td>
					{if !empty($filegals_manager)}
						<a href="#" onclick="window.opener.insertAt('{$filegals_manager}','{$files[changes].wiki_syntax|escape}');checkClose();return false;" title="{tr}Click here to use the file{/tr}">{$uploads[ix].name} ({$uploads[ix].size|kbsize})</a>
					{else}
						<b>{$uploads[ix].name} ({$uploads[ix].size|kbsize})</b>
					{/if}
					{button href="#" _flip_id="uploadinfos"|cat:$uploads[ix].fileId _text="{tr}Additional Info{/tr}"}
					<div style="{if $prefs.javascript_enabled eq 'y'}display:none;{/if}" id="uploadinfos{$uploads[ix].fileId}">
						{tr}You can download this file using:{/tr} <div class="code"><a class="link" href="{$uploads[ix].dllink}">{$uploads[ix].fileId|sefurl:file}</a></div>
						{tr}You can link to the file from a Wiki page using:{/tr} <div class="code">[{$uploads[ix].fileId|sefurl:file}|{$uploads[ix].name} ({$uploads[ix].size|kbsize})]</div>
						{tr}You can display an image in a Wiki page using:{/tr} <div class="code">&#x7b;img src="{$uploads[ix].fileId|sefurl:preview}" link="{$uploads[ix].fileId|sefurl:file}" alt="{$uploads[ix].name} ({$uploads[ix].size|kbsize})"}</div>
						{if $prefs.feature_shadowbox eq 'y'}
							{tr}Or using as a thumbnail with ShadowBox:{/tr} <div class="code">&#x7b;img src="{$uploads[ix].fileId|sefurl:thumbnail}" link="{$uploads[ix].fileId|sefurl:preview}" rel="shadowbox[gallery];type=img" alt="{$name} ({$uploads[ix].size|kbsize})"}</div>
						{/if}
						{tr}You can link to the file from an HTML page using:{/tr} <div class="code">&lt;a href="{$uploads[ix].dllink}"&gt;{$uploads[ix].name} ({$uploads[ix].size|kbsize})&lt;/a&gt;</div>
					</div>
				</td>
			</tr>
		{/section}
	</table>
	<br>

	<h2>{tr}Upload File{/tr}</h2>
{elseif isset($fileChangedMessage)}
	<div align="center">
		<div class="wikitext">
			{$fileChangedMessage}
		</div>
	</div>
{/if}

{if $editFileId and isset($fileInfo.lockedby) and $fileInfo.lockedby neq ''}
	{remarksbox type="note" title="{tr}Info{/tr}" icon="lock"}
		{if $user eq $fileInfo.lockedby}
			{tr}You locked the file{/tr}
		{else}
			{tr}The file has been locked by {$fileInfo.lockedby}{/tr}
		{/if}
	{/remarksbox}
{/if}

<div>

	{capture name=upload_file assign=upload_str}

		<div class="fgal_file">
			<div class="fgal_file_c1">
				{if $prefs.file_galleries_use_jquery_upload neq 'y' or $editFileId}
					{if $simpleMode neq 'y'}
						<div class="form-group">
							<label for="name" class="col-sm-3 control-label">{tr}File title{/tr}</label>
							<div class="col-sm-9">
								<input class="form-control" type="text" id="name" name="name[]"
									{if isset($fileInfo) and $fileInfo.name}
										value="{$fileInfo.name|escape}"
									{/if}
									size="40"
								>
								{if isset($gal_info.type) and ($gal_info.type eq "podcast" or $gal_info.type eq "vidcast")}
									({tr}required field for podcasts{/tr})
								{/if}
							</div>
						</div>
						<div class="form-group">
							<label for="description" class="col-sm-3 control-label">{tr}File description{/tr}</label>
							<div class="col-sm-9">
								<textarea class="form-control" rows="2" cols="40" id="description" name="description[]">{if isset($fileInfo.description)}{$fileInfo.description|escape}{/if}</textarea>
								{if isset($gal_info.type) and ($gal_info.type eq "podcast" or $gal_info.type eq "vidcast")}
									<br><em>{tr}Required for podcasts{/tr}.</em>
								{/if}
							</div>
						</div>
					{/if}
					{if $prefs.javascript_enabled neq 'y' || !$editFileId}
						<div class="form-group">
							<label for="userfile" class="col-sm-3 control-label">{tr}Upload from disk{/tr}</label>
							<div class="col-sm-9">
								{if $editFileId}
									{$fileInfo.filename|escape}
								{/if}

								<input id="userfile" name="userfile[]" type="file" size="40">
							</div>
						</div>
					{/if}
				{else}{* file_galleries_use_jquery_upload = y *}
					{filegal_uploader}
				{/if}

			</div>

			{if $simpleMode neq 'y'}
				<div class="fgal_file_c2">
					{if !$editFileId and $tiki_p_batch_upload_files eq 'y'}
						<div class="form-group">
							<label for="isbatch" class="col-sm-9 col-sm-offset-3">
								<input type="checkbox" id="isbatch" name="isbatch[]">
								{tr}Unzip zip files{/tr}
							</label>
						</div>
					{/if}

					{if $prefs.fgal_delete_after eq 'y'}
						<div class="form-group">
							<label for="deleteAfter" class="col-sm-3 control-label">{tr}File can be deleted after{/tr}</label>
								{if $editFileId}
									{html_select_duration prefix='deleteAfter' id="deleteAfter" default_value=$fileInfo.deleteAfter}
								{else}
									{html_select_duration prefix='deleteAfter[]' id="deleteAfter" default_unit=week}
								{/if}
						</div>
					{/if}

					{if $editFileId}
						<input type="hidden" name="galleryId" value="{$galleryId}">
						<input type="hidden" name="fileId" value="{$editFileId}">
						<input type="hidden" name="lockedby" value="{$fileInfo.lockedby|escape}">
					{else}
						{if count($galleries) eq 0}
							{if !empty($galleryId)}
								<input type="hidden" name="galleryId[]" value="{$galleryId}">
							{else}
								<input type="hidden" name="galleryId[]" value="{$treeRootId}">
							{/if}
						{elseif empty($groupforalert)}
							<div class="form-group">
								<label for="galleryId" class="col-sm-3 control-label">{tr}File gallery{/tr}</label>
								<div class="col-sm-9">
									<select id="galleryId" name="galleryId[]" class="form-control">
										<option value="{$treeRootId}" {if $treeRootId eq $galleryId}selected="selected"{/if} style="font-style:italic; border-bottom:1px dashed #666;">{tr}Root{/tr}</option>
										{section name=idx loop=$galleries}
											{if $galleries[idx].id neq $treeRootId and ($galleries[idx].perms.tiki_p_upload_files eq 'y' or $tiki_p_userfiles eq 'y')}
												<option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name|escape}</option>
											{/if}
										{/section}
									</select>
								</div>
							</div>
						{else}
							<input type="hidden" name="galleryId[]" value="{$galleryId}">
						{/if}
					{/if}

					<div class="form-group">
						<label for="user" class="col-sm-3 control-label">{tr}Uploaded by{/tr}</label>
						<div class="col-sm-9">
							{user_selector id='user' name='user[]' select=$fileInfo.user editable=$tiki_p_admin_file_galleries}
						</div>
					</div>

					{if $prefs.feature_file_galleries_author eq 'y'}
						<div class="form-group">
							<label for="author" class="col-sm-3 control-label">{tr}Creator{/tr}</label>
							<div class="col-sm-9">
								<input type="text" id="author"name="author[]" value="{$fileInfo.author|escape}"><br>
								<span class="description">{tr}Creator of file, if different from the 'Uploaded by' user{/tr}</span>
							</div>
						</div>
					{/if}
					{if !empty($groupforalert)}
						{if $showeachuser eq 'y'}
							<div class="form-group">
								<label class="col-sm-3 control-label">{tr}Choose users to alert{/tr}</label>
								<div class="col-sm-9">
									{section name=idx loop=$listusertoalert}
										<label>
											<input type="checkbox" name="listtoalert[]" value="{$listusertoalert[idx].user|escape}"> {$listusertoalert[idx].user|escape}
										</label>
									{/section}
								</div>
							</div>
						{else}
							{section name=idx loop=$listusertoalert}
								<input type="hidden" name="listtoalert[]" value="{$listusertoalert[idx].user}">
							{/section}
						{/if}
					{/if}
					{if $editFileId}
						<div class="form-group">
							<label for="filetype" class="col-sm-3 control-label">{tr}File Type{/tr}</label>
							<div class="col-sm-9">
								<select id="filetype" class="form-control" name="filetype[]">
									{foreach $mimetypes as $type}
										<option value="{$type}"{if $fileInfo.filetype eq $type && $fileInfo.extension eq $type@key} selected="selected"{/if}>
											{$type|truncate:60} (*.{$type@key})
										</option>
									{/foreach}
								</select>
							</div>
						</div>
					{/if}
				</div>
				<div class="fgal_file_c3">
					{if $prefs.fgal_limit_hits_per_file eq 'y'}
						<div class="form-group">
							<label for="hit_limit" class="col-sm-3 form-label">{tr}Maximum number of downloads{/tr}</label>
							<div class="col-sm-9">
								<input type="text" id="hit_limit" name="hit_limit[]" value="{$hit_limit|default:0}">
								<br><em>{tr}Use{/tr} {tr}-1 for no limit{/tr}.</em>
							</div>
						</div>
					{else}
						<input type="hidden" id="hit_limit" name="hit_limit[]" value="{$hit_limit|default:-1}">
					{/if}

					{* We want comments only on updated files *}
					{if $prefs.javascript_enabled neq 'y' && $editFileId}
						<div class="form-group">
							<label for="comment" class="col-sm-3 form-label">{tr}Comment{/tr}</label>
							<div class="col-sm-9">
								<input type="text" id="comment" name="comment[]" value="" size="40">
							</div>
						</div>
					{/if}
				</div>
				{if $prefs.javascript_enabled eq 'y' and !$editFileId}
					{include file='categorize.tpl'}<br/>
				{/if}
			{else}
				<input type="hidden" name="galleryId[]" value="{$galleryId}">
			{/if}
			{if $prefs.javascript_enabled eq 'y' and !$editFileId}
				<input type="hidden" name="upload">
			{/if}
		</div>
	{/capture}

	<div id="form">
		<form method="post"
			action='tiki-upload_file.php'
			enctype='multipart/form-data'
			class="form-horizontal"
			id="file_0"
		>
			<input type="hidden" name="simpleMode" value="{$simpleMode}">
			{if !empty($filegals_manager)}
				<input type="hidden" name="filegals_manager" value="{$filegals_manager}">
			{/if}
			{if !empty($insertion_syntax)}
				<input type="hidden" name="insertion_syntax" value="{$insertion_syntax}">
			{/if}
			{if isset($token_id) and $token_id neq ''}
				<input type="hidden" value="{$token_id}" name="TOKEN">
			{/if}

			{$upload_str}

			{if $editFileId}
				{include file='categorize.tpl'}<br>
				<div id="page_bar" class="form-group">
					<div class="col-sm-9 col-sm-offset-3">
						<input name="upload" type="submit" class="btn btn-default" value="{tr}Save{/tr}">
					</div>
				</div>
			{elseif $prefs.javascript_enabled neq 'y'}
				{if $prefs.file_galleries_use_jquery_upload neq 'y'}
					{$upload_str}
					{$upload_str}
					{include file='categorize.tpl'}<br>
					<div id="page_bar" class="form-group">
						<div class="col-sm-9 col-sm-offset-3">
							<input type="submit" class="btn btn-default btn-sm" name="upload" value="{tr}Upload{/tr}">
						</div>
					</div>
				{/if}
			{/if}

			{if !$editFileId && $prefs.file_galleries_use_jquery_upload neq 'y'}
				<div id="page_bar" class="form-group">
					<div class="col-sm-9 col-sm-offset-3">
						<input type="submit" class="btn btn-primary btn-sm"
							onClick="upload_files(); return false"
							id="btnUpload"
							name="upload"
							value="{tr}Upload File(s){/tr}"
						>
						<input type="submit" class="btn btn-default btn-sm" onclick="javascript:add_upload_file('multiple_upload'); return false" value="{tr}Add Another File{/tr}">
					</div>
				</div>
			{/if}
		</form>
	</div>

	{if !empty($fileInfo.lockedby) and $user ne $fileInfo.lockedby}
		{icon name="lock"}
		<span class="attention">{tr}The file has been locked by {$fileInfo.lockedby}{/tr}</span>
	{/if}
	<br>

	{if !$editFileId}
		{remarksbox type="note" title="{tr}Information{/tr}"}
			{tr}Maximum file size is around:{/tr}
			{if $tiki_p_admin eq 'y'}<a title="{$max_upload_size_comment}">{/if}
				{$max_upload_size|kbsize:true:0}
			{if $tiki_p_admin eq 'y'}</a>
				{if $is_iis}<br>{tr}Note: You are running IIS{/tr}. {tr}maxAllowedContentLength also limits upload size{/tr}. {tr}Please check web.config in the Tiki root folder{/tr}{/if}
			{/if}
		{/remarksbox}
	{/if}

</div>

{if not empty($metarray) and $metarray|count gt 0}
	{include file='metadata/meta_view_tabs.tpl'}
{/if}

{if ! $editFileId and $prefs.file_galleries_use_jquery_upload neq 'y'}
	{if $prefs.feature_jquery_ui eq 'y'}
		{jq}$('.datePicker').datepicker({minDate: 0, maxDate: '+1m', dateFormat: 'dd/mm/yy'});{/jq}
	{/if}
	{jq notonready=true}
		$('#file_0').ajaxForm({target: '#progress_0', forceSync: true});
		var nb_upload = 1;

		function add_upload_file() {
			var clone = $('#form form').eq(0).clone().resetForm().attr('id', 'file_' + nb_upload).ajaxForm({target: '#progress_' + nb_upload, forceSync: true});
			clone.insertAfter($('#form form').eq(-1));
			document.getElementById('progress').innerHTML += "<div id='progress_"+nb_upload+"'></div>";
			nb_upload += 1;
		}

		function upload_files(){
			$("#form form").each(function(n) {
				if ($(this).find('input[name="userfile\\[\\]"]').val() != '') {

					var $progress = $('#progress_'+n).html("<img src='img/spinner.gif'>{tr}Uploading file...{/tr}");

					$( document ).ajaxError(function(event, jqxhr, ajaxSettings, thrownError ) {
						$progress.hide();
						show('form');
						$("#form").showError(tr("File upload error:") + " " + thrownError)
					});
					$(this).submit();
					this.reset();
				} else {
					$('#progress_'+n).html("{tr}No File to Upload...{/tr} <span class='button'><a href='#' onclick='location.replace(location.href);return false;'>{tr}Retry{/tr}</a></span>");
				}
			});
			hide('form');
		}
	{/jq}

	{if $prefs.fgal_upload_from_source eq 'y' and $tiki_p_upload_files eq 'y'}
		<form class="remote-upload" method="post" action="{service controller=file action=remote}">
			<h3>{tr}Upload from URL{/tr}</h3>
			<p>
				<input type="hidden" name="galleryId" value="{$galleryId|escape}">
				<label>{tr}URL:{/tr} <input type="url" name="url" placeholder="http://" size="40"></label>
				{if $prefs.vimeo_upload eq 'y'}
					<label>{tr}Reference:{/tr}
						<input type="checkbox" name="reference" value="1" class="tips" title="{tr}Upload from URL{/tr}|{tr}Keeps a reference to the remote file{/tr}">
					</label>
				{/if}
				<input type="submit" class="btn btn-default btn-sm" value="{tr}Add{/tr}">
			</p>
			<div class="result"></div>
		</form>
		{jq}
			$('.remote-upload').submit(function () {
				var form = this;
				$.ajax({
					method: 'POST',
					url: $(form).attr('action'),
					data: $(form).serialize(),
					dataType: 'html',
					success: function (data) {
						$('.result', form).html(data);
						$(form.url).val('');
					},
					complete: function () {
						$('input', form).prop('disabled', false);
					},
					error: function (e) {
						alert(tr("A remote file upload error occurred:") + "\n\"" + e.statusText + "\" (" + e.status + ")");
					}
				});

				$('input', this).prop('disabled', true);
				return false;
			});
		{/jq}
		{if $prefs.vimeo_upload eq 'y'}
			<fieldset>
				<h3>{tr}Upload Video{/tr}</h3>
				{wikiplugin _name='vimeo'}{/wikiplugin}
			</fieldset>
			{jq}
				var handleVimeoFile = function (link, data) {
					if (data != undefined) {
					$("#form").hide();
					$("#progress").append(
						$("<p> {tr}Video file uploaded:{/tr} " + data.file + "</p>")
							.prepend($("<img src='img/icons/vimeo.png' width='16' height='16'>"))
						);
					}
				}
			{/jq}
		{/if}
	{/if}

{/if}

