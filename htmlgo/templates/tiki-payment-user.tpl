<p>{$payment.amount_paid|escape}&nbsp;{$currency|escape} was paid on {$payment.fpayment_date|escape}.</p>
{tr}Payment information{/tr}
<p>By {$payment.details.user|userlink}</p>
{if $payment.details.note}
	<p><strong>{tr}Note{/tr}:</strong> {$payment.details.note|escape|nl2br}</p>
{/if}
