{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-sheets.tpl,v 1.22.2.3 2008-01-30 15:33:51 nyloth Exp $ *}
<h1><a href="tiki-sheets.php" class="pagetitle">{tr}TikiSheet{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=TikiSheet" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Tiki Sheet{/tr}">
{icon _id='help'}</a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-sheets.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}sheets tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>{/if}
</h1>

{if $tiki_p_edit_sheet eq 'y'}
{if $edit_mode eq 'y'}
{if $sheetId eq 0}
<h2>{tr}Create a sheet{/tr}</h2>
{else}
<h2>{tr}Edit this sheet:{/tr} {$title}</h2>
{if $tiki_p_edit_sheet eq 'y'}
<div class="navbar"><a class="linkbut" href="tiki-sheets.php?edit_mode=1&amp;sheetId=0">{tr}Create New Sheet{/tr}</a></div>
{/if}
{/if}
<div align="center">
{if $individual eq 'y'}
<a class="gallink" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=sheet&amp;permType=sheet&amp;objectId={$sheetId}">{tr}There are individual permissions set for this sheet{/tr}</a>
{/if}
<form action="tiki-sheets.php" method="post">
<input type="hidden" name="sheetId" value="{$sheetId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Title{/tr}:</td><td class="formcolor"><input type="text" name="title" value="{$title|escape}"/></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Class Name{/tr}:</td><td class="formcolor"><input type="text" name="className" value="{$className|escape}"/></td></tr>
<tr><td class="formcolor">{tr}Header Rows{/tr}:</td><td class="formcolor"><input type="text" name="headerRow" value="{$headerRow|escape}"/></td></tr>
<tr><td class="formcolor">{tr}Footer Rows{/tr}:</td><td class="formcolor"><input type="text" name="footerRow" value="{$footerRow|escape}"/></td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" value="{tr}Save{/tr}" name="edit" /></td></tr>
</table>
</form>
</div>
<br />
{else}
<div class="navbar"><a href="tiki-sheets.php?edit_mode=edit&sheetId=0" class="linkbut">{tr}Create new Sheet{/tr}</a></div>
{/if}
{/if}

{if $sheetId > 0}
{if $edited eq 'y'}
<div class="wikitext">
{tr}You can access the sheet using the following URL{/tr}: <a class="gallink" href="{$url}?sheetId={$sheetId}">{$url}?sheetId={$sheetId}</a>
</div>
{/if}
{/if}
<h2>{tr}Available Sheets{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-sheets.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td  class="heading">{tr}Actions{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$sheets}
<tr>
  <td class="{cycle advance=false}"><a class="galname" href="tiki-view_sheets.php?sheetId={$sheets[changes].sheetId}">{$sheets[changes].title}</a></td>
  <td class="{cycle advance=false}">{$sheets[changes].description}</td>
  <td class="{cycle advance=false}">{$sheets[changes].author}</td>
  <td class="{cycle}">
  {if $chart_enabled eq 'y'}
    <a class="gallink" href="tiki-graph_sheet.php?sheetId={$sheets[changes].sheetId}"><img src='pics/icons2/chart_curve.png' border='0' width='16' height='16' alt='{tr}Graph{/tr}' title='{tr}Graph{/tr}' /></a>
  {/if}
  {if $tiki_p_view_sheet_history eq 'y'}
    <a class="gallink" href="tiki-history_sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheets[changes].sheetId}">{icon _id='application_form_magnify' alt='{tr}History{/tr}'}</a>
  {/if}
    <a class="gallink" href="tiki-export_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheets[changes].sheetId}">{icon _id='disk' alt='{tr}Export{/tr}'}</a>
  {if $sheets[changes].tiki_p_edit_sheet eq 'y'}
    <a class="gallink" href="tiki-import_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheets[changes].sheetId}">{icon _id='folder_add' alt='{tr}Import{/tr}'}</a>
  {/if}
  {if $tiki_p_admin_sheet eq 'y'}
     <a class="gallink" href="tiki-objectpermissions.php?objectName={$sheets[changes].title|escape:"url"}&amp;objectType=sheet&amp;permType=sheet&amp;objectId={$sheets[changes].sheetId}">
    {if $sheets[changes].individual eq 'y'}
	{icon _id='key_active' alt='{tr}Active Perms{/tr}'}
    {else}
	{icon _id='key' alt='{tr}Perms{/tr}'}
    {/if}
    </a>
  {/if}
  {if $sheets[changes].tiki_p_edit_sheet eq 'y'}
    <a class="gallink" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;sheetId={$sheets[changes].sheetId}">{icon _id='wrench' alt='{tr}Edit{/tr}'}</a>
    <a class="gallink" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removesheet=y&amp;sheetId={$sheets[changes].sheetId}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
  {/if}
  </td>
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
[<a class="galprevnext" href="tiki-sheets.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="galprevnext" href="tiki-sheets.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-sheets.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
