{if $wp_member_requestpending neq 'y' }
<form method="post" action="#pluginMemberpayment{$iPluginMemberpayment}">
	<p>
	{if !empty($wp_member_title) or !empty($wp_member_anniversary_day) or !empty($wp_member_anniversary_day)}
		{wiki}{tr _0=$wp_member_group.groupName _1=$wp_member_price _2=$prefs.payment_currency _4=$wp_member_group.expireAfter _5=$wp_member_group.expireAfterYear}{$wp_member_title}{/tr}{/wiki}
		{if empty($wp_member_title) or (!empty($wp_member_title) and (empty($wp_member_titleonly) or $wp_member_titleonly eq 'n'))}
			{tr}Period:{/tr} {if isset($wp_member_group.expireAfterYear) && $wp_member_group.expireAfterYear eq 1}{tr _0=$wp_member_group.expireAfterYear}%0 year{/tr}
							 {elseif isset($wp_member_group.expireAfterYear)}{tr _0=$wp_member_group.expireAfterYear}%0 years{/tr}
							 {elseif $wp_member_group.expireAfter}{tr _0=$wp_member_group.expireAfter}%0 days{/tr}
							 {elseif $wp_member_anniversary_day and $wp_member_anniversary_month}{tr _0=$wp_member_anniversary_day _1=$wp_member_anniversary_month}Membership term commences on %0-%1 each year{/tr}
							 {elseif $wp_member_anniversary_day}{tr _0=$wp_member_anniversary_day}Membership commences on %0 day of each month{/tr}{/if}<br>
			{tr}Cost for one period:{/tr} {$wp_member_price} {$prefs.payment_currency|escape}
			{if !empty($wp_member_prorated)}<br>{if $wp_member_freeprorated}{tr}Free for the rest of the current period, a prorated value of:{/tr}{else}{tr}Prorated cost for first period:{/tr}{/if} {$wp_member_prorated|string_format:"%.2f"} {$prefs.payment_currency|escape}{/if}
			{if $wp_member_freeperiods}<br>{tr}Special offer! Free additional periods:{/tr} {$wp_member_freeperiods|escape}{/if}
		{/if}
	{elseif isset($wp_member_group.expireAfterYear) and $wp_member_group.expireAfterYear eq 1}
		{tr _0=$wp_member_group.groupName _1=$wp_member_group.expireAfterYear _2=$wp_member_price _3=$prefs.payment_currency}Membership to %0 for %1 year at %2 %3{/tr}
	{elseif isset($wp_member_group.expireAfterYear)}
		{tr _0=$wp_member_group.groupName _1=$wp_member_group.expireAfterYear _2=$wp_member_price _3=$prefs.payment_currency}Membership to %0 for %1 years at %2 %3{/tr}
	{else}
		{tr _0=$wp_member_group.groupName _1=$wp_member_group.expireAfter _2=$wp_member_price _3=$prefs.payment_currency}Membership to %0 for %1 days at %2 %3{/tr}
	{/if}
	</p>
	<p>
		<input type="hidden" name="wp_member_offset" value="{$wp_member_offset|escape}"/>
		{if $wp_member_currentuser ne 'y'}
			{tr}Users:{/tr} <input type="text" id="user{$wp_member_offset|escape}" name="wp_member_users" value="{$user|escape}"/> ({tr}separated by |{/tr})
		{/if}
	</p>
	<p>
		{if isset($hideperiod) && $hideperiod eq 'y'}
			<input type="hidden" name="wp_member_periods" value="1"/>
		{else}
			{tr}{$periodslabel}{/tr}
			<input type="text" name="wp_member_periods" value="1"/>
		{/if}
		<input type="submit" value="{tr}Continue{/tr}"/>
	</p>
	{jq}
		$('#user{{$wp_member_offset|escape}}').tiki("autocomplete", "username", {multiple: true, multipleSeparator: "|"});
	{/jq}
</form>
{else}
	{payment id=$wp_member_paymentid returnurl=$returnurl}
{/if}
