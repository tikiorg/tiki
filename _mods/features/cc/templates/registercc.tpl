<a href="cc.php?page=registercc" class="pagetitle">Register for a cc</a>
<br /><br />
{if $tiki_p_cc_admin eq 'y'}
<span class="button2"><a href="cc.php?page=admincc" class="linkbut">Admin currencies</a></span>
{/if}
{if $tiki_p_cc_create eq 'y' or $tiki_p_cc_admin eq 'y'}
<span class="button2"><a href="cc.php?page=newcc" class="linkbut">Create new currency</a></span>
<br /><br />
{/if}

<table class="normal">
<tr class="heading">
<th>&nbsp;</th>
<th><a class="tableheading" href="cc.php?page=registercc&amp;sort_mode=id_{if $sort_mode eq 'id_desc'}asc{else}desc{/if}">{tr}Id{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=registercc&amp;sort_mode=cc_name_{if $sort_mode eq 'cc_name_desc'}asc{else}desc{/if}">{tr}Name{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=registercc&amp;sort_mode=cc_description_{if $sort_mode eq 'cc_description_desc'}asc{else}desc{/if}">{tr}Description{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=registercc&amp;sort_mode=owner_id_{if $sort_mode eq 'owner_id_desc'}asc{else}desc{/if}">{tr}Owner{/tr}</a></th>
<th><a class="tableheading" href="cc.php?page=registercc&amp;sort_mode=requires_approval_{if $sort_mode eq 'requires_approval_desc'}asc{else}desc{/if}">{tr}Approval?{/tr}</a></th>
</tr>

{cycle values="odd,even" print=false}
{section name=i loop=$thelist}
<tr class="{cycle}">
{assign var=ccid value=$thelist[i].id}
{if $ccuser.registered_cc.$ccid}
<td class="highlight">
<a href="cc.php?page=registercc&amp;unregister={$thelist[i].id}">Unregister</a></td>
{else}
<td>
<a href="cc.php?page=registercc&amp;register={$thelist[i].id}">Register</a></td>
{/if}
<td>{$thelist[i].id}</td>
<td>{$thelist[i].cc_name}</td>
<td>{$thelist[i].cc_description}</td>
<td>{$thelist[i].owner_id|userlink}</td>
<td>{$thelist[i].requires_approval}</td>
</tr>
{sectionelse}
<td colspan="7">No entry</td>
{/section}
</table>

<br /><br />
