<h1><a href="tiki-lastchanges.php?days={$days}" class="pagetitle">{tr}Last Changes{/tr}</a></h1>
[<a class="linkbut" href="tiki-lastchanges.php?days=1">{tr}Today{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=2">{tr}|Last{/tr} 2 {tr}days{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=3">{tr}|Last{/tr} 3 {tr}days{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=5">{tr}|Last{/tr} 5 {tr}days{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=7">{tr}|Last{/tr} {tr}week{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=14">{tr}|Last{/tr} 2 {tr}weeks{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=31">{tr}|Last{/tr} {tr}month{/tr}</a>
<a class="linkbut" href="tiki-lastchanges.php?days=0">{tr}|All{/tr}</a>]
<br/><br/>
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-lastchanges.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="days" value="0" />
   </form>
   </td>
{if $findwhat != ""}
   <td>
   <a href="tiki-lastchanges.php" class="wiki">{tr}Search by Date{/tr}</a>
   </td>
{/if}   
</tr>
</table>
<br/>
{if $findwhat!=""}
{tr}Found{/tr} "<b>{$findwhat}</b>" {tr}in{/tr} {$cant_records} {tr}last changes{/tr} 
{/if}
<div align="left">
<table class="normal">
<tr>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Page{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'action_desc'} action_asc{else}action_desc{/if}">{tr}Action{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'ip_desc'}ip_asc{else}ip_desc{/if}">{tr}IP{/tr}</a></td>
<td class="heading" bgcolor="#bbbbbb"><a class="tableheading" href="tiki-lastchanges.php?days={$days}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}Comment{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$lastchanges}
<tr>
<td class="{cycle advance=false}">&nbsp;{$lastchanges[changes].lastModif|tiki_short_datetime}&nbsp;</td>
<td class="{cycle advance=false}">&nbsp;<a href="tiki-index.php?page={$lastchanges[changes].pageName|escape:"url"}" class="tablename" title="{$lastchanges[changes].pageName}">{$lastchanges[changes].pageName|truncate:20:"...":true}</a>
&nbsp;(<a class="link" href="tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}" title="History">{tr}h{/tr}</a>

&nbsp;<a class="link" href="tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;preview={$lastchanges[changes].version}"
 title="{tr}view{/tr}">v</a>&nbsp;
{if $tiki_p_rollback eq 'y'}
<a class="link" href="tiki-rollback.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;version={$lastchanges[changes].version}" title="{tr}Rollback{/tr}">b</a>&nbsp;
{/if}
<a class="link" href="tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff={$lastchanges[changes].version}" title="{tr}Compare{/tr}">c</a>&nbsp;
<a class="link" href="tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff2={$lastchanges[changes].version}" title="{tr}Difference{/tr}">d</a>&nbsp;
<a class="link" href="tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;source={$lastchanges[changes].version}" title="{tr}Source{/tr}">s</a>)


</td>

<td class="{cycle advance=false}">{$lastchanges[changes].action}</td>
<td class="{cycle advance=false}">&nbsp;{$lastchanges[changes].user}&nbsp;</td>
<td class="{cycle advance=false}">&nbsp;{$lastchanges[changes].ip}&nbsp;</td>
<td class="{cycle}">&nbsp;{$lastchanges[changes].comment}&nbsp;</td>


</tr>
{sectionelse}
<tr><td class="even" colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<div class="mini" align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-lastchanges.php?find={$find}&amp;days={$days}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
