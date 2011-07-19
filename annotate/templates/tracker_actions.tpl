{* $Id: $ *}
{* navbar buttons at the top of the tracker pages *}

{if $tiki_p_admin_trackers eq 'y'}
	{button href="tiki-admin_trackers.php?cookietab=1" _text="{tr}Admin Trackers{/tr}"}
	{if !empty($trackerId)}
		{button href="tiki-admin_trackers.php?trackerId=$trackerId&cookietab=2" _text="{tr}Edit Tracker{/tr}"}
		{button href="tiki-admin_tracker_fields.php?trackerId=$trackerId" _text="{tr}Edit Fields{/tr}"}
	{else}
		{button href="tiki-admin_trackers.php?cookietab=2" _text="{tr}Create Tracker{/tr}"}
	{/if}
{/if}

{if $tiki_p_list_trackers eq 'y' or $tiki_p_view_trackers eq 'y'}
	{button href="tiki-list_trackers.php" _text="{tr}List Trackers{/tr}"}
{/if}

{if !empty($trackerId) and $tiki_p_create_tracker_items eq 'y' and $prefs.feature_tabs ne 'y'}
	{button href="tiki-view_tracker.php?trackerId=$trackerId#content2" _text="{tr}Create Item{/tr}"}
{/if}

{if !empty($trackerId) and $tiki_p_view_trackers eq 'y'}
	{button href="tiki-view_tracker.php?trackerId=$trackerId" _text="{tr}View Items{/tr}"}
{/if}

