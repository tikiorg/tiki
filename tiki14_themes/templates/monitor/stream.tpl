{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<ol>
		{foreach from=$result item=activity}
			<li>{activity info=$activity}</li>
		{foreachelse}
			<li class="invalid">{tr}No activity for you.{/tr}</li>
		{/foreach}
	</ol>
	{pagination_links resultset=$result}{/pagination_links}
{/block}
