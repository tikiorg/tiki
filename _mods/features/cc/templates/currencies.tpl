<h1><a href="cc.php?page=currencies{if $userid}&amp;user={$userid}{/if}" class="pagetitle">List of currencies</a></h1>
<br />
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span>

{if $tiki_p_cc_create eq 'y' or $tiki_p_cc_admin eq 'y'}
<span class="button2"><a href="cc.php?page=currencies&amp;new=1" class="linkbut">Create new currency</a></span>
<br /><br />
{/if}

{if $msg}<div class="simplebox">{$msg}</div>{/if}

{if $view eq 'my'}
{assign var=focus value='my'}
{elseif $view eq 'reg'}
{assign var=focus value='reg'}
{else}
{assign var=focus value='all'}
{/if}

<div>
<span class="button2"><a href="cc.php?page=currencies&amp;all=1" class="linkbut{if $focus eq 'all'} highlight{/if}">All Currencies</a></span>
{if $tiki_p_cc_use eq 'y'}
<span class="button2"><a href="cc.php?page=currencies&amp;reg=1" class="linkbut{if $focus eq 'reg'} highlight{/if}">Registered Currencies</a></span>
{/if}
{if $tiki_p_cc_use eq 'y'}
<span class="button2"><a href="cc.php?page=currencies&amp;my=1" class="linkbut{if $focus eq 'my'} highlight{/if}">Owned Currencies</a></span>
{/if}
</div>

<div class="simplebox">
<form method="post" action="cc.php">
Register an existing unlisted currency
<input type="hidden" name="page" value="currencies" />
<input type="text" name="register" value="" />
<input type="submit" name="act" value="{tr}Register{/tr}" />
</form>
</div>

<table class="normal">
<tr class="heading">
<th>&nbsp;</th>
<th align="left"><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=id_{if $smarty.request.sort_mode eq 'id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Id{/tr}</a></th>
<th align="left"><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=cc_name_{if $smarty.request.sort_mode eq 'cc_name_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Name{/tr}</a></th>
<th align="left"><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=cc_description_{if $smarty.request.sort_mode eq 'cc_description_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Description{/tr}</a></th>
<th align="left"><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=owner_id_{if $smarty.request.sort_mode eq 'owner_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Owner{/tr}</a></th>
<th align="center"><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=requires_approval_{if $smarty.request.sort_mode eq 'requires_approval_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Approval?{/tr}</a></th>
<th align="center"><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=listed_{if $smarty.request.sort_mode eq 'listed_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Listed?{/tr}</a></th>
<th align="right"><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=population_{if $smarty.request.sort_mode eq 'population_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Population{/tr}</a></th>
<th align="right" width="60">{tr}Action{/tr}</th>
</tr>

{foreach key=ccid item=it from=$thelist}
<tr class="even"
{if $ccuser.registered_cc.$ccid.approved eq 'y'}
style="background-color : #cfc;"
{elseif $ccuser.registered_cc.$ccid.approved eq 'n'}
style="background-color : #fec;"
{elseif $ccuser.registered_cc.$ccid.approved eq 'c'}
style="background-color : #ddf;"
{else}
style="background-color : #eee;"
{/if}>
<td>
{if $ccuser.registered_cc.$ccid.approved eq 'y'}
<a href="cc.php?page=transactions&amp;currency={$it.id}&amp;new=1" title="{tr}Transaction{/tr}"><img src="img/cc/transaction.png" width="20" height="12" border="0" alt="{tr}Transaction{/tr}" /></a>
{else}&nbsp;{/if}
</td>
<td>{$it.id}</td>
<td><a href="cc.php?page=currencies&amp;cc_id={$it.id}&view=1">
<img src="img/cc/currency.png" width="9" height="10" border="0" alt="{tr}Examine{/tr}" />
<b>{$it.cc_name}</b></a></td>
<td>{$it.cc_description}</td>
<td>{$it.owner_id|userlink}</td>
<td align="center">{$it.requires_approval}</td>
<td align="center">{$it.listed}</td>
<td align="center">{$it.population}</td>
<td align="right">
{if $ccuser.registered_cc.$ccid.approved eq 'y'}
<a href="cc.php?page=currencies&amp;unregister={$it.id}" title="{tr}Unregister{/tr}"><img src="img/cc/unregister_c.png" width="20" height="12" border="0" alt="{tr}Unregister{/tr}" /></a>
{elseif $ccuser.registered_cc.$ccid.approved eq 'c'}
<a href="cc.php?page=currencies&amp;register={$it.id}" title="{tr}Reregister{/tr}"><img src="img/cc/register_c.png" width="20" height="12" border="0" alt="{tr}Reregister{/tr}" /></a>
{elseif $ccuser.registered_cc.$ccid.approved eq 'n'}
<img src="img/cc/pending_register_c.png" width="20" height="12" border="0" alt="{tr}Pending Registration{/tr}" />
{else}
<a href="cc.php?page=currencies&amp;register={$it.id}" title="{tr}Register{/tr}"><img src="img/cc/register_c.png" width="20" height="12" border="0" alt="{tr}Register{/tr}" /></a>
{/if}
{if $tiki_p_cc_admin eq 'y' or $it.owner_id eq $user}
<a href="cc.php?page=currencies&amp;cc_id={$it.id}" title="{tr}Edit{/tr}"><img src="img/cc/edit_c.png" width="20" height="12" border="0" alt="{tr}Edit{/tr}" /></a>
{/if}

</td>
</tr>
{foreachelse}
<td colspan="7">No entry</td>
{/foreach}
</table>

<br /><br />
