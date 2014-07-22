{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=monitor action=object type=$type object=$object}">
		<table class="table">
			<thead>
				<tr>
					<th>{tr}Notification{/tr}</th>
					<th>{tr}Priority{/tr}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $options as $option}
					<tr>
						<td>
							{$option.description|escape}
							{if $option.type eq 'category'}
								{if $option.isParent}
									<span class="label label-warning">{tr}Parent Category{/tr}</span>
								{else}
									<span class="label label-info">{tr}Category{/tr}</span>
								{/if}
							{elseif $option.type eq 'structure'}
								<span class="label label-info">{tr}Structure{/tr}</span>
							{elseif $option.type eq 'tracker'}
								<span class="label label-info">{tr}Type{/tr}</span>
							{elseif $option.type eq 'forum'}
								<span class="label label-info">{tr}Forum{/tr}</span>
							{elseif $option.type eq 'global'}
								<span class="label label-warning">{tr}Global{/tr}</span>
							{elseif $option.type eq 'wiki page trans' or $option.type eq 'article trans'}
								<span class="label label-default">{tr}Translation{/tr}</span>
							{/if}
						</td>
						<td>
							<select name="notification~{$option.hash|escape}" class="nochosen">
								{foreach $priorities as $priority => $info}
									<option value="{$priority|escape}" {if $priority eq $option.priority}selected{/if}>{$info.label|escape}</option>
								{/foreach}
							</select>
						</td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="2">{tr}No notifications available{/tr}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>

		<div class="well">
			<h4>{tr}Priorities{/tr}</h4>
			{foreach $priorities as $priority}
				{if $priority.description}
					<p><strong>{$priority.label|escape}:</strong> {$priority.description|escape}</p>
				{/if}
			{/foreach}
		</div>

		<div class="submit">
			<input type="submit" class="btn btn-primary" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
		</div>
	</form>
{/block}
