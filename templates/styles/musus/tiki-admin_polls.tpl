<a class="pagetitle" href="tiki-admin_polls.php">{tr}Admin Polls{/tr}</a>
<!-- the help link info -->
  
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Polls" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin polls{/tr}"><img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_polls.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin polls tpl{/tr}"><img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- begin -->

<br /><br />
<a href="tiki-admin_polls.php?setlast=1" class="linkbut">{tr}Set last poll as current{/tr}</a>
<a href="tiki-admin_polls.php?closeall=1" class="linkbut">{tr}Close all polls but last{/tr}</a>
<a href="tiki-admin_polls.php?activeall=1" class="linkbut">{tr}Activate all polls{/tr}</a>
<h2>{tr}Create/edit Polls{/tr}</h2>
<form action="tiki-admin_polls.php" method="post">
<input type="hidden" name="pollId" value="{$pollId|escape}" />
<table>
<tr><td>{tr}Title{/tr}:</td><td><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr><td>{tr}Active{/tr}:</td><td>
<select name="active">
<option value='a' {if $active eq 'a'}selected="selected"{/if}>{tr}active{/tr}</option>
<option value='c' {if $active eq 'c'}selected="selected"{/if}>{tr}current{/tr}</option>
<option value='x' {if $active eq 'x'}selected="selected"{/if}>{tr}closed{/tr}</option>
</select>
</td></tr>
{include file=categorize.tpl}
<tr><td>{tr}PublishDate{/tr}:</td><td>
{html_select_date time=$publishDate end_year="+1"} {tr}at{/tr} {html_select_time time=$publishDate display_seconds=false}
</td></tr>
<tr><td >&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Polls{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-admin_polls.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table>
<tr>
<th><a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pollId_desc'}pollId_asc{else}pollId_desc{/if}">{tr}ID{/tr}</a></th>
<th><a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></th>
<th><a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'active_desc'}active_asc{else}active_desc{/if}">{tr}active{/tr}</a></th>
<th><a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'votes_desc'}votes_asc{else}votes_desc{/if}">{tr}votes{/tr}</a></th>
<th><a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}Publish{/tr}</a></th>
<th>{tr}options{/tr}</th>
<th>{tr}action{/tr}</th>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr class="odd">
<td>{$channels[user].pollId}</td>
<td><a class="tablename" href="tiki-poll_results.php?pollId={$channels[user].pollId}">{$channels[user].title}</a></td>
<td>{$channels[user].active}</td>
<td>{$channels[user].votes}</td>
<td>{$channels[user].publishDate|tiki_short_datetime}</td>
<td>{$channels[user].options}</td>
<td>
   <a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pollId}">{tr}remove{/tr}</a>
   <a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;pollId={$channels[user].pollId}">{tr}edit{/tr}</a>
   <a href="tiki-admin_poll_options.php?pollId={$channels[user].pollId}">{tr}options{/tr}</a>
</td>
</tr>
{else}
<tr class="even">
<td>{$channels[user].pollId}</td>
<td><a class="tablename" href="tiki-poll_results.php?pollId={$channels[user].pollId}">{$channels[user].title}</a></td>
<td>{$channels[user].active}</td>
<td>{$channels[user].votes}</td>
<td>{$channels[user].publishDate|tiki_short_datetime}</td>
<td>{$channels[user].options}</td>
<td>
   <a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pollId}">{tr}remove{/tr}</a>
   <a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;pollId={$channels[user].pollId}">{tr}edit{/tr}</a>
   <a href="tiki-admin_poll_options.php?pollId={$channels[user].pollId}">{tr}options{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_polls.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_polls.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_polls.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>