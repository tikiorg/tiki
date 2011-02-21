{title}Mass Search and Replace{/title}

{remarksbox type="note" title="{tr}Note{/tr}"}
{tr}This feature currently searches and replaces within wiki pages only{/tr}
{/remarksbox}
 
<div class="simplebox">
<form action="tiki-search_replace.php" method="post">
{tr}Search:{/tr} <input type="text" size="30" name="searchtext" value="{$searchtext|escape}" />
&nbsp;{tr}Case sensitive:{/tr} <input type="checkbox" name="casesensitive" value="y" {if $casesensitive eq 'y'}checked="checked"{/if} />
<br />{tr}Replace:{/tr} <input type="text" size="30" name="replacetext" value="{$replacetext|escape}" />
<br />{tr}Max number of pages at a time:{/tr} <input type="text" size="5" name="maxRecords" value="{$maxRecords|escape}" />
&nbsp;{tr}Number of surrounding chars to preview:{/tr} <input type="text" size="5" name="paddingLength" value="{$paddingLength|escape}" />
<br />
<select name="categId">
	<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
	{section name=ix loop=$categories}
		<option value="{$categories[ix].categId|escape}" {if $find_categId eq $categories[ix].categId}selected="selected"{/if}>
		{capture}{tr}{$categories[ix].categpath}{/tr}{/capture}{$smarty.capture.default|escape}
		</option>
	{/section}
</select>
<input type="submit" name="search" value="{tr}Search{/tr}" />
</form>
</div>
<div class="searchreplace_results">
{if isset($message)}
	{$message}
{/if}
<form action="tiki-search_replace.php" method="post">
{if isset($results)}
	<table class="normal">
	<tr>
		<td style="text-align: right">
			{select_all checkbox_names='checked[]'}
		</td>
	</tr>
	{cycle values="even,odd" print=false}
	{section name=search loop=$results}
	{if isset($results[search].searchreplace)}
	<tr>
		<td>
			<table>
			<tr><th colspan="3">
			<a href="{$results[search].pageName|sefurl}" target="_blank">{$results[search].pageName|escape}</a>
			</th></tr>
			{section name=snippet loop=$results[search].beforeSnippet}
			<tr class="{cycle}">
				<td style="border: 1px solid">
					{* note that non-escaping is intentional *}
					{$results[search].beforeSnippet[snippet]}
				</td>
				<td style="border: 1px solid">
					{* note that non-escaping is intentional *}
					{$results[search].afterSnippet[snippet]}
				</td>
				<td>
					{if $results[search].searchreplace[snippet] != '0:0:0'}<input type="checkbox" name="checked[]" value="{$results[search].searchreplace[snippet]}"/>{/if} 
				</td>
			</tr>
			{/section}
			</table>
		</td>
	</tr>
	{/if}
	{/section}
	</table>
	<input type="hidden" name="searchtext" value="{$searchtext}" /> 
	<input type="hidden" name="replacetext" value="{$replacetext}" />
	<input type="hidden" name="maxRecords" value="{$maxRecords}" />
	<input type="hidden" name="casesensitive" value="{$casesensitive}" />
	<input type="hidden" name="paddingLength" value="{$paddingLength}" />
	<input type="submit" name="replace" value="{tr}Replace selected{/tr}" />
	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{/if}
</form>
</div>
