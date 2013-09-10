{strip}
{if $error}
	<div class="user-info">
		<span>{$error}</span>
	</div>
{else}
	<div class="user-info friend-container" data-controller="user" data-action="info" data-params='{ldelim}"username":"{$other_user}"{rdelim}'>
		<h3>{$fullname|escape} <span class="star">{$starHtml}</span></h3>
		{if $avatarHtml}
			<span class="avatar">{$avatarHtml}</span>
		{else}
			<span class="avatar">{icon _id='img/noavatar.png'}</span>
		{/if}
		<p class="info">
			{if $gender}
				<span class="gender">{icon _id=$gender|lower}<span> Gender: {$gender}</span></span>
			{/if}
			{if $country}
				<span class="country">{icon _id='img/flags/'|cat:$country|cat:'.gif'}<span> {$country|stringfix}</span></span>
				<span class="distance">{$distance}<span> away</span></span>
			{/if}
			{if $email}
				<span class="email">Email: {$email}</span>
			{/if}
			<span class="lastseen"><span>{tr}Last seen{/tr} </span>{$lastSeen|tiki_short_datetime}</span>
		</p>
		<ul class="friendship">
			{foreach from=$friendship item=relation}
				<li>
					{icon _id='social_'|cat:$relation.type _title=$relation.label|escape}<span> {$relation.label|escape}</span>
					{if $prefs.social_network_type neq 'follow_approval' or $relation.type neq 'follower'}
						<a class="floatright remove-friend" href="{service controller=social action=remove_friend friend=$other_user}"
									data-confirm="{tr _0=$other_user}Do you really want to remove %0?{/tr}">
							{icon _id=cross alt="{tr}Reject{/tr}"}
						</a>
					{/if}
					{if $relation.type eq 'incoming'}
						{if $prefs.social_network_type eq 'follow_approval'}
							<a class="floatright approve-friend" href="{service controller=social action=approve_friend friend=$other_user}">
								{icon _id=accept alt="{tr}Accept Request{/tr}"}
							</a>
						{else}
							<a class="floatright add-friend" href="{service controller=social action=add_friend username=$other_user}">
								{icon _id=add alt="{tr}Accept &amp; Add{/tr}"}
							</a>
						{/if}
					{/if}
				</li>
			{foreachelse}
				<li>
					<a class="add-friend" href="{service controller=social action=add_friend username=$other_user}">
						{icon _id=add alt="{tr}Add{/tr}"}
						{if $prefs.social_network_type eq 'friend'}
							{tr}Add friend{/tr}
						{else}
							{tr}Follow{/tr}
						{/if}
					</a>
				</li>
			{/foreach}
		</ul>
	</div>
{/if}
{/strip}