<a href="cc.php?page=app" class="pagetitle">Main Ledger</a>
{if $userid} for {$userid}{/if}
<br /><br />

<table class="normal">
<tr class="heading">
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=last_tr_date_{if $sort_mode eq 'last_tr_date_desc'}asc{else}desc{/if}">{tr}Last transaction Date{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=acct_id_{if $sort_mode eq 'acct_id_desc'}asc{else}desc{/if}">{tr}User{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=cc_id_{if $sort_mode eq 'cc_id_desc'}asc{else}desc{/if}">{tr}Currency{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=balance_{if $sort_mode eq 'balance_desc'}asc{else}desc{/if}">{tr}Balance{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=tr_total_{if $sort_mode eq 'tr_total_desc'}asc{else}desc{/if}">{tr}Total volume{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=tr_count_{if $sort_mode eq 'tr_count_desc'}asc{else}desc{/if}">{tr}Transactions{/tr}</a></th>
</tr>

{cycle values="odd,even" print=false}
{section name=i loop=$thelist}
<tr class="{cycle}">
<td>{$thelist[i].last_tr_date}</td>
<td>{$thelist[i].acct_id|userlink}</td>
<td>{$thelist[i].cc_id}</td>
<td>{$thelist[i].balance}</td>
<td>{$thelist[i].tr_total}</td>
<td>{$thelist[i].tr_count}</td>
</tr>
{sectionelse}
<td colspan="7">No entry</td>
{/section}
</table>

<br /><br />
<br /><br />

