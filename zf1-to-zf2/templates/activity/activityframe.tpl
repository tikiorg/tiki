<div class="activity" data-id="{$activityframe.object.id|escape}">
	{if $activity_format eq 'summary' and $activityframe.summary neq 'content'}
		<div class="media" data-href="{service controller=object action=infobox type=$activityframe.object.type object=$activityframe.object.id format=extended}">
			<div class="pull-left">
				{$activityframe.activity.user|avatarize:'':'img/noavatar.png'}
			</div>
			<div class="media-body">
				<h4 class="media-heading">{$activityframe.heading}</h4>
				{if $activityframe.activity.type && $activityframe.activity.object}
					<span class="pull-right">
						{$activityframe.activity.modification_date|tiki_short_datetime}
					</span>
					<div>
						{icon name="link"}
						{object_link type=$activityframe.activity.type id=$activityframe.activity.object backuptitle=$object.activity.title}
					</div>
				{/if}
			</div>
		</div>
	{elseif $activity_format eq 'summary'}
		<div class="media" data-href="{service controller=object action=infobox type=$activityframe.object.type object=$activityframe.object.id format=extended}">
			<div class="pull-left">
				{$activityframe.activity.user|avatarize:'':'img/noavatar.png'}
			</div>
			<div class="media-body">
				<h4 class="media-heading">{$activityframe.heading}</h4>
				<span class="pull-right">
					{$activityframe.activity.modification_date|tiki_short_datetime}
				</span>
				<div class="content">{$activityframe.content}</div>
			</div>
		</div>
	{else}
		<span class="pull-right">
			{$activityframe.activity.modification_date|tiki_short_datetime}
		</span>
		<strong style="vertical-align: middle;">{$activityframe.activity.user|avatarize:'':'img/noavatar.png'} {$activityframe.heading}</strong>
		<div class="description">
			{if in_array($user, $activityframe.activity.user_followers)}
				{tr}This user is your friend!{/tr}
			{/if}
			{if $activityframe.sharedgroups and $user != $activityframe.activity.user}
				{tr}You share the following groups with this user:{/tr}
				{foreach $activityframe.sharedgroups as $s_grp}
					{$s_grp|addongroupname|escape}{if !$s_grp@last}, {/if}
				{/foreach}
			{/if}
		</div>
		<div class="content">{$activityframe.content}</div>
		<div class="footer">
			{if $activityframe.comment && $activity_format neq 'extended'}
				<a class="comment btn btn-xs" href="{service controller=comment action=list type=$activityframe.comment.type objectId=$activityframe.comment.id modal=true}">
					{tr}Comment{/tr}
					{if $activityframe.activity.comment_count}({$activityframe.activity.comment_count|escape}){/if}
				</a>
			{/if}
			{if $prefs.feature_friends eq 'y' && $activityframe.likeactive}
				{if $activityframe.like}
					<a class="like btn btn-xs" href="{service controller=social action=unlike type=$activityframe.object.type id=$activityframe.object.id}">
						{tr}Unlike{/tr}
						{if $activityframe.activity.like_list}({$activityframe.activity.like_list|count}){/if}
					</a>
				{else}
					<a class="like btn btn-xs" href="{service controller=social action=like type=$activityframe.object.type id=$activityframe.object.id}">
						{tr}Like{/tr}
						{if $activityframe.activity.like_list}({$activityframe.activity.like_list|count}){/if}
					</a>
				{/if}
			{/if}
			{if $tiki_p_admin == 'y'}
				<a class="delete-activity btn btn-xs" href="{bootstrap_modal controller=managestream action=deleteactivity activityId=$activityframe.activity.object_id}" data-activity-id="{$activityframe.activity.object_id}">
					{tr}Delete{/tr}
				</a>
			{/if}
		</div>
	{/if}
	{if $activityframe.comment && $activity_format eq 'extended'}
		<div class="comment-container" data-reload="{service controller=comment action=list type=$activityframe.comment.type objectId=$activityframe.comment.id}">
			{service_inline controller=comment action=list type=$activityframe.comment.type objectId=$activityframe.comment.id _silent=true}
		</div>
	{/if}
</div>
