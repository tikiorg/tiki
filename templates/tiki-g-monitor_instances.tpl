{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-g-monitor_instances.php">{tr}Monitor instances{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="{$helpurl}GalaxiaMonitorInstances" target="tikihelp" class="tikihelp" title="{tr}Galaxia Monitor Instances{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' />{/if}
                        {if $feature_help eq 'y'}</a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-g-monitor_instances.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Galaxia Monitor Instances tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt="{tr}edit tpl{/tr}" /> {/if}
{if $feature_view_tpl eq 'y'}</a>{/if}

<!-- beginning of next bit -->






<br /><br />

<h3>{tr}List of instances{/tr} ({$cant})</h3>

{* FILTERING FORM *}
<form action="tiki-g-monitor_instances.php" method="post">
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
	<input size="8" type="text" name="find" value="{$find|escape}" />
</td>
<td >
	<select name="filter_process">
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
    {foreach from=$all_procs item=proc}
	<option {if $proc.pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$proc.pId|escape}">{$proc.name}</option>
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
	<select name="filter_status">
	<option {if '' eq $smarty.request.filter_status}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$statuses name=ix}
	<option {if $types[ix] eq $smarty.request.filter_status}selected="selected"{/if} value="{$statuses[ix]|escape}">{$statuses[ix]}</option>
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
	<option {if $types[ix] eq $smarty.request.filter_user}selected="selected"{/if} value="{$users[ix]|escape}">{$users[ix]}</option>
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
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'instanceId_desc'}{sameurl sort_mode='instanceId_asc'}{else}{sameurl sort_mode='instanceId_desc'}{/if}">{tr}Id{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Activity{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'status_desc'}{sameurl sort_mode='status_asc'}{else}{sameurl sort_mode='status_desc'}{/if}">{tr}Status{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'user_desc'}{sameurl sort_mode='user_asc'}{else}{sameurl sort_mode='user_desc'}{/if}">{tr}User{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$items item=proc}
<tr>
	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_instance.php?iid={$proc.instanceId}">{$proc.instanceId}</a>
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$proc.name}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$proc.status}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$proc.user}
	</td>
</tr>
{foreachelse}
<tr>
	<td class="{cycle advance=false}" colspan="6">
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
[<a class="prevnext" href="tiki-g-monitor_instances.php?where={$where}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-g-monitor_instances.php?where={$where}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-g-monitor_instances.php?where={$where}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
{* END OF PAGINATION *}
