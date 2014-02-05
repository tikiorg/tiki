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
	{if $quantity}
		<div class="submit">
			<a class="btn btn-primary" href="{service controller=monitor action=stream critical=$critical high=$high low=$low}">{tr}Show More{/tr}</a>
		</div>
	{else}
		{pagination_links resultset=$result}{/pagination_links}
	{/if}
{/block}
