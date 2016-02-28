{if $payment.status eq 'paid'}
	<p>{$payment.amount_paid|escape}&nbsp;{$payment.details.CURRENCY_CODE|escape} was paid on {$payment.payment_date|escape}.</p>
	<p><strong>{tr}Israel Post{/tr}</strong> ({tr}Order ID{/tr} - {$payment.details.ORDERID|escape})</p>
{elseif $payment.status eq 'auth_pending'}
	<p>{tr}Payment authorized (not captured){/tr}: {$payment.details.TOTAL_PAID|escape} {$payment.details.CURRENCY_CODE|escape}</p>
{elseif $payment.status eq 'auth_captured'}
	<p>{tr}Payment authorized (captured){/tr}: {$payment.details.TOTAL_PAID|escape} {$payment.details.CURRENCY_CODE|escape}</p>
{else}
	{tr}Unknown state{/tr}
{/if}
