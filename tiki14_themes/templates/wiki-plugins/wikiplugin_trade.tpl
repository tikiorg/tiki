<form method="post" action="#pluginTrade{$iPluginTrade}">
	<div id="pluginTradeDiv{$iPluginTrade}" class="pluginTradeDiv">
		{if !empty($wp_trade_title)}
			{wiki}{$wp_trade_title}{/wiki}
		{/if}
		<input type="hidden" name="wp_trade_offset" value="{$wp_trade_offset|escape}">
		{if $wp_trade_other_user_set eq "n"}
			<input type="text" id="other_user{$wp_trade_offset|escape}" name="wp_trade_other_user" value="{$wp_trade_other_user.login|escape}"> ({tr}separated by |{/tr})
			{jq}
				$('#other_user{{$wp_trade_offset|escape}}').tiki("autocomplete", "username", {multiple: true, multipleSeparator: "|"});
			{/jq}
		{/if}
		{if $user}
			<input type="submit" class="btn btn-default btn-sm" value="{$wp_trade_action}">
		{else}
			{remarksbox type="info" title="{tr}Login{/tr}"}
				{tr}Please login first{/tr}
				{button _script="tiki-login_scr.php" _text="{tr}Click here{/tr}"}
			{/remarksbox}
		{/if}
	</div>
</form>
