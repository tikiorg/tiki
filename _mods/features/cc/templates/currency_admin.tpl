<a href="cc.php?page=currencies&amp;cc_id={$info.id}&amp;view=1" class="pagetitle">{tr}Examine Currency{/tr}</a>
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span><br />
<br /><br />

<span class="button2"><a href="cc.php?page=currencies" class="linkbut">{tr}List Currencies{/tr}</a></span>
<span class="button2"><a href="cc.php?page=currencies&amp;my=1" class="linkbut">{tr}My Currencies{/tr}</a></span>
<br /><br />

{if $msg}<div class="simplebox">{$msg}</div>{/if}
<style>{literal}
table.formcolor td:last-child { background-color : #ededed; }
table.formcolor td:first-child { background-color : #dedede; }
</style>{/literal}
<table class="formcolor">

<tr class="formrow">
<td>Id</td>
<td>{$info.id}</td>
</tr>

<tr class="formrow">
<td>{tr}Name{/tr}</td>
<td>{$info.cc_name}</td>
</tr>

<tr class="formrow">
<td>{tr}Description{/tr}</td>
<td>{$info.cc_description}</td>
</tr>

<tr class="formrow">
<td>{tr}Requires approval{/tr}</td>
<td>{$info.requires_approval}</td>
</tr>

<tr class="formrow">
<td>{tr}Listed publicly{/tr}</td>
<td>{$info.listed}</td>
</tr>

<tr class="formrow">
<td>{tr}Owner{/tr}</td>
<td>{$info.owner_id|userlink}</td>
</tr>

</table>

<table class="formrow">
{section name=i loop=$population}
<tr><td style="border-bottom:1px solid #aaaaaa;">{$population[i].acct_id}</td></tr>
{/section}
</table>
