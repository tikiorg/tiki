{if isset($items)}
	{$encodedItems = json_encode($items)}
	<input type='hidden' name='items' value="{$encodedItems|escape}">
{/if}
{if isset($extra)}
	{$encodedExtra = json_encode($extra)}
	<input type='hidden' name='extra' value="{$encodedExtra|escape}">
{/if}
{if isset($toList)}
	{$encodedToList = json_encode($toList)}
	<input type='hidden' name='toList' value="{$encodedToList|escape}">
{/if}
<input type='hidden' name='ticket' value="{$ticket}">
<input type="hidden" name="daconfirm" value="y">