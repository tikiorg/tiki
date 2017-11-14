<div class='quote'>
	<div class='quoteheader'>
		{if $replyto}
			<cite>{$replyto|username}</cite> {tr}wrote{/tr}{if $date} {tr}on{/tr} {$date|tiki_short_date}{/if}:
		{else}
			<i class="fa fa-quote-left" aria-hidden="true"></i>
		{/if}
	</div>
	<div class='quotebody'>
		{$data}
		{if $source_url}
			<div class='quoteurl'>
				{tr}Source:{/tr} <a target="_blank" class="wiki external" href="{$source_url}"><cite>{$source_url}</cite></a>{icon name='link-external'}
			</div>
		{/if}
	</div>
</div>
