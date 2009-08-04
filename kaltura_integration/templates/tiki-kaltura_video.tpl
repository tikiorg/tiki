{title help="Kaltura" admpage="kaltura"}
	{if $mode eq 'edit'}{tr}Edit Info:{/tr}{$videoInfo.name}
	{elseif $mode eq 'remix' || $mode eq 'dupl'}{tr}Remix{/tr}
	{elseif $mode eq 'view'}{tr}View:{/tr}{$videoInfo.name}
	{else}{tr}Upload File{/tr}{/if}{/title}

<div class="navbar">
	{if $tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y'}
	{button _text="{tr}List Entries{/tr}" href="tiki-list_kaltura_entries.php" }
	{/if}
	{if $mode ne 'edit' and ($tiki_p_edit_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y')}
	{button _text="{tr}Edit{/tr}" href="tiki-kaltura_video.php?videoId=$videoId&action=edit" }
	{/if}
	{if $mode ne 'remix' and ($tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y')}
	{button _text="{tr}Remix{/tr}" href="tiki-kaltura_video.php?videoId=$videoId&action=remix" }
	{/if}
</div>

<hr class="clear"/>

	{capture name=upload_file assign=edit_info}
		<div class="fgal_file">
		<div class="fgal_file_c2">
		<table width="100%">
		<tr>
			<td width="50%">
			<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" height="365" width="400" data="http://www.kaltura.com/index.php/kwidget/wid/_23929/uiconf_id/48411/entry_id/{$videoInfo.id}">
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="allowFullScreen" value="true" />
			<param name="bgcolor" value="#000000" />
			<param name="movie" value="http://www.kaltura.com/index.php/kwidget/wid/_23929/uiconf_id/48411/entry_id/{$videoInfo.id}"/>
			<param name="flashVars" value="entry_id={$videoInfo.id}"/>
			<param name="wmode" value="opaque"/>
			</object>			
			</td>
			<td>
				<table width="100%">
				<tr>
					<td>{tr}Video Title:{/tr}</td>
					<td>
						{if $mode eq 'edit'}
						<input style="width:100%" type="text" name="name" {if $videoInfo.name}value="{$videoInfo.name}"{/if} size="40" />
						{else}
						{$videoInfo.name}
						{/if}
					</td>
				</tr>
				<tr>
					<td>{tr}Description:{/tr}</td>
					<td>
						{if $mode eq 'edit'}
						<textarea style="width:100%" rows="2" cols="40" name="description">{if $videoInfo.description}{$videoInfo.description}{/if}</textarea>
						{else}
						{$videoInfo.description}
						{/if}
					</td>
				</tr>
				<tr>
					<td>{tr}Tags:{/tr}</td>
					<td>
						{if $mode eq 'edit'}
						<input style="width:100%" type="text" name="tags" {if $videoInfo.tags}value="{$videoInfo.tags}"{/if} size="40" />
						{else}
						{$videoInfo.tags}
						{/if}
					</td>
				</tr>
				
				{if $mode eq 'view'}
				<td>{tr}Duration:{/tr}</td>
					<td>
						{$videoInfo.duration}'s
					</td>
				</tr>
				<td>{tr}Views:{/tr}</td>
					<td>
						{$videoInfo.views}
					</td>
				</tr>
				<td>{tr}Plays:{/tr}</td>
					<td>
						{$videoInfo.plays}
					</td>
				</tr>
				{/if}
						<input type="hidden" name="videoId" value="{$videoInfo.id}"/>
				</table>
			</td>
		</tr>
		</table>
		</div>
		</div>
	{/capture}
	
	{capture name=upload_file assign=new_entries}
		<div class="fgal_file">
		<div class="fgal_file_c2">
		{section name=idx loop=$entries}
		<table width="100%">
		<tr><td width="50%"><img src="{$entries[idx].thumbnailUrl}"></img></td><td>
		<table width="100%">
		<tr>
			<td>{tr}Video Title:{/tr}</td>
			<td>
				{$entries[idx].name}
			</td>
		</tr>
		<tr>
			<td>{tr}Description:{/tr}</td>
			<td>
				{$entries[idx].description}
			</td>
		</tr>
		<tr>
			<td>{tr}Tags:{/tr}</td>
			<td>
				{$entries[idx].tags}
			</td>
		</tr>
		<tr>
		</table>
		</td></tr>
		{/section}
		<br>
		</table>
		</div>
		</div>
	{/capture}
	
	{capture name=upload_video assign=kcw}
		<object id="kaltura_contribution_wizard" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="360" width="680" data="http://www.kaltura.com/kcw/ui_conf_id/36200">
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="movie" value="http://www.kaltura.com/kcw/ui_conf_id/36200"/>
    		<param name="flashVars" value="{$cwflashVars}" />
		</object>
	{/capture}
	
	{capture name=remix_video assign=edit_remix}
		<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" height="546" width="890"	data="http://www.kaltura.com/kse/ui_conf_id/36300">
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="allowFullScreen" value="true" />
			<param name="bgcolor" value="#000000" />
			<param name="movie" value="http://www.kaltura.com/kse/ui_conf_id/36300"/>
			<param name="flashVars" value="{$seflashVars}"/>
			<param name="wmode" value="opaque"/>
		</object>
	{/capture}
	
	<div>	
	{if $mode eq 'edit'}
	<div id="form">
	<form  action='tiki-upload_video.php' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
	{$edit_info}
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
	{elseif $mode eq 'new_entries'}
	<div id="new_entries">
	{$new_entries}
	</div>
	{else}
	<div>
		{$kcw}
		<form name='kcw' id='kcw' action='tiki-kaltura_video.php' method='post' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
		<input type="hidden" name="kcw" value="true"/>
		<div id="kcw_entries">
		</div>
		</form>
	</div>
	{/if}
	</div>
	
<script type="text/javascript">
	
{literal}
		function afterAddEntry (entries) {	
		var tmp='';
			
			for( var i = 0; i < entries.length; i++)
			{
				tmp += '<input type="hidden" name="entryId[]" value="'+entries[i].entryId+'"/>';
				
			}
		document.getElementById('kcw_entries').innerHTML = tmp;
		document.kcw.submit();
		}
		
		function handleGotoEditorWindow (kshowId, pd_extraData) {
          alert('Editor');
        }
{/literal}
		</script>


