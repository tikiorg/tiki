<ul class="arrowLinks">
	{foreach item=result from=$results}
		{if $result.parent_thread_id}
			<li><a href="./tiki-view_forum_thread.php?comments_parentId={$result.parent_thread_id|escape}" class="title">{$result.title|escape}</a></li>

		{else}
			<li><a href="./tiki-view_forum_thread.php?comments_parentId={$result.object_id|escape}" class="title">{$result.title|escape}</a></li>
		{/if}
	{foreachelse}
		{tr}No items{/tr}
	{/foreach}
</ul>
