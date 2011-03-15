{title}{tr}Upload to Kaltura{/tr}{/title}
<div class="navbar">
	{if $tiki_p_remix_videos eq 'y' or $tiki_p_admin_video_galleries eq 'y' or $tiki_p_admin eq 'y'}
	{button _text="{tr}List Media Entries{/tr}" href="tiki-list_kaltura_entries.php?list=media"}
	{button _text="{tr}List Remix Entries{/tr}" href="tiki-list_kaltura_entries.php"}
	{/if}
</div>

<script type="text/javascript" src="lib/swfobject/swfobject.js"></script>
<br />
{if $count > 0}
{remarksbox type="info" title="{tr}Upload Successful{/tr}"}{tr}You have successfully added {$count} new entry/entries{/tr}{/remarksbox}
<p>
{button _text="{tr}Add more media{/tr}" href="tiki-kaltura_upload.php"}
</p>
{else}
<br />
<div id="kcwFlashObject"></div>

{jq notonready=true}
var params = {
       allowScriptAccess: "always",
       allowNetworking: "all",
       wmode: "opaque"
};
	
function afterAddEntry (entries) {	
	var tmp='';
	for( var i = 0; i < entries.length; i++) {
		tmp += '<input type="hidden" name="entryId[]" value="'+entries[i].entryId+'"/>';
	}
	document.getElementById('new_entries').innerHTML = tmp;
	document.kcw.submit();
}
var flashVars = {{$cwflashVars}};
swfobject.embedSWF("{{$prefs.kServiceUrl}}kcw/ui_conf_id/{{$prefs.kcwUIConf|escape}}", "kcwFlashObject", "680", "360", "9.0.0", "expressInstall.swf", flashVars, params);
{/jq}
		
<form name='kcw' id='kcw' action='tiki-kaltura_upload.php' method='post' enctype='multipart/form-data' style='margin:0px; padding:0px'>
	<input type="hidden" name="kcw" value="true"/>
	<div id="new_entries">
	</div>
</form>
{/if}
