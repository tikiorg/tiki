{*Smarty template*}
<a class="pagetitle" href="tiki-user_tasks.php?tasks_useDates={$tasks_useDates}">{tr}Tasks{/tr}</a>

{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=UserTasks" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}User Tasks{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-user_tasks.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}User Tasks tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>
{/if}







{include file=tiki-mytiki_bar.tpl}
<br/><br/>
[<a class="link" href="tiki-user_tasks.php?tasks_useDates=y">{tr}Use dates{/tr}</a> |
<a class="link" href="tiki-user_tasks.php?tasks_useDates=n">{tr}All tasks{/tr}</a>]
<br/><br/>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-user_tasks.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<form action="tiki-user_tasks.php" method="post">
<input type="submit" name="delete" value="{tr}delete{/tr}" />
<input type="submit" name="complete" value="{tr}mark as done{/tr}" />
<input type="submit" name="open" value="{tr}open tasks{/tr}" />
<table class="normal">
<tr>
<td class="heading">&nbsp;</td>
<td class="heading" width="80%"><a class="tableheading" href="tiki-user_tasks.php?tasks_useDates={$tasks_useDates}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></td>
<td class="heading" width="10%"><a class="tableheading" href="tiki-user_tasks.php?tasks_useDates={$tasks_useDates}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'date_desc'}date_asc{else}date_desc{/if}">{tr}start{/tr}</a></td>
<td style="text-align:right;" class="heading" width="10%"><a class="tableheading" href="tiki-user_tasks.php?tasks_useDates={$tasks_useDates}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'priority_desc'}priority_asc{else}priority_desc{/if}">{tr}priority{/tr}</a></td>
<td style="text-align:right;" class="heading" width="10%"><a class="tableheading" href="tiki-user_tasks.php?tasks_useDates={$tasks_useDates}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'percentage_desc'}percentage_asc{else}percentage_desc{/if}">{tr}completed{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="prio{$channels[user].priority}">
<input type="checkbox" name="task[{$channels[user].taskId}]" />
</td>
<td class="prio{$channels[user].priority}"><a {if $channels[user].status eq 'c'}style="text-decoration:line-through;"{/if} class="link" href="tiki-user_tasks.php?task_useDates={$task_useDates}&amp;taskId={$channels[user].taskId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{$channels[user].title}</a></td>
<td {if $channels[user].status eq 'c'}style="text-decoration:line-through;"{/if} class="prio{$channels[user].priority}">{$channels[user].date|date_format:"%d/%m/%Y"}</td>
<td style="text-align:right;{if $channels[user].status eq 'c'}text-decoration:line-through;{/if}" class="prio{$channels[user].priority}">{$channels[user].priority}</td>
<td style="text-align:right;{if $channels[user].status eq 'c'}text-decoration:line-through;{/if}" class="prio{$channels[user].priority}">
<select name="task_perc[{$channels[user].taskId}]">
	{section name=zz loop=$percs}
		<option value="{$percs[zz]|escape}" {if $channels[user].percentage eq $percs[zz]}selected="selected"{/if}>{$percs[zz]}%</option>	
	{/section}
</select>
</td>
</tr>
{sectionelse}
<tr>
	<td class="odd" colspan="16">{tr}No tasks entered{/tr}</td>
</tr>
{/section}
<tr>
	<td class="formcolor" colspan="16" style="text-align:center;">
		<input type="submit" name="update" value="{tr}update{/tr}" />
	</td>
</tr>
</table>
</form>
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-user_tasks.php?tasks_useDates={$tasks_useDates}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-user_tasks.php?tasks_useDates={$tasks_useDates}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-user_tasks.php?tasks_useDates={$tasks_useDates}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>


<h3>{tr}Add or edit a task{/tr}</h3>
<form action="tiki-user_tasks.php" method="post">
<input type="hidden" name="taskId" value="{$taskId|escape}" />
<input type="hidden" name="tasks_useDates" value="{$tasks_useDates|escape}" />
<input type="hidden" name="Date_Day" value="{$Date_Day|escape}" />
<input type="hidden" name="Date_Month" value="{$Date_Month|escape}" />
<input type="hidden" name="Date_Year" value="{$Date_Year|escape}" />
<table class="normal">
  <tr><td class="formcolor">{tr}Title{/tr}</td>
      <td class="formcolor"><input type="text" name="title" value="{$info.title|escape}" /></td>
  </tr>
  <tr><td class="formcolor">{tr}Description{/tr}</td>
      <td class="formcolor">
        <textarea rows="10" cols="80" name="description">{$info.description|escape}</textarea>
      </td>
  </tr>
  {if $tasks_useDates eq 'y'}
  
  <tr><td class="formcolor">{tr}Start date{/tr}</td>
      <td class="formcolor">{html_select_date time=$info.date end_year="+1"}</td>
  </tr>
  
  {if $info.status eq 'c'}
  <tr><td class="formcolor">{tr}Completed{/tr}</td>
      <td class="formcolor">{$info.completed|tiki_short_date}</td>
  </tr>
  {/if}
  {/if}
  
  <tr><td class="formcolor">{tr}Status{/tr}</td>
      <td class="formcolor">
        <select name="status">
          <option value="o" {if $info.status eq 'o'}selected="selected"{/if}>{tr}open{/tr}</option>
          <option value="c" {if $info.status eq 'c'}selected="selected"{/if}>{tr}completed{/tr}</option>
        </select>
      </td>
  </tr>
  <tr><td class="formcolor">{tr}Priority{/tr}</td>
      <td class="formcolor">
        <select name="priority">
          <option value="1" {if $info.priority eq 1}selected="selected"{/if}>1</option>
          <option value="2" {if $info.priority eq 2}selected="selected"{/if}>2</option>
          <option value="3" {if $info.priority eq 3}selected="selected"{/if}>3</option>
          <option value="4" {if $info.priority eq 4}selected="selected"{/if}>4</option>
          <option value="5" {if $info.priority eq 5}selected="selected"{/if}>5</option>
        </select>
      </td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Percentage completed{/tr}</td>
    <td class="formcolor">
      <select name="percentage">
      {html_options values="$comp_array" output="$comp_array_p" selected="$info.percentage"}
      </select>
    </td>
  </tr>
  <tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}save{/tr}" /></td>
  </tr>
</table>
</form>

