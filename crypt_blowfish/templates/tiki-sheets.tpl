{* $Id$ *}

{title help="Spreadsheet"}{tr}Spreadsheets{/tr}{/title}

{tabset}
{tab name="{tr}List{/tr}"}
{if $tiki_p_edit_sheet eq 'y'}
	<div class="navbar">
		{button href="tiki-sheets.php?edit_mode=1&amp;sheetId=0" _text="{tr}Create New Sheet{/tr}"}
	</div>
{/if}
<h2>{tr}Spreadsheet{/tr}</h2>
{if $sheets or $find ne ''}
  {include file='find.tpl'}
{/if}

<table class="normal">
	<tr>
		<th>{self_link _sort_arg='sort_mode' _sort_field='title'}{tr}Title{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='description'}{tr}Description{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last Modif{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='user'}{tr}User{/tr}{/self_link}</th>
		<th>{tr}Actions{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{foreach item=sheet from=$sheets}
		{include name='base' file='tiki-sheets_listing.tpl' sheet=$sheet}
		{foreach item=childSheet from=$sheet.children}
			{include name='child' file='tiki-sheets_listing.tpl' sheet=$childSheet}
		{/foreach}
	{foreachelse}
		{norecords _colspan=6}
	{/foreach}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
{/tab}

{if $tiki_p_edit_sheet eq 'y'}
	{capture name=title}{if $sheetId eq 0}{tr}Create{/tr}{else}{tr}Configure{/tr}{/if}{/capture}
	{tab name=$smarty.capture.title}
		{if $sheetId eq 0}
			<h2>{tr}Create a sheet{/tr}</h2>
		{else}
			<h2>{tr}Configure this sheet:{/tr} {$title|escape}</h2>
		{/if}
		
		{if $individual eq 'y'}
			<a class="gallink" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=sheet&amp;permType=sheet&amp;objectId={$sheetId}">
				{tr}There are individual permissions set for this sheet{/tr}
			</a>
		{/if}
		<form action="tiki-sheets.php" method="post">
			<input type="hidden" name="sheetId" value="{$sheetId|escape}">
			<table class="formcolor">
				<tr><td>{tr}Title:{/tr}</td><td><input type="text" name="title" value="{$title|escape}"></td></tr>
				<tr><td>{tr}Description:{/tr}</td><td><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></td></tr>
				<!--<tr><td>{tr}Class Name:{/tr}</td><td><input type="text" name="className" value="{$className|escape}"></td></tr>
				<tr><td>{tr}Header Rows:{/tr}</td><td><input type="text" name="headerRow" value="{$headerRow|escape}"></td></tr>
				<tr><td>{tr}Footer Rows:{/tr}</td><td><input type="text" name="footerRow" value="{$footerRow|escape}"></td></tr>-->
				<tr>
					<td>{tr}Wiki Parse Values:{/tr}</td><td>
						<input type="checkbox" name="parseValues"{if $parseValues eq 'y'} checked="checked"{/if}>
					</td>
				</tr>
				{include file='categorize.tpl'}
				{if $tiki_p_admin_sheet eq "y"}
				<tr>
					<td>{tr}Creator:{/tr}</td><td>
						{user_selector name="creator" editable=$tiki_p_admin_sheet user=$creator}
					</td>
				</tr>
				{/if}
				<tr>
					<td>{tr}Parent Spreadsheet:{/tr}</td>
					<td>
						<select name="parentSheetId">
							<option value="0">{tr}None{/tr}</option>
							{foreach item=sheet from=$sheets}
								<option value="{$sheet.sheetId}"{if $parentSheetId eq $sheet.sheetId} selected="selected"{/if}>
									{$sheet.title|escape} - ({$sheet.sheetId})
								</option>
							{/foreach}
						</select>
						<em>{tr}Makes this sheet a "child" sheet of a multi-sheet set{/tr}</em>
					</td>
				</tr>
				<tr><td>&nbsp;</td><td><input type="submit" value="{tr}Save{/tr}" name="edit"></td></tr>
			</table>
		</form>
		
	{if $sheetId > 0}
		<div class="wikitext">
			{tr}You can access the sheet using the following URL:{/tr} <a class="gallink" href="{$url}?sheetId={$sheetId}">{$url}?sheetId={$sheetId}</a>
		</div>
	{/if}
	{/tab}
{/if}
{/tabset}
