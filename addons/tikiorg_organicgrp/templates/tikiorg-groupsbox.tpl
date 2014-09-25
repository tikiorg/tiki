<div class="group_box">
	<div class="group_avatar">
		{$result.tracker_field_og_creator|avatarize}
	</div>
	<div class="group_title">
		<h3><a href="tikiorg_organicgrp_grouphomepage?itemId={$result.object_id}">{$result.tracker_field_og_title|escape}</a> {if $result.tracker_status == 'p'}(Private){/if}</h3>
		<p>Created by {$result.tracker_field_og_creator|userlink}</p>
	</div>
	<p>{$result.og_descrip|nl2br}</p>
	{if $result.tracker_status == 'o'}
		{if $result.tracker_field_og_forum_ID|forumtopiccount != 0}<h4>Recent conversations</h4>{/if}
		{wikiplugin _name="list"}
		{literal}
			{list max="2"}
			{filter type="forum post"}
			{filter exact="0" field="parent_thread_id"}
			{filter exact="{/literal}{$result.tracker_field_og_forum_ID}{literal}" field="parent_object_id"}
			{sort mode="modification_date_desc"}
			{output template="addons/tikiorg_organicgrp/templates/tikiorg-groups_recdisc.tpl"}
		{/literal}
		{/wikiplugin}
	{/if}
</div>
<div class="group_footer_box">
	<div class="group_member_icon">
		<span>{$grpname|groupmembercount}</span>
		<div>Members</div>
	</div>
	<div class="group_topic_icon">
		<span>{$result.tracker_field_og_forum_ID|forumtopiccount}</span>
		<div>Forum Topics</div>
	</div>
</div>
