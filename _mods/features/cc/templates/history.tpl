<a href="cc.php?page=app{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}" class="pagetitle">{tr}Transactions history{/tr}</a>
<br />
{if $userid}... for <b>{$userid}</b> {/if}
{if $ccid}in <b>{$ccid}</b>{/if}
<br />

<table class="normal">
<tr class="heading">
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=tr_date_{if $sort_mode eq 'tr_date_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Date{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=acct_id_{if $sort_mode eq 'acct_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}User{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=other_id_{if $sort_mode eq 'other_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Peer{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=cc_id_{if $sort_mode eq 'cc_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Currency{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=amount_{if $sort_mode eq 'amount_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Amount{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=balance_{if $sort_mode eq 'balance_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Balance{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=app&amp;sort_mode=item_{if $sort_mode eq 'item_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Label{/tr}</a></th>
</tr>

{cycle values="odd,even" print=false}
{section name=i loop=$thelist}
<tr class="{cycle}">
<td>{$thelist[i].tr_date}</td>
<td>{$thelist[i].acct_id|userlink}</td>
<td>{$thelist[i].other_id|userlink}</td>
<td>{$thelist[i].cc_id}</td>
<td>{$thelist[i].amount}</td>
<td>{$thelist[i].balance}</td>
<td>{$thelist[i].item}</td>
</tr>
{sectionelse}
<td colspan="7">No entry</td>
{/section}
</table>

<br /><br />

