{title help="Invoice"}{tr}Invoices{/tr}{/title}

{include file='tiki-invoice_menu.tpl'}

{* vars to deal with keys that have spaces *}
{assign var=ClientId value="Client Id"}
{assign var=ItemAmounts value="Item Amounts"}
{assign var=WorkDescription value="Work Description"}
{assign var=InvoiceNumber value="Invoice Number"}
{assign var=DateIssued value="Date Issued"}
{assign var=InvoiceNote value="Invoice Note"}
{assign var=InvoiceId value="Invoice Id"}

<table>
	<tr>
		<td>{tr}Invoice{/tr}</td>
		<td>{tr}Date Issued{/tr}</td>
		<td>{tr}Client Name{/tr}</td>
		<td>{tr}Amount{/tr}</td>
		<td>{tr}Status{/tr}</td>
	</tr>

	{foreach from=$invoices key=k item=invoice}
		<tr>
			<td><a href='tiki-view_invoice.php?InvoiceId={$invoice.$InvoiceId}'>{$invoice.$InvoiceNumber}</a></td>
			<td><a href='tiki-view_invoice.php?InvoiceId={$invoice.$InvoiceId}'>{$invoice.$DateIssued}</a></td>
			<td><a href='tiki-view_invoice_client.php?invoice={$invoice.$ClientId}'>{$invoice.$ClientName}</a></td>
			<td><a href='tiki-view_invoice.php?InvoiceId={$invoice.$InvoiceId}'>{$invoice.Amount}</a></td>
			<td><a href='tiki-view_invoice.php?InvoiceId={$invoice.$InvoiceId}'>{$invoice.Status}</a></td>
		</tr>
	{/foreach}

</table>