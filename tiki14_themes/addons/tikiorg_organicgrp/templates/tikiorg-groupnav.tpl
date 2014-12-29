{foreach item=result from=$results}
	{assign var=grpname value="tikiorg_organicgrp_`$result.object_id`"}
	{assign var=mgrpname value="tikiorg_organicgrp_managers_`$result.object_id`"}
	{assign var=pgrpname value="tikiorg_organicgrp_pending_`$result.object_id`"}
	<div class="row">
		<h2>{$prefs.ta_tikiorg_organicgrp_sterm} - {$result.title}</h2>
	</div>
	<div class="row">
		{$result.logo_image}
	</div>
	<div class="row">{$result.tracker_field_og_description|escape}<br /><br /></div>

	<div class="row">
	{if !$mgrpname|in_group}
		{if $result.tracker_status eq 'o'}
			{wikiplugin _name="subscribegroup" group=$grpname subscribe_action="Join {$prefs.ta_tikiorg_organicgrp_sterm}" postsubscribe_url="tikiorg_organicgrp_grouphomepage?itemId={$result.object_id}" unsubscribe_action="Leave {$prefs.ta_tikiorg_organicgrp_sterm}" postunsubscribe_url="tikiorg_organicgrp_joingroups" subscribe="" unsubscribe=""}{/wikiplugin}
		{elseif $result.tracker_status eq 'p' && !$grpname|in_group}
			{wikiplugin _name="subscribegroup" group=$pgrpname subscribe_action="Request to Join {$prefs.ta_tikiorg_organicgrp_sterm}" postsubscribe_url="tikiorg_organicgrp_grouphomepage?itemId={$result.object_id}" unsubscribe_action="Cancel Request to Join {$prefs.ta_tikiorg_organicgrp_sterm}" postunsubscribe_url="tikiorg_organicgrp_joingroups" subscribe="" unsubscribe=""}{/wikiplugin}
		{elseif $result.tracker_status eq 'p' && $grpname|in_group}
			{wikiplugin _name="subscribegroup" group=$grpname subscribe_action="Join {$prefs.ta_tikiorg_organicgrp_sterm}" postsubscribe_url="tikiorg_organicgrp_grouphomepage?itemId={$result.object_id}" unsubscribe_action="Leave {$prefs.ta_tikiorg_organicgrp_sterm}" postunsubscribe_url="tikiorg_organicgrp_joingroups" subscribe="" unsubscribe="" allowLeaveNonUserChoice="y"}{/wikiplugin}
		{elseif $result.tracker_status eq 'p' && $pgrpname|in_group}
			{tr}Your membership to this {$prefs.ta_tikiorg_organicgrp_sterm} is pending approval{/tr}
		{/if}
	{else}
		<div class="row">
			<div class="col-md-3">
				<a href="tikiorg_organicgrp_managegrp?itemId={$result.object_id}"><button class="btn btn-default">Manage Members</button></a>
			</div>
			<div class="col-md-9">
				{wikiplugin _name="mail" bypass_preview="y" popup="y" showgroupdd="n" showuser="n" group="{$grpname}" mail_subject="A message from the leader of {$result.title|replace|replace:'~/np~':''|replace:'~np~':''}" label_name="Send email to all Members"}{/wikiplugin}
			</div>
		</div>
	{/if}
	</div>

	{if $grpname|in_group || $result.tracker_status eq 'o'}
	<div class="row">
		<div class="btn-group">
			<a href="tikiorg_organicgrp_grouphomepage?itemId={$result.object_id}"><button type="button" class="here_grouphome btn btn-default">Home</button></a>
			<a href="tikiorg_organicgrp_{$result.object_id}:_:whiteboard_{$result.object_id}?organicgroup={$result.object_id}&cat={$result.tracker_field_og_categoryID}"><button type="button" class="here_groupboard btn btn-default">Whiteboard</button></a>
			<a href="tiki-view_forum.php?forumId={$result.tracker_field_og_forum_ID}"><button type="button" class="here_groupforum btn btn-default">Forum</button></a>
			<a href="tikiorg_organicgrp_groupmembers?organicgroup={$result.object_id}&cat={$result.tracker_field_og_categoryID}"><button type="button" class="here_groupmembers btn btn-default">Members</button></a>
		</div>
	</div>
	{/if}

	{if $result.tracker_status eq 'o' && !$grpname|in_group}
		<div class="row">
			{remarksbox type="note" title="Join!" close="n"}
				{tr}You are currently not yet a member. You can view but not participate. Join to gain full access and to receive notifications.{/tr}
			{/remarksbox}
		</div>
	{/if}
{/foreach}
