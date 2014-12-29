{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="navbar">
		{if $canModify}
			<a class="btn btn-default" href="{bootstrap_modal controller=tracker action=update_item trackerId=$trackerId itemId=$itemId}">{icon name="edit"} {tr}Edit{/tr}</a>
		{/if}
		{include file="tracker_actions.tpl"}
	</div>
{/block}

{block name="content"}
	{trackerfields mode=view trackerId=$trackerId fields=$fields itemId=$itemId format=$format}
{/block}
