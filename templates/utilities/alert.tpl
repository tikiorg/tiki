{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	{remarksbox type="{$type}" icon="{$icon}" close="n" title="{$heading}"}
		{tr}
			{$msg}
		{/tr}
		{if isset($items) && $items|count > 0}
			<ul>
				{foreach from=$items key=id item=name}
					<li>{$name|escape}</li>
				{/foreach}
			</ul>
		{/if}
	{/remarksbox}
	{if !empty($timeoutMsg)}
		<h5>{$timeoutMsg}</h5>
	{/if}
{/block}