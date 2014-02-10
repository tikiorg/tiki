{tikimodule error=$module_params.error title=$tpl_module_title name="cart" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if !empty($cart_content)}
	{if $module_params.showItems neq 'n'}
		<form method="post" action="{if $module_params.ajax eq 'n'}{query _keepall='y' _type='relative'}{else}{service controller='module' action='execute'}{/if}"{if $module_params.ajax eq 'y'} class="mod-cart-form"{/if}>
		<table>
			<tr>
				<th>{tr}Product{/tr}</th>
				<th style="width:5em;">{tr}Unit cost{/tr}</th>
				<th style="width:2em;">{tr}Qty{/tr}</th>
			</tr>
	
			{foreach from=$cart_content item=item} 
					<tr>
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
						<td style="width:2em;"><input type="text" name="cart[{$item.code|escape}]" style="width:2em;text-align:right;" value="{$item.quantity|escape}"></td>
					</tr>
					{foreach from=$item.bundledproducts item=child_item}
						<tr>
							<td colspan="3">
								{tr}Bundled Product{/tr} - {$child_item.description|escape} {if $child_item.quantity > 1}(x{$child_item.quantity|escape}){/if}
							</td>
							<td style="width:5em;" align="right">{$item.price|escape}</td>
							<td style="width:2em;"><input type="text" name="cart[{$item.code|escape}]" style="width:2em;text-align:right;" value="{$item.quantity|escape}"></td>
						</tr>
					{/foreach}
			{/foreach}
			<tr>
				<td></td>
				<td colspan="2" align="right"><input type="submit" class="btn btn-default btn-sm" name="update" value="{tr}Update{/tr}"></td>
			</tr>
		</table>
		</form>
	{/if}
	{if $module_params.showCount eq 'y'}
		<span class="item_count">{tr _0=count($cart_content)}Cart contains %0 items{/tr}</span>
	{/if}

	<form method="post" action=""{if $module_params.ajax eq 'y'} class="mod-cart-checkout-form"{$json_data}{/if}>
		{if $has_gift_certificate}
			<div class="gift_certificate">
				{if $gift_certificate_redeem_code && $gift_certificate_amount}
					<p>
						{tr}Gift Certificate:{/tr} {$gift_certificate_redeem_code}<br>
						{tr}Value{/tr}
						: {$gift_certificate_mode_symbol_before}{$gift_certificate_amount}{$gift_certificate_mode_symbol_after}
					</p>
				{/if}
				{if $gift_certificate_redeem_code}
					<input type="submit" class="btn btn-warning btn-sm" name="remove_gift_certificate" value="{tr}Remove Gift Certificate{/tr}">
				{else}
					Code: <input type="text" name="gift_certificate_redeem_code" style="width: 70px;">
					<input type="submit" class="btn btn-default btn-sm" name="add_gift_certificate" value="{tr}Add Gift Certificate{/tr}">
				{/if}
			</div>
		{/if}
		<p>{tr}Total:{/tr} <strong>{$cart_total|escape} {$prefs.payment_currency|escape}</strong></p>

		<input type="submit" class="btn btn-default btn-sm" name="checkout" value="{tr}Check-out{/tr}">
	</form>
{else}
	<p>{tr}Your cart is empty{/tr}</p>
{/if}
{/tikimodule}
