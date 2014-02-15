{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<div class="well">
		{$goal.description|escape}
	</div>
	<ul>
		{foreach $goal.eligible as $groupName}
			<li><a href="{service controller=goal action=show goalId=$goal.goalId group=$groupName}">{$groupName|escape}</a></li>
		{foreachelse}
			<li>{tr}No groups are eligible to this goal.{/tr}</li>
		{/foreach}
	</ul>
{/block}
