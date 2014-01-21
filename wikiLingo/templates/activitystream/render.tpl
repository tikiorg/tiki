{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<div class="stream-container{if $autoScroll} auto-scroll{/if}">
	{$body}
	{if $nextPossible}
		<button class="show-more btn btn-default" data-page="{$pageNumber|escape}" data-stream="{$stream|escape}">{tr}Show More{/tr}</button>
	{/if}
</div>
{/block}
