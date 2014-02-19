{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<div class="well">
		{$goal.description|escape}
	</div>
	{foreach $goal.conditions as $condition}
		{if !$condition.hidden}
			<h4>{$condition.label|escape} <span class="badge">{$condition.metric|escape} / {$condition.count|escape}</span></h4>
			<div class="progress">
				<div class="progress-bar progress-bar-{if $condition.operator eq 'atLeast'}success{else}danger{/if}" role="progressbara" aria-valuenow="{$condition.metric|escape}" aria-valuemax="{$condition.count|escape}" style="width: {$condition.metric/max(1, $condition.count) *100}%;">
					<span class="sr-only">{$condition.metric|escape} / {$condition.count|escape}</span>
				</div>
			</div>
		{/if}
	{/foreach}
{/block}
