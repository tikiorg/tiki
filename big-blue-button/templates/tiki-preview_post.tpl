<h2>{tr}Preview{/tr}</h2>
<div style="text-align:left" class="posthead">
{if $blog_data.use_title eq 'y'}
	{$title|escape}<br />
	<small>{tr}Posted by{/tr} {$author|userlink} on {$created|tiki_short_datetime}</small>
{else}
	{$created|tiki_short_datetime}<small>{tr}Posted by{/tr} {$author|userlink}</small>
{/if}
</div>
<div class="postbody">
{$parsed_data}
</div>

