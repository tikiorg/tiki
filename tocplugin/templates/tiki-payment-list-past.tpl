<div id="{$table_id}-div" class="{if $js === 'y'}table-responsive{/if} ts-wrapperdiv" {if $ts.enabled}style="visibility:hidden;"{/if}>
<table id="{$table_id}" class="table table-striped table-hover" data-count="{$payments.cant|escape}">
	<thead>
		<tr>
			<th id="id">{tr}ID{/tr}</th>
			<th id="description">{tr}Description{/tr}</th>
			{if $ts.enabled}<th id="detail">{tr}Detail{/tr}</th>{/if}
			<th id="amount">{tr}Amount{/tr}</th>
			<th id="pmt_date">{tr}Date Paid{/tr}</th>
			<th id="pmt_type">{tr}Type{/tr}</th>
			{if $tiki_p_admin eq 'y'}<th id="user">{tr}User{/tr}</th>{/if}
			{if $tiki_p_admin eq 'y'}<th id="payer">{tr}Payer{/tr}</th>{/if}
			<th id="actions"></th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$payments.data item=payment}
			<tr>
				<td class="id">{$payment.paymentRequestId}</td>
				<td class="text">
					{self_link invoice=$payment.paymentRequestId}
					{if isset($smarty.request.invoice) && $payment.paymentRequestId eq $smarty.request.invoice}
						<strong>{$payment.description|escape}</strong>
					{else}
						{$payment.description|escape}
					{/if}
					{/self_link}
				</td>
				{if $ts.enabled}
					<td>
						{$payment.request_detail}
					</td>
				{/if}
				<td class="integer">{$payment.amount|escape}&nbsp;{$payment.currency|escape}</td>
				<td class="date">{if !empty($payment.payment_date)}{$payment.payment_date|tiki_short_date}{/if}</td>
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
					{capture name=past_actions}
						{strip}
							{$libeg}{self_link invoice=$payment.paymentRequestId _icon_name="textfile" _menu_text='y' _menu_icon='y'}{tr}View payment info{/tr}{/self_link}{$liend}
							{permission type=payment object=$payment.paymentRequestId name=payment_admin}
								{$libeg}{permission_link type=payment id=$payment.paymentRequestId title=$payment.description mode=text}{$liend}
							{/permission}
							{if isset($cancel) and ($payment.user eq $user or $tiki_p_payment_admin)}
								{$libeg}{self_link _ajax=n cancel=$payment.paymentRequestId _icon_name='remove' _menu_text='y' _menu_icon='y'}{tr}Cancel this payment request{/tr}{/self_link}{$liend}
							{/if}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}" href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.past_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='settings'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.past_actions}</ul></li></ul>
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
