<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />

{title help="Spreadsheet"}{$title}{/title}

<div>
  {$description}
</div>

<table style="width: 100%;">
	<tr>
		<th>Edit Date</th>
		<th>User</th>
		<th>Compare</th>
		<th></th>
	</tr>
	{section name=revision_date loop=$history}
		<tr>
			<td>
				{$history[revision_date].string}
			</td>
    		<td>
    			{$history[revision_date].user}
    		</td>
	    	<td style="vertical-align: middle; text-align: center;">
			   		<input type="checkbox" class="compareSheet" value="{$history[revision_date].stamp}" />
	    	</td>
    		<td>
    			<a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$history[revision_date].stamp}">View</a>
    		</td>
    	</tr>
	{/section}
	<tr>
		<td></td>
		<td></td>
		<td style="text-align: center;">
			<input type="hidden" id="sheetId" value="{$sheetId}" />
			<input type="button" id="compareSheetsSubmit" value="compare" />
		</td>
		<td></td>
	</tr>
</table>

<table style="width: 100%;" id="tiki_sheet_container">
	<tr>
		{foreach from=$grid_content item=thisGrid}
			<td style="width: 50%;">
				<div class="tiki_sheet" {if !empty($tiki_sheet_div_style)} 
						style="{$tiki_sheet_div_style}"
					{/if}>
					{$thisGrid}
				</div>
			</td>
		{/foreach}
	</tr>
</table>