{title help="Kaltura" admpage="video"}
	{if $kmode eq 'edit'}{tr}Change Details:{/tr}{$videoInfo->name}
	{elseif $kmode eq 'view'}{tr}View:{/tr}{$videoInfo->name}
	{else}{tr}Kaltura Video{/tr}{/if}
{/title}
<div class="navbar btn-group">
	{if $tiki_p_list_videos eq 'y'}
		{button class="btn btn-default" _text="{tr}List Media{/tr}" href="tiki-list_kaltura_entries.php"}
	{/if}
	{if $kmode ne ''}
		{if $kmode ne 'edit' and ($tiki_p_edit_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y')}
			{if $entryType eq "media"}
				{button class="btn btn-default" _text="{tr}Change Details{/tr}" href="tiki-kaltura_video.php?mediaId=$videoId&action=edit"}
			{else}
				{button class="btn btn-default" _text="{tr}Change Details{/tr}" href="tiki-kaltura_video.php?mixId=$videoId&action=edit"}
			{/if}
		{/if}
	{/if}
</div>

<hr class="clear"/>
{capture name=upload_file assign=edit_info}
	<div class="fgal_file">
		<div class="fgal_file_c2">
			<table width="100%">
				<tr>
					<td width="50%" align="center">
						{wikiplugin _name=kaltura id=$videoInfo->id}{/wikiplugin}
					</td>
				</tr>
			</table>
			<table width="100%" class="formcolor">
				<tr>
					<td class="even">{tr}Video Title{/tr}</td>
					<td class="even">
						{if $kmode eq 'edit'}
							<input style="width:99%" type="text" name="name" {if $videoInfo->name}value="{$videoInfo->name}"{/if} size="40">
						{else}
							{$videoInfo->name}
						{/if}
					</td>
				</tr>
				<tr>
					<td class="odd">{tr}Description{/tr}</td>
					<td class="odd">
						{if $kmode eq 'edit'}
							<textarea style="width:99%" rows="2" cols="40" name="description">{if $videoInfo->description}{$videoInfo->description}{/if}</textarea>
						{else}
							{$videoInfo->description}
						{/if}
					</td>
				</tr>
				<tr>
					<td class="even">{tr}Tags{/tr}</td>
					<td class="even">
						{if $kmode eq 'edit'}
							<input style="width:99%" type="text" name="tags" {if $videoInfo->tags}value="{$videoInfo->tags}"{/if} size="40">
						{else}
							{$videoInfo->tags}
						{/if}
					</td>
				</tr>
				{if isset($smarty.request.mixId)}
					<tr>
						<td class="even">{tr}Editor{/tr}</td>
						<td class="even">
							{if $kmode eq 'edit'}
								<select name="editor">
									<option value="kse"{if $videoInfo->editorType eq 1}selected="selected"{/if}>{tr}Simple{/tr}</option>
									<option value="kae"{if $videoInfo->editorType eq 2}selected="selected"{/if}>{tr}Advanced{/tr}</option>
								</select>
							{else}
								{if $videoInfo->editorType eq 1}{tr}Simple{/tr}{else}{tr}Advanced{/tr}{/if}
							{/if}
						</td>
					</tr>
				{/if}

				<tr>
					<td class="even">{tr}Embed code{/tr}</td>
					<td class="even">
						{ldelim}kaltura id="{$videoId}"{rdelim}
					</td>
				</tr>

				{if $kmode eq 'view'}
					<tr>
						<td class="odd">{tr}Duration{/tr}</td>
						<td class="odd">{$videoInfo->duration}s</td>
					</tr>
					<tr>
						<td class="even">{tr}Views{/tr}</td>
						<td class="even">{$videoInfo->views}</td>
					</tr>
					<tr>
						<td class="odd">{tr}Plays{/tr}</td>
						<td class="odd">{$videoInfo->plays}</td>
					</tr>
				{/if}
			</table>

		</div>
	</div>
{/capture}

<div>
	{if $kmode eq 'edit'}
		<div id="form">
			<form action='tiki-kaltura_video.php' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
				{$edit_info}
				<input type="hidden" name="action" value="edit">
				<input type="hidden" name="{$entryType}Id" value="{$videoInfo->id}">
				<input name="update" type="submit" class="btn btn-default" value="{tr}Save{/tr}">
			</form>
		</div>
	{elseif $kmode eq 'view'}
		<div>
			{$edit_info}
		</div>
	{else}
		{tr}No action specified.{/tr} {tr}This file is not expected to be called directly.{/tr} {$kmode}
	{/if}
</div>

{jq notonready=true}
function CloseClick(isModified) {
	window.location="./tiki-list_kaltura_entries.php";
}
function SaveClick() {
	window.location="./tiki-list_kaltura_entries.php";
}
function closeEditorHandler() {
	window.location="./tiki-list_kaltura_entries.php";
}
var kaeCallbacksObj = {
	// unfortunately the advanced editor has sends the publish event before you've picked the thumb
	// hideous hack courtesy of the OVC Hack Day.
	publishHandler: function () { setTimeout( function() {SaveClick();}, 10000) },
	closeHandler: closeEditorHandler
};
{/jq}

