{* $Id$ *}
{title}Mass Search and Replace{/title}

{remarksbox type="note" title="{tr}Note{/tr}"}
	{tr}This feature currently searches and replaces within wiki pages only{/tr}
{/remarksbox}

<div class="panel panel-default">
	<div class="panel-body">
		<form action="tiki-search_replace.php" method="post">
			{tr}Search:{/tr} <input type="text" size="30" name="searchtext" value="{$searchtext|escape}">
			&nbsp;{tr}Case sensitive:{/tr} <input type="checkbox" name="casesensitive" value="y" {if $casesensitive eq 'y'}checked="checked"{/if}>
			<br>{tr}Replace:{/tr} <input type="text" size="30" name="replacetext" value="{$replacetext|escape}">
			<br>{tr}Max number of pages at a time:{/tr} <input type="text" size="5" name="maxRecords" value="{$maxRecords|escape}">
			&nbsp;{tr}Number of surrounding chars to preview:{/tr} <input type="text" size="5" name="paddingLength" value="{$paddingLength|escape}">
			<br>
			<select name="categId">
				<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
				{foreach $categories as $catix}
					<option value="{$catix.categId|escape}" {if $find_categId eq $catix.categId}selected="selected"{/if}>
						{capture}{tr}{$catix.categpath}{/tr}{/capture}{$smarty.capture.default|escape}
					</option>
				{/foreach}
			</select>
			<input type="submit" class="btn btn-default btn-sm" name="search" value="{tr}Search{/tr}">
		</form>
	</div>
</div>
<div class="searchreplace_results">
	{if isset($message)}
		{$message}
	{/if}
	<form action="tiki-search_replace.php" method="post">
		{if isset($results)}
			<div class="table-responsive">
				<table class="table">
					<tr>
						<td style="text-align: right">
							{select_all checkbox_names='checked[]'}
						</td>
					</tr>

					{section name=search loop=$results}
						{if isset($results[search].searchreplace)}
							<tr>
								<td>
									<table>
										<tr>
											<th colspan="3">
												<a href="{$results[search].pageName|sefurl}" target="_blank">{$results[search].pageName|escape}</a>
											</th>
										</tr>
										{section name=snippet loop=$results[search].beforeSnippet}
											<tr>
												<td style="border: 1px solid">
													{* note that non-escaping is intentional *}
													{$results[search].beforeSnippet[snippet]}
												</td>
												<td style="border: 1px solid">
													{* note that non-escaping is intentional *}
													{$results[search].afterSnippet[snippet]}
												</td>
												<td>
													{if $results[search].searchreplace[snippet] != '0:0:0'}<input type="checkbox" name="checked[]" value="{$results[search].searchreplace[snippet]}">{/if}
												</td>
											</tr>
										{/section}
									</table>
								</td>
							</tr>
						{/if}
					{/section}
				</table>
			</div>
			<input type="hidden" name="searchtext" value="{$searchtext}">
			<input type="hidden" name="replacetext" value="{$replacetext}">
			<input type="hidden" name="maxRecords" value="{$maxRecords}">
			<input type="hidden" name="casesensitive" value="{$casesensitive}">
			<input type="hidden" name="paddingLength" value="{$paddingLength}">
			<input type="submit" class="btn btn-default btn-sm" name="replace" value="{tr}Replace selected{/tr}">
			{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
		{/if}
	</form>
</div>
