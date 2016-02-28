<div class="table-responsive">
<table class="table">
	<tr>
		<th>{tr}ID{/tr}</th>
		<th>{tr}Description{/tr}</th>
		<th>{tr}Amount{/tr}</th>
		<th>{tr}Request Date{/tr}</th>
		{if $tiki_p_admin eq 'y'}<th>{tr}User{/tr}</th>{/if}
		<th>{tr}Actions{/tr}</th>
	</tr>
	{foreach from=$payments.data item=payment}
		<tr>
			<td class="id">{$payment.paymentRequestId}</td>
			<td class="text">{if $payment.paymentRequestId eq $smarty.request.invoice}<strong>{$payment.description|escape}</strong>{else}{self_link invoice=$payment.paymentRequestId}{$payment.description|escape}{/self_link}{/if}</td>
			<td class="integer">{$payment.amount|escape}&nbsp;{$payment.currency|escape}</td>
			<td class="date">{$payment.request_date|tiki_short_date|escape}</td>
			{if $tiki_p_admin eq 'y'}<td class="text">{$payment.user|userlink}</td>{/if}
			<td class="action">
				{self_link invoice=$payment.paymentRequestId _icon_name='textfile' _class=tips _title=":{tr}View payment request{/tr}"}
				{/self_link}
				{permission type=payment object=$payment.paymentRequestId name=payment_admin}
					{permission_link type="payment" id=$payment.paymentRequestId title=$payment.description}
				{/permission}
				{if $cancel and ($payment.user eq $user or $tiki_p_payment_admin)}
					{self_link _ajax=n cancel=$payment.paymentRequestId _icon_name='remove' _class='tips' _title=":{tr}Cancel this payment request{/tr}"}
					{/self_link}
				{/if}
			</td>
		</tr>
	{/foreach}
</table>
</div>

{pagination_links cant=$payments.cant step=$payments.max offset=$payments.offset offset_arg=$payments.offset_arg}{/pagination_links}
