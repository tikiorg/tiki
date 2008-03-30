{* $Id$ *}
<h1><a class="pagetitle" href="tiki-admin_polls.php">{tr}Admin Polls{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Polls" target="tikihelp" class="tikihelp" title="{tr}Admin Polls{/tr}">
{icon _id='help'}</a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_polls.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Polls Template{/tr}">
{icon _id='shape_square_edit'}</a>{/if}</h1>

{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=polls">{icon _id='wrench' alt="{tr}Configure Polls{/tr}"}</a>
{/if}

<br /><br />
<a href="tiki-admin_polls.php?setlast=1" class="linkbut">{tr}Set last poll as current{/tr}</a>
<a href="tiki-admin_polls.php?closeall=1" class="linkbut">{tr}Close all polls but last{/tr}</a>
<a href="tiki-admin_polls.php?activeall=1" class="linkbut">{tr}Activate all polls{/tr}</a>
<h2>{tr}Create/edit Polls{/tr}</h2>
<form action="tiki-admin_polls.php" method="post">
<input type="hidden" name="pollId" value="{$pollId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Title{/tr}:</td><td class="formcolor"><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Active{/tr}:</td><td class="formcolor">
<select name="active">
<option value='a' {if $active eq 'a'}selected="selected"{/if}>{tr}active{/tr}</option>
<option value='c' {if $active eq 'c'}selected="selected"{/if}>{tr}current{/tr}</option>
<option value='x' {if $active eq 'x'}selected="selected"{/if}>{tr}closed{/tr}</option>
<option value='t' {if $active eq 't'}selected="selected"{/if} style="border-top:1px solid black;">{tr}template{/tr}</option>
<option value='o' {if $active eq 'o'}selected="selected"{/if}>{tr}object{/tr}</option>
</select>
</td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}PublishDate{/tr}:</td><td class="formcolor">
{html_select_date time=$publishDate end_year="+1" field_order=$prefs.display_field_order} {tr}at{/tr} {html_select_time time=$publishDate display_seconds=false}
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Polls{/tr}</h2>
<div align="center">
{if $channels}
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_polls.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
{/if}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pollId_desc'}pollId_asc{else}pollId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
{if $prefs.poll_list_categories eq 'y'}<td class="heading">{tr}Categories{/tr}</td>{/if}
{if $prefs.poll_list_objects eq 'y'}<td class="heading">{tr}Objects{/tr}</td>{/if}
<td class="heading"><a class="tableheading" href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'active_desc'}active_asc{else}active_desc{/if}">{tr}Active{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'votes_desc'}votes_asc{else}votes_desc{/if}">{tr}Votes{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}Publish{/tr}</a></td>
<td class="heading">{tr}Options{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].pollId}</td>
<td class="{cycle advance=false}"><a class="tablename" href="tiki-poll_results.php?pollId={$channels[user].pollId}">{$channels[user].title}</a></td>
{if $prefs.poll_list_categories eq 'y'}<td class="{cycle advance=false}">
{section name=cat loop=$channels[user].categories}{$channels[user].categories[cat].name}{if !$smarty.section.cat.last}<br />{/if}{/section}</td>{/if}
{if $prefs.poll_list_objects eq 'y'}<td class="{cycle advance=false}">{section name=obj loop=$channels[user].objects}<a href="{$channels[user].objects[obj].href}">{$channels[user].objects[obj].name}</a>{if !$smarty.section.obj.last}<br />{/if}{/section}</td>{/if}
<td class="{cycle advance=false}">{$channels[user].active}</td>
<td class="{cycle advance=false}">{$channels[user].votes}</td>
<td class="{cycle advance=false}">{$channels[user].publishDate|tiki_short_datetime}</td>
<td class="{cycle advance=false}">{$channels[user].options}</td>
<td class="{cycle}">
   <a class="link" href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pollId}">{tr}Delete{/tr}</a>
   <a class="link" href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;pollId={$channels[user].pollId}">{tr}Edit{/tr}</a>
   <a class="link" href="tiki-admin_poll_options.php?pollId={$channels[user].pollId}">{tr}Options{/tr}</a>
</td>
</tr>
{sectionelse}
<tr><td colspan="{if $prefs.feature_poll_categories eq 'y'}8{else}7{/if}" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_polls.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_polls.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_polls.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

<h2>{tr}Add poll to pages{/tr}</h2>
<form action="tiki-admin_polls.php" method="post">
<table class="normal">
<tr><td class="formcolor">
{tr}Poll{/tr}</td><td class="formcolor">
<select name="poll_template">
{section name=ix loop=$channels}
{if $channels[ix].active eq 't'}
<option value="{$channels[ix].pollId|escape}"{if $smarty.section.ix.first} selected="selected"{/if}>{tr}{$channels[ix].title}{/tr}</option>
{/if}
{/section}
</select></td></tr>
<tr><td class="formcolor">{tr}Title{/tr}</td><td class="formcolor"><input type="text" name="poll_title" /></td></tr>
<tr><td class="formcolor">
{tr}Wiki pages{/tr}</td><td class="formcolor">
<select name="pages[]" multiple="multiple" size="20">
{section name=ix loop=$listPages}
<option value="{$listPages[ix].pageName|escape}">{tr}{$listPages[ix].pageName}{/tr}</option>
{/section}
</select><br /><i>{tr}Tip: Hold down CTRL to select multiple{/tr}</i>
</td></tr>
<tr><td class="formcolor">{tr}Lock the pages{/tr}</td><td class="formcolor"><input type="checkbox" name="locked" /></td></tr>
<tr><td class="formcolor"></td><td class="formcolor"><input type="submit" name="addPoll" value="{tr}Add{/tr}" /></td></tr></table>
</form>
