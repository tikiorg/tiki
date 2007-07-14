<h1>{tr}My Transactions{/tr}</h1>
<div class="navbar">
{if $tiki_p_tinvoice_edit eq 'y'}<a class="linkbut" href="tiki-tinvoice_transaction_edit.php">{tr}new transaction{/tr}</a>&nbsp;{/if}
<a class="linkbut" href="tiki-tinvoice_prefs.php">{tr}preferences{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_graph.php">{tr}Graphs{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_banks.php">{tr}Banks{/tr}</a>
</div>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}account name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}ref{/tr}</a></td>
<td class="heading" align="right"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}debit{/tr}</a></td>
<td class="heading" align="right"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}credit{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}status{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=op loop=$transactions}
<tr>
<td class="{cycle advance=false}">{$transactions[op].date}</td>
<td class="{cycle advance=false}">{$transactions[op].bankId}</td>
<td class="{cycle advance=false}">{$transactions[op].label}</td>
<td class="{cycle advance=false}" align="right">{$transactions[op].debit}</td>
<td class="{cycle advance=false}" align="right">{$transactions[op].credit}</td>
<td class="{cycle advance=false}" align="right">{$transactions[op].status}</td>
<td><a href="tiki-tinvoice_transaction_edit.php?tId={$transactions[op].id}"><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a>
<a href="tiki-tinvoice_transaction_edit.php?drop={$transactions[op].id}" style="margin-left:20px;"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
</td>
</tr>
{/section}
</table>

