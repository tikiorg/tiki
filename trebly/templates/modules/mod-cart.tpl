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
				</td>
				<td style="width:5em;" align="right">{$item.price|escape}</td>
				<td style="width:2em;"><input type="text" name="cart[{$item.code|escape}]" style="width:2em;text-align:right;" value="{$item.quantity|escape}"/></td>
			</tr>
		{/foreach}
		<tr>
			<td></td>
			<td colspan="2" align="right"><input type="submit" name="update" value="{tr}Update{/tr}"/></td>
		</tr>
	</table>
	</form>
	<p>{tr}Total:{/tr} <strong>{$cart_total|escape} {$prefs.payment_currency|escape}</strong></p>
	<form method="post" action="">
		<p><input type="submit" name="checkout" value="{tr}Check-out{/tr}"/></p>
	</form>
{else}
	<p>{tr}Your cart is empty{/tr}</p>
{/if}
{/tikimodule}
