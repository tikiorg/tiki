{tikimodule error=$module_params.error title=$tpl_module_title name="cart" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form method="post" action="">
	<table>
		<tr>
			<th>{tr}Product{/tr}</th>
			<th>{tr}Unit cost{/tr}</th>
			<th>{tr}Qty{/tr}</th>
		</tr>
		{foreach from=$cart_content item=item}
			<tr>
				<td>
					{if $item.href}
						<a href="{$item.href|escape}">{$item.description|escape}</a>
					{else}
						{$item.description|escape}
					{/if}
				</td>
				<td>{$item.price|escape}</td>
				<td><input type="text" name="cart[{$item.code|escape}]" size="3" value="{$item.quantity|escape}"/></td>
			</tr>
		{/foreach}
		<tr>
			<td></td>
			<td></td>
			<td><input type="submit" name="update" value="{tr}Update{/tr}"/></td>
		</tr>
	</table>
	</form>
	<p>{tr}Total{/tr}: <strong>{$cart_total|escape} {$prefs.payment_currency|escape}</strong></p>
	<form method="post" action="">
		<p><input type="submit" name="checkout" value="{tr}Check-out{/tr}"/></p>
	</form>
{/tikimodule}
