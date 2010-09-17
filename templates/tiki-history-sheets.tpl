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
	<table style="width: 100%;" id="tiki_sheet_container">
		<tr>
			{section name=date loop=$readdate}
				<td style="width: 50%;">
					<div class="tiki_sheet" {if !empty($tiki_sheet_div_style)} 
							style="{$tiki_sheet_div_style}"
						{/if}>
						{$grid_content[$smarty.section.date.index]}
					</div>
					
					{button href="tiki-view_sheets.php?sheetId=$sheetId&readdate=`$readdate[$smarty.section.date.index]`" _id="edit_button" _text="{tr}Edit{/tr}" _htmlelement="role_main" sheetId="$sheetId" _class="" parse="edit" editSheet="y" _auto_args="*" _title="{tr}Edit{/tr}"}
				</td>
			{/section}
		</tr>
	</table>
	
	<div class="navbar">
	
		{button href="tiki-history_sheets.php?sheetId=$sheetId" _id="back_button" _text="{tr}Back{/tr}" _htmlelement="role_main" sheetId="$sheetId" _class="" parse="back" _title="{tr}Back{/tr}"}

	</div>
{/if}
