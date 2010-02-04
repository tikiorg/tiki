{title help="Kaltura" admpage="kaltura"}
	{if $mode eq 'edit'}{tr}Change Details:{/tr}{$videoInfo->name}
	{elseif $mode eq 'remix' || $mode eq 'dupl'}{tr}Remix{/tr}
	{elseif $mode eq 'view'}{tr}View:{/tr}{$videoInfo->name}
	{else}{/if}{/title}
{if $editor eq ''}
{assign var=editor value=$prefs.default_kaltura_editor}
{/if}
<div class="navbar">
	{if $tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y'}
	{button _text="{tr}List Entries{/tr}" href="tiki-list_kaltura_entries.php" }
	{/if}
	{if $mode ne ''}
	{if $mode ne 'edit' and ($tiki_p_edit_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y')}
		{if $entryType eq "media"}
			{button _text="{tr}Change Details{/tr}" href="tiki-kaltura_video.php?mediaId=$videoId&action=edit" }
		{else}
			{button _text="{tr}Change Details{/tr}" href="tiki-kaltura_video.php?mixId=$videoId&action=edit" }		
		{/if}
	{/if}
	{if $mode ne 'remix' and ($tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y')}
		{if $entryType eq "media"}
			{button _text="{tr}Remix{/tr}" href="tiki-kaltura_video.php?mediaId=$videoId&action=remix" }
		{else}
			{button _text="{tr}Remix{/tr}" href="tiki-kaltura_video.php?mixId=$videoId&action=remix" }		
		{/if}
	{/if}
	{if $mode eq 'remix' and $editor eq 'kse'}
	{button _text="{tr}Advanced Editor{/tr}" href="tiki-kaltura_video.php?mixId=$videoId&action=remix&editor=kae" }
	{/if}
	{if $mode eq 'remix' and $editor eq 'kae'}
	{button _text="{tr}Simple Editor{/tr}" href="tiki-kaltura_video.php?mixId=$videoId&action=remix&editor=kse" }
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
			<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" height="365" width="700" data="http://www.kaltura.com/index.php/kwidget/wid/{$prefs.kdpWidget}/uiconf_id/{$prefs.kdpUIConf}/entry_id/{$videoInfo->id}">
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="allowFullScreen" value="true" />
			<param name="movie" value="http://www.kaltura.com/index.php/kwidget/wid/{$prefs.kdpWidget}/uiconf_id/{$prefs.kdpUIConf}/entry_id/{$videoInfo->id}"/>
			<param name="flashVars" value="entry_id={$videoInfo->id}"/>
			<param name="wmode" value="opaque"/>
			</object>			
			</td>
		</tr>
		</table>
     <table width="100%" class="normal">
				<tr>
					<td class="even">{tr}Video Title{/tr}</td>
					<td class="even">
						{if $mode eq 'edit'}
						<input style="width:100%" type="text" name="name" {if $videoInfo->name}value="{$videoInfo->name}"{/if} size="40" />
						{else}
						{$videoInfo->name}
						{/if}
					</td>
				</tr>
				<tr>
					<td class="odd">{tr}Description{/tr}</td>
					<td class="odd">
						{if $mode eq 'edit'}
						<textarea style="width:100%" rows="2" cols="40" name="description">{if $videoInfo->description}{$videoInfo->description}{/if}</textarea>
						{else}
						{$videoInfo->description}
						{/if}
					</td>
				</tr>
				<tr>
					<td class="even">{tr}Tags{/tr}</td>
					<td class="even">
						{if $mode eq 'edit'}
						<input style="width:100%" type="text" name="tags" {if $videoInfo->tags}value="{$videoInfo->tags}"{/if} size="40" />
						{else}
						{$videoInfo->tags}
						{/if}
					</td>
				</tr>
				
				{if $mode eq 'view'}
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
						<input type="hidden" name="{$entryType}Id" value="{$videoInfo->id}"/>
	</table>

		</div>
		</div>
	{/capture}
	
	{capture name=remix_video assign=edit_remix}
		<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" data="http://www.kaltura.com/{if $editor eq 'kae'}kae/ui_conf_id/{$prefs.kaeUIConf}" height="672" width="825" {else}kse/ui_conf_id/{$prefs.kseUIConf}" height="546" width="890"{/if}>
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="allowFullScreen" value="true" />
			<param name="flashVars" value="{$seflashVars}"/>
			<param name="wmode" value="opaque"/>
		</object>
	{/capture}
	
	<div>	
	{if $mode eq 'edit'}
	<div id="form">
	<form  action='tiki-kaltura_video.php' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
	{$edit_info}
	<input type="hidden" name="action" value="edit">
	<input name="update" type="submit" value="{tr}Save{/tr}"/>
	</form>
	</div>
	{elseif $mode eq 'view'}
	<div>
	{$edit_info}
	</div>
	{elseif $mode eq 'remix' || $mode eq 'dupl'}
	<div>
	{$edit_remix}
	</div>
	{else}

	{/if}
	</div>

{jq}
    function CloseClick(isModified) {
			window.location="./tiki-list_kaltura_entries.php";
		}

		function SaveClick() {
			window.location="./tiki-list_kaltura_entries.php";
		}
		
		function closeEditorHandler() {
			window.location="./tiki-list_kaltura_entries.php";
		}
{/jq}


