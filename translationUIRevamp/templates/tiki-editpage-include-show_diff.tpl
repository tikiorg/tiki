{if $diff_style}
	<div style="overflow:auto;height:200px;">
		{include file='pagehistory.tpl'}
	</div>
	{if $diff_summaries}
		<div class="wikitext">
			<ul>
				{foreach item=diff from=$diff_summaries}
					<li>{tr}Version:{/tr} {$diff.version|escape} - {$diff.comment|escape|default:"<em>{tr}No comment{/tr}</em>"}</li>
				{/foreach}
			</ul>
		</div>
	{/if}
{/if}