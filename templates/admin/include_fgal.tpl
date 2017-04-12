{* $Id$ *}
{if !empty($errors)}
	{remarksbox type="errors" title="{tr}Errors{/tr}"}
		{foreach from=$errors item=error}
			{$error|escape}
			<br>
		{/foreach}
	{/remarksbox}
{/if}
{if !empty($feedbacks)}
	{remarksbox type="note" title="{tr}Feedback{/tr}"}
		{foreach from=$feedbacks item=feedback}
			{$feedback|escape}
			<br>
		{/foreach}
	{/remarksbox}
{/if}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}To create or remove file galleries, select{/tr} <a class="rbox-link" href="tiki-list_file_gallery.php">{tr}File Galleries{/tr}</a> {tr}from the application menu{/tr}.
	<hr>
	{tr}If you decide to store files in a directory you must ensure that the user cannot access directly to the directory.{/tr}
	{tr}You have two options to accomplish this:<br><ul><li>Use a directory outside your document root, make sure your php script can read and write to that directory</li><li>Use a directory inside the document root and use .htaccess to prevent the user from listing the directory contents</li></ul>{/tr}
	{tr}To configure the directory path use UNIX like paths for example files/ or c:/foo/files or /www/files/{/tr}
{/remarksbox}

<form class="form-horizontal" action="tiki-admin.php?page=fgal" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="link" class="btn btn-link" href="tiki-list_file_gallery.php" title="{tr}List{/tr}">
				{icon name="list"} {tr}File Galleries{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="filegalhandlers" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
	{tabset name="fgal_admin"}

		{tab name="{tr}General Settings{/tr}"}
			<h2>{tr}General Settings{/tr}</h2>

			<fieldset>
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_file_galleries visible="always"}
			</fieldset>

			{preference name=home_file_gallery}
			{preference name='fgal_use_db'}
			<div class="adminoptionboxchild fgal_use_db_childcontainer n">
				{preference name='fgal_use_dir'}
			</div>
			<div class="col-sm-8 col-sm-offset-4 margin-bottom-md">
			{button href="tiki-admin.php?page=fgal&amp;move=to_fs" _text="{tr}Move files from database to directory{/tr}"}
			{button href="tiki-admin.php?page=fgal&amp;move=to_db" _text="{tr}Move files from directory to database{/tr}"}
			</div>

			{preference name='fgal_podcast_dir'}

			<input type="hidden" name="filegalfeatures">

			<fieldset>
				<legend>{tr}Features{/tr}{help url="File+Gallery+Config"}</legend>

				{preference name='feature_file_galleries_rankings'}
				{preference name='feature_file_galleries_comments'}
				<div class="adminoptionboxchild" id="feature_file_galleries_comments_childcontainer">
					<a class="link" href="tiki-admin.php?page=comments">{tr}Manage comment settings{/tr}</a>
				</div>
				{preference name='fgal_display_zip_option'}
				{preference name='fgal_limit_hits_per_file'}
				{preference name='fgal_prevent_negative_score'}

				{preference name='fgal_allow_duplicates'}
				{preference name='file_galleries_use_jquery_upload'}

				{preference name='feature_file_galleries_batch'}
				<div class="adminoptionboxchild" id="feature_file_galleries_batch_childcontainer">
					{remarksbox title="Note"}
						{tr}You are highly recommended to use a file directory as the File Gallery storage, when using this feature{/tr}
					{/remarksbox}
					<br/>
					{preference name='fgal_batch_dir'}
				</div>

				{preference name='feature_file_galleries_author'}
				{preference name='fgal_delete_after'}
				<div class="adminoptionboxchild" id="fgal_delete_after_childcontainer">
					{remarksbox type="warning" title="Cron"}
						{tr}A cron job must be set up in order to delete the files.{/tr}
					{/remarksbox}
					{preference name='fgal_delete_after_email'}
				</div>
				{preference name='fgal_keep_fileId'}
				{preference name='feature_use_fgal_for_user_files'}
				{preference name='feature_use_fgal_for_wiki_attachments'}
				{preference name='feature_file_galleries_save_draft'}
				{preference name='feature_file_galleries_templates'}
				{preference name='fgal_tracker_existing_search'}

				{preference name='fgal_fix_mime_type'}
				<div class="adminoptionboxchild" id="fgal_fix_mime_type_childcontainer">
					<input type="submit" class="btn btn-default btn-sm" name="updateMime" id="updateMime" value="{tr}Update mime of all non archived octet-stream files{/tr}" />
				</div>

				{preference name='fgal_upload_from_source'}
				<div class="adminoptionboxchild" id="fgal_upload_from_source_childcontainer">
					{preference name='fgal_source_refresh_frequency'}
					{preference name='fgal_source_show_refresh'}
				</div>
				{preference name='tiki_check_file_content'}
			</fieldset>

			<fieldset>
				<legend>{tr}Quota{/tr}{help url="File+Gallery+Config#Quota"}</legend>
				{preference name='fgal_quota'}{tr}Used:{/tr} {$usedSize|kbsize}
				<div class="adminoptionboxchild" id="fgal_quota_childcontainer">
					{if !empty($prefs.fgal_quota)}
						{capture name='use'}{math equation="round((100*x)/(1024*1024*y))" x=$usedSize y=$prefs.fgal_quota}{/capture}
						{quotabar length='100' value='$smarty.capture.use'}
					{/if}
				</div>
				{preference name='fgal_quota_per_fgal'}
				<div class="adminoptionboxchild" id="fgal_quota_per_fgal_childcontainer">
					{preference name='fgal_quota_default'}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Upload Regex{/tr}{help url="File+Gallery+Config#Filename_must_match:"}</legend>
				{preference name='fgal_match_regex'}
				{preference name='fgal_nmatch_regex'}
			</fieldset>
		{/tab}

		{tab name="{tr}Plugins{/tr}"}
			<h2>{tr}Plugins{/tr}</h2>
			<fieldset class="table">
				<legend>{tr}Plugins{/tr}</legend>
				{preference name=wikiplugin_files}
				{preference name=wikiplugin_file}
				{preference name=wikiplugin_img}
				{preference name=wikiplugin_carousel}
				{preference name=wikiplugin_galleriffic}
				{preference name=wikiplugin_colorbox}
				{preference name=wikiplugin_draw}
				{preference name=wikiplugin_annotation}
				{preference name=wikiplugin_flash}
			</fieldset>
		{/tab}

		{tab name="{tr}Listings{/tr}"}
			<h2>{tr}Listings{/tr}</h2>
			<span class="help-block">{tr}Configuration for gallery listings{/tr}</span>
			{remarksbox title="Note"}
				{tr}Changing these settings will <em>not</em> affect existing file galleries. These changes will apply <em>only</em> to new file galleries{/tr}.
			{/remarksbox}

			<input type="hidden" name="filegallistprefs" />
			<div class="adminoptionbox clearfix margin-bottom-md">
				<label class="col-sm-4 control-label" for="fgal_default_view">
					{tr}Default View{/tr}
				</label>
				<div class="col-sm-8">
					<select id="fgal_default_view" name="fgal_default_view" class="form-control">
						<option value="list"{if $prefs.fgal_default_view eq 'list'} selected="selected"{/if}>
							{tr}List{/tr}
						</option>
						<option value="browse"{if $prefs.fgal_default_view eq 'browse'} selected="selected"{/if}>
							{tr}Browse{/tr}
						</option>
						<option value="page"{if $prefs.fgal_default_view eq 'page'} selected="selected"{/if}>
							{tr}Page{/tr}
						</option>
						{if $prefs.fgal_elfinder_feature eq 'y'}
							<option value="finder"{if $prefs.fgal_default_view eq 'finder'} selected="selected"{/if}>
								{tr}Finder View{/tr}
							</option>
						{/if}
					</select>
				</div>
			</div>
			<div class="adminoptionbox clearfix">
				<label for="fgal_sortorder" class="col-sm-4 control-label">{tr}Default sort order{/tr}:</label>
				<div class="col-sm-8">
					<select name="fgal_sortorder" id="fgal_sortorder" class="form-control">
						{foreach from=$options_sortorder key=key item=item}
							<option value="{$item|escape}" {if $fgal_sortorder == $item} selected="selected"{/if}>{$key}</option>
						{/foreach}
					</select>
					<span class="help-block">
						<label class="radio-inline" for="fgal_sortdirection1">
							<input type="radio" id="fgal_sortdirection1" name="fgal_sortdirection" value="desc" {if $fgal_sortdirection == 'desc'}checked="checked"{/if} />
							{tr}Descending{/tr}
						</label>
						<label class="radio-inline" for="fgal_sortdirection2">
							<input type="radio" id="fgal_sortdirection2" name="fgal_sortdirection" value="asc" {if $fgal_sortdirection == 'asc'}checked="checked"{/if} />
							{tr}Ascending{/tr}
						</label>
					</span>
				</div>
			</div>
			{preference name='fgal_quota_show'}
			{preference name='fgal_search'}
			{preference name='fgal_search_in_content'}
			{preference name='fgal_show_thumbactions'}
			{preference name='fgal_thumb_max_size'}
			{preference name='fgal_browse_name_max_length'}
			{preference name='fgal_image_max_size_x'}
			{preference name='fgal_image_max_size_y'}
			{preference name='fgal_list_ratio_hits'}
			{preference name='fgal_display_properties'}
			{preference name='fgal_display_replace'}
			{preference name='fgal_checked'}
			<fieldset>
				{include file='fgal_listing_conf.tpl'}
			</fieldset>
		{/tab}

		{if $section eq 'admin'}
			{tab name="{tr}Admin Listings{/tr}"}
				<h2>{tr}Admin Listings{/tr}</h2>
				<span class="help-block">{tr}Configuration for gallery administration listings{/tr}</span>
				<fieldset>
					{include file='fgal_listing_conf.tpl' fgal_options='' fgal_listing_conf=$fgal_listing_conf_admin}
				</fieldset>
			{/tab}
		{/if}


		{tab name="{tr}Search Indexing{/tr}"}
			{preference name=fgal_enable_auto_indexing}

			<input name="filegalhandlers" type="hidden" />
			<div class="adminoptionbox">
				<fieldset>
					<legend>{tr}Handlers{/tr}{help url="File+Gallery+Config#File_galleries_search_indexing"}</legend>
					<div class="adminoptionbox">
						<div class="adminoptionlabel">{tr}Add custom handlers to make your files &quot;searchable&quot; content{/tr}.
							<ul>
								<li>
									{tr}Use <strong>%1</strong> as the internal file name. For example, use <strong>strings %1</strong> to convert the document to text, using the Unix <strong>strings</strong> command{/tr}.
								</li>
								<li>
									{tr}To delete a handler, leave the <strong>System Command</strong> field blank{/tr}.
								</li>
							</ul>
						</div>
					</div>

					{if !empty($missingHandlers)}
						{tr}Tiki is pre-configured to handle many common types. If any of those are listed here, it is because the command line tool is unavailable.{/tr}
						{remarksbox type=warning title="{tr}Missing Handlers{/tr}"}
							{foreach from=$missingHandlers item=mime}
								{$mime|escape}
								<br>
							{/foreach}
						{/remarksbox}
						{if $vnd_ms_files_exist}
							<div class="adminoptionbox">
								{remarksbox type=info title="{tr}Mime Types{/tr}"}
									<p>
										{tr}Previous versions of Tiki may have assigned alternative mime-types to Microsoft Office files, such as "application/vnd.ms-word" and these need to be changed to be "application/msword" for the default file indexing to function properly.{/tr}
									</p>
									<input type="submit" class="btn btn-default btn-sm" name="filegalfixvndmsfiles" value="{tr}Fix vnd.ms-* mime type files{/tr}"/>
								{/remarksbox}
							</div>
						{/if}
					{/if}

					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<table class="table table-responsive">
								<thead>
									<tr>
										<th>{tr}MIME Type{/tr}</th>
										<th>{tr}System Command{/tr}</th>
									</tr>
								</thead>
								<tbody>
								{foreach key=mime item=cmd from=$fgal_handlers}
									<tr>
										<td>{$mime}</td>
										<td>
											<input name="mimes[{$mime}]" class="form-control" type="text" value="{$cmd|escape:html}" />
										</td>
									</tr>
								{/foreach}
								<tr>
									<td class="odd">
										<input name="newMime" type="text" size="30" />
									</td>
									<td class="odd">
										<input name="newCmd" type="text" size="30"/>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</fieldset>

				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<div align="center">
							<input type="submit" class="btn btn-default btn-sm" name="filegalredosearch" value="{tr}Reindex all files for search{/tr}"/>
						</div>
					</div>
				</div>
			</div>
		{/tab}

		{tab name="{tr}Enhancements{/tr}"}
			<h2>{tr}Enhancements{/tr}</h2>

			<fieldset>
				<legend>{tr}Access{/tr}</legend>
				{preference name='feature_webdav'}
				{preference name='fgal_elfinder_feature'}
				{preference name='feature_docs'}
				{preference name='fgal_viewerjs_feature'}
				<div class="adminoptionboxchild" id="fgal_viewerjs_feature_childcontainer">
					{preference name='fgal_viewerjs_uri'}
					{if $viewerjs_err}
						<div class="col-sm-8 pull-right">
							{remarksbox type='errors' title="{tr}Warning{/tr}"}{$viewerjs_err}{/remarksbox}
						</div>
					{/if}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Draw{/tr}</legend>
				{preference name=feature_draw}
				<div class="adminoptionboxchild" id="feature_draw_childcontainer">
					{preference name=feature_draw_hide_buttons}
					{preference name=feature_draw_separate_base_image}
					<div class="adminoptionboxchild" id="feature_draw_separate_base_image_childcontainer">
						{preference name=feature_draw_in_userfiles}
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Capture{/tr}</legend>
				{preference name=feature_jcapture}
				<div class="adminoptionboxchild" id="feature_jcapture_childcontainer">
					{preference name=fgal_for_jcapture}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}SCORM{/tr}</legend>
				{preference name=scorm_enabled}
				<div class="adminoptionboxchild" id="scorm_enabled_childcontainer">
					{preference name=scorm_tracker}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Vimeo{/tr}</legend>
				{preference name=vimeo_upload}
				{preference name=vimeo_delete}
				<div class="adminoptionboxchild" id="vimeo_upload_childcontainer">
					{preference name=vimeo_default_gallery}
					{preference name=vimeo_consumer_key}
					{preference name=vimeo_consumer_secret}
					{preference name=vimeo_access_token}
					{preference name=vimeo_access_token_secret}
				</div>
			</fieldset>

		{/tab}
	{/tabset}

	<br>{* I cheated. *}
	 <div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="filegalhandlers" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
