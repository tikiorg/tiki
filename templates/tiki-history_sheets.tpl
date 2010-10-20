
{title help="Spreadsheet"}Spreadsheet History: {$title}{/title}

<div>
  {$description}
</div>

{if not $grid_content eq ''}
	<a href="#" id="go_fullscreen">Full Screen</a>
	<table style="width: 100%;" id="tiki_sheet_container">
		<tr>
			<td>
				{pagination_links cant=$ver_cant itemname="{tr}Sheet{/tr}" offset_arg="idx_0" offset="`$sheetIndexes[0]`" show_numbers=n}{/pagination_links}
			</td>
			<td>
				{pagination_links cant=$ver_cant itemname="{tr}Sheet{/tr}" offset_arg="idx_1" offset="`$sheetIndexes[1]`" show_numbers=n}{/pagination_links}
			</td>
		</tr>
		<tr>
			{section name=date loop=$grid_content}
				<td style="width: 50%;">
					<div style="font-size: 1.5em; text-align: center;">Revision: {$datesFormatted[$smarty.section.date.index]}</div>
					
					<div class="tiki_sheet" {if !empty($tiki_sheet_div_style)} style="{$tiki_sheet_div_style}"{/if}>
						{$grid_content[$smarty.section.date.index]}
					</div>
					
					{button href="tiki-view_sheets.php?sheetId=$sheetId&readdate=`$history[$smarty.section.date.index].stamp`&parse=edit" _id="edit_button" _text="{tr}Edit{/tr}" _htmlelement="role_main" sheetId="$sheetId" _class="" parse="edit" editSheet="y" _auto_args="*" _title="{tr}Edit{/tr}"}
				</td>
			{/section}
		</tr>
	</table>
	
	<div class="navbar">
		{button href="tiki-history_sheets.php?sheetId=$sheetId" _text="{tr}Back{/tr}"}
	</div>
{/if}


<table style="width: 100%;">
	<tr>
		<th>Edit Date</th>
		<th>User</th>
		<th colspan="2">
			<input type="hidden" id="sheetId" value="{$sheetId}" />
			<input type="button" id="compareSheetsSubmit" value="compare" onclick="compareSheetsSubmitClick(this); return false;" />
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
			   	<input type="radio" name="compareSheet1" class="compareSheet1" value="{$history[revision_date].index}" onclick="compareSheetClick(this);" />
	    	</td>
	    	<td style="vertical-align: middle; text-align: center;">
			   	<input type="radio" name="compareSheet2" class="compareSheet2" value="{$history[revision_date].index}" onclick="compareSheetClick(this);" />
	    	</td>
    		<td>
    			<a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$history[revision_date].stamp}">View</a>
    		</td>
    	</tr>
	{/section}
</table>