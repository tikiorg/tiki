{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-g-monitor_processes.php">{tr}Monitor processes{/tr}</a>
<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=GalaxiaMonitorProcesses" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Galaxia Monitor Processes{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' />{/if}
                        {if $feature_help eq 'y'}</a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-g-monitor_processes.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Galaxia Monitor Processes tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /> {/if}
{if $feature_view_tpl eq 'y'}</a>{/if}

<!-- beginning of next bit -->






<br /><br />
{include file=tiki-g-monitor_bar.tpl}
<h3>{tr}List of processes{/tr} ({$cant})</h3>

{* FILTERING FORM *}
<form action="tiki-g-monitor_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
<tr>
<td>
	<small>{tr}find{/tr}</small>
</td>
<td >
	<small>{tr}proc{/tr}</small>
</td>
<td >
	<small>{tr}active{/tr}</small>
</td>
<td >
	<small>{tr}valid{/tr}</small>
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
	{section loop=$all_procs name=ix}
	<option {if $all_procs[ix].pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$all_procs[ix].pId|escape}">{$all_procs[ix].name}</option>
	{/section}
	</select>
</td>
<td >
	<select name="filter_active">
	<option {if '' eq $smarty.request.filter_active}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="y" {if 'y' eq $smarty.request.filter_active}selected="selected"{/if}>{tr}Active{/tr}</option>
	<option value="n" {if 'n' eq $smarty.request.filter_active}selected="selected"{/if}>{tr}Inactive{/tr}</option>
	</select>
</td>
<td >
	<select name="filter_valid">
	<option {if '' eq $smarty.request.filter_valid}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option {if 'y' eq $smarty.request.filter_valid}selected="selected"{/if} value="y">{tr}Valid{/tr}</option>
	<option {if 'n' eq $smarty.request.filter_valid}selected="selected"{/if} value="n">{tr}Invalid{/tr}</option>
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
<form action="tiki-g-monitor_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
<tr>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Name{/tr}</a></td>
<td  class="heading" >{tr}Activs{/tr}</td>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'isActive_desc'}{sameurl sort_mode='isActive_asc'}{else}{sameurl sort_mode='isActive_desc'}{/if}">{tr}act{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'isValid_desc'}{sameurl sort_mode='isValid_asc'}{else}{sameurl sort_mode='isValid_desc'}{/if}">{tr}val{/tr}</a></td>
<td  class="heading" >{tr}Instances{/tr}<br />
</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}">
	  <a href="tiki-g-admin_processes.php?pid={$items[ix].pId}">{$items[ix].name} {$items[ix].version}</a>
	</td>
	<td class="{cycle advance=false}" style="text-align:right;">
		<a href="tiki-g-monitor_activities.php?filter_process={$items[ix].pId}">{$items[ix].activities}</a>
	</td>


	  

	<td class="{cycle advance=false}" style="text-align:center;">
	  {if $items[ix].isActive eq 'y'}
	  <img src='lib/Galaxia/img/icons/refresh2.gif' alt=' ({tr}active{/tr}) ' title='{tr}active process{/tr}' />
	  {else}
	  &nbsp;
	  {/if}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
	  {if $items[ix].isValid eq 'n'}
	  <img src='lib/Galaxia/img/icons/red_dot.gif' alt=' ({tr}invalid{/tr}) ' title='{tr}invalid process{/tr}' />
	  {else}
	  <img src='lib/Galaxia/img/icons/green_dot.gif' alt=' ({tr}valid{/tr}) ' title='{tr}valid process{/tr}' />
	  {/if}

	</td>
	
	<td class="{cycle}" style="text-align:right;">
		<table >
		<tr>
		 <td style="text-align:right;"><a style="color:green;" href="tiki-g-monitor_instances.php?filter_process={$items[ix].pId}&amp;filter_status=active">{$items[ix].active_instances}</a></td>
		 <td style="text-align:right;"><a style="color:black;" href="tiki-g-monitor_instances.php?filter_process={$items[ix].pId}&amp;filter_status=completed">{$items[ix].completed_instances}</a></td>
		 <td style="text-align:right;"><a style="color:grey;" href="tiki-g-monitor_instances.php?filter_process={$items[ix].pId}&amp;filter_status=aborted">{$items[ix].aborted_instances}</a></td>
		 <td style="text-align:right;"><a style="color:red;" href="tiki-g-monitor_instances.php?filter_process={$items[ix].pId}&amp;filter_status=exception">{$items[ix].exception_instances}</a></td>
		</tr>
		</table>
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="6">
	{tr}No processes defined yet{/tr}
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
[<a class="prevnext" href="{sameurl offset=$prev_offset}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="{sameurl offset=$next_offset}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
{* END OF PAGINATION *}

{include file=tiki-g-monitor_stats.tpl}
