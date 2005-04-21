<a href="cc.php?page=currencies&amp;cc_id={$info.id}&amp;view=1" class="pagetitle">{tr}Examine Currency{/tr} {$info.id}</a>
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span><br />
<br /><br />

<span class="button2"><a href="cc.php?page=currencies" class="linkbut">{tr}List Currencies{/tr}</a></span>
<span class="button2"><a href="cc.php?page=currencies&amp;my=1" class="linkbut">{tr}My Currencies{/tr}</a></span>
<span class="button2"><a href="cc.php?page=currencies&amp;cc_id={$info.id}" class="linkbut">{tr}Edit currency{/tr} {$info.id}</a></span>
<br /><br />

{if $msg}<div class="simplebox">{$msg}</div>{/if}

<table class="formcolor">
<tr class="formrow"><td>Id</td><td>{$info.id}</td></tr>
<tr class="formrow"><td>{tr}Name{/tr}</td><td>{$info.cc_name}</td></tr>
<tr class="formrow"><td>{tr}Description{/tr}</td><td>{$info.cc_description}</td></tr>
<tr class="formrow"><td>{tr}Requires approval{/tr}</td><td>{$info.requires_approval}</td></tr>
<tr class="formrow"><td>{tr}Listed publicly{/tr}</td><td>{$info.listed}</td></tr>
<tr class="formrow"><td>{tr}Owner{/tr}</td><td>{$info.owner_id|userlink}</td></tr>
</table>

<br /><br />

{if $population}
<table class="normal">
<tr>
<th>{tr}Name{/tr}</th>
<th>{tr}Balance{/tr}</th>
<th>{tr}Amount of transactions{/tr}</th>
<th>{tr}Number of transactions{/tr}</th>
<th>{tr}Last transaction{/tr}</th>
<th>{tr}Approved?{/tr}</th>
<th>{tr}Actions{/tr}</th>
</tr>
{section name=i loop=$population}
<tr{if $population[i].approved eq 'y'}style="background-color:#ffcc99;"{/if}>
<td>{$population[i].acct_id|userlink}</td>
<td>{$population[i].balance}</td>
<td>{$population[i].tr_total}</td>
<td>{$population[i].tr_count}</td>
<td title="{$population[i].age|duration}">{$population[i].last_tr_date|date_format:"%Y/%m/%d"}</td>
<td>{$population[i].approved}</td>
<td>
{if $population[i].approved eq 'y'}
<a href="cc.php?page=currencies&amp;cc_id={$info.id}&amp;view=1&amp;who={$population[i].acct_id|escape:'url'}&amp;app=n">Unapprove</a>
<a href="cc.php?page=currencies&amp;cc_id={$info.id}&amp;view=1&amp;who={$population[i].acct_id|escape:'url'}&amp;app=c">Close</a>
{else}
<a href="cc.php?page=currencies&amp;cc_id={$info.id}&amp;view=1&amp;who={$population[i].acct_id|escape:'url'}&amp;app=y">Approve</a>
{/if}
</td>
</tr>
{/section}
</table>
{/if}

