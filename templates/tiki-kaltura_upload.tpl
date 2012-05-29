{title help="Kaltura" admpage="video"}{tr}Upload Media{/tr}{/title}

<div class="navbar">
	{if $tiki_p_list_videos eq 'y'}
	{button _text="{tr}List Media{/tr}" href="tiki-list_kaltura_entries.php"}
	{/if}
</div>

{if $count > 0}
	{remarksbox type="info" title="{tr}Upload Successful{/tr}"}
		{if $count eq 1}
			{tr}You have successfully added one new media item{/tr}
		{else}
			{tr _0=$count}You have successfully added %0 new media items{/tr}
		{/if}
	{/remarksbox}
	<p>
		{button _text="{tr}Add more media{/tr}" href="tiki-kaltura_upload.php"}
	</p>
{else}
	<div id="kcwFlashObject" class="kcwFlashObject kcwUIConf"></div>

	<form name='kcw' id='kcw' action='tiki-kaltura_upload.php' method='post' enctype='multipart/form-data' style='margin:0px; padding:0px'>
		<input type="hidden" name="kcw" value="true"/>
	</form>

	{jq notonready=true}

function afterAddEntry (entries) {
	for( var i = 0; i < entries.length; i++) {
		$("#kcw").append($('<input type="hidden" name="entryId[]" value="'+entries[i].entryId+'"/>'));
	}
	$("#kcw").submit();
}

var params = {
	   allowScriptAccess: "always",
	   allowNetworking: "all",
	   wmode: "opaque"
};

var flashVars = {{$cwflashVars}};
swfobject.embedSWF("{{$prefs.kaltura_kServiceUrl}}kcw/ui_conf_id/{{$prefs.kaltura_kcwUIConf|escape}}", "kcwFlashObject", "680", "360", "9.0.0", "expressInstall.swf", flashVars, params);

	{/jq}

{/if}
