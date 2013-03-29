{if $row.tracker_field_tracker_products_stock|nonp gt 0}
	{wikiplugin _name='paypal' button_type="_cart" cart_action="add" item_name=$row.title amount=$row.price_raw paypal_button="small_button" item_number=$row.object_id}{/wikiplugin}
{/if}
