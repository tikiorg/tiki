{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-monitor_instances.php">{tr}Monitor instances{/tr}</a>

      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}GalaxiaMonitorInstances" target="tikihelp" class="tikihelp" title="{tr}Galaxia Monitor Instances{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-g-monitor_instances.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Galaxia Monitor Instances tpl{/tr}"><img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}


</h1>

<h2>{tr}List of instances{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="tiki-g-monitor_instances.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
<tr>
<td >
	<small>{tr}Process{/tr}</small>
</td>
<td >
	<small>{tr}Name{/tr}</small>
</td>
<td >
	<small>{tr}Status{/tr}</small>
</td>
<td >
	<small>{tr}Creator{/tr}</small>
</td>
<td >
	&nbsp;
</td>	
</tr>

<tr>
<td >
	<select name="filter_process">
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
    {foreach from=$all_procs item=proc}
	<option {if $proc.pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$proc.pId|escape}">{$proc.name}</option>
	{/foreach}
	</select>
</td>
<td >
	<select name="filter_instanceName">
	<option {if '' eq $smarty.request.filter_instanceName}selected="selected"{/if} value="">{tr}All{/tr}</option>
    {*foreach from=$names item=name*}
    {section loop=$names name=ix}
    <option {if $names[ix] eq $smarty.request.filter_instanceName}selected="selected"{/if} value="{$names[ix]|escape}">{$names[ix]}</option>
    {/section}
    {*/foreach*}
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
	<select name="filter_owner">
	<option {if '' eq $smarty.request.filter_owner}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$users name=ix}
	<option {if $types[ix] eq $smarty.request.filter_user}selected="selected"{/if} value="{$users[ix]|escape}">{$users[ix]}</option>
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
<form action="tiki-g-monitor_instances.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'instanceId_desc'}{sameurl sort_mode='instanceId_asc'}{else}{sameurl sort_mode='instanceId_desc'}{/if}">{tr}Id{/tr}</a></td>
{*<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Activity{/tr}</a></td>*}
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'insName_desc'}{sameurl sort_mode='insName_asc'}{else}{sameurl sort_mode='insName_desc'}{/if}">{tr}Name{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Process{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'started_desc'}{sameurl sort_mode='started_asc'}{else}{sameurl sort_mode='started_desc'}{/if}">{tr}Started{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'status_desc'}{sameurl sort_mode='status_asc'}{else}{sameurl sort_mode='status_desc'}{/if}">{tr}Status{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'ended_desc'}{sameurl sort_mode='ended_asc'}{else}{sameurl sort_mode='ended_desc'}{/if}">{tr}Ended{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'user_desc'}{sameurl sort_mode='user_asc'}{else}{sameurl sort_mode='owner_desc'}{/if}">{tr}Creator{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{foreach from=$items item=proc}
<tr>
	<td class="{cycle advance=false}">
	  	<a class="link" href="tiki-g-admin_instance.php?iid={$proc.instanceId}">{$proc.instanceId}</a>
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		<a class="link" href="tiki-g-admin_instance.php?iid={$proc.instanceId}">{$proc.insName}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{tr}{$proc.name}{/tr}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
		{$proc.started|date_format}
	</td>	<td class="{cycle advance=false}" style="text-align:center;">
		{$proc.status}
	</td>	<td class="{cycle advance=false}" style="text-align:center;">
		{if $proc.ended eq 0} {tr}Not ended{/tr} {else} {$proc.ended|date_format} {/if}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
	{$proc.owner}
	</td>
</tr>
{foreachelse}
<tr>
	<td class="{cycle advance=false}" colspan="7">
	{tr}No instances created yet{/tr}
	</td>
</tr>	
{/foreach}
</table>
</form>
{* END OF LISTING *}

{* PAGINATION *}
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{* END OF PAGINATION *}
