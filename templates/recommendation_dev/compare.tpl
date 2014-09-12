{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{tabset}
		{foreach $recommendations as $set}
			{tab name=$set->getEngine()}
				<ol>
					{foreach $set as $rec}
						<li>
							{object_link type=$rec->getType() id=$rec->getId()}
						</li>
					{/foreach}
				</ol>
			{/tab}
		{/foreach}
	{/tabset}
{/block}
