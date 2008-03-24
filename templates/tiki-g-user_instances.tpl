{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-user_instances.php">{tr}User instances{/tr}</a>

  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}GalaxiaUserInstances" target="tikihelp" class="tikihelp" title="{tr}Galaxia User Instances{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}



      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-g-user_instances.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Galaxia User Instances tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}

</h1>
{include file=tiki-g-user_bar.tpl}
<h2>{tr}List of processes{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="tiki-g-user_instances.php" method="post">
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
	<small>{tr}Status{/tr}</small>
</td>
{*<td >
	<small>{tr}act status{/tr}</small>
</td>*}
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
	{section loop=$all_procs name=ix}
	<option {if $all_procs[ix].pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$all_procs[ix].pId|escape}">{$all_procs[ix].procname} {$all_procs[ix].version}</option>
	{/section}
	</select>
</td>
<td >
	<select name="filter_status">
	<option {if '' eq $smarty.request.filter_status}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$statuses name=ix}
	<option {if $statuses[ix] eq $smarty.request.filter_status}selected="selected"{/if} value="{$statuses[ix]|escape}">{tr}{$statuses[ix]}{/tr}</option>
	{/section}
	</select>
</td>
{*<td >
	<select name="filter_act_status">
	<option {if '' eq $smarty.request.filter_act_status}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option value="running" {if 'y' eq $smarty.request.filter_act_status}selected="selected"{/if}>{tr}running{/tr}</option>
	<option value="completed" {if 'n' eq $smarty.request.filter_act_status}selected="selected"{/if}>{tr}Completed{/tr}</option>
	</select>
</td>*}
<td >
<select name="filter_user">
	<option {if '' eq $smarty.request.filter_user}selected="selected"{/if} value="">{tr}All{/tr}</option>
	<option {if '*' eq $smarty.request.filter_user}selected="selected"{/if} value="*">*</option>
	<option {if $user eq $smarty.request.filter_user}selected="selected"{/if} value="{$user|escape}">{$user}</option>
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
<form action="tiki-g-user_instances.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'iname_desc'}{sameurl sort_mode='iname_asc'}{else}{sameurl sort_mode='iname_desc'}{/if}">{tr}Name{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'owner_desc'}{sameurl sort_mode='owner_asc'}{else}{sameurl sort_mode='owner_desc'}{/if}">{tr}Owner{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'status_desc'}{sameurl sort_mode='status_asc'}{else}{sameurl sort_mode='status_desc'}{/if}">{tr}Inst Status{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Activity{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'user_desc'}{sameurl sort_mode='user_asc'}{else}{sameurl sort_mode='user_desc'}{/if}">{tr}User{/tr}</a></td>
{*<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'actstatus_desc'}{sameurl sort_mode='actstatus_asc'}{else}{sameurl sort_mode='actstatus_desc'}{/if}">{tr}Act status{/tr}</a></td>*}
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'exptime_desc'}{sameurl sort_mode='exptime_asc'}{else}{sameurl sort_mode='exptime_desc'}{/if}">{tr}Expiration Date{/tr}</a></td>
<td class="heading" >{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}">
	  {$items[ix].iname}
	</td>
	<td class="{cycle advance=false}">
	  {$items[ix].owner}
	</td>
	<td class="{cycle advance=false}">
	  {$items[ix].status}
	</td>
	<td class="{cycle advance=false}">
	  {$items[ix].procname} {$items[ix].version}
	</td>
	<td class="{cycle advance=false}">
	  {$items[ix].type|act_icon:$items[ix].isInteractive} {$items[ix].name}
	</td>
	<td class="{cycle advance=false}">
	  {$items[ix].user}
	</td>
{*	<td class="{cycle advance=false}">
	  {$items[ix].actstatus}
	</td>*}
	<td class="{cycle advance=false}">
	{if $items[ix].exptime eq 0}
	    {tr}Not defined{/tr}
	{else}
	  {$items[ix].exptime|date_format:"%A, %B %e, %H:%M:%S"}
	{/if}
	</td>
	<td class="{cycle}">
	  {*actions*}
	  <table>
	  <tr>
	  {*exception*}
      {if $tiki_p_exception_instance eq 'y'}
	  {if $items[ix].status ne 'aborted' and $items[ix].status ne 'exception' and $items[ix].user eq $user}
	  <td><a onclick="javascript:return confirm('Are you sure you want to expception this instance?');" href="tiki-g-user_instances.php?exception=1&amp;iid={$items[ix].instanceId}&amp;aid={$items[ix].activityId}"><img border='0' title='{tr}exception instance{/tr}' alt='{tr}exceptions instance{/tr}' src='lib/Galaxia/img/icons/stop.gif' /></a></td>
	  {/if}
      {/if}
	  {if $items[ix].isAutoRouted eq 'n' and $items[ix].actstatus eq 'completed'}
	  {*send*}
	  <td><a href="tiki-g-user_instances.php?send=1&amp;iid={$items[ix].instanceId}&amp;aid={$items[ix].activityId}"><img border='0' title='{tr}Send Instance{/tr}' alt='{tr}Send Instance{/tr}' src='lib/Galaxia/img/icons/linkto.gif' /></a></td>
	  {/if}
	  {if $items[ix].isInteractive eq 'y' and $items[ix].status eq 'active'}
	  {*run*}
	  <td><a href="tiki-g-run_activity.php?iid={$items[ix].instanceId}&amp;activityId={$items[ix].activityId}"><img border='0' title='{tr}run instance{/tr}' alt='{tr}run instance{/tr}' src='lib/Galaxia/img/icons/next.gif' /></a></td>
	  {/if}
	  {*abort*}
      {if $tiki_p_abort_instance eq 'y'}
	  {if $items[ix].status ne 'aborted' and $items[ix].user eq $user}
	  <td><a onclick="javascript:return confirm('Are you sure you want to abort this instance?');" href="tiki-g-user_instances.php?abort=1&amp;iid={$items[ix].instanceId}&amp;aid={$items[ix].activityId}"><img border='0' title='{tr}abort instance{/tr}' alt='{tr}abort instance{/tr}' src='lib/Galaxia/img/icons/trash.gif' /></a></td>
      {/if}
	  {/if}
	  {if $items[ix].user eq '*' and $items[ix].status eq 'active'}
	  {*grab*}
	  <td><a href="tiki-g-user_instances.php?grab=1&amp;iid={$items[ix].instanceId}&amp;aid={$items[ix].activityId}"><img border='0' title='{tr}grab instance{/tr}' alt='{tr}grab instance{/tr}' src='lib/Galaxia/img/icons/fix.gif' /></a></td>
	  {else}
	  {*release*}
	  {if $items[ix].status eq 'active'}
	  <td><a href="tiki-g-user_instances.php?release=1&amp;iid={$items[ix].instanceId}&amp;aid={$items[ix].activityId}"><img border='0' title='{tr}release instance{/tr}' alt='{tr}release instance{/tr}' src='lib/Galaxia/img/icons/float.gif' /></a></td>
	  {/if}
	  {/if}
	  </tr>
	  </table>
	</td>

</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="8">
	{tr}No instances defined yet{/tr}
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

