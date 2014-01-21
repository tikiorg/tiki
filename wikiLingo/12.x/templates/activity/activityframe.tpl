<div class="activity" data-id="{$activityframe.activity.object_id|escape}">
	<strong style="vertical-align: middle;">{$activityframe.activity.user|avatarize} {$activityframe.heading}</strong>
	{if in_array($user, $activityframe.activity.user_followers)}
	This user is your friend!
	{/if}
	{if $activityframe.sharedgroups and $user != $activityframe.activity.user}
	You share the following groups with this user:
	{foreach $activityframe.sharedgroups as $s_grp}
	{$s_grp|escape}{if !$s_grp@last}, {/if}
	{/foreach}
	{/if}
	<div class="content">{$activityframe.content}</div>
	<div class="footer">
		<span class="floatright">
			{$activityframe.activity.modification_date|tiki_short_datetime}
		</span>
		<a class="comment" href="{service controller=comment action=list type=activity objectId=$activityframe.activity.object_id}">
			{tr}Comment{/tr}
			{if $activityframe.activity.comment_count}({$activityframe.activity.comment_count|escape}){/if}
		</a>
		{if $activityframe.like}
			<a class="like" href="{service controller=social action=unlike type=activity id=$activityframe.activity.object_id}">
				{tr}Unlike{/tr}
				{if $activityframe.activity.like_list}({$activityframe.activity.like_list|count}){/if}
			</a>
		{else}
			<a class="like" href="{service controller=social action=like type=activity id=$activityframe.activity.object_id}">
				{tr}Like{/tr}
				{if $activityframe.activity.like_list}({$activityframe.activity.like_list|count}){/if}
			</a>
		{/if}
	</div>
</div>
