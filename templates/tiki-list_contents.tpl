<a class="pagetitle" href="tiki-list_contents.php">{tr}Dynamic content system{/tr}</a><br/><br/>
<h3>Create or edit content block</h3><a class="link" href="tiki-list_contents.php">{tr}create new block{/tr}</a>
<form action="tiki-list_contents.php" method="post">
<input type="hidden" name="contentId" value="{$contentId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Description{/tr}:</td>
<td class="formcolor">
<textarea rows="5" cols="40" name="description">{$description}</textarea>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor">
<input type="submit" name="save" value="{tr}save{/tr}" />
</td></tr>
</table>
</form>
<h3>{tr}Available content blocks{/tr}</h3>
<table class="findtable">
<tr><td class="findtable">Find</td>
   <td class="findtable">
   <form method="get" action="tiki-list_contents.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_contents.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'contentId_desc'}contentId_asc{else}contentId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_contents.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Desc{/tr}</a></td>
<td class="heading">{tr}Current ver{/tr}</td>
<td class="heading">{tr}Next ver{/tr}</td>
<td class="heading">{tr}Versions{/tr}</td>
<td class="heading">{tr}Old vers{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].contentId}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].description}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].actual|date_format:"%a %d of %b [%H:%M]"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].next|date_format:"%a %d of %b [%H:%M]"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].future}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].old}&nbsp;</td>
<td class="odd">
<a class="link" href="tiki-list_contents.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].contentId}">{tr}Remove{/tr}</a>
<a class="link" href="tiki-list_contents.php?edit={$listpages[changes].contentId}">{tr}Edit{/tr}</a>
<a class="link" href="tiki-edit_programmed_content.php?contentId={$listpages[changes].contentId}">{tr}Program{/tr}</a>
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].contentId}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].description}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].actual|date_format:"%a %d of %b [%H:%M]"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].next|date_format:"%a %d of %b [%H:%M]"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].future}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].old}&nbsp;</td>
<td class="even">
<a class="link" href="tiki-list_contents.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].contentId}">{tr}Remove{/tr}</a>
<a class="link" href="tiki-list_contents.php?edit={$listpages[changes].contentId}">{tr}Edit{/tr}</a>
<a class="link" href="tiki-edit_programmed_content.php?contentId={$listpages[changes].contentId}">{tr}Program{/tr}</a>
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_contents.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_contents.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_contents.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
