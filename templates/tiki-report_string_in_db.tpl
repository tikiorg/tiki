{* $Id$ *}
<h1 class="pagetitle">Occurences of string in database</h1>

<form action="tiki-report_string_in_db.php" method="post">
<input type="text" name="string_in_db_search" size="60" /> <input type="submit" class="btn btn-default btn-sm" value="Search" />
</form>
<hr/>
{if isset($errorMsg)}
	<span id="error">{$errorMsg}</span>
{else}
	{if isset($searchString)}
		{remarksbox}{tr}Results for: {/tr}<b>{$searchString|escape}</b>{/remarksbox}{$searchString|escape}
		<p>
		<table class="string_in_db_search table normal">
		<tr>
		<th>{tr}Table{/tr}</th>
		<th>{tr}Column{/tr}</th>
		<th>{tr}Occurrences{/tr}</th>
		<th>&nbsp;</th>
		</tr>
		{foreach from=$searchResult item=res}
			<tr>
			<td>{$res['table']|escape}</td>
			<td>{$res['column']|escape}</td>
			<td>{$res['occurrences']|escape}</td>
			<td>
				<form action="tiki-report_string_in_db.php" method="post">
					<input type="hidden" name="query" value="{$searchString}">
					<input type="hidden" name="table" value="{$res['table']}">
					<input type="hidden" name="column" value="{$res['column']}">
					<input type="submit" class="btn btn-default" value="View">
				</form>
			</td>
			</tr>
		{/foreach}
		</table>
		</p>
	{/if}

	{if isset($tableHeaders)}
	<table class="table">
		<tr>
		{foreach from=$tableHeaders item=hdr}
			<th>{$hdr}</th>
		{/foreach}
		</tr>

		{foreach from=$tableData item=row}
			<tr>
			{foreach from=$row item=val}
				<td>{$val|escape}</td>
			{/foreach}
			</tr>
		{/foreach}
	</table>
	{/if}
{/if}
