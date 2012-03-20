{* $Id$ *}
{* navbar buttons at the top of the tracker pages *}

{if $tiki_p_admin_trackers eq 'y' and !empty($trackerId)}
	{button href="tiki-list_trackers.php?trackerId=$trackerId&show=mod" _text="{tr}Edit This Tracker{/tr}"}	
	{button href="tiki-admin_tracker_fields.php?trackerId=$trackerId" _text="{tr}Edit Fields{/tr}"}
{/if}

{if $tiki_p_list_trackers eq 'y'}
	{button href="tiki-list_trackers.php" _text="{tr}Trackers{/tr}"}
{/if}

{if !empty($trackerId) and $tiki_p_create_tracker_items eq 'y' and $prefs.feature_tabs ne 'y'}
	{button href="tiki-view_tracker.php?trackerId=$trackerId#content2" _text="{tr}Create Item{/tr}"}
{/if}

{if !empty($trackerId) and $tiki_p_view_trackers eq 'y'}
	{button href="tiki-view_tracker.php?trackerId=$trackerId" _text="{tr}View Items{/tr}"}
{/if}

