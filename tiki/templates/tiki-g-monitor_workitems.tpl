{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-g-monitor_workitems.php">{tr}Monitor workitems{/tr}</a>
<br/><br/>
{include file=tiki-g-monitor_bar.tpl}
<h3>{tr}List of workitems{/tr} ({$cant})</h3>

{* FILTERING FORM *}
<form action="tiki-g-monitor_workitems.php" method="post">
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<input type="hidden" name="filter_instance" value="{$filter_instance}" />
<table>
<tr>
<td>
	<small>{tr}find{/tr}</small>
</td>
<td >
	<small>{tr}proc{/tr}</small>
</td>
<td >
	<small>{tr}act{/tr}</small>
</td>
<td >
	<small>{tr}instance{/tr}</small>
</td>
<td >
	<small>{tr}user{/tr}</small>
</td>
<td >
	&nbsp;
</td>	
</tr>

<tr>
<td >
	<input size="8" type="text" name="find" value="{$find}" />
</td>
<td >
	<select name="filter_process">
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$all_procs name=ix}
	<option {if $all_procs[ix].pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$all_procs[ix].pId}">{$all_procs[ix].name} {$all_procs[ix].version}</option>
	{/section}
	</select>
</td>
<td >
	<select name="filter_activity">
	<option {if '' eq $smarty.request.filter_activity}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$all_procs name=ix}
	<option {if $all_acts[ix].activityId eq $smarty.request.filter_activity}selected="selected"{/if} value="{$all_acts[ix].activityId}">{$all_acts[ix].name}</option>
	{/section}
	</select>
</td>
<td >
	<input type="text" name="filter_instance" value="{$smarty.request.filter_instance}" size="4" />
</td>
<td >
	<select name="filter_user">
	<option {if '' eq $smarty.request.filter_user}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$users name=ix}
	<option {if $users[ix] eq $smarty.request.filter_user}selected="selected"{/if} value="{$users[ix]}">{$users[ix]}</option>
	{/section}
	</select>
</td>

<td >	
	<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td>
</tr>
</table>	
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="tiki-g-monitor_workitems.php" method="post">
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="find" value="{$find}" />
<input type="hidden" name="where" value="{$where}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<table class="normal">
<tr>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'itemId_desc'}{sameurl sort_mode='itemId_asc'}{else}{sameurl sort_mode='itemId_desc'}{/if}">{tr}Id{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'actname_desc'}{sameurl sort_mode='actname_asc'}{else}{sameurl sort_mode='actname_desc'}{/if}">{tr}Activity{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'instanceId_desc'}{sameurl sort_mode='instanceId_asc'}{else}{sameurl sort_mode='instanceId_desc'}{/if}">{tr}Ins{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'orderId_desc'}{sameurl sort_mode='orderId_asc'}{else}{sameurl sort_mode='orderId_desc'}{/if}">{tr}#{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'started_desc'}{sameurl sort_mode='started_asc'}{else}{sameurl sort_mode='started_desc'}{/if}">{tr}Start{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'duration_desc'}{sameurl sort_mode='duration_asc'}{else}{sameurl sort_mode='duration_desc'}{/if}">{tr}time{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'user_desc'}{sameurl sort_mode='user_asc'}{else}{sameurl sort_mode='user_desc'}{/if}">{tr}User{/tr}</a></td>
</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}" style="text-align:left;">  
		<a href="tiki-g-view_workitem.php?itemId={$items[ix].itemId}" class="link">{$items[ix].itemId}</a>
	</td>

	<td class="{cycle advance=false}" style="text-align:left;">  
		{$items[ix].procname} {$items[ix].version}
	</td>

	<td class="{cycle advance=false}" style="text-align:left;">
		{$items[ix].type|act_icon:"$items[ix].isInteractive"} {$items[ix].actname} 
	</td>

	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_instance.php?iid={$items[ix].instanceId}">{$items[ix].instanceId}</a>
	</td>
	
	<td class="{cycle advance=false}">
	  {$items[ix].orderId}
	</td>

	<td class="{cycle advance=false}">
	  {$items[ix].started|tiki_short_datetime}
	</td>
	
	<td class="{cycle advance=false}">
	  {$items[ix].duration|duration}
	</td>

	<td class="{cycle}">
	  {$items[ix].user}
	</td>

	

	
	
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="7">
	{tr}No instances created yet{/tr}
	</td>
</tr>	
{/section}
</table>
</form>
{* END OF LISTING *}

{* PAGINATION *}
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-g-monitor_workitems.php?where={$where}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-g-monitor_workitems.php?where={$where}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-g-monitor_workitems.php?where={$where}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
{* END OF PAGINATION *}

{include file=tiki-g-monitor_stats.tpl}