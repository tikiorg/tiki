{title help="Invoice"}{tr}Invoice Clients{/tr}{/title}

{include file='tiki-invoice_menu.tpl'}

{* vars to deal with keys that have spaces *}
{assign var=ClientId value="Client Id"}
{assign var=Address1 value="Address 1"}
{assign var=Address2 value="Address 2"}
{assign var=PostalCode value="Postal Code"}
{assign var=TaxStatus value="Tax Status"}
{assign var=ClientNotes value="Client Notes"}
{assign var=TaxCode value="Tax Code"}

{foreach from=$Clients item=Client}
	<div>
		<h3 class='ClientName'>{$Client.Name}</h3>
		<div class='ClientDetails' style='display: none;'>
			<table>
				<tr>
					<td>
						{if $Client.$Address1}{$Client.$Address1}<br />{/if}
						{if $Client.$Address2}{$Client.$Address2}<br />{/if}
						{if $Client.City && $Client.Province && $Client.$PostalCode}{$Client.City} {$Client.Province}, {$Client.$PostalCode}<br />{/if}
						{if $Client.Website}<a href='{$Client.Website}'>{$Client.Website}</a><br />{/if}
						{tr}Tax Status:{/tr} {if $Client.$TaxStatus eq 'y'}{tr}Taxable{/tr}{else}Not Taxable{/if}<br />
					</td>
					<td>
						
					</td>
				</tr>
			</table>
		</div>
	</div>
{/foreach}