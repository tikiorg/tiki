{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{tabset skipsingle=1}
		{foreach item=help from=$help_sections}
			{tab name=$help.title}
				{$help.content}
			{/tab}
		{/foreach}
	{/tabset}
{/block}

