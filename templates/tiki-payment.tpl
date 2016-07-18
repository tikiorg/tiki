{title help="Payment" admpage="payment"}{tr}Payment{/tr}{/title}

{if isset($invoice)}
	{payment id=$invoice}
{/if}
{if $user}
	{if $prefs.javascript_enabled !== 'y'}
		{$js = 'n'}
		{$libeg = '<li>'}
		{$liend = '</li>'}
	{else}
		{$js = 'y'}
		{$libeg = ''}
		{$liend = ''}
	{/if}
	{tabset}
		{permission name=payment_view}
			{tab name="{tr}Outstanding{/tr}"}
				{if $overdue.cant > 0 || $outstanding.data|@count > 0 || $authorized.data|@count > 0}
					{if $overdue.cant > 0}
						<h4>{tr}Overdue{/tr}</h4>
						{include file='tiki-payment-list.tpl' payments=$overdue cancel=1 table_id='pmt_overdue'}
					{/if}

					{if $outstanding.cant > 0}
						<h4>{tr}Outstanding{/tr}</h4>
						{include file='tiki-payment-list.tpl' payments=$outstanding cancel=1 table_id='pmt_outstanding'}
					{/if}

					{if $authorized.cant > 0}
						<h4>{tr}Authorized{/tr}</h4>
						{include file='tiki-payment-list.tpl' payments=$authorized cancel=1 table_id='pmt_authorized'}
					{/if}
				{else}
					<br><em>{tr}No outstanding payments found{/tr}</em>
				{/if}
			{/tab}
			{tab name="{tr}Past{/tr}"}
				{if $past.cant > 0}
					{include file='tiki-payment-list-past.tpl' payments=$past table_id='pmt_past'}
				{else}
					<br><em>{tr}No paid payments found{/tr}</em>
				{/if}
			{/tab}
			{tab name="{tr}Cancelled{/tr}"}
				{if $canceled.cant > 0}
					{include file='tiki-payment-list.tpl' payments=$canceled table_id='pmt_canceled'}
				{else}
					<br>{tr}<em>No cancelled payments found</em>{/tr}
				{/if}
			{/tab}
		{/permission}
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
