{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-monitor_workitems.php">{tr}Monitor workitems{/tr}</a>
</h1>
{include file=tiki-g-monitor_bar.tpl}
<h2>{tr}List of workitems{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="tiki-g-monitor_workitems.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="filter_instance" value="{$filter_instance|escape}" />
<table>
<tr>
<td>
	<small>{tr}Find{/tr}</small>
</td>
<td >
	<small>{tr}Proc{/tr}</small>
</td>
<td >
	<small>{tr}act{/tr}</small>
</td>
<td >
	<small>{tr}instance{/tr}</small>
</td>
<td >
	<small>{tr}User{/tr}</small>
</td>
<td >
	&nbsp;
</td>	
</tr>

<tr>
<td >
	<input size="8" type="text" name="find" value="{$find|escape}" />
</td>
<td >
	<select name="filter_process">
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
    {foreach from=$all_procs item=proc}
	<option {if $proc.pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$proc.pId|escape}">{$proc.name} {$proc.version}</option>
	{/foreach}
	</select>
</td>
<td >
	<select name="filter_activity">
	<option {if '' eq $smarty.request.filter_activity}selected="selected"{/if} value="">{tr}All{/tr}</option>
    {foreach from=$all_acts item=proc}
	<option {if $proc.activityId eq $smarty.request.filter_activity}selected="selected"{/if} value="{$proc.activityId|escape}">{$proc.name}</option>
	{/foreach}
	</select>
</td>
<td >
	<input type="text" name="filter_instance" value="{$smarty.request.filter_instance|escape}" size="4" />
</td>
<td >
	<select name="filter_user">
	<option {if '' eq $smarty.request.filter_user}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$users name=ix}
	<option {if $users[ix] eq $smarty.request.filter_user}selected="selected"{/if} value="{$users[ix]|escape}">{$users[ix]}</option>
	{/section}
	</select>
</td>

<td >	
	<input type="submit" name="filter" value="{tr}Filter{/tr}" />
</td>
</tr>
</table>	
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="tiki-g-monitor_workitems.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'itemId_desc'}{sameurl sort_mode='itemId_asc'}{else}{sameurl sort_mode='itemId_desc'}{/if}">{tr}Id{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'actname_desc'}{sameurl sort_mode='actname_asc'}{else}{sameurl sort_mode='actname_desc'}{/if}">{tr}Activity{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'iname_desc'}{sameurl sort_mode='iname_asc'}{else}{sameurl sort_mode='iname_desc'}{/if}">{tr}Instance Id{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'instanceId_desc'}{sameurl sort_mode='instanceId_asc'}{else}{sameurl sort_mode='instanceId_desc'}{/if}">{tr}Instance Name{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'status_desc'}{sameurl sort_mode='status_asc'}{else}{sameurl sort_mode='status_desc'}{/if}">{tr}Instance Status{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'orderId_desc'}{sameurl sort_mode='orderId_asc'}{else}{sameurl sort_mode='orderId_desc'}{/if}">{tr}#{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'started_desc'}{sameurl sort_mode='started_asc'}{else}{sameurl sort_mode='started_desc'}{/if}">{tr}Start{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'duration_desc'}{sameurl sort_mode='duration_asc'}{else}{sameurl sort_mode='duration_desc'}{/if}">{tr}Elapsed time{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'user_desc'}{sameurl sort_mode='user_asc'}{else}{sameurl sort_mode='user_desc'}{/if}">{tr}User{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$items item=workitem}
<tr>
	<td class="{cycle advance=false}" style="text-align:left;">  
		<a href="tiki-g-view_workitem.php?itemId={$workitem.itemId}" class="link">{$workitem.itemId}</a>
	</td>
	<td class="{cycle advance=false}" style="text-align:left;">  
		{$workitem.procname} {$workitem.version}
	</td>
	<td class="{cycle advance=false}" style="text-align:left;">
		{$workitem.type|act_icon:$workitem.isInteractive} {$workitem.actname} 
	</td>
	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_instance.php?iid={$workitem.instanceId}">{$workitem.instanceId}</a>
	</td>
	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_instance.php?iid={$workitem.instanceId}">{$workitem.iname}</a>
	</td>
	<td class="{cycle advance=false}">
	  {$workitem.status}
	</td>
	<td class="{cycle advance=false}">
	  {$workitem.orderId}
	</td>
	<td class="{cycle advance=false}">
	  {$workitem.started|tiki_short_datetime}
	</td>
	<td class="{cycle advance=false}">
	  {$workitem.duration} day(s)
	</td>
	<td class="{cycle}">
	  {$workitem.user}
	</td>
</tr>
{foreachelse}
<tr>
	<td class="{cycle advance=false}" colspan="8">
	{tr}No instances created yet{/tr}
	</td>
</tr>	
{/foreach}
</table>
</form>
{* END OF LISTING *}

{* PAGINATION *}
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="{sameurl offset=$prev_offset}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="{sameurl offset=$next_offset}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
{* END OF PAGINATION *}

{include file=tiki-g-monitor_stats.tpl}
