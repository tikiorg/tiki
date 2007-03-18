<h1><a href="tiki-tinvoice_list.php" class="pagetitle">{tr}Invoices{/tr}</a></h1>
<div class="navbar">
{if $tiki_p_admin eq 'y'}<a class="linkbut" href="tiki-tinvoice_edit.php">{tr}create new invoice{/tr}</a>&nbsp;{/if}
<a class="linkbut" href="tiki-tinvoice_prefs.php">{tr}Invoices preferences{/tr}</a>
</div>
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

{include file=tiki-tinvoice_graph.tpl}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Libelle{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Ref{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}amount{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}paid{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$invoices item=invoice}
<tr>
<td class="{cycle advance=false}">{$invoice->get_date()|tiki_short_date}</td>
<td class="{cycle advance=false}">{$invoice->get_libelle()|escape}</td>
<td class="{cycle advance=false}">{$invoice->get_ref()|escape}</td>
<td class="{cycle advance=false}">{$invoice->get_amount()|escape}</td>
<td class="{cycle advance=false}">{$invoice->get_paid()|escape}</td>
<td class="{cycle advance=false}">&nbsp;
<a href="tiki-tinvoice_edit.php?invoiceId={$invoice->get_id()}&amp;pdf=1" title="{tr}view invoices{/tr}"><img src="pics/icons/table.png" border="0" height="16" width="16" alt='{tr}Contact Invoices{/tr}' /></a>
<a href="tiki-tinvoice_edit.php?invoiceId={$invoice->get_id()}"><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a>
<a href="tiki-tinvoice_list.php?delete={$invoice->get_id()}" style="margin-left:20px;"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
</td>
</tr>
{/foreach}
</table>
