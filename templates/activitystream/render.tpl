<div class="stream-container">
	{$body}
	{if $nextPossible}
		<button class="show-more" data-page="{$pageNumber|escape}" data-stream="{$stream|escape}">{tr}Show More{/tr}</button>
	{/if}
</div>
