<ul class="friend-list">
	{foreach from=$friends item=friend}
		<li>{$friend.user|userlink}</li>
	{foreachelse}
		<li>{tr}You do not have friends.{/tr}</li>
	{/foreach}
</ul>
<button class="add-friend">{tr}Add Friend{/tr}</button>
