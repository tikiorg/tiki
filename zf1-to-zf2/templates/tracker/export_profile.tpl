{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{if $yaml}
	<div class="t_navbar margin-bottom-md">
		{include file="tracker_actions.tpl"}
	</div>
	{remarksbox type="note" title="{tr}YAML Export{/tr}"}
		<p>{tr _0=$trackerId}Profile for trackerId %0{/tr}</p>
	{/remarksbox}

	{$yaml}

{/if}
{/block}
