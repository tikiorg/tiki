<a class="pagetitle" href="tiki-admin_newsletters.php">Admin newsletters</a><br/><br/>
[<a class="link" href="tiki-newsletters.php">{tr}list newsletters{/tr}</a>
]<br/><br/>
<h2>{tr}Create/edit newsletters{/tr}</h2>
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=newsletter%20{$info.name}&amp;objectType=newsletter&amp;permType=newsletters&amp;objectId={$info.nlId}">{tr}There are individual permissions set for this newsletter{/tr}</a><br/><br/>
{/if}
<form action="tiki-admin_newsletters.php" method="post">
<input type="hidden" name="nlId" value="{$info.nlId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$info.name}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$info.description}</textarea></td></tr>
<tr><td class="formcolor">{tr}Users can subscribe any email addresss{/tr}</td><td class="formcolor">
<input type="checkbox" name="allowAnySub" {if $info.allowAnySub eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Frequency{/tr}</td><td class="formcolor">
<select name="frequency">
{section name=ix loop=$freqs}
<option value="{$freqs[ix].t}" {if $info.frequency eq $freqs[ix].t}selected="selected"{/if}>{$freqs[ix].i} days</option>
{/section}
</select>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>newsletters</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_newsletters.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'nlId_desc'}nlId_asc{else}nlId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}">{tr}users{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'editions_desc'}editions_asc{else}editions_desc{/if}">{tr}editions{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastSent_desc'}lastSent_asc{else}lastSent_desc{/if}">{tr}last sent{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].nlId}</td>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<td class="{cycle advance=false}">{$channels[user].users}</td>
<td class="{cycle advance=false}">{$channels[user].editions}</td>
<td class="{cycle advance=false}">{$channels[user].lastSent|date_format:"%a %d of %b [%H:%M]"}</td>
<td class="{cycle}">
   <a class="link" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].nlId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;nlId={$channels[user].nlId}">{tr}edit{/tr}</a>
   {if $channels[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName=newsletter%20{$channels[user].name}&amp;objectType=newsletter&amp;permType=newsletters&amp;objectId={$channels[user].nlId}">{tr}perms{/tr}</a>{if $channels[user].individual eq 'y'}){/if}
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_newsletters.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_newsletters.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>

