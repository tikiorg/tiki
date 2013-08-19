<div class="activity">
	<strong style="vertical-align: middle;">{$activityframe.activity.user|avatarize} {$activityframe.heading}</strong>
	<div class="content">{$activityframe.content}</div>
	<div class="footer">
		<span class="floatright">
			{$activityframe.activity.modification_date|tiki_short_datetime}
		</span>
		<a class="comment" href="{service controller=comment action=list type=activity objectId=$activityframe.activity.object_id}">
			{tr}Comment{/tr}
			{if $activityframe.activity.comment_count}({$activityframe.activity.comment_count|escape}){/if}
		</a>
	</div>
</div>
