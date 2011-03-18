{$current_field_ins.value|how_many_user_inscriptions}{if $context.list_mode ne 'csv'} {tr}Subscriptions{/tr}{/if}
{if $context.list_mode eq 'n'}
	{if $current_field_ins.maxsubscriptions}(max : {$current_field_ins.maxsubscriptions}){/if} :
	{foreach from=$current_field_ins.users_array name=U_user item=U_user}
		{$U_user.login|userlink}{if $U_user.friends} (+{$U_user.friends}){/if}{if $smarty.foreach.U_user.last}{else},&nbsp;{$last}{/if}
	{/foreach}
	{if $user}
		<br />
		{if $current_field_ins.user_subscription} {tr}You have ever subscribed{/tr}.{else}{tr}You have not yet subscribed{/tr}.{/if}
		<form method="POST">
		<input type="submit" name="user_subscribe" value="{tr}Subscribe{/tr}" /> {tr}with{/tr}
		{if $current_field_ins.list}
			{html_options options=$current_field_ins.list name="user_friends" selected=$current_field_ins.user_nb_friends} {tr}friends{/tr}
		{else}
			<input type="text" size="4" name="user_friends" value="{$current_field_ins.user_nb_friends}" /> {tr}friends{/tr}
		{/if}
		{if $current_field_ins.user_subscription}<br /><input type="submit" name="user_unsubscribe" value="{tr}Unsubscribe{/tr}" />{/if}
		</form>
	{/if}
{/if}