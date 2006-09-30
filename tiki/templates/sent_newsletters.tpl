{* url - nlId - sort_mode - find - offset - channels - prev_offset - next_offset - pages_cant *}
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="{$url}">
     <input type="text" name="{$cur}_find" value="{$find|escape}" />
     <input type="hidden" name="{$bak}_find" value="{$find_bak|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="nlId" value="{$nlId}" />
     <input type="hidden" name="cookietab" value="{$tab}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}newsletter{/tr}</a></td>
<td class="heading"><a class="tableheading" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}subject{/tr}</a></td>
{if $view_editions eq 'y'}
<td class="heading"><a class="tableheading" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}users{/tr}</a></td>
<td class="heading"><a class="tableheading" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={if $sort_mode eq 'sent_desc'}sent_asc{else}sent_desc{/if}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}sent{/tr}</a></td>
{/if}
<td class="heading">{tr}error{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{if $channels[user].tiki_p_admin_newsletters eq 'y'}<a class="link" href="{$url}?nlId={$channels[user].nlId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;nlId={$channels[user].nlId}&amp;remove={$channels[user].editionId}" title="{tr}remove{/tr}"><img src="pics/icons/cross.png" border="0" width="16" height="16" alt='{tr}remove{/tr}' /></a>{else}&nbsp;{/if}</td>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].subject}</td>
{if $view_editions eq 'y'}
<td class="{cycle advance=false}">{$channels[user].users}</td>
<td class="{cycle advance=false}">{$channels[user].sent|tiki_short_datetime}</td>
{/if}
<td class="{cycle advance=false}">{if $channels[user].nbErrors > 0}<a href="tiki-newsletter_archives.php?nlId={$channels[user].nlId}&amp;error={$channels[user].editionId}">{$channels[user].nbErrors}</a>{else}0{/if}</td>
<td class="{cycle}">
{if $url == "tiki-newsletter_archives.php"}<a class="link" href="{$url}?{if $nl_info}nlId={$channels[user].nlId}&amp;{/if}offset={$offset}&amp;sort_mode={$sort_mode}&amp;editionId={$channels[user].editionId}">{tr}view{/tr}</a>&nbsp;{/if}
{if $channels[user].tiki_p_send_newsletters eq 'y'}<a class="link" href="tiki-send_newsletters.php?nlId={$channels[user].nlId}&amp;editionId={$channels[user].editionId}">{tr}use{/tr}{else}&nbsp;{/if}</a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$prev_offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={$sort_mode}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$next_offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={$sort_mode}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$selector_offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={$sort_mode}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
