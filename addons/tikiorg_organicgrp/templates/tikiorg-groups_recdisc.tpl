{foreach item=result from=$results}
	<div class="row">
		<div class="group_dis_title"><a href="./tiki-view_forum_thread.php?comments_parentId={$result.object_id|escape}" class="title">{$result.title|escape}</a></div>
		<div class="group_avatar">
			{$result.author|replace:'~/np~':''|replace:'~np~':''|avatarize}
		</div>
		<div class="group_title">
			{if $result.parent_thread_id}
				<div class="author_group_info">{$result.contributors|userlink}</a> replied to {$result.parent_contributors|userlink}'s discussion</div>
				<div class="">{$result.post_snippet|truncate:100:"..."}<br /><i><a href="./tiki-view_forum_thread.php?comments_parentId={$result.object_id|escape}">read reply</a></i></div>
			{else}
				<div class="author_group_info">{$result.contributors|userlink} started a new discussion</div>
				<div class="">{$result.post_snippet|truncate:100:"..."}</div>
			{/if}
		</div>
	</div>
{/foreach}
