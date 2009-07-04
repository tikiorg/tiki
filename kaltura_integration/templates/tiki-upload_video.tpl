{title help="Video+Galleries" admpage="vgal"}{if $editVideoId}{tr}Edit Video:{/tr} {$videoInfo.filename}{else}{tr}Upload File{/tr}{/if}{/title}

{if !empty($galleryId) or (count($galleries) > 0 and $tiki_p_list_video_galleries eq 'y') or count($uploads) > 0}
<div class="navbar">
	{if !empty($galleryId)}
			{button href="tiki-list_file_gallery.php?galleryId=$galleryId" _text="{tr}Browse Gallery{/tr}"}
	{/if}
	{if count($galleries) > 0 and $tiki_p_list_video_galleries eq 'y'}
			{button href="tiki-list_file_gallery.php" _text="{tr}List Galleries{/tr}"}
	{/if}
	
	{if $editVideoId and $editMode eq 'info' and ($tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y')}
	{button href="tiki-upload_video.php?galleryId=$galleryId&videoId=$editVideoId&edit=remix" _text="{tr}Remix{/tr}"}
	{/if}
</div>
{/if}

{if count($galleries) > 0 || $editVideoId}	
	{capture name=upload_file assign=edit_info}
		<hr class="clear"/>
		<div class="fgal_file">
		<div class="fgal_file_c2">
		<table width="100%">
		<tr>
			<td width="50%">
			<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" height="365" width="400" data="http://www.kaltura.com/index.php/kwidget/wid/_23929/uiconf_id/1000308">
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="allowFullScreen" value="true" />
			<param name="bgcolor" value="#000000" />
			<param name="movie" value="http://www.kaltura.com/index.php/kwidget/wid/_23929/uiconf_id/1000308"/>
			<param name="flashVars" value="entryId={$videoInfo.entryId}"/>
			<param name="wmode" value="opaque"/>
			</object>
			
			</td>
			<td>
		<table width="100%">
		<tr>
			<td>{tr}Video Title:{/tr}</td>
			<td>
				<input style="width:100%" type="text" name="name[]" {if $videoInfo.name}value="{$videoInfo.name}"{/if} size="40" />
			</td>
		</tr>
		<tr>
			<td>{tr}Description:{/tr}</td>
			<td>
				<textarea style="width:100%" rows="2" cols="40" name="description[]">{if $videoInfo.description}{$videoInfo.description}{/if}</textarea>
			</td>
		</tr>
		<tr>
			<td>{tr}Tags:{/tr}</td>
			<td>
				<input style="width:100%" type="text" name="tags[]" {if $videoInfo.tags}value="{$videoInfo.tags}"{/if} size="40" />
			</td>
		</tr>
		<input type="hidden" name="galleryId" value="{$galleryId}"/>
		<input type="hidden" name="videoId" value="{$editVideoId}"/>
	
		<tr>
			<td>{tr}Gallery:{/tr}</td>
			<td>
				<select name="galleryId[]">
				{section name=idx loop=$galleries}
					<option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $videoInfo.galleryId}selected="selected"{/if}>{$galleries[idx].name|escape}</option>
				{/section}
				</select>
			</td>
		</tr>
	</table></td></tr>
	</table>
		</div>
		</div>
	{/capture}
	
	{capture name=upload_file assign=edit_entries}
		<hr class="clear"/>
		<div class="fgal_file">
		<div class="fgal_file_c2">
		{section name=idx loop=$videoEntries}
		<table width="100%">
		<tr><td width="50%"><img src="{$videoEntries[idx].thumbnail}"></img></td><td>
		<table width="100%">
		<tr>
			<td>{tr}Video Title:{/tr}</td>
			<td>
				<input style="width:100%" type="text" name="name[]" {if $videoEntries[idx].name}value="{$videoEntries[idx].name}"{/if} size="40" />
			</td>
		</tr>
		<tr>
			<td>{tr}Description:{/tr}</td>
			<td>
				<textarea style="width:100%" rows="2" cols="40" name="description[]">{if $videoEntries[idx].description}{$videoEntries[idx].description}{/if}</textarea>
			</td>
		</tr>
		<tr>
			<td>{tr}Tags:{/tr}</td>
			<td>
				<input style="width:100%" type="text" name="tags[]" {if $videoEntries[idx].tags}value="{$videoEntries[idx].tags}"{/if} size="40" />
			</td>
		</tr>
		<tr>
		{if !$galleryId}
			<td>{tr}Gallery:{/tr}</td>
			<td>
				<select name="galleryId[]">
				{section name=idy loop=$galleries}
					<option value="{$galleries[idy].id|escape}" {if $galleries[idy].id eq $galleryId}selected="selected"{/if}>{$galleries[idy].name|escape}</option>
				{/section}
				</select>
			</td>
		</tr>
		{/if}
		<input type="hidden" name="entryId[]" value="{$videoEntries[idx].entryId}"/>
		<input type="hidden" name="videoId[]" value="{$videoEntries[idx].id}"/>
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
	<param name="bgcolor" value=#000000 />
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
	{if $editVideoId}
	<div id="form">
	{if $editMode eq 'info'}
	<form  action='tiki-upload_video.php' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
	{$edit_info}
	{include file=categorize.tpl notable='y'}<br/>
	<input name="update" type="submit" value="{tr}Save{/tr}"/>
	</form>
	{/if}
	{if $editMode eq 'remix'}
	{$edit_remix}
	{/if}
	</div>
	{elseif $editEntries}
	<div id="kcw_edit">
	<form action='tiki-upload_video.php' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
	{$edit_entries}
	<input type="submit" name="update_entries" value="Save"/>
	</form>
	</div>
	{else}
	<div id="kcw">
		{$kcw}
		<form name='kcw_form' id=name='kcw_form' action='tiki-upload_video.php' method='post' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
		<input type="hidden" name="gallery" value="{$galleryId}"/>
		<div id="kcw_entries">
		</div>
		</form>
	</div>
	{/if}
	<hr class="clear"/>
</div>

{else}
	{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle;"}
	{tr}No gallery available.{/tr}
	{tr}You have to create a gallery first!{/tr}
	<p><a href="tiki-list_file_gallery.php{if $filegals_manager neq ''}?filegals_manager={$filegals_manager|escape}{/if}">{tr}Create New Gallery{/tr}</a></p>
{/if}
	
<script type="text/javascript">
	
{literal}
		function afterAddEntry (entries) {	
		var tmp='<input type="hidden" name="kcw_next" value="true"/>';
			
			for( var i = 0; i < entries.length; i++)
			{
				tmp += '<input type="hidden" name="entryId[]" value="'+entries[i].entryId+'"/>';
				
			}
		document.getElementById('kcw_entries').innerHTML = tmp;
		//alert(tmp);
		document.kcw_form.submit();
		}
		
		function handleGotoEditorWindow (kshowId, pd_extraData) {
          alert('Editor');
        }
{/literal}
		</script>


