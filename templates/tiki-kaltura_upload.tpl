{title}{tr}Upload to Kaltura{/tr}{/title}
<div class="navbar">
	{if $tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y'}
	{button _text="{tr}List Entries{/tr}" href="tiki-list_kaltura_entries2.php" }
	{/if}
	{if $mode ne '' and $mode ne 'new_entries'}
	{if $mode ne 'edit' and ($tiki_p_edit_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y')}
	{button _text="{tr}Change Details{/tr}" href="tiki-kaltura_video2.php?videoId=$videoId&action=edit" }
	{/if}
	{if $mode ne 'remix' and ($tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y')}
	{button _text="{tr}Remix{/tr}" href="tiki-kaltura_video2.php?videoId=$videoId&action=remix" }
	{/if}
	{if $mode eq 'remix' and $editor eq 'kse'}
	{button _text="{tr}Advance Editor{/tr}" href="tiki-kaltura_video2.php?videoId=$videoId&action=remix&editor=kae" }
	{/if}
	{if $mode eq 'remix' and $editor eq 'kae'}
	{button _text="{tr}Simple Editor{/tr}" href="tiki-kaltura_video2.php?videoId=$videoId&action=remix&editor=kse" }
	{/if}
	{/if}
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<br>
{if $count > 0}
{remarksbox type="info" title="{tr}Upload Successful{/tr}" }{tr}You can successfully added {$count} new entry/entries{/tr}{/remarksbox}
{/if}
<br>		
<div id="kcwFlashObject"></div>

<script type="text/javascript">
{literal}
	var params = {
       	allowScriptAccess: "always",
       	allowNetworking: "all",
       	wmode: "opaque"
	};
	
	function afterAddEntry (entries) {	
		var tmp='';
			
			for( var i = 0; i < entries.length; i++)
			{
				tmp += '<input type="hidden" name="entryId[]" value="'+entries[i].entryId+'"/>';
				
			}
		document.getElementById('new_entries').innerHTML = tmp;
		document.kcw.submit();
		}
{/literal}
	var flashVars = {$cwflashVars};
	swfobject.embedSWF("http://www.kaltura.com/kcw/ui_conf_id/1000741", "kcwFlashObject", "680", "360", "9.0.0", "expressInstall.swf", flashVars, params);
</script>
		
<form name='kcw' id='kcw' action='tiki-kaltura_upload.php' method='post' enctype='multipart/form-data' style='margin:0px; padding:0px'>
	<input type="hidden" name="kcw" value="true"/>
	<div id="new_entries">
	</div>
</form>