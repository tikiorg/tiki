{title help="Invoice"}{tr}Edit Invoice{/tr}{/title}

{include file='tiki-invoice_menu.tpl'}

{* vars to deal with keys that have spaces *}
{assign var=clientId value="Client Id"}
{assign var=workDescription value="Work Description"}
{assign var=invoiceNumber value="Invoice Number"}
{assign var=dateIssued value="Date Issued"}
{assign var=invoiceNote value="Invoice Note"}

<form action="tiki-edit_invoice.php" method="post" id="InvoiceForm">
	{tr}Client{/tr} <select name="ClientId" class="InvoiceClientId">
		{foreach from=$clients key=k item=client}
			<option value='{$client.$clientId}' {if $client.$clientId eq $invoice.$clientId} selected='true' {/if}>{$client.Name}</option>
		{/foreach}
	</select>

	<table class='InvoiceDetails'>
		<tr>
			<td>
				{tr}Invoice Number{/tr} <input name='InvoiceNumber' id='InvoiceNumber' type='text' value='{$invoice.$invoiceNumber}' />
				<br />
				{tr}Date Issued{/tr} <input name='DateIssued' id='DateIssued' value='{$invoice.$dateIssued}' />
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
				<td><input name='Quantity[]' class='InvoiceQuantity' type='text' value='{$invoiceItem.Quantity}' /></td>
				<td><textarea name='Work Description[]' class='InvoiceWorkDescription'>{$invoiceItem.$workDescription}</textarea></td>
				<td><input name='Taxable[]' class='InvoiceTaxable' type='checkbox' value='y' {if $invoiceItem.Taxable eq 'y'} checked='true' {/if} /></td>
				<td><input name='Amount[]' class='InvoiceAmount' type='text' value='{$invoiceItem.Amount}' /></td>
				<td>
					<a href="#" class="DeleteItem">Delete</a>
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
	<textarea name="InvoiceNote" id="InvoiceNote">{$invoice.$invoiceNote}</textarea>
	<br />
	<input type="submit" value="{tr}Save Invoice{/tr}" name="submit" />
</form>