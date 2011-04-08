{tikimodule error=$module_params.error title=$tpl_module_title name="cart" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if !empty($cart_content)}
	<form method="post" action="{query _keepall='y' _type='relative'}">
	<table>
		<tr>
			<th>{tr}Product{/tr}</th>
			<th style="width:5em;">{tr}Unit cost{/tr}</th>
			<th style="width:2em;">{tr}Qty{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{foreach from=$cart_content item=item} 
				<tr class="{cycle}">
					<td>
						{if $item.href}
							<a href="{$item.href|escape}">{$item.description|escape}</a>
						{else}
							{$item.description|escape}
						{/if}
						{if $item.onbehalf}
							{tr}for{/tr} {$item.onbehalf|escape|truncate:16}
						{/if}
					</td>
					<td style="width:5em;" align="right">{$item.price|escape}</td>
					<td style="width:2em;"><input type="text" name="cart[{$item.code|escape}]" style="width:2em;text-align:right;" value="{$item.quantity|escape}"/></td>
				</tr>
				{foreach from=$item.bundledproducts item=child_item}
					<tr class="{cycle}">
						<td colspan="3">
							{tr}Bundled Product{/tr} - {$child_item.description|escape}
						</td>
					</tr>
				{/foreach}
		{/foreach}
		<tr>
			<td></td>
			<td colspan="2" align="right"><input type="submit" name="update" value="{tr}Update{/tr}"/></td>
		</tr>
	</table>
	</form>
	
	<form method="post" action="">
		<p>
			{if $has_gift_certificate eq 'true'}
				{if $gift_certificate_redeem_code && $gift_certificate_amount}
					<span>
						{tr}Gift Certificate{/tr}: {$gift_certificate_redeem_code}<br />
						{tr}Value{/tr}
						: {$gift_certificate_mode_symbol_before}{$gift_certificate_amount}{$gift_certificate_mode_symbol_after}
					</span>
					<br />
				{/if}
				{if $gift_certificate_redeem_code}
					<input type="submit" name="remove_gift_certificate" value="{tr}Remove Gift Certificate{/tr}"/>
				{else}
					Code: <input type="text" name="gift_certificate_redeem_code" style="width: 70px;" />
					<input type="submit" name="add_gift_certificate" value="{tr}Add Gift Certificate{/tr}"/>
				{/if}
				<br />
				<br />
			{/if}
			
			<p>{tr}Total{/tr}: <strong>{$cart_total|escape} {$prefs.payment_currency|escape}</strong></p>
			
			<input type="submit" name="checkout" value="{tr}Check-out{/tr}"/>
		</p>
	</form>
{else}
	<p>{tr}Your cart is empty{/tr}</p>
{/if}
{/tikimodule}
