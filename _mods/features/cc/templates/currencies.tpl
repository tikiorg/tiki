<a href="cc.php?page={$page}{if $userid}&amp;user={$userid}{/if}" class="pagetitle">List of currencies</a>
<br /><br />
{if $tiki_p_cc_create eq 'y' or $tiki_p_cc_admin eq 'y'}
<span class="button2"><a href="cc.php?page=currencies&amp;new" class="linkbut">Create new currency</a></span>
<br /><br />
{/if}

{if $msg}<div class="simplebox">{$msg}</div>{/if}

<table class="normal">
<tr class="heading">
<th>&nbsp;</th>
{if $tiki_p_cc_admin eq 'y'}
<th>&nbsp;</th>
{/if}
<th><a class="tableheading" href="cc.php?page={$page}&amp;sort_mode=id_{if $sort_mode eq 'id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Id{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page={$page}&amp;sort_mode=cc_name_{if $sort_mode eq 'cc_name_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Name{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page={$page}&amp;sort_mode=cc_description_{if $sort_mode eq 'cc_description_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Description{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page={$page}&amp;sort_mode=owner_id_{if $sort_mode eq 'owner_id_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Owner{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page={$page}&amp;sort_mode=requires_approval_{if $sort_mode eq 'requires_approval_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Approval?{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page={$page}&amp;sort_mode=listed_{if $sort_mode eq 'listed_desc'}asc{else}desc{/if}{if $userid}&amp;user={$userid}{/if}">{tr}Listed?{/tr}</a></th>
</tr>

{cycle values="odd,even" print=false}
{section name=i loop=$thelist}
<tr class="{cycle}">
{assign var=ccid value=$thelist[i].id}
{if $ccuser.registered_cc.$ccid.approved eq 'y'}
<td class="highlight"><a href="cc.php?page={$page}&amp;unregister={$thelist[i].id}">Unregister</a></td>
{elseif $ccuser.registered_cc.$ccid.approved eq 'c'}
<td><a href="cc.php?page={$page}&amp;register={$thelist[i].id}">Reregister</a></td>
{else}
<td><a href="cc.php?page={$page}&amp;register={$thelist[i].id}">{tr}Register{/tr}</a></td>
{/if}
{if $tiki_p_cc_admin eq 'y'}
<td><a href="cc.php?page={$page}&amp;cc_id={$thelist[i].id}">{tr}Edit{/tr}</a></td>
{/if}
<td>{$thelist[i].id}</td>
<td>{$thelist[i].cc_name}</td>
<td>{$thelist[i].cc_description}</td>
<td>{$thelist[i].owner_id|userlink}</td>
<td>{$thelist[i].requires_approval}</td>
<td>{$thelist[i].listed}</td>
</tr>
{sectionelse}
<td colspan="7">No entry</td>
{/section}
</table>

<br /><br />
