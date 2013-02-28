<div {if isset($iPluginMemberpayment)}id="pluginMemberpayment{$iPluginMemberpayment}" {elseif isset($datachannel_execution)}id="{$datachannel_execution}" {/if}class="payment">
	<fieldset>
		<legend style="font-weight: bold">{tr}Payment Request{/tr}</legend>
		{if isset($wp_member_title)}
			{if !empty($wp_member_title)}
				<br>{wiki}{tr _0=$wp_member_group.groupName _1=$wp_member_price _2=$prefs.payment_currency _4=$wp_member_group.expireAfter _5=$wp_member_group.expireAfterYear}{$wp_member_title}{/tr}{/wiki}<br>
			{/if}
		{else}
			<h2>{$payment_info.description|escape}</h2>
		{/if}
		{if $wp_member_titleonly neq 'y'}
			<fieldset>
				<legend style="font-style: italic">{tr}Payment Status{/tr}</legend>
				<table style="width: auto">
					<tr>
						<td class="italiclabel">
							{tr}Status:{/tr}
						</td>
						<td style="font-weight: bold">
							{tr}{$payment_info.state|capitalize|escape}{/tr}
						</td>
						<td></td>
					</tr>
				{if $payment_info.fullview and !empty($payment_detail)}
					<tr>
						<td colspan="2">
							<div class="clearfix wikitext">
								{$payment_detail}
							</div>
						</td>
						<td></td>
					</tr>
				{/if}
				{if $payment_info.state eq 'past'}
					<tr>
						<td class="italiclabel">
							{tr}Paid amount:{/tr}
						</td>
						<td class="integer">
							{$payment_info.amount_original|escape}
						</td>
						<td style="font-style: italic" >
							{$payment_info.currency|escape}
						</td>
					</tr>
				{else}
					<tr>
						<td class="italiclabel">
							{tr}Initial amount:{/tr}
						</td>
						<td class="integer">
							{$payment_info.amount_original|escape}
						</td>
						<td style="font-style: italic">
							{$payment_info.currency|escape}
						</td>
					</tr>
					<tr>
						<td class="italiclabel">
							{tr}Amount remaining:{/tr}
						</td>
						<td class="integer">
							{$payment_info.amount_original|escape}
						</td>
						<td style="font-style: italic">
							{$payment_info.currency|escape}
						</td>
					</tr>
					<tr>
						<td class="italiclabel">
							{tr}Payment request initiated:{/tr}
						</td>
						<td>
							{$payment_info.request_date|tiki_short_date}
						</td>
						<td></td>
					</tr>
					<tr>
						<td class="italiclabel">
							{tr}Payment request due:{/tr}
						</td>
						<td>
							{$payment_info.due_date|tiki_short_date}
						</td>
						<td></td>
					</tr>
				{/if}
				</table>
			</fieldset>
		{/if}
		{if ( $payment_info.state eq 'outstanding' || $payment_info.state eq 'overdue' )}
			<fieldset>
				{if $prefs.payment_system eq 'paypal' && $prefs.payment_paypal_business neq ''}
					<legend style="font-style: italic">{tr}Pay with PayPal{/tr}</legend>
					<form action="{$prefs.payment_paypal_environment|escape}" method="post">
						<input type="hidden" name="business" value="{$prefs.payment_paypal_business|escape}" />
						<input type="hidden" name="cmd" value="_xclick" />
						<input type="hidden" name="item_name" value="{$payment_info.description|escape}" />
						<input type="hidden" name="charset" value="utf-8">
						<input type="hidden" name="amount" value="{$payment_info.amount_remaining_raw|escape}" />
						<input type="hidden" name="currency_code" value="{$prefs.payment_currency|escape}" />
						<input type="hidden" name="invoice" value="{$prefs.payment_invoice_prefix|escape}{$payment_info.paymentRequestId|escape}" />
						<input type="hidden" name="return" value="{$payment_info.returnurl|escape}" />
						{*<input type="hidden" name="rm" value="2" />*}
						{if $prefs.payment_paypal_ipn eq 'y'}
							<input type="hidden" name="notify_url" value="{$payment_info.paypal_ipn|escape}" />
						{/if}
						<br><input type="image" style="display:block; margin-left: 15px" name="submit" border="0" src="https://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif" alt="PayPal" title="{tr}Pay with Paypal{/tr}"/>
						<br><input type="image" name="submit" border="0" src="https://www.paypal.com/en_US/i/bnr/horizontal_solution_PPeCheck.gif" border="0" alt="PayPal" />
					</form>
				{elseif $prefs.payment_system eq 'cclite' && $prefs.payment_cclite_gateway neq ''}
					<legend style="font-style: italic">{tr}Pay With Cclite{/tr}</legend>
					{if (!empty($ccresult) or !empty($ccresult2)) and $ccresult_ok}
						<form action="{query _type='relative'}" method="post">
							<input type="hidden" name="invoice" value="{$payment_info.paymentRequestId|escape}" />
							<input type="hidden" name="cookietab" value="1" />
							<input type="submit" value="{tr}Refresh page{/tr}" />
						</form>
						{remarksbox title="{tr}Payment info{/tr}" type="info"}
							{$ccresult}<br>
							{$ccresult2}
						{/remarksbox}
					{else}
						<form action="{query _type='relative'}" method="post">
							<input type="hidden" name="invoice" value="{$payment_info.paymentRequestId|escape}" />
							<input type="hidden" name="cclite_payment_amount" value="{$payment_info.amount_remaining|escape}" />
							<input type="submit" value="{tr}Trade with Cclite{/tr}" />
						</form>
						{if (!empty($ccresult) or !empty($ccresult2))}
							{remarksbox title="{tr}Payment problem{/tr}" type="info"}
								{$ccresult}<br>
								{$ccresult2}
							{/remarksbox}
						{/if}
					{/if}
				{elseif $prefs.payment_system eq 'tikicredits'}
					<legend style="font-style: italic">{tr}Pay With Tiki Credits{/tr}</legend>
					<form action="{query _type='relative'}" method="post">
						{tr}Pay with Tiki User Credits:{/tr}
						<table class="normal">
							<tr>
								<th>{tr}Credit type{/tr}</th>
								<th>{tr}Credits left{/tr}</th>
								<th>{tr}Amount to pay{/tr}</th>
								<th>{tr}Pay using{/tr}</th>
							</tr>
							{foreach key=id item=data from=$userpaycredits}
							<tr>
								<td class="text">{$data.display_text|escape}</td>
								<td class="text">{$data.remain|escape}</td>
								<td class="integer">{$data.price|escape}</td>
								<td class="text"><input type="radio" name="tiki_credit_type" value="{$id|escape}" {if !$data.enough}disabled="disabled"{/if} /></td>
							</tr>
							{/foreach}
							<tr>
								<td colspan="4">
									<input type="hidden" name="invoice" value="{$payment_info.paymentRequestId|escape}" />
									<input type="hidden" name="tiki_credit_amount" value="{$payment_info.amount_remaining|escape}" />
									<input type="submit" name="tiki_credit_pay" value="{tr}Pay with Tiki User Credits{/tr}" />
								</td>
							</tr>
						</table>
					</form>
				{/if}
			</fieldset>

			{if !empty($prefs.payment_manual)}
				{capture name=wp_payment_manual}wiki:{$prefs.payment_manual}{/capture}
				{include file=$smarty.capture.wp_payment_manual}
			{/if}
		{/if}

		{if $payment_info.fullview && $payment_info.payments|@count}
			<fieldset>
				<legend style="font-style: italic">{tr}Payment Details{/tr}</legend>
				{if count($payment_info.payments) ne 1}<ol>{else}<ul>{/if}
					{foreach from=$payment_info.payments item=payment}
						<li>
							{if $payment.type eq 'user'}
								{include file='tiki-payment-user.tpl' payment=$payment currency=$payment_info.currency}
							{elseif $payment.type eq 'paypal'}
								{include file='tiki-payment-paypal.tpl' payment=$payment}
							{elseif $payment.type eq 'cclite'}
								{include file='tiki-payment-cclite.tpl' payment=$payment}
							{elseif $payment.type eq 'tikicredits'}
								{include file='tiki-payment-tikicredits.tpl' payment=$payment}
							{/if}
						</li>
					{/foreach}
				{if count($payment_info.payments) ne 1}</ol>{else}</ul>{/if}
			</fieldset>
		{/if}

		{if $payment_info.state eq 'outstanding' || $payment_info.state eq 'overdue'}
			{permission type=payment object={$payment.paymentRequestId} name=payment_manual}
				<form method="post" action="tiki-payment.php">
					<fieldset>
						<legend style="font-style: italic">{tr}Enter a Manual Payment{/tr}</legend>

						<p>
							<input type="text" name="manual_amount" class="right" />&nbsp;<span style="font-style: italic">{$payment_info.currency|escape}</span>
						</p>
						<p>
							<label for="payment-note" style="font-style: italic">{tr}Note{/tr}</label>
							<textarea id="payment-note" name="note" style="width: 98%;" rows="6"></textarea>
                        </p>
						<p>
							<input type="hidden" name="returnurl" value="{$payment_info.returnurl|escape}" />
							<input type="submit" value="{tr}Enter payment{/tr}" />
							<input type="hidden" name="invoice" value="{$payment_info.paymentRequestId|escape}" />
						</p>
					</fieldset>
				</form>
			{/permission}
		{/if}
    </fieldset>
</div>
