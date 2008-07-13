{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-user_processes.php">{tr}User processes{/tr}</a>

  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}GalaxiaUserProcesses" target="tikihelp" class="tikihelp" title="{tr}Galaxia User Processes{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}



      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-g-user_processes.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Galaxia User Processes tpl{/tr}"><img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}

</h1>
{include file=tiki-g-user_bar.tpl}
<h2>{tr}List of processes{/tr} ({$cant})</h2>

{* FILTERING FORM *}
<form action="tiki-g-user_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
<tr>
<td>
	<small>{tr}Find{/tr}</small>
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
	<input type="submit" name="filter" value="{tr}Filter{/tr}" />
</td>
</tr>
</table>	
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="tiki-g-user_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></td>
<td  class="heading" >{tr}Activities{/tr}</td>
<td  class="heading" >{tr}Instances{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-user_activities.php?filter_process={$items[ix].pId}">{$items[ix].procname} {$items[ix].version}</a>
	</td>
	<td class="{cycle advance=false}" style="text-align:right;">
		<a class="link" href="tiki-g-user_activities.php?filter_process={$items[ix].pId}">{$items[ix].activities}</a>
	</td>
	<td class="{cycle}" style="text-align:right;">
		<a class="link" href="tiki-g-user_instances.php?filter_process={$items[ix].pId}">{$items[ix].instances}</a>
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="3">
	{tr}No processes defined or activated yet{/tr}
	</td>
</tr>	
{/section}
</table>
</form>
{* END OF LISTING *}

{* PAGINATION *}
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{* END OF PAGINATION *}

