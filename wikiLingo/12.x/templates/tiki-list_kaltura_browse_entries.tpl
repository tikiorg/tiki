{* $Id$ *}
{jq notonready=true}
	function loadMedia(entryId) {
		$('#mykdp')[0].sendNotification("changeMedia", {entryId:entryId});
	}
{/jq}

{if $tiki_p_upload_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}{button _text="{tr}Add New Media{/tr}" href="tiki-kaltura_upload.php"}{/if}

{if $tiki_p_list_videos eq 'y'}
{if $entryType eq "mix"}
	{button _text="{tr}List Media{/tr}" href="tiki-list_kaltura_entries.php"}
	{if $prefs.kaltura_legacyremix == 'y'}{button _text="{tr}List Remix Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix"}{/if}
{else}
	{if $prefs.kaltura_legacyremix == 'y'}{button _text="{tr}List Remix Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix"}{/if}
	{button _text="{tr}List Media{/tr}" href="tiki-list_kaltura_entries.php"}
{/if}
{/if}

	<div class="center">
		<object name="mykdp" id="mykdp" type="application/x-shockwave-flash" height="365" width="595" data="{$prefs.kaltura_kServiceUrl}index.php/kwidget/wid/_{$prefs.kaltura_partnerId}/uiconf_id/{$prefs.kaltura_kdpUIConf}/entry_id/{$videoInfo->id}">
			<param name="allowScriptAccess" value="always">
			<param name="allowNetworking" value="all">
			<param name="allowFullScreen" value="true">
			<param name="movie" value="{$prefs.kaltura_kServiceUrl}index.php/kwidget/wid/_{$prefs.kaltura_partnerId}/uiconf_id/{$prefs.kaltura_kdpUIConf}/entry_id/{$klist[0]->id}">
			<param name="flashVars" value="entry_id={$klist[0]->id}">
			<param name="wmode" value="opaque">
		</object>
		<div class="navi kaltura">
			<a class="prev"></a> 
			<div class="scrollable"> 
				<div class="items"> 
					{foreach from=$klist key=key item=item}					
						<a href="#" onclick="loadMedia('{$item->id}'); return false"><img class="athumb" src="{$item->thumbnailUrl}" alt="{$item->description}" height="80" width="120"></a>					
					{/foreach}
				</div>
			</div>
			<a class="next"></a>
		</div>
	</div>
