{* $Id$ *}
<h2 class="panel-title">Occurences of string in database</h2>
{if $SCRIPT_NAME == '/tiki-report_string_in_db.php'}
	<BR>
	<h3 class="panel-title">{tr}This page is no longer functional. Please use the Search feature in control panel.{/tr}</h3>
{else}
	<input type="text" name="string_in_db_search" size="60" /> <input type="submit" class="btn btn-default btn-sm" value="Search" onClick="document.getElementById('redirect').value='0';"/>
	<input type="hidden" id="redirect" name="redirect" value="1">
{/if}

<hr/>
{if isset($errorMsg)}
	<span id="error">{$errorMsg}</span>
{else}
	{if isset($searchString)}
		{remarksbox}{tr}Results for: {/tr}<b>{$searchString|escape}</b>{/remarksbox}{$searchString|escape}
		<p>

		<input type="hidden" name="query" value="{$searchString}">
		<input type="hidden" id="table" name="table" value="">
		<input type="hidden" id="column" name="column" value="">

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
				<input type="submit" class="btn btn-default" value="View" onClick="document.getElementById('table').value='{$res['table']}'; document.getElementById('column').value='{$res['column']}'; document.getElementById('redirect').value='0';">
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
			{foreach from=$row key=column item=val}
				{if $tableName=='tiki_pages' && $column=='pageName'}
					<td><a href=tiki-index.php?page={$val|escape} class="link tips" title="{$val|escape}:{tr}View page{/tr}">{$val|escape}</a></td>
				{else}
					<td>{$val|escape}</td>
				{/if}
			{/foreach}
			</tr>
		{/foreach}
	</table>
	{/if}
{/if}
