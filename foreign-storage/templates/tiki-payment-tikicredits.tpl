{* $Id$ *}
<p>{$payment.amount_paid|escape} was paid on {$payment.payment_date|escape} using {$payment.details.creditAmount|escape} {$payment.details.creditType|escape}.</p>
<p>{tr}using{/tr} <strong>{tr}Tiki User Credits{/tr}</strong></p>
Paid by {$payment.details.username|userlink}
