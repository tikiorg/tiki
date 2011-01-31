{* $Id$ *}

{title help="Spreadsheet"}{tr}Spreadsheets{/tr}{/title}

{tabset}
{tab name="{tr}List{/tr}"}
<h2>{tr}Available Sheets{/tr}</h2>
{if $sheets or $find ne ''}
  {include file='find.tpl'}
{/if}

<table class="normal">
	<tr>
		<th>{self_link _sort_arg='sort_mode' _sort_field='title'}{tr}Title{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='description'}{tr}Description{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='user'}{tr}User{/tr}{/self_link}</th>
		<th>{tr}Actions{/tr}</th>
	</tr>
{cycle values="odd,even" print=false}
	{section name=changes loop=$sheets}
		<tr class="{cycle}">
			<td><a class="galname" href="tiki-view_sheets.php?sheetId={$sheets[changes].sheetId}">{$sheets[changes].title}</a></td>
			<td>{$sheets[changes].description}</td>
			<td>{$sheets[changes].author}</td>
			<td>
				{if $chart_enabled eq 'y'}
					<a class="gallink" href="tiki-graph_sheet.php?sheetId={$sheets[changes].sheetId}">
						<img src='pics/icons/chart_curve.png' width='16' height='16' alt="{tr}Graph{/tr}" title="{tr}Graph{/tr}" />
					</a>
				{/if}
				{if $tiki_p_view_sheet_history eq 'y'}
					<a class="gallink" href="tiki-history_sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheets[changes].sheetId}">
						{icon _id='application_form_magnify' alt="{tr}History{/tr}"}
					</a>
				{/if}
				<a class="gallink" href="tiki-export_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheets[changes].sheetId}">
					{icon _id='disk' alt="{tr}Export{/tr}"}
				</a>
				{if $sheets[changes].tiki_p_edit_sheet eq 'y'}
					<a class="gallink" href="tiki-import_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheets[changes].sheetId}">
						{icon _id='folder_add' alt="{tr}Import{/tr}"}
					</a>
				{/if}
				{if $tiki_p_admin_sheet eq 'y'}
					<a class="gallink" href="tiki-objectpermissions.php?objectName={$sheets[changes].title|escape:"url"}&amp;objectType=sheet&amp;permType=sheet&amp;objectId={$sheets[changes].sheetId}">
					{if $sheets[changes].individual eq 'y'}
						{icon _id='key_active' alt="{tr}Active Perms{/tr}"}
					{else}
						{icon _id='key' alt="{tr}Perms{/tr}"}
					{/if}
					</a>
				{/if}
				{if $sheets[changes].tiki_p_edit_sheet eq 'y'}
					<a class="gallink" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;sheetId={$sheets[changes].sheetId}">
						{icon _id='page_edit' alt="{tr}Edit{/tr}"}
					</a>
					<a class="gallink" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removesheet=y&amp;sheetId={$sheets[changes].sheetId}">
						{icon _id='cross' alt="{tr}Delete{/tr}"}
					</a>
				{/if}
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=4}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
{/tab}

{if $tiki_p_edit_sheet eq 'y'}
	{capture name=title}{if $sheetId eq 0}{tr}Create{/tr}{else}{tr}Edit{/tr}{/if}{/capture}
	{tab name=$smarty.capture.title}
		{if $sheetId eq 0}
			<h2>{tr}Create a sheet{/tr}</h2>
		{else}
			<h2>{tr}Edit this sheet:{/tr} {$title}</h2>
			{if $tiki_p_edit_sheet eq 'y'}
				<div class="navbar">
					{button href="tiki-sheets.php?edit_mode=1&amp;sheetId=0" _text="{tr}Create New Sheet{/tr}"}
				</div>
			{/if}
		{/if}
		
		{if $individual eq 'y'}
			<a class="gallink" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=sheet&amp;permType=sheet&amp;objectId={$sheetId}">
				{tr}There are individual permissions set for this sheet{/tr}
			</a>
		{/if}
		<form action="tiki-sheets.php" method="post">
			<input type="hidden" name="sheetId" value="{$sheetId|escape}" />
			<table class="formcolor">
				<tr><td>{tr}Title:{/tr}</td><td><input type="text" name="title" value="{$title|escape}"/></td></tr>
				<tr><td>{tr}Description:{/tr}</td><td><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></td></tr>
				<tr><td>{tr}Class Name:{/tr}</td><td><input type="text" name="className" value="{$className|escape}"/></td></tr>
				<tr><td>{tr}Header Rows:{/tr}</td><td><input type="text" name="headerRow" value="{$headerRow|escape}"/></td></tr>
				<tr><td>{tr}Footer Rows:{/tr}</td><td><input type="text" name="footerRow" value="{$footerRow|escape}"/></td></tr>
				<tr>
					<td>{tr}Wiki Parse Values:{/tr}</td><td>
						<input type="checkbox" name="parseValues"{if $parseValues eq 'y'} checked="checked"{/if}/>
					</td>
				</tr>
				{include file='categorize.tpl'}
				<tr>
					<td>{tr}Creator:{/tr}</td><td>
						{user_selector name="creator" editable=$tiki_p_admin_sheet}
					</td>
				</tr>
				<tr>
					<td>{tr}Parent SheetId:{/tr}</td>
					<td>
						<select name="parentSheetId">
							<option value="">{tr}None{/tr}</option>
							{section name=sheet loop=$sheets}
								{if $sheets[sheet].parentSheetId eq '0'}
								<option value="{$sheets[sheet].sheetId}"{if $parentSheetId eq $sheets[sheet].sheetId} selected="selected"{/if}>
									{$sheets[sheet].title|escape}
								</option>
								{/if}
							{/section}
						</select>
						<em>{tr}Makes this sheet a "child" sheet of a multi-sheet set{/tr}</em>
					</td>
				</tr>
				<tr><td>&nbsp;</td><td><input type="submit" value="{tr}Save{/tr}" name="edit" /></td></tr>
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
