<h1><a href="cc.php?page=currencies{if $userid}&amp;user={$userid}{/if}" class="pagetitle">List of currencies</a></h1>
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span><br />

<span class="button2"><a href="cc.php?page=currencies" class="linkbut">All Currencies</a></span>
{if $tiki_p_cc_use eq 'y'}
<span class="button2"><a href="cc.php?page=currencies&amp;my" class="linkbut">Owned Currencies</a></span>
<span class="button2"><a href="cc.php?page=currencies&amp;reg" class="linkbut">Registered Currencies</a></span>
{/if}
{if $tiki_p_cc_create eq 'y' or $tiki_p_cc_admin eq 'y'}
<span class="button2"><a href="cc.php?page=currencies&amp;new" class="linkbut">Create new currency</a></span>
<br /><br />
{/if}

{if $msg}<div class="simplebox">{$msg}</div>{/if}

{if $view eq 'my'}
<h2>Owned Currencies</h2>
{elseif $view eq 'reg'}
<h2>Registered Currencies</h2>
{else}
<h2>All Currencies</h2>
{/if}

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
<th colspan="2">&nbsp;</th>
<th><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=id_{if $sort_mode eq 'id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Id{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=cc_name_{if $sort_mode eq 'cc_name_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Name{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=cc_description_{if $sort_mode eq 'cc_description_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Description{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=owner_id_{if $sort_mode eq 'owner_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Owner{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=requires_approval_{if $sort_mode eq 'requires_approval_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Approval?{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=currencies&amp;sort_mode=listed_{if $sort_mode eq 'listed_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Listed?{/tr}</a></th>
</tr>

{cycle values="odd,even" print=false}
{foreach key=ccid item=it from=$thelist}
<tr class="{cycle}">
{if $ccuser.registered_cc.$ccid.approved eq 'y'}
<td class="highlight">
<a href="cc.php?page=currencies&amp;unregister={$it.id}">{tr}Unregister{/tr}</a>
<a href="cc.php?page=transactions&amp;currency={$it.id}&amp;new">{tr}Transaction{/tr}</a>
</td>
{elseif $ccuser.registered_cc.$ccid.approved eq 'c'}
<td><a href="cc.php?page=currencies&amp;register={$it.id}">{tr}Reregister{/tr}</a></td>
{else}
<td><a href="cc.php?page=currencies&amp;register={$it.id}">{tr}Register{/tr}</a></td>
{/if}
{if $tiki_p_cc_admin eq 'y' or $it.owner_id eq $user}
<td><a href="cc.php?page=currencies&amp;cc_id={$it.id}">{tr}Edit{/tr}</a></td>
{else}
<td>&nbsp;</td>
{/if}
<td>{$it.id}</td>
<td>{$it.cc_name}</td>
<td>{$it.cc_description}</td>
<td>{$it.owner_id|userlink}</td>
<td>{$it.requires_approval}</td>
<td>{$it.listed}</td>
</tr>
{foreachelse}
<td colspan="7">No entry</td>
{/foreach}
</table>

<br /><br />
