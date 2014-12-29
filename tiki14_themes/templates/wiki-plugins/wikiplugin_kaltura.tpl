<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" height="{$kaltura.height|escape}" width="{$kaltura.width|escape}" data="{$kaltura.media_url|escape}" resource="{$kaltura.media_url|escape}">
	<param name="allowScriptAccess" value="always">
	<param name="allowNetworking" value="all">
	<param name="allowFullScreen" value="true">
	<param name="movie" value="{$kaltura.media_url|escape}">
	{if $kaltura.playlistAPI}
		<param name="flashVars" value="{$kaltura.playlistAPI}">
	{else}
		<param name="flashVars" value="entry_id={$kaltura.id|escape:'url'}&amp;ks={$kaltura.session|escape:'url'}">
	{/if}
	<param name="wmode" value="opaque">
</object>
