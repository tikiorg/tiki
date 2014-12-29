{title help="Payment" admpage="payment"}{tr}Payment{/tr}{/title}

{if $invoice}
	{payment id=$invoice}
{/if}
{if $user}
	{tabset}
		{permission name=payment_view}
			{tab name="{tr}Outstanding{/tr}"}
				<h2>{tr}Outstanding{/tr}</h2>
				{if $overdue.data}
					<p>{tr}Overdue{/tr}</p>
					{include file='tiki-payment-list.tpl' payments=$overdue cancel=1}
				{/if}

				<p>{tr}Outstanding{/tr}</p>
				{include file='tiki-payment-list.tpl' payments=$outstanding cancel=1}

				{if $authorized.data}
				<p>{tr}Authorized{/tr}</p>
				{include file='tiki-payment-list.tpl' payments=$authorized cancel=1}
				{/if}
			{/tab}
			{tab name="{tr}Past{/tr}"}
				<h2>{tr}Past{/tr}</h2>
				{include file='tiki-payment-list-past.tpl' payments=$past}
			{/tab}
			{tab name="{tr}Cancelled{/tr}"}
				<h2>{tr}Cancelled{/tr}</h2>
				{include file='tiki-payment-list.tpl' payments=$canceled}
			{/tab}
		{/permission}
		{permission name=payment_request}
			{tab name="{tr}Request{/tr}"}
				<h2>{tr}Request{/tr}</h2>
				<form method="post" action="">
					{if $prefs.feature_categories eq 'y'}
						<fieldset>
							<legend>{tr}Categories{/tr}</legend>
							{include file="categorize.tpl" notable=y}
						</fieldset>
					{/if}
					<fieldset>
						<label for="description">{tr}Description:{/tr}</label>
						<input type="text" id="description" name="description">
					</fieldset>

					<fieldset>
						<label for="detail">{tr}Detail:{/tr}</label>
						<textarea id="detail" name="detail" style="width: 100%;" rows="10"></textarea>
					</fieldset>

					<fieldset>
						<label for="amount">{tr}Amount:{/tr}</label>
						<input type="text" id="amount" name="amount" class="text-right">
						{$prefs.payment_currency|escape}
					</fieldset>

					<fieldset>
						<label for="payable">{tr}Payable within:{/tr}</label>
						<input type="text" id="payable" class="text-right" name="payable" value="{$prefs.payment_default_delay|escape}">
						{tr}days{/tr}
					</fieldset>

					<p><input type="submit" class="btn btn-default btn-sm" name="request" value="{tr}Request{/tr}"></p>
				</form>
			{/tab}
		{/permission}
	{/tabset}
{/if}
