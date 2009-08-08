{* $Id$ *}
{if $prefs.feature_trackers ne 'y'}
	<span class="error">{tr}This feature is disabled{/tr}</span>
{else}
	<span class="error">{tr}Missing or incorrect trackerId parameter for the plugin.{/tr}</span>
	{if $tiki_p_admin_trackers eq 'y'}{button href="tiki-admin_trackers.php" _text="{tr}Admin Trackers{/tr}"}{/if}
	{if $tiki_p_view_trackers eq 'y'}{button href="tiki-list_trackers.php" _text="{tr}List Trackers{/tr}"}{/if}
{/if}
