{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-g-monitor_instances.php">{tr}Monitor instances{/tr}</a>
<br/><br/>

<h3>{tr}List of instances{/tr} ({$cant})</h3>

{* FILTERING FORM *}
<form action="tiki-g-monitor_instances.php" method="post">
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
	<option {if $all_procs[ix].pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$all_procs[ix].pId}">{$all_procs[ix].name}</option>
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
	<option {if $types[ix] eq $smarty.request.filter_status}selected="selected"{/if} value="{$statuses[ix]}">{$statuses[ix]}</option>
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
	<select name="filter_user">
	<option {if '' eq $smarty.request.filter_user}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$users name=ix}
	<option {if $types[ix] eq $smarty.request.filter_user}selected="selected"{/if} value="{$users[ix]}">{$users[ix]}</option>
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
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'instanceId_desc'}{sameurl sort_mode='instanceId_asc'}{else}{sameurl sort_mode='instanceId_desc'}{/if}">{tr}Id{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Activity{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'status_desc'}{sameurl sort_mode='status_asc'}{else}{sameurl sort_mode='status_desc'}{/if}">{tr}Status{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'user_desc'}{sameurl sort_mode='user_asc'}{else}{sameurl sort_mode='user_desc'}{/if}">{tr}User{/tr}</a></td>
</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_instance.php?instanceId={$items[ix].instanceId}">{$items[ix].instanceId}</a>
	</td>
  

	<td class="{cycle advance=false}" style="text-align:center;">
		{$items[ix].name}
	</td>
	
	<td class="{cycle advance=false}" style="text-align:center;">
		{$items[ix].status}
	</td>


	<td class="{cycle advance=false}" style="text-align:center;">
		{$items[ix].user}
	</td>
	
	
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="6">
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