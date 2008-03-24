{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/error_tracker.tpl,v 1.2 2007-10-04 22:17:51 nyloth Exp $ *}
{if $prefs.feature_trackers ne 'y'}
<span class="error">{tr}This feature is disabled{/tr}</span>
{else}
<span class="error">{tr}Missing or incorrect trackerId parameter for the plugin.{/tr}</span>
{if $tiki_p_admin_trackers eq 'y'}<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>{/if}
{if $tiki_p_view_trackers eq 'y'}<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>{/if}
{/if}
