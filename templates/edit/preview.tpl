{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{strip}
		{if $inPage}
			<div class="wikitext">
				{$parsed}
			</div>
			{if !empty($parsed_footnote)}
				<div class="wikitext">{$parsed_footnote}</div>
			{/if}
		{else}
			{$parsed}
		{/if}
	{/strip}
{/block}
