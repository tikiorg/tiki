<h1><a href="cc.php?page=transactions{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}" class="pagetitle">Transactions </a>
{if $userid}for ($userid}{/if}
{if $ccid}in <b>{$ccid}</b>{/if}
</h1>
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span><br />

<br /><br />

{if $msg}<div class="simplebox">{$msg}</div>{/if}

<table class="normal">
<tr class="heading">
<th align=left><a class="tableheading" href="cc.php?page=transactions&amp;sort_mode=tr_date_{if $sort_mode eq 'tr_date_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Date{/tr}</a></th>
<th align=left><a class="tableheading" href="cc.php?page=transactions&amp;sort_mode=item_{if $sort_mode eq 'item_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Label{/tr}</a></th>
<th align=left><a class="tableheading" href="cc.php?page=transactions&amp;sort_mode=acct_id_{if $sort_mode eq 'acct_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}User{/tr}</a></th>
<th align=left><a class="tableheading" href="cc.php?page=transactions&amp;sort_mode=other_id_{if $sort_mode eq 'other_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Peer{/tr}</a></th>
<th align=right><a class="tableheading" href="cc.php?page=transactions&amp;sort_mode=amount_{if $sort_mode eq 'amount_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if} align=left">{tr}Amount{/tr}</a></th>
<th align=right><a class="tableheading" href="cc.php?page=transactions&amp;sort_mode=cc_id_{if $sort_mode eq 'cc_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Currency{/tr}</a></th>
<th align=right><a class="tableheading" href="cc.php?page=transactions&amp;sort_mode=balance_{if $sort_mode eq 'balance_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}{if $ccid}&amp;cc={$ccid}{/if}">{tr}Balance{/tr}</a></th>
</tr>

{cycle values="odd,even" print=false}
{section name=i loop=$thelist}
<tr class="{cycle}">
<td title="{$thelist[i].tr_date|duration}">{$thelist[i].tr_date|date_format:"%d/%m/%Y"}</td>
<td>{$thelist[i].item}</td>
<td>{$thelist[i].acct_id|userlink}</td>
<td>{$thelist[i].other_id|userlink}</td>
<td align=right>{$thelist[i].amount}</td>
<td align=right>{$thelist[i].cc_id}</td>
<td align=right>{$thelist[i].balance}</td>
</tr>
{sectionelse}
<td colspan="7">No entry</td>
{/section}
</table>

<br /><br />

