<div class="table-responsive">
<table class="table table-striped table-hover">
	<tr>
		<th>{tr}ID{/tr}</th>
		<th>{tr}Description{/tr}</th>
		<th>{tr}Amount{/tr}</th>
		<th>{tr}Payment Date{/tr}</th>
		<th>{tr}Payment Type{/tr}</th>
		{if $tiki_p_admin eq 'y'}<th>{tr}User{/tr}</th>{/if}
		{if $tiki_p_admin eq 'y'}<th>{tr}Payer{/tr}</th>{/if}
		<th></th>
	</tr>
	{foreach from=$payments.data item=payment}
		<tr>
			<td class="id">{$payment.paymentRequestId}</td>
			<td class="text">
				{self_link invoice=$payment.paymentRequestId}
					{if $payment.paymentRequestId eq $smarty.request.invoice}
						<strong>{$payment.description|escape}</strong>
					{else}
						{$payment.description|escape}
					{/if}
				{/self_link}
			</td>
			<td class="integer">{$payment.amount|escape}&nbsp;{$payment.currency|escape}</td>
			<td class="date">{$payment.payment_date|tiki_short_date|escape}</td>
			<td class="text">{$payment.type|escape}</td>
			{if $tiki_p_admin eq 'y'}
				<td class="text">
					{if $payment.user}
						{$payment.user|userlink}
					{else}
						{tr}Anonymous{/tr}
					{/if}
				</td>
			{/if}
			{if $tiki_p_admin eq 'y'}
				<td class="text">
					{if $payment.payer}
						{$payment.payer|userlink}
					{else}
						<em title="{tr _0=$payment.type|escape}Payer email from %0{/tr}" class="text-info">
							{$payment.payer_email}
						</em>
					{/if}
				</td>
			{/if}
			<td class="action">
				{self_link invoice=$payment.paymentRequestId _icon_name="textfile" _class=tips _title=":{tr}View payment info{/tr}"}
				{/self_link}
				{permission type=payment object=$payment.paymentRequestId name=payment_admin}
					{permission_link type=payment id=$payment.paymentRequestId title=$payment.description}
				{/permission}
				{if $cancel and ($payment.user eq $user or $tiki_p_payment_admin)}
					{self_link _ajax=n cancel=$payment.paymentRequestId _icon_name='remove' _class=tips _title=":{tr}Cancel this payment request{/tr}"}
					{/self_link}
				{/if}
			</td>
		</tr>
	{/foreach}
</table>
</div>

{pagination_links cant=$payments.cant step=$payments.max offset=$payments.offset offset_arg=$payments.offset_arg}{/pagination_links}
