<h1><a class="pagetitle" href="tiki-list_contents.php">{tr}Dynamic content system{/tr}</a>
  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Dynamic+Content" target="tikihelp" class="tikihelp" title="{tr}Help on Dynamic Content{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_contents.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin DynamicContent tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To use content blocks in a text area (Wiki page, etc), a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{content id=x}{/literal}, where x is the ID of the content block.{/tr}</div>
</div>
<br />

<h2>{tr}Create or edit content block{/tr}</h2><a class="linkbut" href="tiki-list_contents.php">{tr}Create New Block{/tr}</a>
<form action="tiki-list_contents.php" method="post">
<input type="hidden" name="contentId" value="{$contentId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Description{/tr}:</td>
<td class="formcolor">
<textarea rows="5" cols="40" name="description">{$description|escape}</textarea>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor">
<input type="submit" name="save" value="{tr}Save{/tr}" />
</td></tr>
</table>
</form>
<h2>{tr}Available content blocks{/tr}</h2>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_contents.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
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
<td class="heading">{tr}Future vers{/tr}</td>
<td class="heading">{tr}Old vers{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].contentId}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].description}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].actual|tiki_short_datetime}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].next|tiki_short_datetime}&nbsp;</td>
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
<td class="even">&nbsp;{$listpages[changes].actual|tiki_short_datetime}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].next|tiki_short_datetime}&nbsp;</td>
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
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_contents.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_contents.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-list_contents.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
