<div id="{$table_id}-div" class="{if $js === 'y'}table-responsive{/if} ts-wrapperdiv" {if $ts.enabled}style="visibility:hidden;"{/if}>
	<table id="{$table_id}" class="table table-striped table-hover" data-count="{$payments.cant|escape}">
		<thead>
		<tr>
			<th id="id">{tr}ID{/tr}</th>
			<th id="description">{tr}Description{/tr}</th>
			<th id="amount">{tr}Amount{/tr}</th>
			<th id="req_date">{tr}Request Date{/tr}</th>
			{if $tiki_p_admin eq 'y'}<th id="user">{tr}User{/tr}</th>{/if}
			<th id="actions">{tr}Actions{/tr}</th>
		</tr>
		</thead>
		<tbody>
			{foreach from=$payments.data item=payment}
				<tr>
					<td class="id">{$payment.paymentRequestId}</td>
					<td class="text">{if $payment.paymentRequestId eq $smarty.request.invoice}<strong>{$payment.description|escape}</strong>{else}{self_link invoice=$payment.paymentRequestId}{$payment.description|escape}{/self_link}{/if}</td>
					<td class="integer">{$payment.amount|escape}&nbsp;{$payment.currency|escape}</td>
					<td class="date">{if !empty($payment.request_date)}{if $prefs.jquery_timeago eq 'y'}{$payment.request_date|tiki_short_date}{else}{$payment.request_date|tiki_short_date|escape}{/if}{/if}</td>
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
		</tbody>
	</table>
</div>
{if !$ts.enabled}
	{pagination_links cant=$payments.cant step=$payments.max offset=$payments.offset offset_arg=$payments.offset_arg}{/pagination_links}
{/if}
