{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="navbar">
		{if $canModify}
			<a class="btn btn-default" href="{service controller=tracker action=update_item trackerId=$trackerId itemId=$itemId modal=1}" data-toggle="modal" data-target="#bootstrap-modal">{glyph name=pencil} {tr}Edit{/tr}</a>
		{/if}
		{include file="tracker_actions.tpl"}
	</div>
{/block}

{block name="content"}
	{trackerfields mode=view trackerId=$trackerId fields=$fields}
{/block}
