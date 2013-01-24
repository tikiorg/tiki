<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" height="365" width="400" data="{$prefs.kaltura_kServiceUrl|escape}index.php/kwidget/wid/_{$prefs.kaltura_partnerId|escape}/uiconf_id/{$kaltura.player_id|escape}/entry_id/{$kaltura.id|escape:'url'}">
	<param name="allowScriptAccess" value="always" />
	<param name="allowNetworking" value="all" />
	<param name="allowFullScreen" value="true" />
	<param name="movie" value="{$prefs.kaltura_kServiceUrl|escape}index.php/kwidget/wid/_{$prefs.kaltura_partnerId|escape}/uiconf_id/{$kaltura.player_id|escape}/entry_id/{$kaltura.id|escape:'url'}"/>
	<param name="flashVars" value="entry_id={$kaltura.id|escape:'url'}&amp;ks={$kaltura.session|escape:'url'}"/>
	<param name="wmode" value="opaque"/>
</object>
