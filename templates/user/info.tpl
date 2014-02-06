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
							{icon _id='img/noavatar.png' title=$fullname}
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
					<td>{$lastSeen|tiki_short_datetime}</td>
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
									<li>
										{icon _id='social_'|cat:$relation.type _title=$relation.label|escape}<span class="small"> {$relation.label|escape}</span>
										<div class="friendship-actions pull-right">
											{if !empty($relation.remove)}
												<a class="pull-right remove-friend btn btn-default" href="{service controller=social action=remove_friend friend=$other_user}"
															title="{$relation.remove}" data-confirm="{tr _0=$other_user}Do you really want to remove %0?{/tr}">
													{icon _id=cross alt="{$relation.remove}"}
												</a>
											{/if}
											{if !empty($relation.add)}
												<a class="pull-right add-friend btn btn-default" title="{$relation.add}" href="{service controller=social action=add_friend username=$other_user}">
													{icon _id=add alt="{$relation.add}"}
												</a>
											{/if}
											{if !empty($relation.approve)}
												<a class="pull-right approve-friend btn btn-default" title="{$relation.approve}" href="{service controller=social action=approve_friend friend=$other_user}">
													{icon _id=accept alt="{$relation.approve}"}
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
