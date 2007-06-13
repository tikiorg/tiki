<h1><a href="tiki-tinvoice_list.php" class="pagetitle">{tr}Invoices{/tr}</a></h1>
<h2><a href="tiki-tinvoice_edit.php" class="link">{tr}add invoice{/tr}</a></h2>
<a href='tiki-tinvoice_list.php?id_emitter={$me_tikiid}'>Factures Clients</a>&nbsp;&nbsp;&nbsp;
<a href='tiki-tinvoice_list.php?id_receiver={$me_tikiid}'>Factures Fournisseurs</a>&nbsp;&nbsp;&nbsp;

Client : 
<select>
{foreach from=$contacts item=contact}
<option disabled selected>Choisir...</option>
<option>{$contact.firstName|escape} {$contact.lastName|escape}</option>
{/foreach}
</select>

Fournisseur:
<select>
{foreach from=$contacts item=contact}
<option disabled selected>Choisir...</option>
<option>{$contact.firstName|escape} {$contact.lastName|escape}</option>
{/foreach}
</select>

<hr />
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_lists.php">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_lists.php">{tr}Libelle{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_lists.php">{tr}Ref{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_lists.php">{tr}amount{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_lists.php">{tr}paid{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$invoices item=invoice}
<tr>
<td class="{cycle advance=false}">{$invoice->get_date()}</td>
<td class="{cycle advance=false}">{$invoice->get_libelle()}</td>
<td class="{cycle advance=false}">{$invoice->get_ref()}</td>
<td class="{cycle advance=false}">{$invoice->get_amount()}</td>
<td class="{cycle advance=false}">{$invoice->get_paid()}</td>
<td class="{cycle advance=false}">&nbsp;
<a href="tiki-tinvoice_edit.php?invoiceId={$invoice->get_id()}&amp;pdf=1" title="{tr}view invoices{/tr}"><img src="pics/icons/table.png" border="0" height="16" width="16" alt='{tr}Contact Invoices{/tr}' /></a>
<a href="tiki-tinvoice_edit.php?invoiceId={$invoice->get_id()}"><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a>
<a href="tiki-tinvoice_list.php?delete={$invoice->get_id()}" style="margin-left:20px;"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
</td>
</tr>
{/foreach}
</table>

yo
