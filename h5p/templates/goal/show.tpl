{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="navbar btn-group">
		{permission name=goal_admin}
			<a class="btn btn-default" href="{service controller=goal action=admin}">{tr}Goal Administration{/tr}</a>
		{/permission}
		{permission name=goal_admin type=goal object=$goal.goalId}
			<a class="btn btn-default" href="{service controller=goal action=edit goalId=$goal.goalId}">{tr}Edit Goal{/tr}</a>
		{/permission}
		{permission name=goal_modify_eligible type=goal object=$goal.goalId}
			<a class="btn btn-default" href="{service controller=goal action=edit_eligible goalId=$goal.goalId}">{tr}Modify Eligibility{/tr}</a>
		{/permission}

		{if $goal.type eq 'group'}
			<a class="btn btn-default" href="{service controller=goal action=show goalId=$goal.goalId}">{tr}Group List{/tr}</a>
		{/if}
	</div>
{/block}

{block name="content"}
	{if $messages}
		<div class="alert alert-info">
			{foreach $messages as $message}
				<p>{$message|escape}</p>
			{/foreach}
		</div>
	{/if}
	{if $goal.description}
		<div class="well">
			{$goal.description|escape}
		</div>
	{/if}
	{foreach $goal.conditions as $condition}
		<h4>{$condition.label|escape} <span class="badge">{$condition.metric|escape} / {$condition.count|escape}</span></h4>
		<div class="progress">
			<div class="progress-bar progress-bar-{if $condition.operator eq 'atLeast'}success{else}danger{/if}" role="progressbara" aria-valuenow="{$condition.metric|escape}" aria-valuemax="{$condition.count|escape}" style="width: {$condition.metric/max(1, $condition.count) *100}%;">
				<span class="sr-only">{$condition.metric|escape} / {$condition.count|escape}</span>
			</div>
		</div>
	{/foreach}
	{if $goal.rewards}
		<h2>{tr}Rewards{/tr}</h2>
		<ul>
			{foreach $goal.rewards as $reward}
				<li>
					{$reward.label|escape}
				</li>
			{/foreach}
		</ul>
	{/if}
{/block}
