{* url - nlId - sort_mode - find - offset - channels - prev_offset - next_offset - pages_cant *}
<h2>{tr}Sent editions{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="{$url}">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="nlId" value="{$nlId}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="{$url}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}newsletter{/tr}</a></td>
<td class="heading"><a class="tableheading" href="{$url}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}">{tr}subject{/tr}</a></td>
<td class="heading"><a class="tableheading" href="{$url}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}">{tr}users{/tr}</a></td>
<td class="heading"><a class="tableheading" href="{$url}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'sent_desc'}sent_asc{else}sent_desc{/if}">{tr}sent{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}"><a class="link" href="{$url}?nlId={$channels[user].nlId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;nlId={$channels[user].nlId}&amp;remove={$channels[user].editionId}" title="{tr}remove{/tr}"><img src="img/icons2/delete.gif" border="0" width="16" height="16" alt='{tr}remove{/tr}'></a></td>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].subject}</td>
<td class="{cycle advance=false}">{$channels[user].users}</td>
<td class="{cycle advance=false}">{$channels[user].sent|tiki_short_datetime}</td>
<td class="{cycle}">
{if $url == "tiki-newsletter_archives.php"}<a class="link" href="{$url}?{if $nl_info}nlId={$channels[user].nlId}&amp;{/if}offset={$offset}&amp;sort_mode={$sort_mode}&amp;editionId={$channels[user].editionId}">{tr}view{/tr}</a>&nbsp;{/if}
<a class="link" href="tiki-send_newsletters.php?nlId={$channels[user].nlId}&amp;editionId={$channels[user].editionId}">{tr}use{/tr}</a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="{$url}?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="{$url}?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="{$url}?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
