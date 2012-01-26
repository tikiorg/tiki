<p>{$payment.amount_paid|escape}&nbsp;{$payment.details.mc_currency|escape} was paid on {$payment.payment_date|escape}.</p>
<p><strong>{tr}Paypal{/tr}</strong> ({tr}Transaction ID{/tr} - {$payment.details.txn_id|escape})</p>
<p>{tr _0=$payment.details.last_name|escape _1=$payment.details.first_name|escape _2=$payment.details.payer_email|escape}Payment made by <em>%0, %1</em> (%2).{/tr}</p>
<div class="address">
	{$payment.details.address_name|escape}
	<br/>
	{$payment.details.address_street|escape}
	<br/>
	{$payment.details.address_city|escape},
	{$payment.details.address_state|escape}
	<br/>
	{$payment.details.address_country|escape},
	{$payment.details.address_zip|escape}
</div>
