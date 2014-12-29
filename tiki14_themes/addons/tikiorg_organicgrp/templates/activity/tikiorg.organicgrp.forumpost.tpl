{activityframe activity=$activity heading="
<div class='active forum'><hr/></div>
<img class='commentimg' src='addons/tikiorg_organicgrp/img/chat-bubble1.png' alt='forum'/>
{tr _0=$activity.user|userlink}%0 has started a new topic in{/tr} <a href=tiki-view_forum.php?forumId={$activity.forum_id|escape}>{$activity.object|forumname|addongroupname}</a>"}
	<div class="active_part2">
		<img src="addons/tikiorg_organicgrp/img/chat-bubble-copy.png" alt="forum"/>
		<div class="avt_title1">
			{ifsearchexists type="forum post" id="{$activity.object|escape}"}
				<a href="tiki-view_forum_thread.php?comments_parentId={$activity.object|escape}">{$activity.title|escape}</a><br/>
			{$activity.content|truncate:80}
			{/ifsearchexists}
			{ifsearchnotexists type="forum post" id="{$activity.object|escape}"}
			{$activity.title|escape} (deleted)
			{/ifsearchnotexists}
		</div>
	</div>
{/activityframe}
