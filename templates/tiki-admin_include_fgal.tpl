{* $Id$ *}
{if !empty($errors)}
	{remarksbox type="errors" title="{tr}Errors{/tr}"}
		{foreach from=$errors item=error}
			{$error|escape}
			<br />
		{/foreach}
	{/remarksbox}
{/if}
{if !empty($feedbacks)}
	{remarksbox type="note" title="{tr}Feedback{/tr}"}
		{foreach from=$feedbacks item=feedback}
			{$feedback|escape}
			<br />
		{/foreach}
	{/remarksbox}
{/if}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}To create or remove file galleries, select{/tr} <a class="rbox-link" href="tiki-list_file_gallery.php">{tr}File Galleries{/tr}</a> {tr}from the application menu{/tr}.
	<hr />
	{tr}If you decide to store files in a directory you must ensure that the user cannot access directly to the directory.{/tr}
	{tr}You have two options to accomplish this:<br /><ul><li>Use a directory outside your document root, make sure your php script can read and write to that directory</li><li>Use a directory inside the document root and use .htaccess to prevent the user from listing the directory contents</li></ul>{/tr}
	{tr}To configure the directory path use UNIX like paths for example files/ or c:/foo/files or /www/files/{/tr}
{/remarksbox}

<form action="tiki-admin.php?page=fgal" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="filegalhandlers" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="fgal_admin"}
		{tab name="{tr}General Settings{/tr}"}
		
			{preference name=home_file_gallery}
			{preference name='fgal_use_db'}
			<div class="adminoptionboxchild fgal_use_db_childcontainer n">
				{preference name='fgal_use_dir'}
			</div>
			{button href="tiki-admin.php?page=fgal&amp;move=to_fs" _text="{tr}Move files from database to directory{/tr}"}
			{button href="tiki-admin.php?page=fgal&amp;move=to_db" _text="{tr}Move files from directory to database{/tr}"}

			{preference name='fgal_podcast_dir'}

			<input type="hidden" name="filegalfeatures" />

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
				<div class="adminoptionboxchild" id="fgal_prevent_negative_score_childcontainer">
					{if $prefs.feature_score ne 'y'}
						<br />
						{icon _id=information}{tr}Score is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
					{/if}
				</div>

				{preference name='fgal_allow_duplicates'}
				{preference name='feature_file_galleries_batch'}
				<div class="adminoptionboxchild" id="feature_file_galleries_batch_childcontainer">
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

		{tab name="{tr}Gallery Listings{/tr}"}
			{remarksbox title="Note"}
				{tr}Changing these settings will <em>not</em> affect existing file galleries. These changes will apply <em>only</em> to new file galleries{/tr}.
			{/remarksbox}

			<input type="hidden" name="filegallistprefs" />
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="fgal_sortorder">{tr}Default sort order:{/tr}</label>
					<select name="fgal_sortorder" id="fgal_sortorder">
						{foreach from=$options_sortorder key=key item=item}
							<option value="{$item|escape}" {if $fgal_sortorder == $item} selected="selected"{/if}>{$key}</option>
						{/foreach}
					</select>
					<div class="adminoptionboxchild">
						<div class="adminoptionlabel">
							<input type="radio" id="fgal_sortdirection1" name="fgal_sortdirection" value="desc" {if $fgal_sortdirection == 'desc'}checked="checked"{/if} />
							<label for="fgal_sortdirection1">{tr}Descending{/tr}</label>
						</div>
						<div class="adminoptionlabel">
							<input type="radio" id="fgal_sortdirection2" name="fgal_sortdirection" value="asc" {if $fgal_sortdirection == 'asc'}checked="checked"{/if} />
							<label for="fgal_sortdirection2">{tr}Ascending{/tr}</label>
						</div>
					</div>	
				</div>
			</div>
			{preference name='fgal_quota_show'}
			{preference name='fgal_search'}
			{preference name='fgal_search_in_content'}
			{preference name='fgal_show_thumbactions'}
			{preference name='fgal_thumb_max_size'}
			{preference name='fgal_list_ratio_hits'}
			{preference name='fgal_display_properties'}
			{preference name='fgal_display_replace'}
			{preference name='fgal_checked'}

			<fieldset>
				<legend>{tr}Select which items to display when listing galleries: {/tr}</legend>
				<table class="admin">
					{include file='fgal_listing_conf.tpl'}
				</table>
			</fieldset>
		{/tab}

		{if $section eq 'admin'}
			{tab name="{tr}Admin Gallery Listings{/tr}"}
				<fieldset>
					<legend>{tr}Select which items to display when admin galleries: {/tr}</legend>
					<table class="admin">
						{include file='fgal_listing_conf.tpl' fgal_options='' fgal_listing_conf=$fgal_listing_conf_admin}
					</table>
				</fieldset>
			{/tab}
		{/if}


		{tab name="{tr}Search Indexing{/tr}"}
			<div class="adminoptionbox">
				<div class="adminoption">
					<input type="checkbox" id="fgal_enable_auto_indexing" name="fgal_enable_auto_indexing" {if $prefs.fgal_enable_auto_indexing eq 'y'}checked="checked"{/if} />
				</div>
				<div class="adminoptionlabel">
					<label for="fgal_enable_auto_indexing">{tr}Automatically index files on upload or change{/tr}.</label>
				</div>
			</div>

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
						{remarksbox type=warning title="{tr}Missing Handlers{/tr}"}
							{foreach from=$missingHandlers item=mime}
								{$mime|escape}
								<br />
							{/foreach}
						{/remarksbox}
					{/if}

					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							<table class="formcolor">
								<tr>
									<th>{tr}MIME Type{/tr}</th>
									<th>{tr}System Command{/tr}</th>
								</tr>
								{cycle values="odd,even" print=false}
								{foreach key=mime item=cmd from=$fgal_handlers}
									<tr class="{cycle}">
										<td>{$mime}</td>
										<td>
											<input name="mimes[{$mime}]" type="text" value="{$cmd|escape:html}" size="30"/>
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
							</table>
						</div>
					</div>
				</fieldset>

				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<div align="center">
							<input type="submit" name="filegalredosearch" value="{tr}Reindex all files for search{/tr}"/>
						</div>
					</div>
				</div>
			</div>
		{/tab}
	{/tabset}

	<div class="input_submit_container clear" style="text-align: center">
		<input type="submit" name="filegalhandlers" value="{tr}Change preferences{/tr}" />
	</div>
</form>
