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
				<form method="post" action=""><br>
					<div class="form-group">
						<label class="col-sm-3 control-label text-right" for="description">
							{tr}Description{/tr}
						</label>
						<div class="col-sm-8 input-group">
							<input class="form-control" type="text" id="description" name="description">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label text-right" for="detail">
							{tr}Detail{/tr}
						</label>
						<div class="col-sm-8 input-group">
							<textarea class="form-control" id="detail" name="detail" style="width: 100%;" rows="6"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label text-right" for="amount">
							{tr}Amount{/tr}
						</label>
						<div class="col-sm-8 input-group">
							<input type="text" id="amount" name="amount" class="form-control text-right">
							<span class="input-group-addon">
								{$prefs.payment_currency|escape}
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label text-right" for="payable">
							{tr}Payable within{/tr}
						</label>
					</div>
					<div class="col-sm-8 input-group">
						<input type="text" id="payable" class="text-right form-control" name="payable" value="{$prefs.payment_default_delay|escape}">
						<span class="input-group-addon">
							{tr}days{/tr}
						</span>
					</div><br>
					{if $prefs.feature_categories eq 'y'}
						{include file="categorize.tpl" labelcol=3 labelclass='text-right' inputcol=8 inputgroup=y}
					{/if}
					<div class="form-group">
						<div class="col-sm-8 col-sm-offset-3 input-group">
							<input type="submit" class="btn btn-primary" name="request" value="{tr}Request{/tr}">
						</div>
					</div>
				</form>
			{/tab}
		{/permission}
	{/tabset}
{/if}
