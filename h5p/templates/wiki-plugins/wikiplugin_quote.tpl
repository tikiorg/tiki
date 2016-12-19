<div class='quote'>
	<div class='quoteheader'>
		{if $replyto}
			{$replyto|username} {tr}wrote{/tr} {tr}on{/tr} {$comment_info.commentDate|tiki_short_date}:
		{else}
			<i class="fa fa-quote-left" aria-hidden="true"></i>
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
