{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-g-user_processes.php">{tr}User processes{/tr}</a>
<br/><br/>
{include file=tiki-g-user_bar.tpl}
<h3>{tr}List of processes{/tr} ({$cant})</h3>

{* FILTERING FORM *}
<form action="tiki-g-user_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<table>
<tr>
<td>
	<small>{tr}find{/tr}</small>
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
	<input type="submit" name="filter" value="{tr}filter{/tr}" />
</td>
</tr>
</table>	
</form>
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="tiki-g-user_processes.php" method="post">
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="find" value="{$find}" />
<input type="hidden" name="where" value="{$where}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<table class="normal">
<tr>
<td width="55%" class="heading" ><a class="tableheading" href="{if $sort_mode eq 'procname_desc'}{sameurl sort_mode='procname_asc'}{else}{sameurl sort_mode='procname_desc'}{/if}">{tr}Process{/tr}</a></td>
<td width="5%" class="heading" >{tr}Activities{/tr}</td>
<td width="5%" class="heading" >{tr}Instances{/tr}</td>
</td>
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

