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
	</tr>
	{foreach from=$searchResult item=res}
		<tr>
		<td>{$res['table']}</td>
		<td>{$res['column']}</td>
		<td>{$res['occurrences']}</td>
		</tr>
	{/foreach}
	</table>
	</p>
{/if}
