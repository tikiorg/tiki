<table class="data">
	<tr>
		<th>{tr}Description{/tr}</th>
		<th>{tr}Amount{/tr}</th>
		<th>{tr}Actions{/tr}</th>
	</tr>
	{foreach from=$payments.data item=payment}
		<tr>
			<td>{$payment.description|escape}</td>
			<td class="numeric">{$payment.amount|escape}&nbsp;{$payment.currency|escape}</td>
			<td>
				{self_link invoice=$payment.paymentRequestId}{icon _id=page}{/self_link}
				{permission type=payment object=$payment.paymentRequestId name=payment_admin}
					<a class="link" href="tiki-objectpermissions.php?objectName={$payment.description|escape:url}&amp;objectType=payment&amp;permType=payment&amp;objectId={$payment.paymentRequestId|escape:"url"}">
						{icon _id='key' alt='{tr}Perms{/tr}'}
					</a>
					{if $cancel}
						{self_link cancel=$payment.paymentRequestId}{icon _id=cross}{/self_link}
					{/if}
				{/permission}
			</td>
		</tr>
	{/foreach}
</table>
{pagination_links cant=$payments.cant step=$payments.max offset=$payments.offset offset_arg=$payments.offset_arg}{/pagination_links}
