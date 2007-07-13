<h1><a href="tiki-tinvoice_list.php" class="pagetitle">{tr}Invoices{/tr}</a></h1>
<div class="navbar">
{if $tiki_p_tinvoice_edit eq 'y'}<a class="linkbut" href="tiki-tinvoice_edit.php?id_emitter={$me_tikiid}">{tr}new invoice{/tr}</a>&nbsp;{/if}
<a class="linkbut" href="tiki-tinvoice_prefs.php">{tr}preferences{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_graph.php">{tr}Graphs{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_transactions.php">{tr}Transactions{/tr}</a>
</div>
<div style="width: 500px; float: left;" class="button2">
<a class="linkbut" href='tiki-tinvoice_list.php?id_emitter={$me_tikiid}'>{tr}clients invoices{/tr}</a>&nbsp;
<a class="linkbut" href='tiki-tinvoice_list.php?id_receiver={$me_tikiid}'>{tr} supplier invoices{/tr}</a>&nbsp;
<a class="linkbut" href='tiki-tinvoice_payment.php?id_receiver={$me_tikiid}'>{tr}payments {/tr}</a>&nbsp;
<a class="linkbut" href='tiki-tinvoice_expenses.php?id_receiver={$me_tikiid}'>{tr}expenses{/tr}</a>
</div>
<br />
<br />

{tr}Client {/tr}: 
<select>
<option disabled selected>Choisir...</option>
{foreach from=$contacts item=contact}
<option>{$contact.firstName|escape} {$contact.lastName|escape}</option>
{/foreach}
</select>

{tr}Supplier{/tr} :
<select>
<option disabled selected>Choisir...</option>
{foreach from=$contacts item=contact}
<option>{$contact.firstName|escape} {$contact.lastName|escape}</option>
{/foreach}
</select>

<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Libelle{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Ref{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}amount{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}vat{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}status{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$invoices item=invoice}
<tr>
<td class="{cycle advance=false}">{$invoice->get_date()}</td>
<td class="{cycle advance=false}">{$invoice->get_libelle()}</td>
<td class="{cycle advance=false}">{$invoice->get_ref()}</td>
<td class="{cycle advance=false}">{$invoice->get_amount()}</td>
<td class="{cycle advance=false}">{$invoice->get_amount_vat()}</td>
<td class="{cycle advance=false}">{$invoice->get_status()}</td>
<td class="{cycle advance=false}">&nbsp;
<a href="tiki-tinvoice_edit.php?invoiceId={$invoice->get_id()}&amp;pdf=1" title="{tr}view invoices{/tr}"><img src="pics/icons/table.png" border="0" height="16" width="16" alt='{tr}Contact Invoices{/tr}' /></a>
<a href="tiki-tinvoice_edit.php?invoiceId={$invoice->get_id()}"><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a>
<a href="tiki-tinvoice_list.php?delete={$invoice->get_id()}" style="margin-left:20px;"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
</td>
</tr>
{/foreach}
</table>

yo
