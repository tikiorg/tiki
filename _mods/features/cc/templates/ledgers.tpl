<h1><a href="cc.php?page=ledgers{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}" class="pagetitle">{tr}Main Ledger{/tr}</a>
{if $userid} for {$userid}{/if}
{if $ccid}in <b><a href="cc.php?page=currencies&amp;cc_id={$ccid|escape:'url'}&amp;view=1">{$ccid}</a></b>{/if}
</h1>
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span>
{if $ccid}
<span class="button2"><a href="cc.php?page=transactions&amp;cc_id={$ccid|escape:'url'}&amp;new=1" class="linkbut">{tr}New Transaction with{/tr} {$ccid}</a></span>
{/if}
<br />

<br /><br />

{if $msg}<div class="simplebox">{$msg}</div>{/if}

<table class="normal">
<tr class="heading">
<th align=left><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=last_tr_date_{if $smarty.request.sort_mode eq 'last_tr_date_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}last transaction date{/tr}</a></th>
<th align=left><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=acct_id_{if $smarty.request.sort_mode eq 'acct_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}account{/tr}</a></th>
{if !$ccid}
<th align=left><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=cc_id_{if $smarty.request.sort_mode eq 'cc_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}cc{/tr}</a></th>
{/if}
<th align=right><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=balance_{if $smarty.request.sort_mode eq 'balance_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}balance{/tr}</a></th>
<th align=right><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=tr_total_{if $smarty.request.sort_mode eq 'tr_total_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}total volume{/tr}</a></th>
<th align=center><a class="tableheading" href="cc.php?page=ledgers&amp;sort_mode=tr_count_{if $smarty.request.sort_mode eq 'tr_count_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}transactions{/tr}</a></th>
</tr>

{cycle values="odd,even" print=false}
{section name=i loop=$thelist}
<tr class="{cycle}">
<td title="{$thelist[i].age|duration} ago">{$thelist[i].last_tr_date|date_format:"%d/%m"}</td>
<td>{$thelist[i].acct_id|userlink}</td>
{if !$ccid}
<td><a href="cc.php?page=currencies&amp;cc_id={$thelist[i].cc_id|escape:'url'}&amp;view=1">
<img src="img/cc/currency.png" width="9" height="10" border="0" alt="{tr}Examine{/tr}" />
<b>{$thelist[i].cc_id}</b></a></td>
{/if}
<td align=right>{$thelist[i].balance}</td>
<td align=right>{$thelist[i].tr_total}</td>
<td align=center>{$thelist[i].tr_count}</td>
</tr>
{sectionelse}
<td colspan="7">No entry</td>
{/section}
</table>

<br /><br />

