{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-admin_chart_items.php">{tr}Admin chart items{/tr}</a>
<br /><br />
<a class="linkbut" href="tiki-admin_charts.php">{tr}charts{/tr}</a>
<a class="linkbut" href="tiki-admin_charts.php?chartId={$chartId}">{tr}edit chart{/tr}</a>
<a class="linkbut" href="tiki-view_chart.php?chartId={$chartId}">{tr}view{/tr}</a>
<h3>{tr}Add or edit an item{/tr} <a href="tiki-admin_chart_items.php?chartId=$chartId&amp;where={$where}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;itemId=0">{tr}new{/tr}</a>
</h3>
<form action="tiki-admin_chart_items.php" method="post">
<input type="hidden" name="chartId" value="{$chartId|escape}" />
<input type="hidden" name="itemId" value="{$info.itemId|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
	<tr>
		<td><label>{tr}Title{/tr}</label></td>
		<td><input type="text" maxlength="250" name="title" value="{$info.title|escape}" /></td>
	</tr>
	<tr>
		<td><label>{tr}Description{/tr}</label></td>
		<td><textarea rows="4" cols="40" name="description">{$info.description|escape}</textarea></td>
	</tr>
	<tr>
		<td><label>{tr}URL{/tr}</label></td>
		<td><input type="text" maxlength="250" name="URL" value="{$info.URL|escape}" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="save" value="{if $itemId > 0}{tr}update{/tr}{else}{tr}create{/tr}{/if}" /></td>
	</tr>
</table>
</form>

<h3>{tr}Chart items{/tr}</h3>
<form action="tiki-admin_chart_items.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="chartId" value="{$chartId|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<label>{tr}Find{/tr}:</label><input size="8" type="text" name="find" value="{$find|escape}" />
</form>

<form action="tiki-admin_chart_items.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="chartId" value="{$chartId|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
<tr>
<th><input type="submit" name="delete" value="{tr}x{/tr}" /></th>
<th><a href="{if $sort_mode eq 'title_desc'}{sameurl sort_mode="title_asc"}{else}{sameurl sort_mode="title_desc"}{/if}">{tr}Title{/tr}</a></th>
<th><a href="{if $sort_mode eq 'URL_desc'}{sameurl sort_mode="URL_asc"}{else}{sameurl sort_mode="URL_desc"}{/if}">{tr}URL{/tr}</a></th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr class="{cycle advance=false}">
	<td><input type="checkbox" name="item[{$items[ix].itemId}]" /></td>
	<td><a href="{sameurl itemId=$items[ix].itemId}">{$items[ix].title}</a></td>
	<td>{$items[ix].URL}</td>
</tr>
{sectionelse}
<tr><td colspan="5">{tr}No items defined yet{/tr}</td></tr>	
{/section}
</table>
</form>

<div class="mini" align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="{sameurl offset=$prev_offset}">{tr}prev{/tr}</a>] 
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
 [<a class="prevnext" href="{sameurl offset=$next_offset}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a> 
{/section}
{/if}
</div>