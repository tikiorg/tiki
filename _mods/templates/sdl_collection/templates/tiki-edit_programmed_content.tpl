<a class="pagetitle" href="tiki-edit_programmed_content.php?contentId={$contentId}">{tr}Program dynamic content for block{/tr}: {$contentId}</a><br /><br />
<h3>{tr}Block description: {/tr}{$description}</h3>
<h3>{tr}Create or edit content{/tr}</h3>
{if $pId}
{tr}You are editing block:{/tr}{$pId}<br />
{/if}
<a class="linkbut" href="tiki-edit_programmed_content.php?contentId={$contentId}">[{tr}Create new block{/tr}</a>
<a class="linkbut" href="tiki-list_contents.php">{tr}|Return to block listing{/tr}]</a><br /><br />

<form action="tiki-edit_programmed_content.php" method="post">
<input type="hidden" name="contentId" value="{$contentId|escape}" />
<input type="hidden" name="pId" value="{$pId|escape}" />
<table class="normal">
<tr><td class="formcolor">Description:</td>
<td class="formcolor">
<textarea rows="5" cols="40" name="data">{$data|escape}</textarea>
</td></tr>
<tr><td class="formcolor">{tr}Publishing date{/tr}</td>
<td class="formcolor">{html_select_date time=$publishDate end_year="+1"} at {html_select_time time=$publishDate display_seconds=false}</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor">
<input type="submit" name="save" value="{tr}Save{/tr}" />
</td></tr>
</table>
</form>
<h3>{tr}Available content blocks{/tr}</h3>
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-edit_programmed_content.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-edit_programmed_content.php?contentId={$contentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'contentId_desc'}contentId_asc{else}contentId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-edit_programmed_content.php?contentId={$contentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}Publishing Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-edit_programmed_content.php?contentId={$contentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'data_desc'}data_asc{else}data_desc{/if}">{tr}Data{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $actual eq $listpages[changes].publishDate}
{assign var=class value=third}
{else}
{if $actual > $listpages[changes].publishDate}
{assign var=class value=odd}
{else}
{assign var=class value=even}
{/if}
{/if}
<td class="{$class}">&nbsp;{$listpages[changes].pId}&nbsp;</td>
<td  class="{$class}">&nbsp;{$listpages[changes].publishDate|tiki_short_datetime}&nbsp;</td>
<td  class="{$class}">&nbsp;{$listpages[changes].data}&nbsp;</td>
<td class="{$class}">
<a class="link" href="tiki-edit_programmed_content.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;contentId={$contentId}&amp;remove={$listpages[changes].pId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this content?{/tr}')" >{tr}Delete{/tr}</a>
<a class="link" href="tiki-edit_programmed_content.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;contentId={$contentId}&amp;edit={$listpages[changes].pId}">{tr}Edit{/tr}</a>
</td>
</tr>
{sectionelse}
<tr><td colspan="4">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-edit_programmed_content.php?find={$find}&amp;contentId={$contentId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-edit_programmed_content.php?find={$find}&amp;contentId={$contentId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-edit_programmed_content.php?find={$find}&amp;contentId={$contentId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
