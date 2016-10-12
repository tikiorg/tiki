<div class='quote'>
	<div class='quoteheader'>
		{if $replyto}
			{$replyto|username} {tr}wrote{/tr} on {$comment_info.commentDate|tiki_short_date}:
		{else}
			<i class="fa fa-quote-left" aria-hidden="true"></i>	{tr}Quote:{/tr}
		{/if}
	</div>
	<div class='quotebody'>
		{$data}
		{if $source_url}
			<div class='quoteurl'>
				{tr}Source:{/tr} <a target="_blank" class="wiki external" href="{$source_url}">{$source_url}</a>{icon name='link-external'}
			</div>
		{/if}
	</div>
</div>
