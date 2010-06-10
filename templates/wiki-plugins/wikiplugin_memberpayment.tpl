<form method="post" action="">
	<p>
	{if isset($wp_member_group.expireAfterYear) and $wp_member_group.expireAfterYear eq 1}
		{tr 0=$wp_member_group.groupName 1=$wp_member_group.expireAfterYear 2=$wp_member_price 3=$prefs.payment_currency}Membership to %0 for %1 year at %2&nbsp;%3{/tr}
	{elseif isset($wp_member_group.expireAfterYear)}
		{tr 0=$wp_member_group.groupName 1=$wp_member_group.expireAfterYear 2=$wp_member_price 3=$prefs.payment_currency}Membership to %0 for %1 years at %2&nbsp;%3{/tr}
	{else}
		{tr 0=$wp_member_group.groupName 1=$wp_member_group.expireAfter 2=$wp_member_price 3=$prefs.payment_currency}Membership to %0 for %1 days at %2&nbsp;%3{/tr}
	{/if}
	</p>
	<p>
		<input type="hidden" name="wp_member_offset" value="{$wp_member_offset|escape}"/>
		{if $wp_member_currentuser ne 'y'}
			{tr}Users{/tr}: <input type="text" id="user{$wp_member_offset|escape}" name="wp_member_users" value="{$user|escape}"/> ({tr}separated by |{/tr})
		{/if}
	</p>
	<p>
		{tr}Number of periods{/tr}:
		<input type="text" name="wp_member_periods" value="1"/>
		<input type="submit" value="{tr}Continue{/tr}"/>
	</p>
	{jq}
		$jq('#user{{$wp_member_offset|escape}}').tiki("autocomplete", "username", {multiple: true, multipleSeparator: "|"});
	{/jq}
</form>
