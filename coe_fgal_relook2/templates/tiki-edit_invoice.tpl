{title help="Invoice"}{tr}Edit Invoice{/tr}{/title}

{include file='tiki-invoice_menu.tpl'}

{* vars to deal with keys that have spaces *}
{assign var=clientId value="Client Id"}
{assign var=workDescription value="Work Description"}
{assign var=invoiceNumber value="Invoice Number"}
{assign var=dateIssued value="Date Issued"}
{assign var=invoiceNote value="Invoice Note"}

Client <select>
	{foreach from=$clients key=k item=client}
		<option value='{$client.$clientId}' {if $client.$clientId eq $invoice.$clientId} selected='true' {/if}>{$client.Name}</option>
	{/foreach}
</select>

<table class='invoiceDetails'>
	<tr>
		<td>
			Invoice Number <input name='invoiceNumber' id='invoiceNumber' type='text' value='{$invoice.$invoiceNumber}' />
			<br />
			Date Issued <input name='dateIssued' id='dateIssued' class='invoiceDate' value='{$invoice.$dateIssued}' />
		</td>
	</tr>
</table>
<hr />

<table class='invoiceItems'>
	<tr>
		<th>{tr}Quantity{/tr}</th>
		<th>{tr}Work Description{/tr}</th>
		<th>{tr}Taxable{/tr}</th>
		<th>{tr}Amount{/tr}</th>
	</tr>

	{foreach from=$invoiceItems item=invoiceItem}
		<tr>
			<td><input name='quantity' class='quantity' type='text' value='{$invoiceItem.Quantity}' /></td>
			<td><teaxtarea>{$invoiceItem.$workDescription}</teaxtarea></td>
			<td><input name='taxable' class='taxable' type='checkbox' value='y' {if $invoiceItem.Taxable eq 'y'} checked='true' {/if} /></td>
			<td><input name='amount' class='quantity' type='text' value='{$invoiceItem.Amount}' /></td>
		</tr>
	{/foreach}

	<tr>
		<td colspan='4'>
		<input type='button' value='New Item' class='invoiceNewItem' />
		</td>
	</tr>

</table>

Amount: <span id='amount'></span><br />
Total: <span id='total'></span><br />

Invoice Note
<br />
<textarea>{$invoice.$invoiceNote}</textarea>