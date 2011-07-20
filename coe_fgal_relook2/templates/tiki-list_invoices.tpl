{title help="Invoice"}{tr}Invoices{/tr}{/title}

{include file='tiki-invoice_menu.tpl'}

{* vars to deal with keys that have spaces *}
{assign var=clientId value="Client Id"}
{assign var=itemAmounts value="Item Amounts"}
{assign var=workDescription value="Work Description"}
{assign var=invoiceNumber value="Invoice Number"}
{assign var=dateIssued value="Date Issued"}
{assign var=invoiceNote value="Invoice Note"}
{assign var=invoiceId value="Invoice Id"}

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
			<td><a href='tiki-view_invoice.php?invoice={$invoice.$invoiceId}'>{$invoice.$invoiceNumber}</a></td>
			<td><a href='tiki-view_invoice.php?invoice={$invoice.$invoiceId}'>{$invoice.$dateIssued}</a></td>
			<td><a href='tiki-view_invoice_client.php?invoice={$invoice.$clientId}'>{$invoice.$clientName}</a></td>
			<td><a href='tiki-view_invoice.php?invoice={$invoice.$invoiceId}'>{$invoice.Amount}</a></td>
			<td><a href='tiki-view_invoice.php?invoice={$invoice.$invoiceId}'>{$invoice.Status}</a></td>
		</tr>
	{/foreach}

</table>