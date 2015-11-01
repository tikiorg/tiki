{title help="Invoice"}{tr}Invoices{/tr}{/title}

{include file='tiki-invoice_menu.tpl'}

{* vars to deal with keys that have spaces *}
{assign var=ClientId value="Client Id"}
{assign var=ClientName value="Client Name"}
{assign var=ItemAmounts value="Item Amounts"}
{assign var=WorkDescription value="Work Description"}
{assign var=InvoiceNumber value="Invoice Number"}
{assign var=DateIssued value="Date Issued"}
{assign var=InvoiceNote value="Invoice Note"}
{assign var=InvoiceId value="Invoice Id"}

<table border="0" style="width: 100%;">
	<tr>
		<th>{tr}Invoice{/tr}</th>
		<th>{tr}Date Issued{/tr}</th>
		<th>{tr}Client Name{/tr}</th>
		<th>{tr}Amount{/tr}</th>
		<th>{tr}Status{/tr}</th>
	</tr>

	{foreach from=$Invoices item=Invoice}
		<tr>
			<td><a href='tiki-view_invoice.php?InvoiceId={$Invoice.$InvoiceId}'>{$Invoice.$InvoiceNumber}</a></td>
			<td><a href='tiki-view_invoice.php?InvoiceId={$Invoice.$InvoiceId}'>{$Invoice.$DateIssued}</a></td>
			<td><a href='tiki-view_invoice_client.php?invoice={$Invoice.$ClientId}'>{$Invoice.$ClientName}</a></td>
			<td><a href='tiki-view_invoice.php?InvoiceId={$Invoice.$InvoiceId}'>{$Invoice.Amount}</a></td>
			<td><a href='tiki-view_invoice.php?InvoiceId={$Invoice.$InvoiceId}'>{$Invoice.Status}</a></td>
		</tr>
	{/foreach}

</table>