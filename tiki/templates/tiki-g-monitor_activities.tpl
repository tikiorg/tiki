{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-g-monitor_activities.php">{tr}Monitor activities{/tr}</a>
<!-- the help link info --->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=GalaxiaMonitorActivities" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Galaxia Monitor Activities{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' />{/if}
                        {if $feature_help eq 'y'}</a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-g-monitor_activities.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Galaxia Monitor Activities tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /> {/if}
{if $feature_view_tpl eq 'y'}</a>{/if}

<!--- beginning of next bit --->










<br/><br/>
{include file=tiki-g-monitor_bar.tpl}
<h3>{tr}List of activities{/tr} ({$cant})</h3>

{* FILTERING FORM *}
<form id="filterf" action="tiki-g-monitor_activities.php" method="post">
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
	<small>{tr}act{/tr}</small>
</td>
<td >
	<small>{tr}type{/tr}</small>
</td>
<td >
	<small>{tr}inter{/tr}</small>
</td>
<td >
	<small>{tr}auto{/tr}</small>
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
	<select name="filter_process" onChange='javascript:getElementById("filterf").submit();'>
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$all_procs name=ix}
	<option {if $all_procs[ix].pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$all_procs[ix].pId|escape}">{$all_procs[ix].name} {$all_procs[ix].version}</option>
	{/section}
	</select>
</td>
<td >
	<select name="filter_activity">
	<option {if '' eq $smarty.request.filter_activity}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$all_acts name=ix}
	<option {if $all_acts[ix].activityId eq $smarty.request.filter_activity}selected="selected"{/if} value="{$all_acts[ix].activityId|escape}">{$all_acts[ix].name}</option>
	{/section}
	</select>
</td>
<td >
	<select name="filter_type">
	<option {if '' eq $smarty.request.filter_type}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$types name=ix}
	<option {if $types[ix] eq $smarty.request.filter_type}selected="selected"{/if} value="{$types[ix]|escape}">{$types[ix]}</option>
	{/section}
	</select>
</td>
<td >
	<select name="filter_isInteractive">
	<option {if '' eq $smarty.request.filter_isInteractive}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="y" {if 'y' eq $smarty.request.filter_isInteractive}selected="selected"{/if}>{tr}Interactive{/tr}</option>
	<option value="n" {if 'n' eq $smarty.request.filter_isInteractive}selected="selected"{/if}>{tr}Automatic{/tr}</option>
	</select>
</td>
<td >
	<select name="filter_isAutoRouted">
	<option {if '' eq $smarty.request.filter_isAutoRouted}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="y" {if 'y' eq $smarty.request.filter_isAutoRouted}selected="selected"{/if}>{tr}Manual{/tr}</option>
	<option value="n" {if 'n' eq $smarty.request.filter_isAutoRouted}selected="selected"{/if}>{tr}Automatic{/tr}</option>
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
<form action="tiki-g-monitor_activities.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td width="1%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'type_desc'}{sameurl sort_mode='type_asc'}{else}{sameurl sort_mode='type_desc'}{/if}">&nbsp;</a></td>
<td width="46%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Name{/tr}</a></td>
<td width="30%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'type_desc'}{sameurl sort_mode='type_asc'}{else}{sameurl sort_mode='type_desc'}{/if}">{tr}Type{/tr}</a></td>
<td width="2%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'isInteractive_desc'}{sameurl sort_mode='isInteractive_asc'}{else}{sameurl sort_mode='isInteractive_desc'}{/if}">{tr}int{/tr}</a></td>
<td width="2%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'isAutoRouted_desc'}{sameurl sort_mode='isAutoRouted_asc'}{else}{sameurl sort_mode='isAutoRouted_desc'}{/if}">{tr}routing{/tr}</a></td>
<td width="20%" class="heading" >{tr}Instances{/tr}<br/>
</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$items[ix].type|act_icon:"$items[ix].isInteractive"}
	</td>


	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_activities.php?pid={$items[ix].pId}&amp;activityId={$items[ix].activityId}">{$items[ix].name}</a>
	  {if $items[ix].type eq 'standalone'}
	  <a href="tiki-g-run_activity.php?activityId={$items[ix].activityId}"><img alt='{tr}run{/tr}' title='{tr}run activity{/tr}' src='lib/Galaxia/img/icons/next.gif' border='0' /></a>
	  {/if}
	  {if $items[ix].type eq 'start'}
	  <a href="tiki-g-run_activity.php?activityId={$items[ix].activityId}&amp;createInstance=1"><img alt='{tr}run{/tr}' title='{tr}run activity{/tr}' src='lib/Galaxia/img/icons/next.gif' border='0' /></a>
	  {/if}
	</td>
  

	<td class="{cycle advance=false}" style="text-align:left;">
		{$items[ix].type}
	</td>
	
	<td class="{cycle advance=false}" style="text-align:center;">
		{$items[ix].isInteractive}
	</td>


	<td class="{cycle advance=false}" style="text-align:center;">
		{$items[ix].isAutoRouted}
	</td>
	
	<td class="{cycle}" style="text-align:right;">
		<table width="100%">
		<tr>
 		 <td style="text-align:right;"><a style="color:green;" href="tiki-g-monitor_instances.php?filter_process={$items[ix].pId}&amp;filter_status=active&amp;filter_activity={$items[ix].activityId}">{$items[ix].active_instances}</a></td>
		 <td style="text-align:right;"><a style="color:black;" href="tiki-g-monitor_instances.php?filter_process={$items[ix].pId}&amp;filter_status=completed&amp;filter_activity={$items[ix].activityId}">{$items[ix].completed_instances}</a></td>
		 <td style="text-align:right;"><a style="color:grey;" href="tiki-g-monitor_instances.php?filter_process={$items[ix].pId}&amp;filter_status=aborted&amp;filter_activity={$items[ix].activityId}">{$items[ix].aborted_instances}</a></td>
		 <td style="text-align:right;"><a style="color:red;" href="tiki-g-monitor_instances.php?filter_process={$items[ix].pId}&amp;filter_status=exception&amp;filter_activity={$items[ix].activityId}">{$items[ix].exception_instances}</a></td>

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
<br/>
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
