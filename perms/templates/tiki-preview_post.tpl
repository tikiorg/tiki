<h2>{tr}Preview{/tr}: {$page}</h2>
<div style="text-align:left" class="posthead">
{if $blog_data.use_title eq 'y'}
	{$title}<br />
	<small>{tr}Posted by{/tr} {$author} on {$created|tiki_short_datetime}</small>
{else}
	{$created|tiki_short_datetime}<small>{tr}Posted by{/tr} {$author}</small>
{/if}
</div>
<div class="postbody">
{$parsed_data}
</div>

