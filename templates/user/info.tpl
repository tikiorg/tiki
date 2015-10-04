{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{strip}
{if $error}
	<div class="user-info">
		<span>{$error}</span>
	</div>
{else}
	<div class="user-info friend-container" data-controller="user" data-action="info" data-params='{ldelim}"username":"{$other_user}"{rdelim}'>
		<table class="table table-condensed table-hover">
			<thead>
				<tr>
					<td>
						{if $avatarHtml}
							{$avatarHtml}
						{else}
							{icon name='user'}
						{/if}
					</td>
					<td>
						<h4>{$fullname|escape} <span class="star">{$starHtml}</span></h4>
					</td>
				</tr>
			</thead>
			<tbody>
				{if $gender}
				<tr>
					<td><strong>{tr}Gender{/tr}</strong></td>
					<td>{$gender}</td>
				</tr>
				{/if}
				{if $country}
				<tr>
					<td><strong>{tr}Country{/tr}</strong></td>
					<td>{$country|stringfix}</td>
					{if !empty($distance)}<span class="distance">{tr _0=$distance}%0 away{/tr}</span>{/if}
				</tr>
				{/if}
				{if $email}
				<tr>
					<td><strong>{tr}Email{/tr}</strong></td>
					<td>{$email}</td>
				</tr>
				{/if}
				<tr>
					<td><strong>{tr}Last login{/tr}</strong></td>
					<td>{if !empty($lastSeen)}{$lastSeen|tiki_short_datetime}{else}{tr}Never logged in{/tr}{/if}</td>
				</tr>
				{if $shared_groups}
				<tr>
					<td><strong>{tr}Shared groups{/tr}</strong></td>
					<td>{$shared_groups|escape}</td>
				</tr>
				{/if}
				{if $friendship|count}
					<tr>
						<td><strong>{tr}Friendship{/tr}</strong></td>
						<td>
							<ul class="friendship list-unstyled">
								{foreach from=$friendship item=relation}
									{if $relation.type == 'incoming'}
										{$icon = 'login'}
									{elseif $relation.type == 'outgoing'}
										{$icon = 'logout'}
									{elseif $relation.type == 'friend'}
										{$icon = 'group'}
									{elseif $relation.type == 'following'}
										{$icon = 'share'}
									{elseif $relation.type == 'follower'}
										{$icon = 'backlink'}
									{/if}
									<li>
										{icon name=$icon}<span class="small"> {$relation.label|escape}</span>
										<div class="friendship-actions pull-right">
											{if !empty($relation.remove)}
												<a class="pull-right remove-friend btn btn-default" href="{service controller=social action=remove_friend friend=$other_user}"
															title="{$relation.remove}" data-confirm="{tr _0=$other_user}Do you really want to remove %0?{/tr}">
													{icon name='delete'}
												</a>
											{/if}
											{if !empty($relation.add)}
												<a class="pull-right add-friend btn btn-default" title="{$relation.add}" href="{service controller=social action=add_friend username=$other_user}">
													{icon name='add'}
												</a>
											{/if}
											{if !empty($relation.approve)}
												<a class="pull-right approve-friend btn btn-default" title="{$relation.approve}" href="{service controller=social action=approve_friend friend=$other_user}">
													{icon name='ok'}
												</a>
											{/if}
										</div>
									</li>
								{/foreach}
							</ul>
						</td>
					</tr>
				{/if}
			</tbody>
		</table>
		{if $add_friend_button}
			<a class="add-friend btn btn-default btn-sm center-block" href="{service controller=social action=add_friend username=$other_user}">
				{$add_friend_button}
			</a>
		{/if}
	</div>
{/if}
{/strip}
{/block}
