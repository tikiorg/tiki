{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-monitor_activities.php">{tr}Monitor activities{/tr}</a>

  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}GalaxiaMonitorActivities" target="tikihelp" class="tikihelp" title="{tr}Galaxia Monitor Activities{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}


      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-g-monitor_activities.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Galaxia Monitor Activities tpl{/tr}"><img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}


</h1>
{include file=tiki-g-monitor_bar.tpl}
<h2>{tr}List of activities{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form id="filterf" action="tiki-g-monitor_activities.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
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
	<small>{tr}Type{/tr}</small>
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
	<select name="filter_process" onchange='javascript:getElementById("filterf").submit();'>
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{foreach from=$all_procs item=proc}
	<option {if $proc.pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$proc.pId|escape}">{$proc.name} {$proc.version}</option>
	{/foreach}
	</select>
</td>
<td >
	<select name="filter_activity">
	<option {if '' eq $smarty.request.filter_activity}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{foreach from=$all_acts item=act}
	<option {if $act.activityId eq $smarty.request.filter_activity}selected="selected"{/if} value="{$act.activityId|escape}">{$act.name}</option>
	{/foreach}
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
	<input type="submit" name="filter" value="{tr}Filter{/tr}" />
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
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'type_desc'}{sameurl sort_mode='type_asc'}{else}{sameurl sort_mode='type_desc'}{/if}">&nbsp;</a></td>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Name{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'type_desc'}{sameurl sort_mode='type_asc'}{else}{sameurl sort_mode='type_desc'}{/if}">{tr}Type{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'isInteractive_desc'}{sameurl sort_mode='isInteractive_asc'}{else}{sameurl sort_mode='isInteractive_desc'}{/if}">{tr}int{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'isAutoRouted_desc'}{sameurl sort_mode='isAutoRouted_asc'}{else}{sameurl sort_mode='isAutoRouted_desc'}{/if}">{tr}routing{/tr}</a></td>
<td  class="heading" >{tr}Instances{/tr}<br />
</td>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$items item=act}
<tr>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$act.type|act_icon:$act.isInteractive}
	</td>


	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_activities.php?pid={$act.pId}&amp;activityId={$act.activityId}">{$act.name}</a>
	  {if $act.type eq 'standalone'}
	  <a href="tiki-g-run_activity.php?activityId={$act.activityId}"><img alt='{tr}run{/tr}' title='{tr}run activity{/tr}' src='lib/Galaxia/img/icons/next.gif' border='0' /></a>
	  {/if}
	  {if $act.type eq 'start'}
	  <a onclick="var answer = prompt('{tr}Enter the name of this instance{/tr}:','');while(answer == '')answer = prompt('{tr}The name is not valid. Please, enter the name again{/tr}:','');if (answer != null)window.location = 'tiki-g-run_activity.php?activityId={$act.activityId}&createInstance=1&name='+answer;"><img border="0" src='lib/Galaxia/img/icons/next.gif' alt='{tr}run{/tr}' title='{tr}run activity{/tr}' /></a>{*<a href="tiki-g-run_activity.php?activityId={$act.activityId}&amp;createInstance=1"><img alt='{tr}run{/tr}' title='{tr}run activity{/tr}' src='lib/Galaxia/img/icons/next.gif' border='0' /></a>*}
	  {/if}
	</td>
  

	<td class="{cycle advance=false}" style="text-align:left;">
		{$act.type}
	</td>
	
	<td class="{cycle advance=false}" style="text-align:center;">
		{$act.isInteractive}
	</td>


	<td class="{cycle advance=false}" style="text-align:center;">
		{$act.isAutoRouted}
	</td>
	
	<td class="{cycle}" style="text-align:right;">
		<table >
		<tr>
 		 <td style="text-align:right;"><a style="color:green;">{$act.active_instances}</a></td>
		 <td style="text-align:right;"><a style="color:black;">{$act.completed_instances}</a></td>
		 <td style="text-align:right;"><a style="color:grey;">{$act.aborted_instances}</a></td>
		 <td style="text-align:right;"><a style="color:red;">{$act.exception_instances}</a></td>

		</tr>
		</table>
	</td>
</tr>
{foreachelse}
<tr>
	<td class="{cycle advance=false}" colspan="6">
	{tr}No processes defined yet{/tr}
	</td>
</tr>	
{/foreach}
</table>
</form>
{* END OF LISTING *}

{* PAGINATION *}
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links} 
{* END OF PAGINATION *}

{include file=tiki-g-monitor_stats.tpl}
