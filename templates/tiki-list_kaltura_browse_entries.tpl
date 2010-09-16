<script src="http://cdn.jquerytools.org/1.0.2/jquery.tools.min.js"></script>
<script src="http://static.flowplayer.org/js/jquery.mousewheel.js"></script>

<link rel="stylesheet" type="text/css" href="http://static.flowplayer.org/tools/css/scrollable-horizontal.css" />
<link rel="stylesheet" type="text/css" href="http://static.flowplayer.org/tools/css/scrollable-buttons.css" />	
{literal}	
<style>


</style>
<script language="javascript">
	function loadMedia(entryId) {
		$('#mykdp').get(0).insertMedia("-1",entryId,'true');
	}
</script>

<script>
// execute your scripts when the DOM is ready. this is a good habit
$(document).ready( function() {

	// initialize scrollable
	$("div.scrollable").scrollable().find("a");

});
</script>
{/literal}

<br>
<br>
	{if $entryType eq "mix"}
	{button _text="{tr}Media Entries{/tr}" href="tiki-list_kaltura_entries.php?list=media" }
	{button _text="{tr}List{/tr}" href="tiki-list_kaltura_entries.php?list=mix" }
	{else}
	{button _text="{tr}Mix Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix" }
	{button _text="{tr}List{/tr}" href="tiki-list_kaltura_entries.php?list=media" }
	{/if}
	<br><br>	
	<table>	
	<tr><td>
			<object name="mykdp" id="mykdp" type="application/x-shockwave-flash" height="365" width="685" data="http://www.kaltura.com/index.php/kwidget/wid/{$prefs.kdpWidget}/uiconf_id/{$prefs.kdpUIConf}/entry_id/{$videoInfo->id}">
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="allowFullScreen" value="true" />
			<param name="movie" value="http://www.kaltura.com/index.php/kwidget/wid/{$prefs.kdpWidget}/uiconf_id/1000106/entry_id/{$klist[0]->id}"/>
			<param name="flashVars" value="entry_id={$klist[0]->id}"/>
			<param name="wmode" value="opaque"/>
			</object>
	</td></tr>
	<tr><td>
	<div align="center">
		<div class="navi"></div>
			<a class="prev"></a> 
			<div class="scrollable"> 
				<div class="items"> 
					{foreach from=$klist key=key item=item}					
						<a href="#" onClick = "loadMedia('{$item->id}');" >
						<img class="athumb" src={$item->thumbnailUrl} alt="{$item->description}"  height="80" width="120"/>
						</a>					
					{/foreach}														
				</div>
			</div>
			<a class="next"></a>
		</div>
	</div>
	</td></tr>
	</table>	
