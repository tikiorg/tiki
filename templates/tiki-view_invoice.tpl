{title help="Invoice"}{tr}View Invoice{/tr}{/title}

{include file='tiki-invoice_menu.tpl'}

{* vars to deal with keys that have spaces *}
{assign var=invoiceNumber value="Invoice Number"}
{assign var=dateIssued value="Date Issued"}
{assign var=paymentTerm value="Payment Term"}
{assign var=companyName value="Company Name"}
{assign var=address1 value="Address 1"}
{assign var=address2 value="Address 2"}

<table style='width: 100%;'>
	<tr>
		<td>
			Invoice {$invoice.$invoiceNumber}
			<br />
			Date Issued {$invoice.$dateIssued}
			<br />
			<br />
			Status 
		</td>
		<td>
			{if $setting.Logo neq ''}<img src='{$setting.Logo}' /><br />{/if}
			{$setting.$companyName}<br />
			{$setting.$address1}<br />
			{if $setting.$address2 neq ''}{$setting.address2}<br />{/if}
			{if $setting.Website neq ''}<a href='{$setting.Website}'>{$setting.Website}</a><br />{/if}
		</td>
	</tr>
</table>
<hr />
<h2>{$client.Name}</h2>
<table class='invoiceItems'>
	<tr>
		<th>{tr}Quantity{/tr}</th>
		<th>{tr}Work Description{/tr}</th>
		<th>{tr}Amount{/tr}</th>
		<th>{tr}Total{/tr}</th>
	</tr>

{foreach from=$invoiceItems item=invoiceItem}
	<tr>
		<td>{$invoiceItem.Quantity}</td>
		<td>{$invoiceItem.$workDescription}</td>
		<td>{$invoiceItem.Amount}</td>
		<td>{$invoiceItem.Amount*$invoiceItem.Quantity}</td>
	</tr>
{/foreach}

</table>
Amount: {$amount}<br />
Total: {$amount}<br />
Payment Terms: {$invoice.$paymentTerm}
<br />
<br />
<a href="tiki-edit_invoice?InvoiceId={$InvoiceId}">{tr}Edit Invoice{/tr}</a>