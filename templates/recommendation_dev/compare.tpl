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
							{object_link type=$rec->getType() id=$rec->getId() title=$rec->getTitle()}
						</li>
					{/foreach}
				</ol>
				{remarksbox type=info title="{tr}Debug Information{/tr}"}
					<ul>
						{foreach $set->getDebug() as $info}
							<li>{$info}</li>
						{foreachelse}
							<li>{tr}Engine does not provide any debug information{/tr}</li>
						{/foreach}
					</ul>
				{/remarksbox}
			{/tab}
		{/foreach}
	{/tabset}
{/block}
