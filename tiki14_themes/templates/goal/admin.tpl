{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<table class="table">
		<thead>
			<tr>
				<th>{tr}Goal{/tr}</th>
				<th>{tr}Eligible Groups{/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>
		</thead>
		<tbody>
			{foreach $list as $goal}
				<tr {if $goal.enabled} class="success"{/if}>
					<td>
						<a href="{service controller=goal action=show goalId=$goal.goalId}">{$goal.name|escape}</a>
						{if $goal.type eq 'group'}
							<span class="label label-info">{tr}Group{/tr}</span>
						{/if}

						<div class="description">{$goal.description|escape}</div>
					</td>
					<td>
						<ul class="list-inline">
							{foreach $goal.eligible as $groupName}
								<li>{$groupName|escape}</li>
							{foreachelse}
								<li>{tr}No eligible groups!{/tr}</li>
							{/foreach}
						</ul>
					</td>
					<td>
						<a href="{service controller=goal action=edit goalId=$goal.goalId}">{icon name="edit"} {tr}Modify{/tr}</a>
						{permission_link mode=text type=goal id=$goal.goalId title=$goal.name}
						<a class="text-danger" href="{bootstrap_modal controller=goal action=delete goalId=$goal.goalId}">{icon name="delete"} {tr}Delete{/tr}</a>
					</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="3">
						{tr}No goals configured yet!{/tr}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<a class="btn btn-primary" href="{service controller=goal action=create}">{tr}Create Goal{/tr}</a>
{/block}
