{title help="Invoice"}{tr}Edit Invoice{/tr}{/title}

{include file='tiki-invoice_menu.tpl'}

{* vars to deal with keys that have spaces *}
{assign var=ClientId value="Client Id"}
{assign var=WorkDescription value="Work Description"}
{assign var=InvoiceNumber value="Invoice Number"}
{assign var=DateIssued value="Date Issued"}
{assign var=InvoiceNote value="Invoice Note"}
{assign var=InvoiceItemId value="Invoice Item Id"}

<form action="tiki-edit_invoice.php" method="post" id="InvoiceForm">
	{tr}Client{/tr} <select name="ClientId" class="InvoiceClientId">
		{foreach from=$clients key=k item=client}
			<option value='{$client.$ClientId}' {if $client.$ClientId eq $invoice.$ClientId} selected='true' {/if}>{$client.Name}</option>
		{/foreach}
	</select>

	<table class='InvoiceDetails'>
		<tr>
			<td>
				{tr}Invoice Number{/tr} <input name='InvoiceNumber' id='InvoiceNumber' type='text' value='{$invoice.$InvoiceNumber}' />
				<br />
				{tr}Date Issued{/tr} <input name='DateIssued' id='DateIssued' value='{$invoice.$DateIssued}' />
			</td>
		</tr>
	</table>
	<hr />

	<table id='InvoiceItems'>
		<tr>
			<th>{tr}Quantity{/tr}</th>
			<th>{tr}Work Description{/tr}</th>
			<th>{tr}Taxable{/tr}</th>
			<th>{tr}Amount{/tr}</th>
			<th></th>
		</tr>

		{foreach from=$invoiceItems item=invoiceItem}
			<tr class='InvoiceItem'>
				<input name='InvoiceItemId' class='InvoiceItemId' type='hidden' value='{$invoiceItem.$InvoiceItemId}'
				<td><input name='Quantity[]' class='InvoiceQuantity' type='text' value='{$invoiceItem.Quantity}' /></td>
				<td><textarea name='WorkDescription[]' class='InvoiceWorkDescription'>{$invoiceItem.$WorkDescription}</textarea></td>
				<td><input name='Taxable[]' class='InvoiceTaxable' type='checkbox' value='y' {if $invoiceItem.Taxable eq 'y'} checked='true' {/if} /></td>
				<td><input name='Amount[]' class='InvoiceAmount' type='text' value='{$invoiceItem.Amount}' /></td>
				<td>
					<input type='button' class='DeleteItem' value='{tr}Delete{/tr}' />
				</td>
			</tr>
		{/foreach}

		<tr>
			<td colspan='4'>
				<input type='button' value='{tr}New Item{/tr}' id='InvoiceNewItem' />
			</td>
		</tr>

	</table>

	{tr}Amount:{/tr} <span id='Amount'></span><br />
	{tr}Total:{/tr} <span id='Total'></span><br />

	{tr}Invoice Note{/tr}
	<br />
	<textarea name="InvoiceNote" id="InvoiceNote">{$invoice.$InvoiceNote}</textarea>
	<br />
	<input type="submit" value="{tr}Save Invoice{/tr}" name="submit" />
</form>