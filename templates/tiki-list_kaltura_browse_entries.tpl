{jq notonready=true}
	function loadMedia(entryId) {
		$('#mykdp')[0].sendNotification("changeMedia", {entryId:entryId});
	}
{/jq}

{if $entryType eq "mix"}
	{button _text="{tr}List Media Entries{/tr}" href="tiki-list_kaltura_entries.php?list=media"}
	{button _text="{tr}List Remix Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix"}
{else}
	{button _text="{tr}List Remix Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix"}
	{button _text="{tr}List Media Entries{/tr}" href="tiki-list_kaltura_entries.php?list=media"}
{/if}
	<div class="center">
		<object name="mykdp" id="mykdp" type="application/x-shockwave-flash" height="365" width="595" data="{$prefs.kServiceUrl}index.php/kwidget/wid/{$prefs.kdpWidget}/uiconf_id/{$prefs.kdpUIConf}/entry_id/{$videoInfo->id}">
			<param name="allowScriptAccess" value="always" />
			<param name="allowNetworking" value="all" />
			<param name="allowFullScreen" value="true" />
			<param name="movie" value="{$prefs.kServiceUrl}index.php/kwidget/wid/{$prefs.kdpWidget}/uiconf_id/{$prefs.kdpUIConf}/entry_id/{$klist[0]->id}"/>
			<param name="flashVars" value="entry_id={$klist[0]->id}" />
			<param name="wmode" value="opaque" />
		</object>
		<div class="navi">
			<a class="prev"></a> 
			<div class="scrollable"> 
				<div class="items"> 
					{foreach from=$klist key=key item=item}					
						<a href="#" onclick="loadMedia('{$item->id}'); return false"><img class="athumb" src="{$item->thumbnailUrl}" alt="{$item->description}" height="80" width="120" /></a>					
					{/foreach}
				</div>
			</div>
			<a class="next"></a>
		</div>
	</div>
