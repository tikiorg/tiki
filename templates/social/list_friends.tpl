<ul class="friend-list">
	{foreach from=$friends item=friend}
		<li class="clear">
			{$friend.user|userlink}
			<a class="floatright remove-friend confirm-prompt" href="{service controller=social action=remove_friend friend=$friend.user}" data-confirm="{tr _0=$friend.user}Do you really want to remove %0?{/tr}">{icon _id=cross alt="{tr}Remove Friend{/tr}"}</a>
		</li>
	{foreachelse}
		<li>{tr}You do not have friends.{/tr}</li>
	{/foreach}
</ul>
<button class="add-friend">{tr}Add Friend{/tr}</button>
