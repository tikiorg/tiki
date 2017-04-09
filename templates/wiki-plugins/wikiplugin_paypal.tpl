{* $Id$ *}
<form action="{$prefs.payment_paypal_environment}" method="post" target="paypal" title="{$wppaypal_title|escape}" class="wppaypal_button" style="display:inline-block">
	{foreach key=key item=val from=$wppaypal_hiddens}
		<input type="hidden" name="{$key|escape}" value="{$val|escape}">
	{/foreach}
	{if $wppaypal_quantity}
		<input type="hidden" name="quantity" value="{$wppaypal_quantity|escape}">
	{else}
		<input type="number" name="quantity" value="1" min="1" style="width: 3em;">
	{/if}
	<input type="image" src="{$wppaypal_button}" name="submit" alt="{$wppaypal_title|escape}">
	<img src="{$wppaypal_pixel}" width="1" height="1">
</form>

