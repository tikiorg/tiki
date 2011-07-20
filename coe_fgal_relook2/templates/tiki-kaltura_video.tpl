{title help="Kaltura" admpage="video"}
	{if $kmode eq 'edit'}{tr}Change Details:{/tr}{$videoInfo->name}
	{elseif $kmode eq 'remix' || $kmode eq 'dupl'}{tr}Remix{/tr}
	{elseif $kmode eq 'view'}{tr}View:{/tr}{$videoInfo->name}
	{else}{tr}Kaltura Video{/tr}{/if}{/title}
<div class="navbar">
	{if $tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y'}
	{button _text="{tr}Media Entries{/tr}" href="tiki-list_kaltura_entries.php?list=media"}
	{/if}
	{if $kmode ne ''}
	{if $kmode ne 'edit' and ($tiki_p_edit_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y')}
		{if $entryType eq "media"}
			{button _text="{tr}Change Details{/tr}" href="tiki-kaltura_video.php?mediaId=$videoId&action=edit"}
		{else}
			{button _text="{tr}Change Details{/tr}" href="tiki-kaltura_video.php?mixId=$videoId&action=edit"}		
		{/if}
	{/if}
	{if $kmode ne 'remix' and ($tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y')}
		{if $entryType eq "media"}
			{button _text="{tr}Remix{/tr}" href="tiki-kaltura_video.php?mediaId=$videoId&action=remix"}
		{else}
			{button _text="{tr}Remix{/tr}" href="tiki-kaltura_video.php?mixId=$videoId&action=remix"}		
		{/if}
	{/if}
	{if $kmode eq 'remix' and $editor eq 'kse'}
	{button _text="{tr}Advanced Editor{/tr}" href="tiki-kaltura_video.php?mixId=$videoId&action=remix&editor=kae"}
	{/if}
	{if $kmode eq 'remix' and $editor eq 'kae'}
	{button _text="{tr}Simple Editor{/tr}" href="tiki-kaltura_video.php?mixId=$videoId&action=remix&editor=kse"}
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
			<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" height="365" width="595" data="{$prefs.kServiceUrl}index.php/kwidget/wid/{$prefs.kdpWidget}/uiconf_id/{$prefs.kdpUIConf}/entry_id/{$videoInfo->id}">
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="allowFullScreen" value="true" />
			<param name="movie" value="{$prefs.kServiceUrl}index.php/kwidget/wid/{$prefs.kdpWidget}/uiconf_id/{$prefs.kdpUIConf}/entry_id/{$videoInfo->id}"/>
			<param name="flashVars" value="entry_id={$videoInfo->id}"/>
			<param name="wmode" value="opaque"/>
			</object>			
			</td>
		</tr>
		</table>
     <table width="100%" class="formcolor">
				<tr>
					<td class="even">{tr}Video Title{/tr}</td>
					<td class="even">
						{if $kmode eq 'edit'}
						<input style="width:99%" type="text" name="name" {if $videoInfo->name}value="{$videoInfo->name}"{/if} size="40" />
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
						<input style="width:99%" type="text" name="tags" {if $videoInfo->tags}value="{$videoInfo->tags}"{/if} size="40" />
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
	
	{capture name=remix_video assign=edit_remix}
		<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" data="{$prefs.kServiceUrl}{if $editor eq 'kae'}kae/ui_conf_id/{$prefs.kaeUIConf}" height="672" width="825" {else}kse/ui_conf_id/{$prefs.kseUIConf}" height="546" width="890"{/if}>
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="allowFullScreen" value="true" />
			<param name="flashVars" value="{$seflashVars}"/>
			<param name="wmode" value="opaque"/>
		</object>
	{/capture}
	
	<div>	
	{if $kmode eq 'edit'}
	<div id="form">
	<form  action='tiki-kaltura_video.php' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
		{$edit_info}
		<input type="hidden" name="action" value="edit">
		<input type="hidden" name="{$entryType}Id" value="{$videoInfo->id}"/>
		<input name="update" type="submit" value="{tr}Save{/tr}"/>
	</form>
	</div>
	{elseif $kmode eq 'view'}
	<div>
	{$edit_info}
	</div>
	{elseif $kmode eq 'remix' || $kmode eq 'dupl'}
	<div>
	{$edit_remix}
	</div>
	{else}
		{tr}No action specified{/tr} {$kmode}
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


