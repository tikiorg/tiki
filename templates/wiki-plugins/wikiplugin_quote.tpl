<div class='quote'>
	<div class='quoteheader'>
		{if $replyto}
			{$replyto|username} {tr}wrote{/tr} on {$comment_info.commentDate|tiki_short_date}:
		{else}
			{tr}Quote:{/tr}
		{/if}
	</div>
	<div class='quotebody'>
		{$data}
	</div>
</div>