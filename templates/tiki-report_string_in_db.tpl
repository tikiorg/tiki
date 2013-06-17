{* $Id$ *}
<h1>Occurences of string in database</h1>

<form action="#">
<input type="text" name="string_in_db_search" size="60" /> <input type="submit" value="Search" />
</form>
<hr/>
{if isset($searchString)}
	<p>
	String: {$searchString}
	</p>
	<p>
	<table class="string_in_db_search">
	<tr>
	<td>Table</td>
	<td>Column</td>
	<td>Occurrences</td>
	<td>&nbsp;</td>
	</tr>
	{foreach from=$searchResult item=res}
		<tr>
		<td>{$res['table']}</td>
		<td>{$res['column']}</td>
		<td>{$res['occurrences']}</td>
		<td><a href="tiki-report_string_in_db.php?query={$searchString}&table={$res['table']}&column={$res['column']}">View</a></td>
		</tr>
	{/foreach}
	</table>
	</p>
{/if}

{if isset($tableHeaders)}
<table>
	<tr>
	{foreach from=$tableHeaders item=hdr}
		<td>{$hdr}</td>
	{/foreach}
	</tr>

	{foreach from=$tableData item=row}
		<tr>
		{foreach from=$row item=val}
			<td>{$val}</td>
		{/foreach}
		</tr>
	{/foreach}
</table>
{/if}

