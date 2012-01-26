<table class="normal">
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
				{self_link invoice=$payment.paymentRequestId}{icon _id=page class=titletips title="{tr}View payment request{/tr}" alt="{tr}Invoice{/tr}"}{/self_link}
				{permission type=payment object=$payment.paymentRequestId name=payment_admin}
					<a class="link" href="tiki-objectpermissions.php?objectName={$payment.description|escape:url}&amp;objectType=payment&amp;permType=payment&amp;objectId={$payment.paymentRequestId|escape:"url"}">
						{icon _id='key' class=titletips title="{tr}Assign permissions for payments{/tr}" alt="{tr}Permissions{/tr}"}
					</a>
				{/permission}
				{if $cancel and ($payment.user eq $user or $tiki_p_payment_admin)}
					{self_link _ajax=n cancel=$payment.paymentRequestId}{icon _id=cross class=titletips title="{tr}Cancel this payment request{/tr}" alt="{tr}Cancel{/tr}"}{/self_link}
				{/if}
			</td>
		</tr>
	{/foreach}
</table>
{pagination_links cant=$payments.cant step=$payments.max offset=$payments.offset offset_arg=$payments.offset_arg}{/pagination_links}
