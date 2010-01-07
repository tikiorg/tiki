{title}Payments{/title}

{if $payment_info}
	<div class="payment">
		<h2>{$payment_info.description|escape}</h2>
		<p>{tr}State{/tr}: {$payment_info.state|escape}</p>
		{if $payment_info.fullview}
			<p>{tr}Initial amount{/tr}: {$payment_info.amount_original|escape} {$payment_info.currency|escape}</p>
		{/if}
		<p>{tr}Amount remaining{/tr}: {$payment_info.amount_remaining|escape} {$payment_info.currency|escape}</p>
		<p>{tr 0=$payment_info.request_date 1=$payment_info.due_date}Payment request was sent on %0 and is due by %1.{/tr}

		{if $payment_info.fullview && $payment_info.payments|@count}
			<ol>
				{foreach from=$payment_info.payments item=payment}
					<li>
						{if $payment.type eq 'user'}
							{include file=tiki-payment-user.tpl payment=$payment currency=$payment_info.currency}
						{/if}
					</li>
				{/foreach}
			</ol>
		{/if}

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
	</div>
{/if}

{tabset}
	{tab name="{tr}Outstanding{/tr}"}
		<p>{tr}Overdue{/tr}</p>
		{include file=tiki-payment-list.tpl payments=$overdue cancel=1}

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
