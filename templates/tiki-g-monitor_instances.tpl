{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-g-monitor_instances.php">{tr}Monitor instances{/tr}</a>
<br/><br/>
{include file=tiki-g-monitor_bar.tpl}
<br/><br/>
[<a class="link" href="tiki-g-monitor_instances.php?remove_aborted=1">{tr}Remove all aborted instances{/tr}</a>]
{if $smarty.request.filter_process}
[<a class="link" href="tiki-g-monitor_instances.php?filter_process={$smarty.request.filter_process}&amp;remove_all=1">{tr}Remove all process instances{/tr}</a>]
{/if}
<h3>{tr}List of instances{/tr} ({$cant})</h3>
{* FILTERING FORM *}
<form action="tiki-g-monitor_instances.php" method="post" id='fform'>
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
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
	<small>{tr}status{/tr}</small>
</td>
<td >
	<small>{tr}act status{/tr}</small>
</td>
<td >
	<small>{tr}owner{/tr}</small>
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
	<select name="filter_process" onChange='javascript:document.getElementById("fform").submit();'>
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
	<select name="filter_status">
	<option {if '' eq $smarty.request.filter_status}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$statuses name=ix}
	<option {if $statuses[ix] eq $smarty.request.filter_status}selected="selected"{/if} value="{$statuses[ix]}">{$statuses[ix]}</option>
	{/section}
	</select>
</td>
<td >
	<select name="filter_act_status">
	<option {if '' eq $smarty.request.filter_act_status}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="running" {if 'y' eq $smarty.request.filter_act_status}selected="selected"{/if}>{tr}running{/tr}</option>
	<option value="completed" {if 'n' eq $smarty.request.filter_act_status}selected="selected"{/if}>{tr}completed{/tr}</option>
	</select>
</td>
<td >
	<select name="filter_owner">
	<option {if '' eq $smarty.request.filter_owner}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$owners name=ix}
	<option {if $owners[ix] eq $smarty.request.filter_owner}selected="selected"{/if} value="{$owners[ix]}">{$owners[ix]}</option>
	{/section}
	</select>
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
<form action="tiki-g-monitor_instances.php" method="post">
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="find" value="{$find}" />
<input type="hidden" name="where" value="{$where}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<table class="normal">
<tr>
<td width="2%" class="heading" ><input type="submit" name="delete" value="x " /></td>
<td width="2%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'instanceId_desc'}{sameurl sort_mode='instanceId_asc'}{else}{sameurl sort_mode='instanceId_desc'}{/if}">{tr}Id{/tr}</a></td>
<td width="15%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'status_desc'}{sameurl sort_mode='status_asc'}{else}{sameurl sort_mode='status_desc'}{/if}">{tr}Status{/tr}</a></td>
<td width="15%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></td>
<td width="15%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'owner_desc'}{sameurl sort_mode='owner_asc'}{else}{sameurl sort_mode='owner_desc'}{/if}">{tr}Owner{/tr}</a></td>
<td width="35%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Activity{/tr}</a></td>
<td width="15%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'user_desc'}{sameurl sort_mode='user_asc'}{else}{sameurl sort_mode='user_desc'}{/if}">{tr}User{/tr}</a></td>
<td width="5%" class="heading" >{tr}WIs{/tr}</td>
</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td style="text-align:center;" class="{cycle advance=false}">
		<input type="checkbox" name="inst[{$items[ix].instanceId}]" />
	</td>
	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_instance.php?iid={$items[ix].instanceId}">{$items[ix].instanceId}</a>
	</td>

	<td class="{cycle advance=false}" style="text-align:left;">
		<select name="update_status[{$items[ix].instanceId}]">
		{section name=jj loop=$all_statuses}
		<option value="{$all_statuses[jj]}" {if $items[ix].status eq $all_statuses[jj]}selected="selected"{/if}>{$all_statuses[jj]}</option>
		{/section}
		</select>
		
	</td>

	
	<td class="{cycle advance=false}" style="text-align:left;">  
		<a href="tiki-g-admin_processes.php?pid={$items[ix].pId}" class="link">{$items[ix].procname} {$items[ix].version}</a>
	</td>
	
	

	<td class="{cycle advance=false}" style="text-align:left;">  
		{$items[ix].owner}
	</td>


	<td class="{cycle advance=false}" style="text-align:left;">
	    {if $items[ix].name}
		{$items[ix].type|act_icon:"$items[ix].isInteractive"} <a class="link" href="tiki-g-admin_activities.php?pid={$items[ix].pId}&amp;activityId={$items[ix].activityId}">{$items[ix].name}</a> 
		<select name="update_actstatus[{$items[ix].instanceId}]">
		<option value="running:{$items[ix].activityId}" {if $items[ix].actstatus eq 'running'}selected="selected"{/if}>running</option>
		<option value="completed:{$items[ix].activityId}" {if $items[ix].actstatus eq 'completed'}selected="completed"{/if}>completed</option>
		</select>
  	    {if $items[ix].isInteractive eq 'y' and $items[ix].actstatus eq 'running'}
	       <a href="tiki-g-run_activity.php?iid={$items[ix].instanceId}&amp;activityId={$items[ix].activityId}"><img alt='{tr}run{/tr}' title='{tr}run activity{/tr}' src='lib/Galaxia/img/icons/next.gif' border='0' /></a>
	    {/if}
	    {if $items[ix].actstatus eq 'completed'}
	       <a href="tiki-g-monitor_instances.php?sendInstance={$items[ix].instanceId}&amp;activityId={$items[ix].activityId}"><img border='0' src="lib/Galaxia/img/icons/linkto.gif" alt='{tr}send{/tr}' title='{tr}send to next activity{/tr}' /></a>
	    {/if}
		{/if}
	</td>
	


	<td class="{cycle advance=false}" style="text-align:left;">
		{$items[ix].user}
	</td>
	
	<td class="{cycle}" style="text-align:right;">  
		<a class="link" href="tiki-g-monitor_workitems.php?filter_instance={$items[ix].instanceId}">{$items[ix].workitems}</a>
	</td>

	
	
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="8">
	{tr}No instances created yet{/tr}
	</td>
</tr>	
{/section}
<tr>
	<td colspan="8" class="heading" style="text-align:center;">
	<input type="submit" name="update" value="{tr}update{/tr}" />
	</td>
</tr>	
</table>
</form>
{* END OF LISTING *}

{* PAGINATION *}
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-g-monitor_instances.php?where={$where}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-g-monitor_instances.php?where={$where}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-g-monitor_instances.php?where={$where}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
{* END OF PAGINATION *}

{include file=tiki-g-monitor_stats.tpl}