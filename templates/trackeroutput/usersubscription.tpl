{$field.value|how_many_user_inscriptions}{if $context.list_mode ne 'csv'} {tr}Subscriptions{/tr}{/if}
{if $context.list_mode eq 'n'}
	{if $field.maxsubscriptions}(max : {$field.maxsubscriptions}){/if} :
	{foreach from=$field.users_array name=U_user item=U_user}
		{$U_user.login|userlink}{if $U_user.friends} (+{$U_user.friends}){/if}{if $smarty.foreach.U_user.last}{else},&nbsp;{$last}{/if}
	{/foreach}
	{if $user}
		<br />
		{if $field.user_subscription} {tr}You have ever subscribed{/tr}.{else}{tr}You have not yet subscribed{/tr}.{/if}
		<form method="POST">
		<input type="submit" name="user_subscribe" value="{tr}Subscribe{/tr}" /> {tr}with{/tr}
		{if $U_liste}
			{html_options options=$U_liste name="user_friends" selected=$field.user_nb_friends} {tr}friends{/tr}
		{else}
			<input type="text" size="4" name="user_friends" value="{$field.user_nb_friends}" /> {tr}friends{/tr}
		{/if}
		{if $field.user_subscription}<br /><input type="submit" name="user_unsubscribe" value="{tr}Unsubscribe{/tr}" />{/if}
		</form>
	{/if}
{/if}