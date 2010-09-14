<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />

{title help="Spreadsheet"}{$title}{/title}

<div>
  {$description}
</div>

{if not $history eq ''}

	<table style="width: 100%;">
		<tr>
			<th>Edit Date</th>
			<th>User</th>
			<th colspan="2">
				<input type="hidden" id="sheetId" value="{$sheetId}" />
				<input type="button" id="compareSheetsSubmit" value="compare" />
			</th>
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
				   		<input type="radio" name="compareSheet1" class="compareSheet1" value="{$history[revision_date].stamp}" />
		    	</td>
		    	<td style="vertical-align: middle; text-align: center;">
				   		<input type="radio" name="compareSheet2" class="compareSheet2" value="{$history[revision_date].stamp}" />
		    	</td>
	    		<td>
	    			<a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$history[revision_date].stamp}">View</a>
	    		</td>
	    	</tr>
		{/section}
	</table>
	
{else}
	<a href="tiki-history_sheets.php?sheetId={$sheetId}">Back</a>
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
	
{/if}
