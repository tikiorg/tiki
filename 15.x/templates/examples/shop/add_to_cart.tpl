{if $row.tracker_field_product_stock|nonp gt 0}
	{if $row.use_minicart|nonp}
		{wikiplugin _name='paypal' button_type="_cart" cart_action="add" item_name=$row.title amount=$row.tracker_field_product_price paypal_button="small_button" item_number=$row.object_id}{/wikiplugin}
	{else}
		{wikiplugin _name='addtocart' code=$row.object_id description=$row.name|nonp price=$row.tracker_field_product_price ajaxaddtocart='y' href="cart+product?itemId="|cat:$row.object_id weight=$row.tracker_field_product_weight}{/wikiplugin}
	{/if}
{/if}
