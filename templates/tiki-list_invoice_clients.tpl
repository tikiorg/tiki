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
{assign var=ContactIds value="Contact Ids"}
{assign var=ContactFirstNames value="Contact First Names"}
{assign var=ContactLastNames value="Contact Last Names"}
{assign var=ContactEmails value="Contact Emails"}
{assign var=ContactTitles value="Contact Titles"}

{foreach from=$Clients item=Client}
	<div>
		<h3 class='ClientName'>{$Client.Name}</h3>
		<div class='ClientDetails' style='display: none;'>
			<table style='width: 100%;'>
				<tr>
					<td style='width: 50%;'>
						{if $Client.$Address1}{$Client.$Address1}<br />{/if}
						{if $Client.$Address2}{$Client.$Address2}<br />{/if}
						{if $Client.City && $Client.Province && $Client.$PostalCode}{$Client.City} {$Client.Province}, {$Client.$PostalCode}<br />{/if}
						{if $Client.Website}<a href='{$Client.Website}'>{$Client.Website}</a><br />{/if}
						{tr}Tax Status:{/tr} {if $Client.$TaxStatus eq 'y'}{tr}Taxable{/tr}{else}Not Taxable{/if}<br />
						
						{button href="tiki-invoice_edit_client_contact.php?contact=$contactId" _text="{tr}View{/tr}"}
						{button href="tiki-invoice_edit_client_contact.php?contact=$contactId" _text="{tr}Edit{/tr}"}
						{button href="tiki-invoice_edit_client_contact.php?contact=$contactId" _text="{tr}Delete{/tr}"}
					</td>
					<td style='width: 50%;'>
						{button href="tiki-invoice_edit_client_contact.php" _text="{tr}New Contact{/tr}"}<br />
						{foreach from=$Client.$ContactIds key=k item=contactId}
							{if $contactId}
								{$Client.$ContactTitles[$k]} {$Client.$ContactFirstNames[$k]} {$Client.$ContactLastNames[$k]}<br />
								{$Client.$ContactEmails[$k]}
								
								<br />
								{button href="tiki-invoice_edit_client_contact.php?contact=$contactId" _text="{tr}View{/tr}"}
								{button href="tiki-invoice_edit_client_contact.php?contact=$contactId" _text="{tr}Edit{/tr}"}
								{button href="tiki-invoice_edit_client_contact.php?contact=$contactId" _text="{tr}Delete{/tr}"}
								<hr />
							{/if}
						{/foreach}
					</td>
				</tr>
			</table>
		</div>
	</div>
{/foreach}