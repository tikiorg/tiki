{title}Payments{/title}

{if $payment_info}
	<div class="payment">
		<h2>{$payment_info.description|escape}</h2>
		<p>{tr}State{/tr}: <strong>{$payment_info.state|escape}</strong></p>
		{if $payment_info.fullview}
			<p>{tr}Initial amount{/tr}: {$payment_info.amount_original|escape} {$payment_info.currency|escape}</p>
		{/if}
		<p>
			{tr}Amount remaining{/tr}: <strong>{$payment_info.amount_remaining|escape} {$payment_info.currency|escape}</strong>
			{if ( $payment_info.state eq 'outstanding' || $payment_info.state eq 'overdue' ) && $prefs.payment_paypal_business neq ''}
				<form action="{$prefs.payment_paypal_environment|escape}" method="post">
					<input type="hidden" name="business" value="{$prefs.payment_paypal_business|escape}"/>
					<input type="hidden" name="cmd" value="_xclick"/>
					<input type="hidden" name="item_name" value="{$payment_info.description|escape}"/>
					<input type="hidden" name="amount" value="{$payment_info.amount_remaining_raw|escape}"/>
					<input type="hidden" name="currency_code" value="{$prefs.payment_currency|escape}"/>
					<input type="hidden" name="invoice" value="{$payment_info.paymentRequestId|escape}"/>
					<input type="hidden" name="return" value="{$payment_info.url|escape}"/>
					{if $prefs.payment_paypal_ipn eq 'y'}
						<input type="hidden" name="notify_url" value="{$payment_info.paypal_ipn|escape}"/>
					{/if}
					<input type="image" name="submit" border="0" src="https://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif" alt="PayPal - The safer, easier way to pay online"/>
				</form>
			{/if}
		</p>
		<p>{tr 0=$payment_info.request_date 1=$payment_info.due_date}Payment request was sent on %0 and is due by %1.{/tr}

		{if $payment_info.fullview && $payment_info.payments|@count}
			<ol>
				{foreach from=$payment_info.payments item=payment}
					<li>
						{if $payment.type eq 'user'}
							{include file=tiki-payment-user.tpl payment=$payment currency=$payment_info.currency}
						{elseif $payment.type eq 'paypal'}
							{include file=tiki-payment-paypal.tpl payment=$payment}
						{/if}
					</li>
				{/foreach}
			</ol>
		{/if}

		{if $payment_info.state eq 'outstanding' || $payment_info.state eq 'overdue'}

			{permission type=payment object=$payment.paymentRequestId name=payment_manual}
				<form method="post" action="">
					<fieldset>
						<legend>{tr}Manual payment entry{/tr}</legend>

						<p><input type="text" name="manual_amount"/>&nbsp;{$payment_info.currency|escape}</p>
						<p><label for="payment-note">{tr}Note{/tr}</label></p>
						<p><textarea id="payment-note" name="note" style="width: 98%;" rows="6"></textarea></p>
						<p><input type="submit" value="{tr}Enter payment{/tr}"/><input type="hidden" name="invoice" value="{$payment_info.paymentRequestId|escape}"/></p>
					</fieldset>
				</form>
			{/permission}
		{/if}
	</div>
{/if}

{tabset}
	{tab name="{tr}Outstanding{/tr}"}
		{if $overdue.cant > 0}
			<p>{tr}Overdue{/tr}</p>
			{include file=tiki-payment-list.tpl payments=$overdue cancel=1}
		{/if}

		<p>{tr}Outstanding{/tr}</p>
		{include file=tiki-payment-list.tpl payments=$outstanding cancel=1}
	{/tab}
	{tab name="{tr}Past{/tr}"}
		{include file=tiki-payment-list.tpl payments=$past}
	{/tab}
	{tab name="{tr}Canceled{/tr}"}
		{include file=tiki-payment-list.tpl payments=$canceled}
	{/tab}
	{permission name=payment_request}
		{tab name="{tr}Request{/tr}"}
			<form method="post" action="">
				{if $prefs.feature_categories eq 'y'}
					<fieldset>
						<legend>{tr}Categories{/tr}</legend>
						{include file="categorize.tpl" notable=y}
					</fieldset>
				{/if}
				<fieldset>
					<label for="description">{tr}Description{/tr}:</label>
					<input type="text" id="description" name="description"/>
				</fieldset>

				<fieldset>
					<label for="amount">{tr}Amount{/tr}:</label>
					<input type="text" id="amount" name="amount"/>
					{$prefs.payment_currency|escape}
				</fieldset>

				<fieldset>
					<label for="payable">{tr}Payable within{/tr}:</label>
					<input type="text" id="payable" name="payable" value="{$prefs.payment_default_delay|escape}"/>
					{tr}days{/tr}
				</fieldset>
				
				<p><input type="submit" name="request" value="{tr}Request{/tr}"/></p>
			</form>
		{/tab}
	{/permission}
{/tabset}
