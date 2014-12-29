{extends 'layout_view.tpl'}
{block name="content"}
	{remarksbox type="feedback" close="n" title="{tr}Success{/tr}"}
	{if $selectedTopics|count > 1}
		{if $type === 'move'}
			{tr _0=$toName _1="<em>" _2="</em>"}
				The following topics have been moved to the %1%0%2 forum:
			{/tr}
		{elseif $type === 'merge'}
			{tr _0=$toName _1="<em>" _2="</em>"}
				The following topics have been merged with the %1%0%2 topic:
			{/tr}
		{elseif $type === 'lock'}
			{tr}
				The following topics have been locked:
			{/tr}
		{elseif $type === 'unlock'}
			{tr}
				The following topics have been unlocked:
			{/tr}
		{/if}
	{else}
		{if $type === 'move'}
			{tr _0=$toName _1="<em>" _2="</em>"}
				The following topic has been moved to the %1%0%2 forum:
			{/tr}
		{elseif $type === 'merge'}
			{tr _0=$toName _1="<em>" _2="</em>"}
				The following topic has been merged with the %1%0%2 topic:
			{/tr}
		{elseif $type === 'lock'}
			{tr}
				The following topic has been locked:
			{/tr}
		{elseif $type === 'unlock'}
			{tr}
				The following topic has been unlocked:
			{/tr}
		{/if}
	{/if}
		<ul>
			{foreach from=$selectedTopics key=id item=name}
				<li>{$name|escape}</li>
			{/foreach}
		</ul>
	{/remarksbox}
	<h6><em>{tr}This popup will automatically close in 5 seconds.{/tr}</em></h6>
{/block}
