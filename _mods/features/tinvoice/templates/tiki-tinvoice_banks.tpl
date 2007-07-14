<h1>{tr}Banks{/tr}</h1>
<div class="navbar">
{if $tiki_p_tinvoice_edit eq 'y'}<a class="linkbut" href="tiki-tinvoice_transaction_edit.php?id_emitter={$me_tikiid}">{tr}new transaction{/tr}</a>&nbsp;{/if}
<a class="linkbut" href="tiki-tinvoice_prefs.php">{tr}preferences{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_graph.php">{tr}Graphs{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_transactions.php">{tr}Transactions{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_bank_edit.php">{tr}New bank account{/tr}</a>
</div>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Account Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Bank{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-tinvoice_list.php">{tr}Account{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=b loop=$banks}
<tr>
<td class="{cycle advance=false}">{$banks[b].name}</td>
<td class="{cycle advance=false}">{$banks[b].bank}</td>
<td class="{cycle advance=false}">{$banks[b].account_nb}</td>
<td><a href="tiki-tinvoice_bank_edit.php?bankId={$banks[b].id}"><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a>
<a href="tiki-tinvoice_bank_edit.php?drop={$banks[b].id}" style="margin-left:20px;"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
</td>
</tr>
{/section}
</table>

