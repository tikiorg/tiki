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
	{if $url}
	<p>
		Source: <a target="_blank" class="wiki external" href="{$url}">{$url}<img src="img/icons/external_link.gif" alt=" " width="15" height="14" title=" " class="icon"></a>
	</p>
	{/if}
</div>
