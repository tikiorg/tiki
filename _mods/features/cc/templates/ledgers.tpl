<h1><a href="cc.php?page=ledgers{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}" class="pagetitle">{tr}Main Ledger{/tr}</a>
{if $userid} for {$userid}{/if}
{if $ccid}in <b>{$ccid}</b>{/if}
</h1>
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span><br />

<br /><br />

{if $msg}<div class="simplebox">{$msg}</div>{/if}

<table class="normal">
<tr class="heading">
<th align=left><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=last_tr_date_{if $sort_mode eq 'last_tr_date_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}last transaction date{/tr}</a></th>
<th align=left><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=acct_id_{if $sort_mode eq 'acct_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}account{/tr}</a></th>
<th align=left><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=cc_id_{if $sort_mode eq 'cc_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}cc{/tr}</a></th>
<th align=right><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=balance_{if $sort_mode eq 'balance_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}balance{/tr}</a></th>
<th align=right><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=tr_total_{if $sort_mode eq 'tr_total_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}total volume{/tr}</a></th>
<th align=center><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=tr_count_{if $sort_mode eq 'tr_count_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}transactions{/tr}</a></th>
</tr>

{cycle values="odd,even" print=false}
{section name=i loop=$thelist}
<tr class="{cycle}">
<td title="{$thelist[i].age|duration} ago">{$thelist[i].last_tr_date|date_format:"%d/%m"}</td>
<td>{$thelist[i].acct_id|userlink}</td>
<td>{$thelist[i].cc_id}</td>
<td align=right>{$thelist[i].balance}</td>
<td align=right>{$thelist[i].tr_total}</td>
<td align=center>{$thelist[i].tr_count}</td>
</tr>
{sectionelse}
<td colspan="7">No entry</td>
{/section}
</table>

<br /><br />

