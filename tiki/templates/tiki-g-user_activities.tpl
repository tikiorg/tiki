{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-g-user_activities.php">{tr}User Activities{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=GalaxiaUserActivities" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Galaxia User Activities{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' />{/if}
                        {if $feature_help eq 'y'}</a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-g-user_activities.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Galaxia User Activities tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /> {/if}
{if $feature_view_tpl eq 'y'}</a>{/if}

<!-- beginning of next bit -->







<br/><br/>
{include file=tiki-g-user_bar.tpl}
<h3>{tr}List of processes{/tr} ({$cant})</h3>

{* FILTERING FORM *}
<form action="tiki-g-user_activities.php" method="post" id='fform'>
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
<tr>
<td>
	<small>{tr}process{/tr}</small>
</td>
<td>
	<small>{tr}find{/tr}</small>
</td>

<td >
	&nbsp;
</td>	
</tr>

<tr>
<td >
	<select onchange='javascript:getElementById("fform").submit();' name="filter_process" onchange='javascript:getElementById("filterf").submit();'>
	<option {if '' eq $smarty.request.filter_process}selected="selected"{/if} value="">{tr}All{/tr}</option>
	{section loop=$all_procs name=ix}
	<option {if $all_procs[ix].pId eq $smarty.request.filter_process}selected="selected"{/if} value="{$all_procs[ix].pId|escape}">{$all_procs[ix].procname} {$all_procs[ix].version}</option>
	{/section}
	</select>
</td>
<td >
	<input size="8" type="text" name="find" value="{$find|escape}" />
</td>
<td >	
	<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td>
</tr>
</table>	
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="tiki-g-user_activities.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="{if $sort_mode eq 'name_desc'}{sameurl sort_mode='name_asc'}{else}{sameurl sort_mode='name_desc'}{/if}">{tr}Activity{/tr}</td>
<td  class="heading" >{tr}Instances{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}">
	  {$items[ix].procname} {$items[ix].version}
	</td>
	<td class="{cycle advance=false}" style="text-align:left;">
		{$items[ix].type|act_icon:"$items[ix].isInteractive"} 
		{if $items[ix].instances > 0}
		<a class="link" href="tiki-g-user_instances.php?filter_process={$items[ix].pId}&amp;filter_activity={$items[ix].activityId}">{$items[ix].name}</a>
		{else}
		{$items[ix].name}
		{/if}
		{if $items[ix].isInteractive eq 'y' and ($items[ix].type eq 'start' or $items[ix].type eq 'standalone')}
			<a href="tiki-g-run_activity.php?activityId={$items[ix].activityId}"><img border="0" src='lib/Galaxia/img/icons/next.gif' alt='{tr}run{/tr}' title='{tr}run activity{/tr}' /></a>		  
		{/if}
	</td>
	<td class="{cycle}" style="text-align:right;">
		{$items[ix].instances}
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="3">
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

